<?php

namespace App\Filament\Resources\Users\Tables;

use App\Services\UserExcelService;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UsersTable
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

                ImageColumn::make('profile_photo')
                    ->label('Photo')
                    ->circular()
                    ->size(40)
                    ->toggleable()
                    ->defaultImageUrl(url('images/avatar.webp')),

                TextColumn::make('name')
                    ->label('Full Name')
                    ->sortable()
                    ->searchable()
                    ->weight('medium'),

                TextColumn::make('username')
                    ->label('Username')
                    ->sortable()
                    ->searchable()
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('email')
                    ->label('Email Address')
                    ->sortable()
                    ->searchable()
                    ->color('gray'),

                TextColumn::make('phone_number')
                    ->label('Phone')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->color('gray'),

                TextColumn::make('role')
                    ->label('Role')
                    ->getStateUsing(function ($record) {
                        return $record->roles->first()?->name ?? '-';
                    })
                    ->badge()
                    ->color('warning') 
                    ->toggleable(isToggledHiddenByDefault: false),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger' => 'suspended',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'active',
                        'heroicon-o-clock' => 'inactive',
                        'heroicon-o-x-circle' => 'suspended',
                    ])
                    ->toggleable(),

                // IconColumn::make('email_verified_at')
                //     ->label('Verified')
                //     ->sortable()
                //     ->boolean()
                //     ->trueIcon('heroicon-o-check-badge')
                //     ->falseIcon('heroicon-o-x-mark')
                //     ->trueColor('success')
                //     ->falseColor('danger')
                //     ->width('100px'),

                // TextColumn::make('last_login_at')
                //     ->label('Last Login')
                //     ->dateTime('M d, Y H:i')
                //     ->sortable()
                //     ->toggleable()
                //     ->color('gray')
                //     ->placeholder('Never'),

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
                Action::make('downloadExcel')
                    ->label('Download Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(route('download.users.excel'), true)
                    ->visible(fn() => $authUser->hasPermissionTo('can-download-user-excel-sheet')),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('')
                    ->tooltip('View User')
                    ->color('info')
                    ->slideOver()
                    ->button()
                    ->visible(fn() => $authUser->hasPermissionTo('can-view-user')),
                EditAction::make()
                    ->label('')
                    ->tooltip('Edit User')
                    ->color('warning')
                    ->button()
                    ->visible(fn() => $authUser->hasPermissionTo('can-edit-user')),
                DeleteAction::make()
                    ->label('')
                    ->tooltip('Delete User')
                    ->color('primary')
                    ->button()
                    ->visible(fn() => $authUser->hasPermissionTo('can-delete-user')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Delete Selected')
                        ->visible(fn() => $authUser->hasPermissionTo('can-delete-user')),
                ]),
            ]);
    }
}
