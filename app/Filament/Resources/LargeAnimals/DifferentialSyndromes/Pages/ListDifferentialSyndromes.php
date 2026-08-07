<?php

namespace App\Filament\Resources\LargeAnimals\DifferentialSyndromes\Pages;

use App\Filament\Resources\LargeAnimals\DifferentialSyndromes\DifferentialSyndromeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDifferentialSyndromes extends ListRecords
{
    protected static string $resource = DifferentialSyndromeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
