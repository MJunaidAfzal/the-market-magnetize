<?php

namespace App\Filament\Resources\OrderRevisions\Pages;

use App\Filament\Resources\OrderRevisions\OrderRevisionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrderRevisions extends ListRecords
{
    protected static string $resource = OrderRevisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Revision')
                ->icon('heroicon-o-plus')
                ->slideOver(),
        ];
    }
}
