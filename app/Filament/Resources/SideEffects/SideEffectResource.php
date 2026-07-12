<?php

namespace App\Filament\Resources\SideEffects;

use App\Filament\Resources\SideEffects\Pages\CreateSideEffect;
use App\Filament\Resources\SideEffects\Pages\EditSideEffect;
use App\Filament\Resources\SideEffects\Pages\ListSideEffects;
use App\Filament\Resources\SideEffects\Pages\ViewSideEffect;
use App\Filament\Resources\SideEffects\Schemas\SideEffectForm;
use App\Filament\Resources\SideEffects\Schemas\SideEffectInfolist;
use App\Filament\Resources\SideEffects\Tables\SideEffectsTable;
use App\Models\SideEffect;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SideEffectResource extends Resource
{
    protected static ?string $model = SideEffect::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static ?string $recordTitleAttribute = 'description';

    public static function form(Schema $schema): Schema
    {
        return SideEffectForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SideEffectInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SideEffectsTable::configure($table);
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
            'index' => ListSideEffects::route('/'),
            'create' => CreateSideEffect::route('/create'),
            'view' => ViewSideEffect::route('/{record}'),
            'edit' => EditSideEffect::route('/{record}/edit'),
        ];
    }
}
