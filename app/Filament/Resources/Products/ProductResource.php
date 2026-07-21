<?php

namespace App\Filament\Resources\Products;

use App\Enums\ProductStatus;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Pages\ViewProduct;
use App\Filament\Resources\Products\RelationManagers\ActiveIngredientsRelationManager;
use App\Filament\Resources\Products\RelationManagers\AlternativesRelationManager;
use App\Filament\Resources\Products\RelationManagers\CompaniesRelationManager;
use App\Filament\Resources\Products\RelationManagers\ContraindicationsRelationManager;
use App\Filament\Resources\Products\RelationManagers\DocumentsRelationManager;
use App\Filament\Resources\Products\RelationManagers\DosagesRelationManager;
use App\Filament\Resources\Products\RelationManagers\ImagesRelationManager;
use App\Filament\Resources\Products\RelationManagers\IndicationsRelationManager;
use App\Filament\Resources\Products\RelationManagers\PrecautionsRelationManager;
use App\Filament\Resources\Products\RelationManagers\SideEffectsRelationManager;
use App\Filament\Resources\Products\RelationManagers\WithdrawalPeriodsRelationManager;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Schemas\ProductInfolist;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static string|UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?string $recordTitleAttribute = 'trade_name';

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ActiveIngredientsRelationManager::class,

            ImagesRelationManager::class,

            DosagesRelationManager::class,

            WithdrawalPeriodsRelationManager::class,

            DocumentsRelationManager::class,

            AlternativesRelationManager::class,

            IndicationsRelationManager::class,

            ContraindicationsRelationManager::class,

            PrecautionsRelationManager::class,

            SideEffectsRelationManager::class,

            CompaniesRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'view' => ViewProduct::route('/{record}'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScope('approved');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Product::withoutGlobalScope('approved')->where('status', ProductStatus::Pending)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return static::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
