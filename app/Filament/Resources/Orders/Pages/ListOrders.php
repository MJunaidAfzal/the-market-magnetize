<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        $authUser = Auth::user();

        return [
            CreateAction::make()
                ->label('New Order')
                ->icon('heroicon-o-plus')
                ->visible($authUser && $authUser->hasPermissionTo('can-create-order')),
        ];
    }

    /**
     * Get the base query based on user permissions
     */
    protected function getBaseQuery()
    {
        $user = Filament::auth()->user();
        
        $query = Order::query();

        // Apply permission-based filtering for counts
        if ($user->hasPermissionTo('only-assigned-orders') && !$user->hasPermissionTo('all-orders')) {
            // Show only orders assigned to this user
            $query->whereHas('assignees', function ($subQuery) use ($user) {
                $subQuery->where('user_id', $user->id);
            });
        }

        return $query;
    }

    public function getTabs(): array
    {
        $user = Filament::auth()->user();
        $baseQuery = $this->getBaseQuery();

        // Build tabs based on permissions
        $tabs = [];

        if ($user->hasPermissionTo('all-orders')) {
            $tabs['all'] = \Filament\Schemas\Components\Tabs\Tab::make('All Orders')
                ->badge(Order::count())
                ->icon('heroicon-o-list-bullet');
        } else {
            $tabs['all'] = \Filament\Schemas\Components\Tabs\Tab::make('My Orders')
                ->badge((clone $baseQuery)->count())
                ->icon('heroicon-o-list-bullet');
        }

        // Status tabs - apply permission-based filtering
        $tabs['Pending'] = \Filament\Schemas\Components\Tabs\Tab::make('Pending')
            ->badge((clone $baseQuery)->where('status', 'Pending')->count())
            ->icon('heroicon-o-clock')
            ->modifyQueryUsing(fn($query) => $query->where('status', 'Pending'));

        $tabs['Confirmed'] = \Filament\Schemas\Components\Tabs\Tab::make('Confirmed')
            ->badge((clone $baseQuery)->where('status', 'Confirmed')->count())
            ->icon('heroicon-o-check-circle')
            ->modifyQueryUsing(fn($query) => $query->where('status', 'Confirmed'));

        $tabs['In Progress'] = \Filament\Schemas\Components\Tabs\Tab::make('In Progress')
            ->badge((clone $baseQuery)->where('status', 'In Progress')->count())
            ->icon('heroicon-o-arrow-right-circle')
            ->modifyQueryUsing(fn($query) => $query->where('status', 'In Progress'));

        $tabs['On Hold'] = \Filament\Schemas\Components\Tabs\Tab::make('On Hold')
            ->badge((clone $baseQuery)->where('status', 'On Hold')->count())
            ->icon('heroicon-o-pause-circle')
            ->modifyQueryUsing(fn($query) => $query->where('status', 'On Hold'));

        $tabs['Completed'] = \Filament\Schemas\Components\Tabs\Tab::make('Completed')
            ->badge((clone $baseQuery)->where('status', 'Completed')->count())
            ->icon('heroicon-o-star')
            ->modifyQueryUsing(fn($query) => $query->where('status', 'Completed'));

        $tabs['Cancelled'] = \Filament\Schemas\Components\Tabs\Tab::make('Cancelled')
            ->badge((clone $baseQuery)->where('status', 'Cancelled')->count())
            ->icon('heroicon-o-x-circle')
            ->modifyQueryUsing(fn($query) => $query->where('status', 'Cancelled'));

        return $tabs;
    }
}
