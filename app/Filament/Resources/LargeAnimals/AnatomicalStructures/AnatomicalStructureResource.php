<?php

namespace App\Filament\Resources\LargeAnimals\AnatomicalStructures;

use App\Filament\Resources\LargeAnimals\AnatomicalStructures\Pages\CreateAnatomicalStructure;
use App\Filament\Resources\LargeAnimals\AnatomicalStructures\Pages\EditAnatomicalStructure;
use App\Filament\Resources\LargeAnimals\AnatomicalStructures\Pages\ListAnatomicalStructures;
use App\Filament\Resources\LargeAnimals\AnatomicalStructures\Pages\ViewAnatomicalStructure;
use App\Filament\Resources\LargeAnimals\AnatomicalStructures\Schemas\AnatomicalStructureForm;
use App\Filament\Resources\LargeAnimals\AnatomicalStructures\Schemas\AnatomicalStructureInfolist;
use App\Filament\Resources\LargeAnimals\AnatomicalStructures\Tables\AnatomicalStructuresTable;
use App\Models\LargeAnimals\AnatomicalStructure;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AnatomicalStructureResource extends Resource
{
    protected static ?string $model = AnatomicalStructure::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = 'Large Animals';

    protected static ?string $recordTitleAttribute = 'display_name';

    public static function form(Schema $schema): Schema
    {
        return AnatomicalStructureForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AnatomicalStructureInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnatomicalStructuresTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnatomicalStructures::route('/'),
            'create' => CreateAnatomicalStructure::route('/create'),
            'view' => ViewAnatomicalStructure::route('/{record}'),
            'edit' => EditAnatomicalStructure::route('/{record}/edit'),
        ];
    }
}
