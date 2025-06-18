<?php

namespace App\Filament\Imports;

use App\Models\Company;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class CompanyImporter extends Importer
{
    protected static ?string $model = Company::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('ruc')
                ->requiredMapping()
                ->rules(['required', 'max:11', 'unique:companies,ruc']),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255', 'unique:companies,name']),
        ];
    }

    public function resolveRecord(): ?Company
    {
        return Company::firstOrNew([
            // Update existing records, matching them by `$this->data['column_name']`
            'ruc' => $this->data['ruc'],
            'name' => $this->data['name'],
        ]);

        // return new Company();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your company import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
