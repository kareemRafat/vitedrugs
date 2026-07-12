<?php

namespace App\Filament\Resources\DrugInteractions;

use App\Filament\Resources\DrugInteractions\Pages\CreateDrugInteraction;
use App\Filament\Resources\DrugInteractions\Pages\EditDrugInteraction;
use App\Filament\Resources\DrugInteractions\Pages\ListDrugInteractions;
use App\Filament\Resources\DrugInteractions\Pages\ViewDrugInteraction;
use App\Filament\Resources\DrugInteractions\Schemas\DrugInteractionForm;
use App\Filament\Resources\DrugInteractions\Schemas\DrugInteractionInfolist;
use App\Filament\Resources\DrugInteractions\Tables\DrugInteractionsTable;
use App\Models\DrugInteraction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DrugInteractionResource extends Resource
{
    protected static ?string $model = DrugInteraction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static string|UnitEnum|null $navigationGroup = 'Catalog';

    public static function form(Schema $schema): Schema
    {
        return DrugInteractionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DrugInteractionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DrugInteractionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDrugInteractions::route('/'),
            'create' => CreateDrugInteraction::route('/create'),
            'view' => ViewDrugInteraction::route('/{record}'),
            'edit' => EditDrugInteraction::route('/{record}/edit'),
        ];
    }
}
