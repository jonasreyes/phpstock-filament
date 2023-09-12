<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected ?string $heading = 'Órdenes';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Crear orden'),
                ExportAction::make()->exports([
                        ExcelExport::make('table')
                        ->fromTable()
                        ->withFilename(date('d-M-Y') . '-Ordenes')
                        ->withWriterType(\Maatwebsite\Excel\Excel::ODS),
                ])
        ];
    }
}
