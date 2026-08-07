<?php

namespace App\Filament\Resources\LargeAnimals\DifferentialSyndromes\Pages;

use App\Filament\Resources\LargeAnimals\DifferentialSyndromes\DifferentialSyndromeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDifferentialSyndrome extends EditRecord
{
    protected static string $resource = DifferentialSyndromeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
