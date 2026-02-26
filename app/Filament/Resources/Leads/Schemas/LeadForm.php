<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Models\Lead;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Basic Information Section
                Fieldset::make('Basic Information')
                    ->label('Basic Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('first_name')
                                    ->label('First Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter first name'),
                                TextInput::make('last_name')
                                    ->label('Last Name')
                                    ->maxLength(255)
                                    ->placeholder('Enter last name'),
                                 TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->maxLength(255)
                                    ->placeholder('email@example.com'),
                            ])->columnSpanFull(),
                        Grid::make(3)
                            ->schema([
                                TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->maxLength(20)
                                    ->placeholder('+1 (555) 000-0000'),
                                TextInput::make('company')
                                    ->label('Company')
                                    ->maxLength(255)
                                    ->placeholder('Company name'),
                                TextInput::make('job_title')
                                    ->label('Job Title')
                                    ->maxLength(255)
                                    ->placeholder('Job position'),
                            ])->columnSpanFull(),
                    ])->columnSpanFull(),

                // Source & Status Section
                Fieldset::make('Source & Status')
                    ->label('Source & Status')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('lead_source_id')
                                    ->label('Lead Source')
                                    ->relationship('leadSource', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Select lead source'),
                                Select::make('status')
                                    ->label('Status')
                                    ->options(Lead::getStatusOptions())
                                    ->default('new')
                                    ->native(false)
                                    ->required(),
                            ])->columnSpanFull(),
                    ]),

                // // Value & Scoring Section
                // Fieldset::make('Value & Scoring')
                //     ->label('Value & Scoring')
                //     ->schema([
                //         Grid::make(2)
                //             ->schema([
                //                 TextInput::make('value')
                //                     ->label('Deal Value')
                //                     ->numeric()
                //                     ->prefix('$')
                //                     ->maxLength(15)
                //                     ->placeholder('0.00'),
                //                 TextInput::make('score')
                //                     ->label('Lead Score')
                //                     ->numeric()
                //                     ->default(0)
                //                     ->minValue(0)
                //                     ->maxValue(100)
                //                     ->placeholder('0-100'),
                //             ]),
                //     ]),

     // Assignment Section
                Fieldset::make('Assign To Users')
                    ->label('Assign To Users')
                    ->schema([
                        Select::make('assigned_users')
                            ->label('Assigned Users')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(function () {
                                return User::where('status', 'active')
                                    ->pluck('name', 'id');
                            })
                            ->placeholder('Select users to assign this lead...')
                            ->columnSpanFull(),
                    ]),

                // Address Information Section
                Fieldset::make('Address Information')
                    ->label('Address Information')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextInput::make('country')
                                    ->label('Country')
                                    ->maxLength(255)
                                    ->placeholder('Country name'),
                                TextInput::make('city')
                                    ->label('City')
                                    ->maxLength(255)
                                    ->placeholder('City name'),
                                 DateTimePicker::make('last_contacted_at')
                                    ->label('Last Contacted At')
                                    ->placeholder('Select date and time'),
                                DateTimePicker::make('follow_up_date')
                                    ->label('Follow-up Date')
                                    ->placeholder('Select follow-up date'),
                            ])->columnSpanFull(),
                        Textarea::make('address')
                            ->label('Address')
                            ->rows(2)
                            ->maxLength(500)
                            ->placeholder('Full address')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),

                // Notes Section
                Fieldset::make('Notes')
                    ->label('Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(4)
                            ->maxLength(2000)
                            ->placeholder('Add any additional notes about this lead...')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
