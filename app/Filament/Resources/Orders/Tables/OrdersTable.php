<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Filament\Resources\OrderDeliveries\OrderDeliveryResource;
use App\Filament\Resources\OrderPayments\OrderPaymentResource;
use App\Filament\Resources\OrderRevisions\OrderRevisionResource;
use App\Models\Lead;
use App\Models\Order;
use App\Models\OrderCategory;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\ActionGroup;
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

                // BadgeColumn::make('priority')
                //     ->label('Priority')
                //     ->sortable()
                //     ->colors([
                //         'gray' => 'Low',
                //         'info' => 'Medium',
                //         'warning' => 'High',
                //         'danger' => 'Urgent',
                //     ])
                //     ->toggleable(isToggledHiddenByDefault: true)
                //     ->formatStateUsing(fn($state) => $state),

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
                    ->toggleable(isToggledHiddenByDefault: true)
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
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-'),

                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->color(fn($state) => $state && $state->isPast() ? 'danger' : 'gray')
                    ->toggleable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable()
                    ->toggleable(isToggledHiddenByDefault: true)
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
           
                Action::make('viewDeliveries')
                    ->label('')
                    ->tooltip('View Order Deliveries')
                    ->icon('heroicon-s-truck')
                    ->color('rado')
                    ->url(fn($record) => route('filament.admin.resources.orders.order-deliveries.index', ['order' => $record->id]))
                    ->openUrlInNewTab()
                    ->button(),

                Action::make('viewPayments')
                    ->label('')
                    ->tooltip('View Order Payments')
                    ->icon('heroicon-s-currency-dollar')
                    ->color('stripe')
                    ->url(fn($record) => route('filament.admin.resources.orders.order-payments.index', ['order' => $record->id]))
                    ->openUrlInNewTab()
                    ->button(),

                Action::make('viewRevisions')
                    ->label('')
                    ->tooltip('View Order Revisions')
                    ->icon('heroicon-s-clipboard-document-list')
                    ->color('ligi')
                    ->url(fn($record) => route('filament.admin.resources.orders.order-revisions.index', ['order' => $record->id]))
                    ->openUrlInNewTab()
                    ->button(),

         ActionGroup::make([
              ViewAction::make()
                    ->label('VIEW')
                    ->color('info')
                    ->slideOver()
                    ->visible(fn() => $authUser->hasPermissionTo('can-view-order')),
                EditAction::make()
                    ->label('EDIT')
                    ->color('warning')
                    ->visible(fn() => $authUser->hasPermissionTo('can-edit-order')),
                DeleteAction::make()
                    ->label('DELETE')
                    ->color('danger')
                    ->visible(fn() => $authUser->hasPermissionTo('can-delete-order')),
            ])
             ->visible(fn() => $authUser->hasAnyPermission(['can-view-order', 'can-edit-order', 'can-delete-order']))
             ->color('light'),
            ])
            ->emptyStateActions([
                //
            ]);
    }
}
