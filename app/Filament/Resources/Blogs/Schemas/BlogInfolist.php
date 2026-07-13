<?php

namespace App\Filament\Resources\Blogs\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BlogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                Section::make('Basic Information')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('title'),
                        TextEntry::make('title_ar')
                            ->placeholder('-'),
                        TextEntry::make('slug'),
                        TextEntry::make('category.name')
                            ->label('Category'),
                        TextEntry::make('author.name')
                            ->label('Author'),
                        TextEntry::make('published_at')
                            ->dateTime()
                            ->placeholder('-'),
                        IconEntry::make('is_active')
                            ->boolean(),
                    ]),

                Section::make('SEO')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('meta_title')
                            ->placeholder('-'),
                        TextEntry::make('meta_description')
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ]),

                Section::make('Excerpt')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('excerpt')
                            ->placeholder('-'),
                        TextEntry::make('excerpt_ar')
                            ->placeholder('-'),
                    ]),

                Section::make('Body')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('body')
                            ->html()
                            ->placeholder('-'),
                        TextEntry::make('body_ar')
                            ->html()
                            ->placeholder('-'),
                    ]),

                Section::make('Cover Image')
                    ->columnSpanFull()
                    ->schema([
                        ImageEntry::make('cover_image'),
                    ]),

            ]);
    }
}
