<?php

namespace App\Filament\Resources\Products\Pages;

use App\Enums\ProductStatus;
use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(Product::withoutGlobalScope('approved')->count()),

            ProductStatus::Pending->value => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ProductStatus::Pending))
                ->badge(Product::withoutGlobalScope('approved')->where('status', ProductStatus::Pending)->count())
                ->badgeColor('warning'),

            ProductStatus::Approved->value => Tab::make('Approved')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ProductStatus::Approved))
                ->badge(Product::withoutGlobalScope('approved')->where('status', ProductStatus::Approved)->count())
                ->badgeColor('success'),

            ProductStatus::Rejected->value => Tab::make('Rejected')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ProductStatus::Rejected))
                ->badge(Product::withoutGlobalScope('approved')->where('status', ProductStatus::Rejected)->count())
                ->badgeColor('danger'),
        ];
    }
}
