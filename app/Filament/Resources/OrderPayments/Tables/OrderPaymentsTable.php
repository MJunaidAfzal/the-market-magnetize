<?php

namespace App\Filament\Resources\OrderPayments\Tables;

use App\Models\OrderPayment;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class OrderPaymentsTable
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
                    ->limit(50),

                TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable()
                    ->color('gray')
                    ->toggleable()
                    ->placeholder('-'),

                TextColumn::make('total_amount')
                    ->label('Amount')
                    ->sortable()
                    ->money('USD')
                    ->weight('medium')
                    ->toggleable(),

                BadgeColumn::make('is_paid')
                    ->label('Status')
                    ->sortable()
                    ->colors([
                        'danger' => false,
                        'success' => true,
                    ])
                    ->formatStateUsing(fn($state) => $state ? 'Paid' : 'Unpaid')
                    ->toggleable(),

                TextColumn::make('payment_note')
                    ->label('Payment Note')
                    ->limit(50)
                    ->searchable()
                    ->color('gray')
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
                SelectFilter::make('is_paid')
                    ->label('Payment Status')
                    ->options([
                        '1' => 'Paid',
                        '0' => 'Unpaid',
                    ])
                    ->placeholder('All Statuses'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('')
                    ->tooltip('View Payment')
                    ->color('info')
                    ->slideOver()
                    ->button(),
                EditAction::make()
                    ->label('')
                    ->tooltip('Edit Payment')
                    ->color('warning')
                    ->button(),
                DeleteAction::make()
                    ->label('')
                    ->tooltip('Delete Payment')
                    ->color('danger')
                    ->button(),
            ])
            ->emptyStateActions([
                //
            ]);
    }
}
