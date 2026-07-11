<?php

namespace App\Filament\Resources\Indications\Pages;

use App\Filament\Resources\Indications\IndicationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIndications extends ListRecords
{
    protected static string $resource = IndicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
