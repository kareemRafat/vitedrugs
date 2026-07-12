<?php

namespace App\Filament\Resources\Species;

use App\Filament\Resources\Species\Pages\CreateSpecies;
use App\Filament\Resources\Species\Pages\EditSpecies;
use App\Filament\Resources\Species\Pages\ListSpecies;
use App\Filament\Resources\Species\Pages\ViewSpecies;
use App\Filament\Resources\Species\Schemas\SpeciesForm;
use App\Filament\Resources\Species\Schemas\SpeciesInfolist;
use App\Filament\Resources\Species\Tables\SpeciesTable;
use App\Models\Species;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class SpeciesResource extends Resource
{
    protected static ?string $model = Species::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static string|UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SpeciesForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SpeciesInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SpeciesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSpecies::route('/'),
            'create' => CreateSpecies::route('/create'),
            'view' => ViewSpecies::route('/{record}'),
            'edit' => EditSpecies::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
