<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Filament\Imports\CompanyImporter;
use App\Filament\Resources\CompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCompanies extends ManageRecords
{
    protected static string $resource = CompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ImportAction::make()
            ->importer(CompanyImporter::class),
            Actions\CreateAction::make(),
        ];
    }
}
