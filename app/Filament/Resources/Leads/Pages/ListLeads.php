<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        $authUser = Auth::user();

        return [
            CreateAction::make()
                ->label('New Lead')
                ->icon('heroicon-o-plus')
                ->visible(fn() => $authUser->hasPermissionTo('can-create-leads')),
        ];
    }

    /**
     * Get the base query based on user permissions
     */
    protected function getBaseQuery()
    {
        $user = Filament::auth()->user();
        
        $query = Lead::query();

        // Apply permission-based filtering for counts
        if ($user->hasPermissionTo('assigned-leads-only') && !$user->hasPermissionTo('all-leads')) {
            // Show only leads assigned to this user
            $query->whereHas('assignedUsers', function ($subQuery) use ($user) {
                $subQuery->where('user_id', $user->id)
                         ->where('is_active', true);
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

        if ($user->hasPermissionTo('all-leads')) {
            $tabs['all'] = \Filament\Schemas\Components\Tabs\Tab::make('All Leads')
                ->badge(Lead::count())
                ->icon('heroicon-o-list-bullet');
        } else {
            $tabs['all'] = \Filament\Schemas\Components\Tabs\Tab::make('My Leads')
                ->badge((clone $baseQuery)->count())
                ->icon('heroicon-o-list-bullet');
        }

        // Status tabs - apply permission-based filtering
        $tabs['new'] = \Filament\Schemas\Components\Tabs\Tab::make('New')
            ->badge((clone $baseQuery)->where('status', 'new')->count())
            ->icon('heroicon-o-sparkles')
            ->modifyQueryUsing(fn($query) => $query->where('status', 'new'));

        $tabs['contacted'] = \Filament\Schemas\Components\Tabs\Tab::make('Contacted')
            ->badge((clone $baseQuery)->where('status', 'contacted')->count())
            ->icon('heroicon-o-phone')
            ->modifyQueryUsing(fn($query) => $query->where('status', 'contacted'));

        $tabs['qualified'] = \Filament\Schemas\Components\Tabs\Tab::make('Qualified')
            ->badge((clone $baseQuery)->where('status', 'qualified')->count())
            ->icon('heroicon-o-check-circle')
            ->modifyQueryUsing(fn($query) => $query->where('status', 'qualified'));

        $tabs['won'] = \Filament\Schemas\Components\Tabs\Tab::make('Won')
            ->badge((clone $baseQuery)->where('status', 'won')->count())
            ->icon('heroicon-o-trophy')
            ->modifyQueryUsing(fn($query) => $query->where('status', 'won'));

        $tabs['lost'] = \Filament\Schemas\Components\Tabs\Tab::make('Lost')
            ->badge((clone $baseQuery)->where('status', 'lost')->count())
            ->icon('heroicon-o-x-circle')
            ->modifyQueryUsing(fn($query) => $query->where('status', 'lost'));

        return $tabs;
    }
}
