<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        $authUser = Auth::user();

        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->width('80px'),

                TextColumn::make('full_name')
                    ->label('Name')
                    ->getStateUsing(fn($record) => $record->full_name)
                    ->sortable(['first_name', 'last_name'])
                    ->searchable(['first_name', 'last_name'])
                    ->weight('medium'),

                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable()
                    ->color('gray')
                    ->toggleable()
                    ->placeholder('-'),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->sortable()
                    ->searchable()
                    ->color('gray')
                    ->toggleable()
                    ->placeholder('-'),

                TextColumn::make('company')
                    ->label('Company')
                    ->sortable()
                    ->searchable()
                    ->color('gray')
                    ->toggleable()
                    ->placeholder('-'),

                TextColumn::make('leadSource.name')
                    ->label('Source')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->toggleable()
                    ->placeholder('-'),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->colors([
                        'info' => 'new',
                        'warning' => 'contacted',
                        'purple' => 'qualified',
                        'success' => 'won',
                        'danger' => 'lost',
                    ])
                    ->icons([
                        'heroicon-o-sparkles' => 'new',
                        'heroicon-o-phone' => 'contacted',
                        'heroicon-o-check-circle' => 'qualified',
                        'heroicon-o-trophy' => 'won',
                        'heroicon-o-x-circle' => 'lost',
                    ])
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->toggleable(),

                TextColumn::make('assignedUsers.name')
                    ->label('Assigned To')
                    ->getStateUsing(fn($record) => $record->assignedUsers->pluck('name')->implode(', '))
                    ->limit(30)
                    ->badge()
                    ->color('primary')
                    ->toggleable()
                    ->placeholder('-'),


                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable()
                    ->color('gray'),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('gray'),
            ])
            ->filters([
                // Status Filter
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(Lead::getStatusOptions())
                    ->multiple()
                    ->placeholder('All Statuses'),

                // Lead Source Filter
                SelectFilter::make('lead_source_id')
                    ->label('Lead Source')
                    ->options(LeadSource::where('is_active', true)->pluck('name', 'id'))
                    ->multiple()
                    ->placeholder('All Sources'),

                // Assigned User Filter
                SelectFilter::make('assigned_to')
                    ->label('Assigned To')
                    ->options(function () {
                        return User::where('status', 'active')->pluck('name', 'id');
                    })
                    ->multiple()
                    ->placeholder('All Users')
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['values'])) {
                            $query->whereHas('assignedUsers', function ($subQuery) use ($data) {
                                $subQuery->whereIn('user_id', $data['values'])
                                         ->where('is_active', true);
                            });
                        }
                    }),
               
                // Created Date Filter
                Filter::make('created_at')
                    ->label('Created Date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label('From Date'),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['created_from'])) {
                            $query->whereDate('created_at', '>=', $data['created_from']);
                        }
                        if (!empty($data['created_until'])) {
                            $query->whereDate('created_at', '<=', $data['created_until']);
                        }
                    }),

            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                Action::make('create_order')
                    ->label('Create Order')
                    ->tooltip('Create Order from Lead')
                    ->color('stripe')
                    ->button()
                    ->icon('heroicon-o-shopping-cart')
                    ->url(fn($record) => route('filament.admin.resources.orders.create', ['lead_id' => $record->id]))
                    ->visible(fn() => $authUser->hasPermissionTo('can-create-leads')),
                ViewAction::make()
                    ->label('')
                    ->tooltip('View Lead')
                    ->color('info')
                    ->slideOver()
                    ->button()
                    ->visible(fn() => $authUser->hasPermissionTo('can-view-leads')),
                EditAction::make()
                    ->label('')
                    ->tooltip('Edit Lead')
                    ->color('warning')
                    ->button()
                    ->visible(fn() => $authUser->hasPermissionTo('can-edit-leads')),
                DeleteAction::make()
                    ->label('')
                    ->tooltip('Delete Lead')
                    ->color('danger')
                    ->button()
                    ->visible(fn() => $authUser->hasPermissionTo('can-delete-leads')),
            ])
            ->toolbarActions([
                //
            ])
            ->emptyStateActions([
                //
            ]);
    }
}
