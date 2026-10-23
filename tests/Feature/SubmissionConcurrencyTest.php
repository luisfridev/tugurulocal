<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Listing;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class SubmissionConcurrencyTest extends TestCase
{
    use DatabaseMigrations;

    public function test_concurrent_approvals_create_one_draft_and_one_moderation_event(): void
    {
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $submission = Submission::factory()->create();
        $worker = <<<'WORKER'
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$actor = App\Models\User::findOrFail($argv[1]);
$submission = App\Models\Submission::findOrFail($argv[2]);
echo "ready\n";
try {
    app(App\Actions\Submissions\ModerateSubmission::class)->handle($actor, $submission, ['status' => 'approved', 'mode' => 'create', 'name' => 'Borrador concurrente', 'slug' => $argv[3], 'primary_category_id' => $submission->category_id, 'moderation_reason' => 'Prueba concurrente']);
    echo "approved\n";
} catch (Illuminate\Validation\ValidationException $exception) {
    if (! isset($exception->errors()['status'])) { throw $exception; }
    echo "rejected\n";
}
WORKER;
        $env = ['APP_ENV' => 'testing', 'DB_CONNECTION' => 'mysql', 'DB_DATABASE' => config('database.connections.mysql.database'), 'DB_URL' => ''];
        $processes = [
            new Process([PHP_BINARY, '-r', $worker, (string) $actor->id, (string) $submission->id, 'concurrente-uno'], base_path(), $env, timeout: 15),
            new Process([PHP_BINARY, '-r', $worker, (string) $actor->id, (string) $submission->id, 'concurrente-dos'], base_path(), $env, timeout: 15),
        ];
        DB::beginTransaction();
        City::query()->orderBy('id')->lockForUpdate()->firstOrFail();
        try {
            foreach ($processes as $process) {
                $process->start();
            }
            $deadline = microtime(true) + 5;
            while (microtime(true) < $deadline && collect($processes)->contains(fn (Process $process): bool => ! str_contains($process->getOutput(), 'ready'))) {
                usleep(10000);
            }
            foreach ($processes as $process) {
                $this->assertStringContainsString('ready', $process->getOutput(), $process->getErrorOutput());
                $this->assertTrue($process->isRunning());
            }
            DB::commit();
            foreach ($processes as $process) {
                $process->wait();
                $this->assertTrue($process->isSuccessful(), $process->getErrorOutput());
            }
            $outputs = array_map(fn (Process $process): string => $process->getOutput(), $processes);
            $this->assertCount(1, array_filter($outputs, fn (string $output): bool => str_contains($output, 'approved')));
            $this->assertCount(1, array_filter($outputs, fn (string $output): bool => str_contains($output, 'rejected')));
            $this->assertDatabaseCount('listings', 1);
            $listing = Listing::query()->sole();
            $this->assertSame('draft', $listing->status);
            $this->assertSame('approved', $submission->fresh()->status);
            $this->assertSame($listing->id, $submission->fresh()->listing_id);
            $this->assertSame(1, DB::table('audit_events')->where('action', 'submission.moderated')->count());
            $this->assertSame(1, DB::table('interaction_events')->where('type', 'submission.approved')->count());
        } finally {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            foreach ($processes as $process) {
                if ($process->isRunning()) {
                    $process->stop();
                }
            }
        }
    }
}
