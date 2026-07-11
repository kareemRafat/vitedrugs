<?php

namespace App\Filament\Resources\Contraindications\Pages;

use App\Filament\Resources\Contraindications\ContraindicationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditContraindication extends EditRecord
{
    protected static string $resource = ContraindicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
