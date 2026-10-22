<?php

namespace App\Filament\Resources\AuditEvents;

use App\Enums\AuditAction;
use App\Enums\AuditResult;
use App\Filament\Resources\AuditEvents\Pages\ManageAuditEvents;
use App\Models\AuditEvent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuditEventResource extends Resource
{
    protected static ?string $model = AuditEvent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?string $navigationLabel = 'Auditoría';

    protected static ?string $modelLabel = 'evento de auditoría';

    protected static ?string $pluralModelLabel = 'Auditoría';

    protected static ?string $slug = 'auditoria';

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('occurred_at')->label('Fecha')->dateTime('d/m/Y H:i:s', timezone: 'America/Cancun')->sortable(),
                TextColumn::make('action')->label('Acción')->formatStateUsing(fn (AuditAction $state): string => $state->label())->wrap(),
                TextColumn::make('result')->label('Resultado')->formatStateUsing(fn (AuditResult $state): string => $state->label())->badge(),
                TextColumn::make('actor_id')->label('Cuenta responsable')->prefix('#')->placeholder('Sin sesión identificada'),
                TextColumn::make('subject_id')->label('Registro afectado')->prefix('#')->placeholder('—'),
                TextColumn::make('changed_fields')->label('Campos modificados')->listWithLineBreaks()->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('source')->label('Origen')->formatStateUsing(fn (string $state): string => $state === 'web' ? 'Panel' : 'Consola'),
            ])
            ->filters([
                SelectFilter::make('result')->label('Resultado')->options(collect(AuditResult::cases())->mapWithKeys(fn (AuditResult $result): array => [$result->value => $result->label()])),
                SelectFilter::make('action')->label('Acción')->options(collect(AuditAction::cases())->mapWithKeys(fn (AuditAction $action): array => [$action->value => $action->label()])),
            ])
            ->recordActions([])
            ->toolbarActions([])
            ->recordAction(null)
            ->recordUrl(null)
            ->emptyStateHeading('No hay eventos para mostrar')
            ->emptyStateDescription('Los accesos y cambios administrativos aparecerán aquí. Si aplicaste filtros, prueba con otros valores.');
    }

    /** @return array<string, mixed> */
    public static function getPages(): array
    {
        return ['index' => ManageAuditEvents::route('/')];
    }
}
