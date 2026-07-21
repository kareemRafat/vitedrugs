<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\ProductStatus;
use App\Models\Product;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                Section::make('Basic Information')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('trade_name'),
                        TextEntry::make('trade_name_ar')
                            ->placeholder('-'),
                        TextEntry::make('slug'),
                        TextEntry::make('product_type')
                            ->badge()
                            ->placeholder('-'),
                        IconEntry::make('is_active')
                            ->boolean(),
                    ]),

                Section::make('Classification')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('manufacturer')
                            ->label('Manufacturer')
                            ->state(fn (Product $record): ?string => $record->companies
                                ->firstWhere('pivot.role', 'manufacturer')?->name)
                            ->placeholder('-'),
                        TextEntry::make('agent')
                            ->label('Agent')
                            ->state(fn (Product $record): ?string => $record->companies
                                ->firstWhere('pivot.role', 'agent')?->name)
                            ->placeholder('-'),
                        TextEntry::make('distributor')
                            ->label('Distributor')
                            ->state(fn (Product $record): ?string => $record->companies
                                ->firstWhere('pivot.role', 'distributor')?->name)
                            ->placeholder('-'),
                        TextEntry::make('dosageForm.name')
                            ->label('Dosage Form')
                            ->placeholder('-'),
                        TextEntry::make('package_size')
                            ->placeholder('-'),
                        TextEntry::make('storage_conditions')
                            ->placeholder('-'),
                    ]),

                Section::make('Description')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('description')
                            ->placeholder('-'),
                        TextEntry::make('description_ar')
                            ->placeholder('-'),
                    ]),

                Section::make('Submitted')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (ProductStatus $state): string => $state->color()),
                        TextEntry::make('createdBy.name')
                            ->label('Submitted By')
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->label('Submitted At')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('reviewed_at')
                            ->label('Reviewed At')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('admin_notes')
                            ->label('Admin Notes')
                            ->placeholder('No notes')
                            ->visible(fn (?Product $record): bool => filled($record?->admin_notes)),
                    ]),

                Section::make('Timestamps')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('deleted_at')
                            ->dateTime()
                            ->visible(fn (Product $record): bool => $record->trashed()),
                    ]),

            ]);
    }
}
