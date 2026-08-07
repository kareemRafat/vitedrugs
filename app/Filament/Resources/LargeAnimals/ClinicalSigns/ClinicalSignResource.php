<?php

namespace App\Filament\Resources\LargeAnimals\ClinicalSigns;

use App\Filament\Clusters\Diagnosis\DiagnosisCluster;
use App\Filament\Resources\LargeAnimals\ClinicalSigns\Pages\CreateClinicalSign;
use App\Filament\Resources\LargeAnimals\ClinicalSigns\Pages\EditClinicalSign;
use App\Filament\Resources\LargeAnimals\ClinicalSigns\Pages\ListClinicalSigns;
use App\Filament\Resources\LargeAnimals\ClinicalSigns\Pages\ViewClinicalSign;
use App\Filament\Resources\LargeAnimals\ClinicalSigns\RelationManagers\DiseasesRelationManager;
use App\Filament\Resources\LargeAnimals\ClinicalSigns\Schemas\ClinicalSignForm;
use App\Filament\Resources\LargeAnimals\ClinicalSigns\Schemas\ClinicalSignInfolist;
use App\Filament\Resources\LargeAnimals\ClinicalSigns\Tables\ClinicalSignsTable;
use App\Models\LargeAnimals\ClinicalSign;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ClinicalSignResource extends Resource
{
    protected static ?string $model = ClinicalSign::class;

    protected static ?string $cluster = DiagnosisCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static ?string $recordTitleAttribute = 'display_name';

    public static function form(Schema $schema): Schema
    {
        return ClinicalSignForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClinicalSignInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClinicalSignsTable::configure($table);
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
            'index' => ListClinicalSigns::route('/'),
            'create' => CreateClinicalSign::route('/create'),
            'view' => ViewClinicalSign::route('/{record}'),
            'edit' => EditClinicalSign::route('/{record}/edit'),
        ];
    }
}
