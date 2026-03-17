<?php

namespace App\Filament\Resources\OrderPayments;

use App\Filament\Resources\OrderPayments\Pages\CreateOrderPayment;
use App\Filament\Resources\OrderPayments\Pages\EditOrderPayment;
use App\Filament\Resources\OrderPayments\Pages\ListOrderPayments;
use App\Filament\Resources\OrderPayments\Schemas\OrderPaymentForm;
use App\Filament\Resources\OrderPayments\Tables\OrderPaymentsTable;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\OrderPayment;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OrderPaymentResource extends Resource
{
    protected static ?string $model = OrderPayment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static ?string $navigationLabel = 'Order Payments';

    protected static ?string $modelLabel = 'Order Payment';

    protected static ?string $pluralModelLabel = 'Order Payments';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $parentResource = OrderResource::class;

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        return $user && $user->hasPermissionTo('manage-orders');
    }

    public static function form(Schema $schema): Schema
    {
        return OrderPaymentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrderPaymentsTable::configure($table);
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
            'index' => ListOrderPayments::route('/'),
            'create' => CreateOrderPayment::route('/create'),
            'edit' => EditOrderPayment::route('/{record}/edit'),
        ];
    }

    public static function getParentResourceRegistration(): ?\Filament\Resources\ParentResourceRegistration
    {
        return OrderResource::asParent()
            ->relationship('orderPayments')
            ->inverseRelationship('order');
    }
}
