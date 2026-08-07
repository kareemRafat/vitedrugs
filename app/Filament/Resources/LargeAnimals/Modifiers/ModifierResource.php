<?php

namespace App\Filament\Resources\LargeAnimals\Modifiers;

use App\Filament\Clusters\Diagnosis\DiagnosisCluster;
use App\Filament\Resources\LargeAnimals\Modifiers\Pages\CreateModifier;
use App\Filament\Resources\LargeAnimals\Modifiers\Pages\EditModifier;
use App\Filament\Resources\LargeAnimals\Modifiers\Pages\ListModifiers;
use App\Filament\Resources\LargeAnimals\Modifiers\Pages\ViewModifier;
use App\Filament\Resources\LargeAnimals\Modifiers\Schemas\ModifierForm;
use App\Filament\Resources\LargeAnimals\Modifiers\Schemas\ModifierInfolist;
use App\Filament\Resources\LargeAnimals\Modifiers\Tables\ModifiersTable;
use App\Models\LargeAnimals\Modifier;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ModifierResource extends Resource
{
    protected static ?string $model = Modifier::class;

    protected static ?string $cluster = DiagnosisCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static ?string $recordTitleAttribute = 'display_name';

    public static function form(Schema $schema): Schema
    {
        return ModifierForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ModifierInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ModifiersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListModifiers::route('/'),
            'create' => CreateModifier::route('/create'),
            'view' => ViewModifier::route('/{record}'),
            'edit' => EditModifier::route('/{record}/edit'),
        ];
    }
}
