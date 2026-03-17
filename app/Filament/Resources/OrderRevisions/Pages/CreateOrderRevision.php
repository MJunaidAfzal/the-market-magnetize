<?php

namespace App\Filament\Resources\OrderRevisions\Pages;

use App\Filament\Resources\OrderRevisions\OrderRevisionResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateOrderRevision extends CreateRecord
{
    protected static string $resource = OrderRevisionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set the user_id to the currently authenticated user
        $data['user_id'] = Filament::auth()->id();

        // Get the order_id from the URL parameter
        $orderId = request()->route('order');
        
        // Also check if it was passed as 'parent' parameter
        if (!$orderId && request()->has('parent')) {
            $orderId = request()->input('parent');
        }

        if ($orderId) {
            $data['order_id'] = $orderId;
        }

        return $data;
    }
}
