<?php

namespace App\Filament\Resources\LargeAnimals\DifferentialSyndromes;

use App\Filament\Clusters\Diagnosis\DiagnosisCluster;
use App\Filament\Resources\LargeAnimals\DifferentialSyndromes\Pages\CreateDifferentialSyndrome;
use App\Filament\Resources\LargeAnimals\DifferentialSyndromes\Pages\EditDifferentialSyndrome;
use App\Filament\Resources\LargeAnimals\DifferentialSyndromes\Pages\ListDifferentialSyndromes;
use App\Filament\Resources\LargeAnimals\DifferentialSyndromes\Pages\ViewDifferentialSyndrome;
use App\Filament\Resources\LargeAnimals\DifferentialSyndromes\RelationManagers\ClinicalSignsRelationManager;
use App\Filament\Resources\LargeAnimals\DifferentialSyndromes\RelationManagers\DiseasesRelationManager;
use App\Filament\Resources\LargeAnimals\DifferentialSyndromes\Schemas\DifferentialSyndromeForm;
use App\Filament\Resources\LargeAnimals\DifferentialSyndromes\Schemas\DifferentialSyndromeInfolist;
use App\Filament\Resources\LargeAnimals\DifferentialSyndromes\Tables\DifferentialSyndromesTable;
use App\Models\LargeAnimals\DifferentialSyndrome;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DifferentialSyndromeResource extends Resource
{
    protected static ?string $model = DifferentialSyndrome::class;

    protected static ?string $cluster = DiagnosisCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DifferentialSyndromeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DifferentialSyndromeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DifferentialSyndromesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ClinicalSignsRelationManager::class,
            DiseasesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDifferentialSyndromes::route('/'),
            'create' => CreateDifferentialSyndrome::route('/create'),
            'view' => ViewDifferentialSyndrome::route('/{record}'),
            'edit' => EditDifferentialSyndrome::route('/{record}/edit'),
        ];
    }
}
