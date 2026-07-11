<?php

namespace App\Filament\Resources\ImportJobs\Pages;

use App\Filament\Resources\ImportJobs\ImportJobResource;
use App\Jobs\ProcessImportJob;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditImportJob extends EditRecord
{
    protected static string $resource = ImportJobResource::class;

    protected function getHeaderActions(): array
    {
        return [

            Action::make('runImport')
                ->label('Run Import')
                ->icon('heroicon-o-play')
                ->requiresConfirmation()
                ->action(function () {

                    ProcessImportJob::dispatch(
                        $this->record->id
                    );

                }),

            DeleteAction::make(),

        ];
    }
}
