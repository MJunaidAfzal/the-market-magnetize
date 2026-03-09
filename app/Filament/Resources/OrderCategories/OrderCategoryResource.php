<?php

namespace App\Filament\Resources\OrderCategories;

use App\Filament\Resources\OrderCategories\Pages\CreateOrderCategory;
use App\Filament\Resources\OrderCategories\Pages\EditOrderCategory;
use App\Filament\Resources\OrderCategories\Pages\ListOrderCategories;
use App\Filament\Resources\OrderCategories\Schemas\OrderCategoryForm;
use App\Filament\Resources\OrderCategories\Tables\OrderCategoriesTable;
use App\Models\OrderCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Facades\Filament;

class OrderCategoryResource extends Resource
{
    protected static ?string $model = OrderCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Order Categories';

    protected static ?string $modelLabel = 'Order Category';

    protected static ?string $pluralModelLabel = 'Order Categories';

    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        return $user && $user->hasPermissionTo('manage-order-categories');
    }

    public static function form(Schema $schema): Schema
    {
        return OrderCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrderCategoriesTable::configure($table);
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
            'index' => ListOrderCategories::route('/'),
            'create' => CreateOrderCategory::route('/create'),
            'edit' => EditOrderCategory::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Orders';
    }
}
