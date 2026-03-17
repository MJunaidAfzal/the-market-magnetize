<?php

namespace App\Filament\Resources\OrderPayments\Schemas;

use App\Models\PaymentMethod;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class OrderPaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Hidden order_id field
                Hidden::make('order_id'),

                // First Row - Two Grids Side by Side
                Grid::make(2)
                    ->schema([
                        // Left Column: Payment Method & Amount
                        Grid::make(1)
                            ->schema([
                                
                                TextInput::make('total_amount')
                                    ->label('Total Amount')
                                    ->numeric()
                                    ->prefix('$')
                                    ->placeholder('0.00')
                                    ->required(),

                                    Select::make('payment_method_id')
                                    ->label('Payment Method')
                                    ->options(PaymentMethod::where('status', true)->pluck('name', 'id'))
                                    ->nullable()
                                    ->searchable()
                                    ->placeholder('Select payment method')
                                    ->columnSpanFull(),
                            ])->columnSpan(1),

                        // Right Column: Status Toggle
                        Grid::make(1)
                            ->schema([
                                Toggle::make('is_paid')
                                    ->label('Payment Status')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->helperText('Toggle to mark as paid'),
                            ])->columnSpan(1),
                    ]),

                // Second Row - Payment Receipt (Full Width)
                Grid::make(1)
                    ->schema([
                       
                            Textarea::make('payment_note')
                            ->label('Payment Note')
                            ->rows(4)
                            ->maxLength(2000)
                            ->placeholder('Enter payment notes or comments...')
                            ->columnSpanFull(),
                    ]),

                // Third Row - Payment Note (Full Width)
                FileUpload::make('payment_receipt')
                            ->label('Payment Receipt')
                            ->multiple()
                            ->disk('public')
                            ->directory('order-payments')
                            ->preserveFilenames()
                            ->downloadable()
                            ->openable()
                            ->nullable()
                            ->placeholder('Upload payment receipt files')
                            ->columnSpanFull(),

                        
            ]);
    }
}
