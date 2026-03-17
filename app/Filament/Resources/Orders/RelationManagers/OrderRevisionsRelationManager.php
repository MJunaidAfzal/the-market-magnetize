<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Filament\Resources\OrderRevisions\OrderRevisionResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderRevisionsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderRevisions';

    protected static ?string $relatedResource = OrderRevisionResource::class;

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

                TextColumn::make('revision_note')
                    ->label('Revision Note')
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
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('')
                    ->tooltip('View Revision')
                    ->color('info')
                    ->slideOver()
                    ->button(),
                EditAction::make()
                    ->label('')
                    ->tooltip('Edit Revision')
                    ->color('warning')
                    ->button(),
                DeleteAction::make()
                    ->label('')
                    ->tooltip('Delete Revision')
                    ->color('danger')
                    ->button(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Revision')
                    ->slideOver()
                    ->url(fn(): string => route('filament.admin.resources.orders.order-revisions.create', [
                        'order' => $this->getOwnerRecord()->id,
                    ])),
            ])
            ->modifyQueryUsing(function (Builder $query): void {
                //
            });
    }
}
