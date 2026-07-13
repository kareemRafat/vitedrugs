<?php

namespace App\Filament\Resources\ContactSubmissions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactSubmissionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                Section::make('Sender Information')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email'),
                    ]),

                Section::make('Details')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('subject'),
                        TextEntry::make('created_at')
                            ->dateTime(),
                    ]),

                Section::make('Message')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('message'),
                    ]),

            ]);
    }
}
