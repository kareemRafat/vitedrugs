<?php

namespace App\Filament\Resources\Blogs\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BlogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('title_ar'),
                TextEntry::make('slug'),
                TextEntry::make('category.name'),
                TextEntry::make('author.name'),
                TextEntry::make('excerpt'),
                TextEntry::make('excerpt_ar'),
                TextEntry::make('body')
                    ->html(),
                TextEntry::make('body_ar')
                    ->html(),
                ImageEntry::make('cover_image'),
                TextEntry::make('published_at')
                    ->dateTime(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('meta_title'),
                TextEntry::make('meta_description'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
