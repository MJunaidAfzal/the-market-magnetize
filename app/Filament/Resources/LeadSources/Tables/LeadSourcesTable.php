<?php

namespace App\Filament\Resources\LeadSources\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class LeadSourcesTable
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
                    ->label('Source Name')
                    ->sortable()
                    ->searchable()
                    ->weight('medium'),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->sortable()
                    ->searchable()
                    ->color('gray')
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('leads_count')
                    ->label('Leads')
                    ->counts('leads')
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
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('')
                    ->tooltip('View Lead Source')
                    ->color('info')
                    ->slideOver()
                    ->button(),
                EditAction::make()
                    ->label('')
                    ->tooltip('Edit Lead Source')
                    ->color('warning')
                    ->button(),
                DeleteAction::make()
                    ->label('')
                    ->tooltip('Delete Lead Source')
                    ->color('danger')
                    ->button(),
            ])
            ->toolbarActions([
                //
            ])
            ->emptyStateActions([
                //
            ]);
    }
}
