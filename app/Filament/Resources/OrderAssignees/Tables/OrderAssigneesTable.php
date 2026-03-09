<?php

namespace App\Filament\Resources\OrderAssignees\Tables;

use App\Models\Order;
use App\Models\OrderAssignee;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderAssigneesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->width('80px'),

                TextColumn::make('order.order_number')
                    ->label('Order #')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->limit(20),

                TextColumn::make('order.title')
                    ->label('Order Title')
                    ->sortable()
                    ->searchable()
                    ->limit(50),

                TextColumn::make('user.name')
                    ->label('Assigned User')
                    ->sortable()
                    ->searchable()
                    ->color('primary')
                    ->weight('medium'),

                TextColumn::make('user.email')
                    ->label('User Email')
                    ->sortable()
                    ->searchable()
                    ->color('gray')
                    ->limit(30),

                TextColumn::make('created_at')
                    ->label('Assigned At')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->color('gray'),
            ])
            ->filters([
                // Order Filter
                SelectFilter::make('order_id')
                    ->label('Order')
                    ->options(Order::pluck('title', 'id'))
                    ->multiple()
                    ->placeholder('All Orders')
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['values'])) {
                            $query->whereIn('order_id', $data['values']);
                        }
                    }),

                // User Filter
                SelectFilter::make('user_id')
                    ->label('Assigned User')
                    ->options(User::where('status', 'active')->pluck('name', 'id'))
                    ->multiple()
                    ->placeholder('All Users')
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['values'])) {
                            $query->whereIn('user_id', $data['values']);
                        }
                    }),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->label('')
                    ->tooltip('Remove Assignment')
                    ->color('danger')
                    ->button(),
            ])
            ->emptyStateActions([
                //
            ]);
    }
}
