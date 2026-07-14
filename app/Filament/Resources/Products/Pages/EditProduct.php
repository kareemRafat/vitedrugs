<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['manufacturer_id'] = $this->record->manufacturer()->first()?->id;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['manufacturer_id']);

        return $data;
    }

    protected function afterSave(): void
    {
        $manufacturerId = $this->form->getState()['manufacturer_id'];

        $this->record->companies()
            ->newPivotQuery()
            ->where('role', 'manufacturer')
            ->delete();

        $this->record->companies()->syncWithoutDetaching([
            $manufacturerId => ['role' => 'manufacturer'],
        ]);
    }
}
