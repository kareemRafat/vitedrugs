<?php

namespace App\Filament\Resources\ContactSubmissions\Schemas;

use App\Models\ContactSubmission;
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

                Section::make('Sender')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email'),
                        TextEntry::make('role')
                            ->placeholder('—'),
                        TextEntry::make('locale')
                            ->label('Language')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => strtoupper($state)),
                    ]),

                Section::make('Details')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('category')
                            ->badge()
                            ->color(fn (ContactSubmission $record): ?string => $record->category?->color())
                            ->placeholder('—'),
                        TextEntry::make('subject'),
                        TextEntry::make('created_at')
                            ->label('Received at')
                            ->dateTime(),
                        TextEntry::make('read_at')
                            ->label('Read at')
                            ->dateTime()
                            ->placeholder('Unread'),
                    ]),

                Section::make('Message')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('message')
                            ->prose()
                            ->placeholder('—'),
                    ]),

            ]);
    }
}
