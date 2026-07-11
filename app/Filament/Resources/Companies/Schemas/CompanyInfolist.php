<?php

namespace App\Filament\Resources\Companies\Schemas;

use App\Models\Company;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CompanyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('name'),
                TextEntry::make('name_ar')
                    ->placeholder('-'),
                TextEntry::make('slug'),
                TextEntry::make('company_type')
                    ->badge(),
                TextEntry::make('logo')
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('description_ar')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('country')
                    ->placeholder('-'),
                TextEntry::make('address'),

                TextEntry::make('address_ar'),

                TextEntry::make('governorate'),

                TextEntry::make('coverage_area'),

                TextEntry::make('registration_number'),

                TextEntry::make('google_maps_url')
                    ->url(fn ($state) => $state),
                TextEntry::make('website')
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label('Email address')
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('mobile')
                    ->placeholder('-'),
                TextEntry::make('facebook')
                    ->placeholder('-'),
                TextEntry::make('linkedin')
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Company $record): bool => $record->trashed()),
            ]);
    }
}
