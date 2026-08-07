<?php

namespace App\Filament\Resources\LargeAnimals\MedicalArticles;

use App\Filament\Resources\LargeAnimals\MedicalArticles\Pages\CreateMedicalArticle;
use App\Filament\Resources\LargeAnimals\MedicalArticles\Pages\EditMedicalArticle;
use App\Filament\Resources\LargeAnimals\MedicalArticles\Pages\ListMedicalArticles;
use App\Filament\Resources\LargeAnimals\MedicalArticles\Pages\ViewMedicalArticle;
use App\Filament\Resources\LargeAnimals\MedicalArticles\Schemas\MedicalArticleForm;
use App\Filament\Resources\LargeAnimals\MedicalArticles\Schemas\MedicalArticleInfolist;
use App\Filament\Resources\LargeAnimals\MedicalArticles\Tables\MedicalArticlesTable;
use App\Models\LargeAnimals\MedicalArticle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MedicalArticleResource extends Resource
{
    protected static ?string $model = MedicalArticle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Large Animals';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return MedicalArticleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MedicalArticleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MedicalArticlesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMedicalArticles::route('/'),
            'create' => CreateMedicalArticle::route('/create'),
            'view' => ViewMedicalArticle::route('/{record}'),
            'edit' => EditMedicalArticle::route('/{record}/edit'),
        ];
    }
}
