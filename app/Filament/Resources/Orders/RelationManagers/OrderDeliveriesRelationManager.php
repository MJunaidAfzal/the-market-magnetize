<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Filament\Resources\OrderDeliveries\OrderDeliveryResource;
use App\Models\OrderDelivery;
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

class OrderDeliveriesRelationManager extends RelationManager
{
    protected static string $relationship = 'orderDeliveries';

    protected static ?string $relatedResource = OrderDeliveryResource::class;

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

                TextColumn::make('delivery_note')
                    ->label('Delivery Note')
                    ->limit(50)
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
            ])
            ->filters([
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
            ->headerActions([
                CreateAction::make()
                    ->label('Add Delivery')
                    ->slideOver()
                    ->url(fn(): string => route('filament.admin.resources.orders.order-deliveries.create', [
                        'order' => $this->getOwnerRecord()->id,
                    ])),
            ])
            ->modifyQueryUsing(function (Builder $query): void {
                //
            });
    }
}
