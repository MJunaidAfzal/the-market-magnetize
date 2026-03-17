<?php

namespace App\Filament\Resources\OrderDeliveries\Tables;

use App\Models\OrderDelivery;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class OrderDeliveriesTable
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

                TextColumn::make('order.order_number')
                    ->label('Order Number')
                    ->sortable()
                    ->searchable()
                    ->weight('medium')
                    ->limit(50)
                    ->url(fn($record) => $record->order ? route('filament.admin.resources.orders.edit', $record->order) : null)
                    ->openUrlInNewTab(),

                TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable()
                    ->color('gray')
                    ->toggleable()
                    ->placeholder('-'),

                TextColumn::make('delivery_note')
                    ->label('Delivery Note')
                    ->limit(100)
                    ->searchable()
                    ->color('gray')
                    ->toggleable()
                    ->placeholder('-'),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->colors([
                        'warning' => OrderDelivery::STATUS_PENDING,
                        'success' => OrderDelivery::STATUS_APPROVED,
                        'danger' => OrderDelivery::STATUS_REJECTED,
                    ])
                    ->formatStateUsing(fn($state) => OrderDelivery::getStatusOptions()[$state] ?? 'Unknown')
                    ->toggleable(),

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
                    ->options(OrderDelivery::getStatusOptions())
                    ->placeholder('All Statuses'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('')
                    ->tooltip('View Delivery')
                    ->color('info')
                    ->slideOver()
                    ->button(),
                EditAction::make()
                    ->label('')
                    ->tooltip('Edit Delivery')
                    ->color('warning')
                    ->button(),
                DeleteAction::make()
                    ->label('')
                    ->tooltip('Delete Delivery')
                    ->color('danger')
                    ->button(),
            ])
            ->emptyStateActions([
                //
            ]);
    }
}
