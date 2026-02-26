<?php

namespace App\Filament\Resources\LeadSources;

use App\Filament\Resources\LeadSources\Pages\CreateLeadSource;
use App\Filament\Resources\LeadSources\Pages\EditLeadSource;
use App\Filament\Resources\LeadSources\Pages\ListLeadSources;
use App\Filament\Resources\LeadSources\Schemas\LeadSourceForm;
use App\Filament\Resources\LeadSources\Tables\LeadSourcesTable;
use App\Models\LeadSource;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Facades\Filament;

class LeadSourceResource extends Resource
{
    protected static ?string $model = LeadSource::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookmarkSquare;

    protected static ?string $navigationLabel = 'Lead Sources';

    protected static ?string $modelLabel = 'Lead Source';

    protected static ?string $pluralModelLabel = 'Lead Sources';

    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        return $user && $user->hasPermissionTo('manage-lead-sources');
    }

    public static function form(Schema $schema): Schema
    {
        return LeadSourceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeadSourcesTable::configure($table);
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
            'index' => ListLeadSources::route('/'),
            'create' => CreateLeadSource::route('/create'),
            'edit' => EditLeadSource::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Leads';
    }
}
