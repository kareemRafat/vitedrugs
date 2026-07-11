<?php

namespace App\Filament\Resources\ImportJobs\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ImportJobForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                FileUpload::make('source_file')
                    ->directory('imports')
                    ->nullable(),

                Select::make('source_type')
                    ->options([
                        'pdf' => 'PDF',
                        'docx' => 'DOCX',
                        'html' => 'HTML',
                        'json' => 'JSON',
                        'txt' => 'TXT',
                    ])
                    ->default('JSON')
                    ->required(),

                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ])
                    ->default('pending')
                    ->disabled(),

                Placeholder::make('total_products')
                    ->content(fn ($record) => $record?->total_products ?? 0),

                Placeholder::make('imported_products')
                    ->content(fn ($record) => $record?->imported_products ?? 0),

                Placeholder::make('failed_products')
                    ->content(fn ($record) => $record?->failed_products ?? 0),

                Textarea::make('extracted_json')
                    ->rows(20)
                    ->columnSpanFull(),

                Textarea::make('error_message')
                    ->columnSpanFull(),

            ]);
    }
}