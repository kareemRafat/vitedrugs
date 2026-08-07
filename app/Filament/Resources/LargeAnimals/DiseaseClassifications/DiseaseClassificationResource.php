<?php

namespace App\Filament\Resources\LargeAnimals\DiseaseClassifications;

use App\Filament\Clusters\Diseases\DiseasesCluster;
use App\Filament\Resources\LargeAnimals\DiseaseClassifications\Pages\CreateDiseaseClassification;
use App\Filament\Resources\LargeAnimals\DiseaseClassifications\Pages\EditDiseaseClassification;
use App\Filament\Resources\LargeAnimals\DiseaseClassifications\Pages\ListDiseaseClassifications;
use App\Filament\Resources\LargeAnimals\DiseaseClassifications\Pages\ViewDiseaseClassification;
use App\Filament\Resources\LargeAnimals\DiseaseClassifications\Schemas\DiseaseClassificationForm;
use App\Filament\Resources\LargeAnimals\DiseaseClassifications\Schemas\DiseaseClassificationInfolist;
use App\Filament\Resources\LargeAnimals\DiseaseClassifications\Tables\DiseaseClassificationsTable;
use App\Models\LargeAnimals\DiseaseClassification;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DiseaseClassificationResource extends Resource
{
    protected static ?string $model = DiseaseClassification::class;

    protected static ?string $cluster = DiseasesCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return DiseaseClassificationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DiseaseClassificationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DiseaseClassificationsTable::configure($table);
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
            'index' => ListDiseaseClassifications::route('/'),
            'create' => CreateDiseaseClassification::route('/create'),
            'view' => ViewDiseaseClassification::route('/{record}'),
            'edit' => EditDiseaseClassification::route('/{record}/edit'),
        ];
    }
}
