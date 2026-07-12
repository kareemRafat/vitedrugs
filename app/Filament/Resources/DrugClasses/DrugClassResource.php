<?php

namespace App\Filament\Resources\DrugClasses;

use App\Filament\Resources\DrugClasses\Pages\CreateDrugClass;
use App\Filament\Resources\DrugClasses\Pages\EditDrugClass;
use App\Filament\Resources\DrugClasses\Pages\ListDrugClasses;
use App\Filament\Resources\DrugClasses\Pages\ViewDrugClass;
use App\Filament\Resources\DrugClasses\RelationManagers\ActiveIngredientsRelationManager;
use App\Filament\Resources\DrugClasses\Schemas\DrugClassForm;
use App\Filament\Resources\DrugClasses\Schemas\DrugClassInfolist;
use App\Filament\Resources\DrugClasses\Tables\DrugClassesTable;
use App\Models\DrugClass;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DrugClassResource extends Resource
{
    protected static ?string $model = DrugClass::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DrugClassForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DrugClassInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DrugClassesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ActiveIngredientsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDrugClasses::route('/'),
            'create' => CreateDrugClass::route('/create'),
            'view' => ViewDrugClass::route('/{record}'),
            'edit' => EditDrugClass::route('/{record}/edit'),
        ];
    }
}
