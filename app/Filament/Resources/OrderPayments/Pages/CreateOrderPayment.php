<?php

namespace App\Filament\Resources\OrderPayments\Pages;

use App\Filament\Resources\OrderPayments\OrderPaymentResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateOrderPayment extends CreateRecord
{
    protected static string $resource = OrderPaymentResource::class;

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
