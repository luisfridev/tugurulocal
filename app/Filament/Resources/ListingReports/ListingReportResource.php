<?php

namespace App\Filament\Resources\ListingReports;

use App\Actions\Listings\ResolveListingReport;
use App\Actions\Listings\SubmitListingReport;
use App\Filament\Resources\ListingReports\Pages\ManageListingReports;
use App\Models\ListingReport;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ListingReportResource extends Resource
{
    protected static ?string $model = ListingReport::class;

    protected static ?string $slug = 'reportes';

    protected static ?string $navigationLabel = 'Reportes de fichas';

    protected static ?string $modelLabel = 'reporte';

    protected static ?string $pluralModelLabel = 'Reportes de fichas';

    public static function table(Table $table): Table
    {
        $actions = [];
        foreach (['resolved' => 'Resolver', 'dismissed' => 'Descartar'] as $status => $label) {
            $actions[] = Action::make($status)->label($label)->authorize('update')->visible(fn (ListingReport $record): bool => $record->status === 'pending')->requiresConfirmation()
                ->action(fn (ListingReport $record) => app(ResolveListingReport::class)->handle(auth()->user(), $record, $status));
        }

        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('listing.name')->label('Ficha')->wrap(), TextColumn::make('field')->label('Dato')->formatStateUsing(fn (string $state): string => SubmitListingReport::FIELDS[$state] ?? $state),
            TextColumn::make('explanation')->label('Explicación')->wrap()->limit(2000), TextColumn::make('status')->label('Estado')->badge()->formatStateUsing(fn (string $state): string => ['pending' => 'Pendiente', 'resolved' => 'Resuelto', 'dismissed' => 'Descartado'][$state]),
            TextColumn::make('created_at')->label('Recibido')->dateTime('d/m/Y H:i', timezone: 'America/Cancun'),
        ])->filters([SelectFilter::make('status')->label('Estado')->options(['pending' => 'Pendiente', 'resolved' => 'Resuelto', 'dismissed' => 'Descartado'])])->recordActions($actions)->toolbarActions([])->recordUrl(null)->emptyStateHeading('Sin reportes para mostrar');
    }

    public static function getPages(): array
    {
        return ['index' => ManageListingReports::route('/')];
    }
}
