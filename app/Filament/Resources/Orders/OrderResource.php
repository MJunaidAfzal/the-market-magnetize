<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages\CreateOrder;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Schemas\OrderForm;
use App\Filament\Resources\Orders\Tables\OrdersTable;
use App\Models\Order;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static ?string $navigationLabel = 'Orders';

    protected static ?string $modelLabel = 'Order';

    protected static ?string $pluralModelLabel = 'Orders';

    protected static ?string $recordTitleAttribute = 'order_number';

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        return $user && $user->hasPermissionTo('manage-orders');
    }

    public static function form(Schema $schema): Schema
    {
        return OrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table)
            ->modifyQueryUsing(function (\Illuminate\Database\Eloquent\Builder $query): void {
                $user = Filament::auth()->user();
                
                // Eager load relationships
                $query->with(['lead', 'orderCategory', 'creator', 'assignees']);

                // Apply permission-based filtering
                if ($user->hasPermissionTo('only-assigned-orders') && !$user->hasPermissionTo('all-orders')) {
                    // Show only orders assigned to this user
                    $query->whereHas('assignees', function ($subQuery) use ($user) {
                        $subQuery->where('user_id', $user->id);
                    });
                }
                // If user has all-orders permission, show all orders (no additional filtering)
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
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Orders';
    }
}
