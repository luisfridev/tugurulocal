<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class SlugConcurrencyTest extends TestCase
{
    use DatabaseMigrations;

    public function test_concurrent_rename_and_origin_claim_cannot_reassign_the_historical_url(): void
    {
        Storage::fake('listing_media');
        $actor = User::factory()->superAdmin()->withAppAuthentication()->create();
        $owner = Listing::factory()->published()->create(['slug' => 'origen-concurrente']);
        $claimant = Listing::factory()->recycle($owner->city)->create(['slug' => 'otra-ficha']);
        $worker = <<<'WORKER'
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
Illuminate\Support\Facades\Storage::persistentFake('listing_media');
$actor = App\Models\User::findOrFail($argv[1]);
$listing = App\Models\Listing::findOrFail($argv[2]);
$data = array_replace($listing->only(App\Models\Listing::FIELDS), ['slug' => $argv[3], 'category_ids' => $listing->categories()->pluck('categories.id')->all()]);
echo "ready\n";
try {
    app(App\Actions\Listings\SaveListing::class)->handle($actor, $data, $listing);
    echo "saved\n";
} catch (Illuminate\Validation\ValidationException $exception) {
    if (! isset($exception->errors()['slug'])) { throw $exception; }
    echo "rejected\n";
}
WORKER;
        $env = ['APP_ENV' => 'testing', 'DB_CONNECTION' => 'mysql', 'DB_DATABASE' => config('database.connections.mysql.database'), 'DB_URL' => ''];
        $processes = [
            new Process([PHP_BINARY, '-r', $worker, (string) $actor->id, (string) $owner->id, 'destino-concurrente'], base_path(), $env, timeout: 15),
            new Process([PHP_BINARY, '-r', $worker, (string) $actor->id, (string) $claimant->id, 'origen-concurrente'], base_path(), $env, timeout: 15),
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
            $this->assertStringContainsString('saved', $processes[0]->getOutput());
            $this->assertStringContainsString('rejected', $processes[1]->getOutput());
            $this->assertSame('destino-concurrente', $owner->fresh()->slug);
            $this->assertSame('otra-ficha', $claimant->fresh()->slug);
            $this->assertDatabaseHas('slug_redirects', ['source_path' => '/@origen-concurrente', 'listing_id' => $owner->id]);
            $this->get('/@origen-concurrente')->assertStatus(301)->assertRedirect(url('/@destino-concurrente'));
        } finally {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            foreach ($processes as $process) {
                if ($process->isRunning()) {
                    $process->stop();
                }
            }
            DB::table('slug_redirects')->delete();
        }
    }
}
