<?php

namespace App\Filament\Resources\DrugClasses\Pages;

use App\Filament\Resources\DrugClasses\DrugClassResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDrugClass extends EditRecord
{
    protected static string $resource = DrugClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
