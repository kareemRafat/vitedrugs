<?php

namespace App\Filament\Resources\LargeAnimals\Synonyms;

use App\Filament\Resources\LargeAnimals\Synonyms\Pages\CreateSynonym;
use App\Filament\Resources\LargeAnimals\Synonyms\Pages\EditSynonym;
use App\Filament\Resources\LargeAnimals\Synonyms\Pages\ListSynonyms;
use App\Filament\Resources\LargeAnimals\Synonyms\Pages\ViewSynonym;
use App\Filament\Resources\LargeAnimals\Synonyms\Schemas\SynonymForm;
use App\Filament\Resources\LargeAnimals\Synonyms\Schemas\SynonymInfolist;
use App\Filament\Resources\LargeAnimals\Synonyms\Tables\SynonymsTable;
use App\Models\LargeAnimals\Synonym;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SynonymResource extends Resource
{
    protected static ?string $model = Synonym::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static string|UnitEnum|null $navigationGroup = 'Large Animals';

    protected static ?string $recordTitleAttribute = 'term';

    public static function form(Schema $schema): Schema
    {
        return SynonymForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SynonymInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SynonymsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSynonyms::route('/'),
            'create' => CreateSynonym::route('/create'),
            'view' => ViewSynonym::route('/{record}'),
            'edit' => EditSynonym::route('/{record}/edit'),
        ];
    }
}
