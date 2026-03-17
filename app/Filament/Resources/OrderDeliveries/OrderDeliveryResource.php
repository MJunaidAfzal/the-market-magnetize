<?php

namespace App\Filament\Resources\OrderDeliveries;

use App\Filament\Resources\OrderDeliveries\Pages\CreateOrderDelivery;
use App\Filament\Resources\OrderDeliveries\Pages\EditOrderDelivery;
use App\Filament\Resources\OrderDeliveries\Pages\ListOrderDeliveries;
use App\Filament\Resources\OrderDeliveries\Schemas\OrderDeliveryForm;
use App\Filament\Resources\OrderDeliveries\Tables\OrderDeliveriesTable;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\OrderDelivery;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OrderDeliveryResource extends Resource
{
    protected static ?string $model = OrderDelivery::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaperAirplane;

    protected static ?string $navigationLabel = 'Order Deliveries';

    protected static ?string $modelLabel = 'Order Delivery';

    protected static ?string $pluralModelLabel = 'Order Deliveries';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $parentResource = OrderResource::class;

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        return $user && $user->hasPermissionTo('manage-orders');
    }

    public static function form(Schema $schema): Schema
    {
        return OrderDeliveryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrderDeliveriesTable::configure($table);
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
            'index' => ListOrderDeliveries::route('/'),
            'create' => CreateOrderDelivery::route('/create'),
            'edit' => EditOrderDelivery::route('/{record}/edit'),
        ];
    }

    public static function getParentResourceRegistration(): ?\Filament\Resources\ParentResourceRegistration
    {
        return OrderResource::asParent()
            ->relationship('orderDeliveries')
            ->inverseRelationship('order');
    }
}
