<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Lead;
use App\Models\Order;
use App\Models\OrderCategory;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class OrdersTable
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

                TextColumn::make('order_number')
                    ->label('Order #')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->limit(20),

                TextColumn::make('title')
                    ->label('Title')
                    ->sortable()
                    ->searchable()
                    ->weight('medium')
                    ->limit(50),

                TextColumn::make('lead.full_name')
                    ->label('Lead')
                    ->sortable()
                    ->searchable()
                    ->limit(30)
                    ->color('primary')
                    ->placeholder('-'),

                TextColumn::make('orderCategory.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->placeholder('-'),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->colors([
                        'gray' => 'Pending',
                        'info' => 'Confirmed',
                        'warning' => 'In Progress',
                        'danger' => 'On Hold',
                        'success' => 'Completed',
                        'purple' => 'Cancelled',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'Pending',
                        'heroicon-o-check-circle' => 'Confirmed',
                        'heroicon-o-arrow-right-circle' => 'In Progress',
                        'heroicon-o-pause-circle' => 'On Hold',
                        'heroicon-o-star' => 'Completed',
                        'heroicon-o-x-circle' => 'Cancelled',
                    ])
                    ->formatStateUsing(fn($state) => $state)
                    ->toggleable(),

                BadgeColumn::make('priority')
                    ->label('Priority')
                    ->sortable()
                    ->colors([
                        'gray' => 'Low',
                        'info' => 'Medium',
                        'warning' => 'High',
                        'danger' => 'Urgent',
                    ])
                    ->formatStateUsing(fn($state) => $state)
                    ->toggleable(),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->sortable()
                    ->money('USD')
                    ->weight('medium')
                    ->toggleable(),

                BadgeColumn::make('payment_status')
                    ->label('Payment')
                    ->sortable()
                    ->colors([
                        'danger' => 'Unpaid',
                        'warning' => 'Partial',
                        'success' => 'Paid',
                        'purple' => 'Overdue',
                    ])
                    ->formatStateUsing(fn($state) => $state)
                    ->toggleable(),

                TextColumn::make('assignees.name')
                    ->label('Assigned To')
                    ->getStateUsing(fn($record) => $record->assignees->pluck('name')->implode(', '))
                    ->limit(30)
                    ->badge()
                    ->color('primary')
                    ->toggleable()
                    ->placeholder('-'),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->sortable()
                    ->searchable()
                    ->color('gray')
                    ->toggleable()
                    ->placeholder('-'),

                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->color(fn($state) => $state && $state->isPast() ? 'danger' : 'gray')
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
                    ->options(Order::getStatusOptions())
                    ->multiple()
                    ->placeholder('All Statuses'),

                // Priority Filter
                SelectFilter::make('priority')
                    ->label('Priority')
                    ->options(Order::getPriorityOptions())
                    ->multiple()
                    ->placeholder('All Priorities'),

                // Payment Status Filter
                SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options(Order::getPaymentStatusOptions())
                    ->multiple()
                    ->placeholder('All Payment Status'),

                // Category Filter
                SelectFilter::make('order_category_id')
                    ->label('Category')
                    ->options(OrderCategory::where('is_active', true)->pluck('name', 'id'))
                    ->multiple()
                    ->placeholder('All Categories'),

                // Lead Filter
                // SelectFilter::make('lead_id')
                //     ->label('Lead')
                //     ->options(function () {
                //         return Lead::with('full_name')->get()->pluck('first_name', 'id');
                //     })
                //     ->multiple()
                //     ->placeholder('All Leads'),

                // Assignee Filter
                SelectFilter::make('assignees')
                    ->label('Assigned To')
                    ->options(function () {
                        return User::where('status', 'active')->pluck('name', 'id');
                    })
                    ->multiple()
                    ->placeholder('All Users')
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['values'])) {
                            $query->whereHas('assignees', function ($subQuery) use ($data) {
                                $subQuery->whereIn('user_id', $data['values']);
                            });
                        }
                    }),

                // Due Date Filter
                Filter::make('due_date')
                    ->label('Due Date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('due_from')
                            ->label('From Date'),
                        \Filament\Forms\Components\DatePicker::make('due_until')
                            ->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['due_from'])) {
                            $query->whereDate('due_date', '>=', $data['due_from']);
                        }
                        if (!empty($data['due_until'])) {
                            $query->whereDate('due_date', '<=', $data['due_until']);
                        }
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('')
                    ->tooltip('View Order')
                    ->color('info')
                    ->slideOver()
                    ->button()
                    ->visible(fn() => $authUser->hasPermissionTo('can-view-order')),
                EditAction::make()
                    ->label('')
                    ->tooltip('Edit Order')
                    ->color('warning')
                    ->button()
                    ->visible(fn() => $authUser->hasPermissionTo('can-edit-order')),
                DeleteAction::make()
                    ->label('')
                    ->tooltip('Delete Order')
                    ->color('danger')
                    ->button()
                    ->visible(fn() => $authUser->hasPermissionTo('can-delete-order')),
            ])
            ->emptyStateActions([
                //
            ]);
    }
}
