<?php

namespace App\Filament\Resources\OrderCategories\Tables;

use App\Models\OrderCategory;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class OrderCategoriesTable
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

                TextColumn::make('name')
                    ->label('Category Name')
                    ->sortable()
                    ->searchable()
                    ->weight('medium')
                    ->limit(50),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->sortable()
                    ->searchable()
                    ->color('gray')
                    ->toggleable()
                    ->placeholder('-'),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(100)
                    ->searchable()
                    ->color('gray')
                    ->toggleable()
                    ->placeholder('-'),

                BadgeColumn::make('is_active')
                    ->label('Status')
                    ->sortable()
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ])
                    ->formatStateUsing(fn($state) => $state ? 'Active' : 'Inactive')
                    ->toggleable(),

                TextColumn::make('orders_count')
                    ->label('Orders')
                    ->counts('orders')
                    ->sortable()
                    ->badge()
                    ->color('info')
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
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ])
                    ->placeholder('All Statuses'),

                // Created Date Filter
                Filter::make('created_at')
                    ->label('Created Date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label('From Date'),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('To Date'),
                    ])
                    ->query(function ($query, array $data) {
                        if (!empty($data['created_from'])) {
                            $query->whereDate('created_at', '>=', $data['created_from']);
                        }
                        if (!empty($data['created_until'])) {
                            $query->whereDate('created_at', '<=', $data['created_until']);
                        }
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('')
                    ->tooltip('View Category')
                    ->color('info')
                    ->slideOver()
                    ->button(),
                EditAction::make()
                    ->label('')
                    ->tooltip('Edit Category')
                    ->color('warning')
                    ->button(),
                DeleteAction::make()
                    ->label('')
                    ->tooltip('Delete Category')
                    ->color('danger')
                    ->button(),
            ])
            ->emptyStateActions([
                //
            ]);
    }
}
