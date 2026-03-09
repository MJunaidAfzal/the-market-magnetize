<?php

namespace App\Filament\Resources\OrderAssignees\Pages;

use App\Filament\Resources\OrderAssignees\OrderAssigneeResource;
use Filament\Resources\Pages\ListRecords;

class ListOrderAssignees extends ListRecords
{
    protected static string $resource = OrderAssigneeResource::class;
}
