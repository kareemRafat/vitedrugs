<?php

namespace App\Filament\Resources\Companies\Schemas;

use App\Models\Company;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                Section::make('Basic Information')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('name_ar')
                            ->placeholder('-'),
                        TextEntry::make('slug'),
                        TextEntry::make('company_type')
                            ->badge(),
                        TextEntry::make('parentCompany.name')
                            ->label('Parent Company')
                            ->placeholder('-'),
                        IconEntry::make('is_active')
                            ->boolean(),
                    ]),

                Section::make('Contact')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('email')
                            ->label('Email address')
                            ->placeholder('-'),
                        TextEntry::make('phone')
                            ->placeholder('-'),
                        TextEntry::make('mobile')
                            ->placeholder('-'),
                        TextEntry::make('website')
                            ->placeholder('-'),
                        TextEntry::make('facebook')
                            ->placeholder('-'),
                        TextEntry::make('linkedin')
                            ->placeholder('-'),
                        TextEntry::make('contact_person')
                            ->placeholder('-'),
                        TextEntry::make('whatsapp')
                            ->placeholder('-'),
                        TextEntry::make('telegram')
                            ->placeholder('-'),
                        TextEntry::make('youtube')
                            ->placeholder('-'),
                        TextEntry::make('instagram')
                            ->placeholder('-'),
                    ]),

                Section::make('Location')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('country')
                            ->placeholder('-'),
                        TextEntry::make('address')
                            ->placeholder('-'),
                        TextEntry::make('address_ar')
                            ->placeholder('-'),
                        TextEntry::make('governorate')
                            ->placeholder('-'),
                        TextEntry::make('coverage_area')
                            ->placeholder('-'),
                        TextEntry::make('google_maps_url')
                            ->url(fn ($state) => $state),
                    ]),

                Section::make('Registration & Timestamps')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('registration_number')
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('deleted_at')
                            ->dateTime()
                            ->visible(fn (Company $record): bool => $record->trashed()),
                    ]),

                Section::make('Description')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('description')
                            ->placeholder('-'),
                        TextEntry::make('description_ar')
                            ->placeholder('-'),
                    ]),

                Section::make('Logo')
                    ->columnSpanFull()
                    ->schema([
                        ImageEntry::make('logo'),
                    ]),

            ]);
    }
}
