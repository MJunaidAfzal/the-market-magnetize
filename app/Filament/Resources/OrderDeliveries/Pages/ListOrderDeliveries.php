<?php

namespace App\Filament\Resources\OrderDeliveries\Pages;

use App\Filament\Resources\OrderDeliveries\OrderDeliveryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrderDeliveries extends ListRecords
{
    protected static string $resource = OrderDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Delivery')
                ->icon('heroicon-o-plus')
                ->slideOver(),
        ];
    }
}
