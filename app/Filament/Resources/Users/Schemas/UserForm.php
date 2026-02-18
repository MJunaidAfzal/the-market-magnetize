<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Personal Information')
                    ->label('Personal Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('John Doe')
                            ->columnSpanFull(),
                        TextInput::make('username')
                            ->label('Username')
                            ->maxLength(255)
                            ->placeholder('johndoe')
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->placeholder('john@example.com')
                            ->columnSpanFull(),
                    ]),
                Fieldset::make('Profile Details')
                    ->label('Profile Details')
                    ->schema([
                        TextInput::make('phone_number')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('+1 (555) 000-0000')
                            ->columnSpanFull(),
                        DatePicker::make('date_of_birth')
                            ->label('Date of Birth')
                            ->maxDate(now())
                            ->placeholder('Select date of birth')
                            ->columnSpanFull(),
                        FileUpload::make('profile_photo')
                            ->label('Profile Photo')
                            ->image()
                            ->directory('profile-photos')
                            ->maxSize(2048)
                            ->columnSpanFull(),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'suspended' => 'Suspended',
                            ])
                            ->default('active')
                            ->native(false)
                            ->columnSpanFull(),
                    ]),
                Fieldset::make('Authentication')
                    ->label('Authentication')
                    ->schema([
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required(fn($operation) => $operation === 'create')
                            ->maxLength(255)
                            ->revealable()
                            ->placeholder('Enter password')
                            ->columnSpanFull(),
                    ]),
                Fieldset::make('Roles & Permissions')
                    ->label('Roles & Permissions')
                    ->schema([
                        Select::make('roles')
                            ->label('User Roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->placeholder('Select roles...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
