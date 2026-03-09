<?php

namespace App\Filament\Resources\OrderCategories\Pages;

use App\Filament\Resources\OrderCategories\OrderCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListOrderCategories extends ListRecords
{
    protected static string $resource = OrderCategoryResource::class;

    protected function getHeaderActions(): array
    {
        $authUser = Auth::user();

        return [
            CreateAction::make()
                ->label('New Category')
                ->icon('heroicon-o-plus'),
        ];
    }
}
