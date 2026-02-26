<?php

namespace App\Filament\Resources\Leads;

use App\Filament\Resources\Leads\Pages\CreateLead;
use App\Filament\Resources\Leads\Pages\EditLead;
use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Filament\Resources\Leads\Schemas\LeadForm;
use App\Filament\Resources\Leads\Tables\LeadsTable;
use App\Models\Lead;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Leads';

    protected static ?string $modelLabel = 'Lead';

    protected static ?string $pluralModelLabel = 'Leads';

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        return $user && $user->hasPermissionTo('manage-leads');
    }

    public static function form(Schema $schema): Schema
    {
        return LeadForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeadsTable::configure($table)
            ->modifyQueryUsing(function (\Illuminate\Database\Eloquent\Builder $query): void {
                $user = Filament::auth()->user();
                
                // Eager load relationships
                $query->with(['leadSource', 'assignedUsers']);

                // Apply permission-based filtering
                if ($user->hasPermissionTo('assigned-leads-only') && !$user->hasPermissionTo('all-leads')) {
                    // Show only leads assigned to this user
                    $query->whereHas('assignedUsers', function ($subQuery) use ($user) {
                        $subQuery->where('user_id', $user->id)
                                 ->where('is_active', true);
                    });
                }
                // If user has all-leads permission, show all leads (no additional filtering)
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
            'index' => ListLeads::route('/'),
            'create' => CreateLead::route('/create'),
            'edit' => EditLead::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Leads';
    }
}
