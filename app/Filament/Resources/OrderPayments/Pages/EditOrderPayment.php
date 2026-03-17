<?php

namespace App\Filament\Resources\OrderPayments\Pages;

use App\Filament\Resources\OrderPayments\OrderPaymentResource;
use Filament\Resources\Pages\EditRecord;

class EditOrderPayment extends EditRecord
{
    protected static string $resource = OrderPaymentResource::class;
}
