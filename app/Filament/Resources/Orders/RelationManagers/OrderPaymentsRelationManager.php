<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Filament\Resources\OrderPayments\OrderPaymentResource;
use App\Models\OrderPayment;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderPaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderPayments';

    protected static ?string $relatedResource = OrderPaymentResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->width('80px'),

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

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable()
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
            ->headerActions([
                CreateAction::make()
                    ->label('Add Payment')
                    ->slideOver()
                    ->url(fn(): string => route('filament.admin.resources.orders.order-payments.create', [
                        'order' => $this->getOwnerRecord()->id,
                    ])),
            ])
            ->modifyQueryUsing(function (Builder $query): void {
                //
            });
    }
}
