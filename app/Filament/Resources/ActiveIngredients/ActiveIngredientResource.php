<?php

namespace App\Filament\Resources\ActiveIngredients;

use App\Filament\Resources\ActiveIngredients\Pages\CreateActiveIngredient;
use App\Filament\Resources\ActiveIngredients\Pages\EditActiveIngredient;
use App\Filament\Resources\ActiveIngredients\Pages\ListActiveIngredients;
use App\Filament\Resources\ActiveIngredients\Pages\ViewActiveIngredient;
use App\Filament\Resources\ActiveIngredients\RelationManagers\DrugClassesRelationManager;
use App\Filament\Resources\ActiveIngredients\RelationManagers\DrugInteractionsRelationManager;
use App\Filament\Resources\ActiveIngredients\Schemas\ActiveIngredientForm;
use App\Filament\Resources\ActiveIngredients\Schemas\ActiveIngredientInfolist;
use App\Filament\Resources\ActiveIngredients\Tables\ActiveIngredientsTable;
use App\Models\ActiveIngredient;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ActiveIngredientResource extends Resource
{
    protected static ?string $model = ActiveIngredient::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    protected static string|UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ActiveIngredientForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ActiveIngredientInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActiveIngredientsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DrugInteractionsRelationManager::class,

            DrugClassesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActiveIngredients::route('/'),
            'create' => CreateActiveIngredient::route('/create'),
            'view' => ViewActiveIngredient::route('/{record}'),
            'edit' => EditActiveIngredient::route('/{record}/edit'),
        ];
    }
}
