<?php

namespace App\Filament\Resources\LargeAnimals\Abbreviations;

use App\Filament\Clusters\Search\SearchCluster;
use App\Filament\Resources\LargeAnimals\Abbreviations\Pages\CreateAbbreviation;
use App\Filament\Resources\LargeAnimals\Abbreviations\Pages\EditAbbreviation;
use App\Filament\Resources\LargeAnimals\Abbreviations\Pages\ListAbbreviations;
use App\Filament\Resources\LargeAnimals\Abbreviations\Pages\ViewAbbreviation;
use App\Filament\Resources\LargeAnimals\Abbreviations\Schemas\AbbreviationForm;
use App\Filament\Resources\LargeAnimals\Abbreviations\Schemas\AbbreviationInfolist;
use App\Filament\Resources\LargeAnimals\Abbreviations\Tables\AbbreviationsTable;
use App\Models\LargeAnimals\Abbreviation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AbbreviationResource extends Resource
{
    protected static ?string $model = Abbreviation::class;

    protected static ?string $cluster = SearchCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedListBullet;

    protected static ?string $recordTitleAttribute = 'abbreviation';

    public static function form(Schema $schema): Schema
    {
        return AbbreviationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AbbreviationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AbbreviationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAbbreviations::route('/'),
            'create' => CreateAbbreviation::route('/create'),
            'view' => ViewAbbreviation::route('/{record}'),
            'edit' => EditAbbreviation::route('/{record}/edit'),
        ];
    }
}
