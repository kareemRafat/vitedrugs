<?php

namespace App\Filament\Resources\ProductSubmissions\Tables;

use App\Enums\SubmissionStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductSubmissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('submitted_by_name')
                    ->label('Submitter')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('submitted_by_email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (SubmissionStatus $state): string => match ($state) {
                        SubmissionStatus::Pending => 'warning',
                        SubmissionStatus::Approved => 'success',
                        SubmissionStatus::Rejected => 'danger',
                    }),

                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(SubmissionStatus::class),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
