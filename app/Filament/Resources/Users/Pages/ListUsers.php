<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        $authUser = Auth::user();

        return [
            CreateAction::make()
                    ->visible(fn() => $authUser->hasPermissionTo('can-create-user')),
        ];
    }
}
