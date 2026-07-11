<?php

namespace App\Filament\Resources\Precautions\Pages;

use App\Filament\Resources\Precautions\PrecautionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrecautions extends ListRecords
{
    protected static string $resource = PrecautionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
