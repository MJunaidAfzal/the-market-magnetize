<?php

namespace App\Filament\Resources\OrderAssignees;

use App\Filament\Resources\OrderAssignees\Tables\OrderAssigneesTable;
use App\Models\OrderAssignee;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Facades\Filament;

class OrderAssigneeResource extends Resource
{
    protected static ?string $model = OrderAssignee::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Order Assignees';

    protected static ?string $modelLabel = 'Order Assignee';

    protected static ?string $pluralModelLabel = 'Order Assignees';

    protected static ?string $recordTitleAttribute = 'order_id';

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        return $user && $user->hasPermissionTo('manage-order-assignees');
    }

    public static function table(Table $table): Table
    {
        return OrderAssigneesTable::configure($table)
            ->modifyQueryUsing(function (\Illuminate\Database\Eloquent\Builder $query): void {
                // Eager load relationships
                $query->with(['order', 'user']);
            });
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\OrderAssignees\Pages\ListOrderAssignees::route('/'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Orders';
    }
}
