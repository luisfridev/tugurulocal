<?php

namespace App\Filament\Resources\ListingReports\Pages;

use App\Filament\Resources\ListingReports\ListingReportResource;
use Filament\Resources\Pages\ManageRecords;

class ManageListingReports extends ManageRecords
{
    protected static string $resource = ListingReportResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
