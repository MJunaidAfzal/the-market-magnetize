<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Lead;
use App\Models\Order;
use App\Models\OrderCategory;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Basic Information Section
                Fieldset::make('Basic Information')
                    ->label('Basic Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Order Title')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter order title'),
                                Select::make('order_category_id')
                                    ->label('Category')
                                    ->relationship('orderCategory', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Select category'),
                            ])->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->maxLength(2000)
                            ->placeholder('Enter order description...')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),

                // Client Information Section
                Fieldset::make('Client Information')
                    ->label('Client Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('lead_id')
                                    ->label('Lead')
                                    ->relationship('lead', 'first_name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->placeholder('Select lead')
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $lead = Lead::find($state);
                                            if ($lead) {
                                                $set('lead_first_name', $lead->first_name);
                                                $set('lead_last_name', $lead->last_name ?? '');
                                                $set('lead_email', $lead->email ?? '');
                                                $set('lead_phone', $lead->phone ?? '');
                                            }
                                        } else {
                                            $set('lead_first_name', '');
                                            $set('lead_last_name', '');
                                            $set('lead_email', '');
                                            $set('lead_phone', '');
                                        }
                                    }),
                                TextInput::make('lead_first_name')
                                    ->label('First Name')
                                    ->readOnly(),
                                TextInput::make('lead_last_name')
                                    ->label('Last Name')
                                    ->readOnly(),
                            ])->columnSpanFull(),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('lead_email')
                                    ->label('Email')
                                    ->readOnly(),
                                TextInput::make('lead_phone')
                                    ->label('Phone Number')
                                    ->readOnly(),
                            ])->columnSpanFull(),
                    ])->columnSpanFull(),

                // Workflow Section
                Fieldset::make('Workflow')
                    ->label('Workflow')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->label('Status')
                                    ->options(Order::getStatusOptions())
                                    ->default('Pending')
                                    ->native(false)
                                    ->required(),
                                Select::make('priority')
                                    ->label('Priority')
                                    ->options(Order::getPriorityOptions())
                                    ->default('Medium')
                                    ->native(false)
                                    ->required(),
                            ])->columnSpanFull(),
                    ])->columnSpanFull(),

                // Financial Section
                Fieldset::make('Financial Details')
                    ->label('Financial Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('amount')
                                    ->label('Total Amount')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0)
                                    ->maxLength(15)
                                    ->placeholder('0.00')
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        $amount = floatval($state ?? 0);
                                        $paidAmount = floatval($get('paid_amount') ?? 0);
                                        $set('due_amount', $amount - $paidAmount);
                                        
                                        // Update payment status
                                        $dueAmount = $amount - $paidAmount;
                                        if ($dueAmount <= 0) {
                                            $set('payment_status', 'Paid');
                                        } elseif ($paidAmount > 0) {
                                            $set('payment_status', 'Partial');
                                        } else {
                                            $set('payment_status', 'Unpaid');
                                        }
                                    }),
                                TextInput::make('paid_amount')
                                    ->label('Paid Amount')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0)
                                    ->maxLength(15)
                                    ->placeholder('0.00')
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        $amount = floatval($get('amount') ?? 0);
                                        $paidAmount = floatval($state ?? 0);
                                        $set('due_amount', $amount - $paidAmount);
                                        
                                        // Update payment status
                                        $dueAmount = $amount - $paidAmount;
                                        if ($dueAmount <= 0) {
                                            $set('payment_status', 'Paid');
                                        } elseif ($paidAmount > 0) {
                                            $set('payment_status', 'Partial');
                                        } else {
                                            $set('payment_status', 'Unpaid');
                                        }
                                    }),
                                TextInput::make('due_amount')
                                    ->label('Due Amount')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0)
                                    ->maxLength(15)
                                    ->placeholder('0.00')
                                    ->readOnly(),
                            ])->columnSpanFull(),
                        Select::make('payment_status')
                            ->label('Payment Status')
                            ->options(Order::getPaymentStatusOptions())
                            ->default('Unpaid')
                            ->native(false)
                            ->required(),
                    ])->columnSpanFull(),

                // Dates Section
                Fieldset::make('Dates')
                    ->label('Dates')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                DatePicker::make('start_date')
                                    ->label('Start Date')
                                    ->placeholder('Select start date'),
                                DatePicker::make('due_date')
                                    ->label('Due Date')
                                    ->placeholder('Select due date'),
                                DatePicker::make('completed_at')
                                    ->label('Completed Date')
                                    ->placeholder('Select completion date'),
                            ])->columnSpanFull(),
                    ])->columnSpanFull(),

                // Assignment Section
                Fieldset::make('Assign To Users')
                    ->label('Assign To Users')
                    ->schema([
                        Select::make('assignees')
                            ->label('Assigned Users')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(function () {
                                return User::where('status', 'active')
                                    ->pluck('name', 'id');
                            })
                            ->placeholder('Select users to assign this order...')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),

                // Attachments Section
                Fieldset::make('Attachments')
                    ->label('Attachments')
                    ->schema([
                        FileUpload::make('attachments')
                            ->label('Upload Files')
                            ->multiple()
                            ->directory('order-attachments')
                            ->preserveFilenames()
                            ->maxFiles(10)
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
                            ->placeholder('Add any additional notes about this order...')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
