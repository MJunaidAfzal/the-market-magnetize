<?php

namespace App\Filament\Resources\OrderRevisions;

use App\Filament\Resources\OrderRevisions\Pages\CreateOrderRevision;
use App\Filament\Resources\OrderRevisions\Pages\EditOrderRevision;
use App\Filament\Resources\OrderRevisions\Pages\ListOrderRevisions;
use App\Filament\Resources\OrderRevisions\Schemas\OrderRevisionForm;
use App\Filament\Resources\OrderRevisions\Tables\OrderRevisionsTable;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\OrderRevision;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OrderRevisionResource extends Resource
{
    protected static ?string $model = OrderRevision::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowPath;

    protected static ?string $navigationLabel = 'Order Revisions';

    protected static ?string $modelLabel = 'Order Revision';

    protected static ?string $pluralModelLabel = 'Order Revisions';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $parentResource = OrderResource::class;

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        return $user && $user->hasPermissionTo('manage-orders');
    }

    public static function form(Schema $schema): Schema
    {
        return OrderRevisionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrderRevisionsTable::configure($table);
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
            'index' => ListOrderRevisions::route('/'),
            'create' => CreateOrderRevision::route('/create'),
            'edit' => EditOrderRevision::route('/{record}/edit'),
        ];
    }

    public static function getParentResourceRegistration(): ?\Filament\Resources\ParentResourceRegistration
    {
        return OrderResource::asParent()
            ->relationship('orderRevisions')
            ->inverseRelationship('order');
    }
}
