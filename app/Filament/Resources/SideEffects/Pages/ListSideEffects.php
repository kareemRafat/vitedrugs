<?php

namespace App\Filament\Resources\SideEffects\Pages;

use App\Filament\Resources\SideEffects\SideEffectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSideEffects extends ListRecords
{
    protected static string $resource = SideEffectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
