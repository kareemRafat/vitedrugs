<?php

namespace App\Filament\Resources\LargeAnimals\HostSpecies\Pages;

use App\Filament\Resources\LargeAnimals\HostSpecies\HostSpeciesResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditHostSpecies extends EditRecord
{
    protected static string $resource = HostSpeciesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
