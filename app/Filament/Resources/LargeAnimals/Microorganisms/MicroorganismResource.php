<?php

namespace App\Filament\Resources\LargeAnimals\Microorganisms;

use App\Filament\Resources\LargeAnimals\Microorganisms\Pages\CreateMicroorganism;
use App\Filament\Resources\LargeAnimals\Microorganisms\Pages\EditMicroorganism;
use App\Filament\Resources\LargeAnimals\Microorganisms\Pages\ListMicroorganisms;
use App\Filament\Resources\LargeAnimals\Microorganisms\Pages\ViewMicroorganism;
use App\Filament\Resources\LargeAnimals\Microorganisms\RelationManagers\ActiveIngredientsRelationManager;
use App\Filament\Resources\LargeAnimals\Microorganisms\RelationManagers\DiseasesRelationManager;
use App\Filament\Resources\LargeAnimals\Microorganisms\Schemas\MicroorganismForm;
use App\Filament\Resources\LargeAnimals\Microorganisms\Schemas\MicroorganismInfolist;
use App\Filament\Resources\LargeAnimals\Microorganisms\Tables\MicroorganismsTable;
use App\Models\LargeAnimals\Microorganism;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MicroorganismResource extends Resource
{
    protected static ?string $model = Microorganism::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    protected static string|UnitEnum|null $navigationGroup = 'Large Animals';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return MicroorganismForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MicroorganismInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MicroorganismsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ActiveIngredientsRelationManager::class,
            DiseasesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMicroorganisms::route('/'),
            'create' => CreateMicroorganism::route('/create'),
            'view' => ViewMicroorganism::route('/{record}'),
            'edit' => EditMicroorganism::route('/{record}/edit'),
        ];
    }
}
