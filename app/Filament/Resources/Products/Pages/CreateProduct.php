<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['manufacturer_id']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $manufacturerId = $this->form->getState()['manufacturer_id'];

        $this->record->companies()->syncWithoutDetaching([
            $manufacturerId => ['role' => 'manufacturer'],
        ]);
    }
}
