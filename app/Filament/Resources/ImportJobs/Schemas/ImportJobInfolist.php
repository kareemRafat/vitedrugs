<?php

namespace App\Filament\Resources\ImportJobs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ImportJobInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('source_file'),
                TextEntry::make('source_type')
                    ->badge(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('total_products')
                    ->numeric(),
                TextEntry::make('imported_products')
                    ->numeric(),
                TextEntry::make('failed_products')
                    ->numeric(),
                TextEntry::make('extracted_json')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('error_message')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('started_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('completed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
