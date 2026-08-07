<?php

namespace App\Filament\Resources\LargeAnimals\HostSpecies;

use App\Filament\Resources\LargeAnimals\HostSpecies\Pages\CreateHostSpecies;
use App\Filament\Resources\LargeAnimals\HostSpecies\Pages\EditHostSpecies;
use App\Filament\Resources\LargeAnimals\HostSpecies\Pages\ListHostSpecies;
use App\Filament\Resources\LargeAnimals\HostSpecies\Pages\ViewHostSpecies;
use App\Filament\Resources\LargeAnimals\HostSpecies\RelationManagers\DiseasesRelationManager;
use App\Filament\Resources\LargeAnimals\HostSpecies\Schemas\HostSpeciesForm;
use App\Filament\Resources\LargeAnimals\HostSpecies\Schemas\HostSpeciesInfolist;
use App\Filament\Resources\LargeAnimals\HostSpecies\Tables\HostSpeciesTable;
use App\Models\LargeAnimals\HostSpecies;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class HostSpeciesResource extends Resource
{
    protected static ?string $model = HostSpecies::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static string|UnitEnum|null $navigationGroup = 'Large Animals';

    protected static ?string $recordTitleAttribute = 'display_name';

    public static function form(Schema $schema): Schema
    {
        return HostSpeciesForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return HostSpeciesInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HostSpeciesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DiseasesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHostSpecies::route('/'),
            'create' => CreateHostSpecies::route('/create'),
            'view' => ViewHostSpecies::route('/{record}'),
            'edit' => EditHostSpecies::route('/{record}/edit'),
        ];
    }
}
