<?php

namespace App\Filament\Resources\ContactSubmissions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ContactSubmissionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email'),
                TextEntry::make('subject'),
                TextEntry::make('message'),
                TextEntry::make('created_at')
                    ->dateTime(),
            ]);
    }
}
