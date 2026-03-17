<?php

namespace App\Filament\Resources\OrderDeliveries\Schemas;

use App\Models\OrderDelivery;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderDeliveryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Hidden order_id field
                Hidden::make('order_id'),

                // First Row - Two Grids
                Grid::make(2)
                    ->schema([
                        // First Grid: Attachments
                        Grid::make(1)
                            ->schema([
                                Section::make('Delivery Details')
                                    ->schema([
                                        FileUpload::make('delivery_files')
                                            ->label('Attachments')
                                            ->multiple()
                                            ->disk('public')
                                            ->directory('order-deliveries')
                                            ->preserveFilenames()
                                            ->downloadable()
                                            ->openable()
                                            ->nullable()
                                            ->columnSpanFull(),
                                        Select::make('status')
                                            ->label('Status')
                                            ->options(OrderDelivery::getStatusOptions())
                                            ->default(OrderDelivery::STATUS_PENDING)
                                            ->required()
                                            ->live()
                                            ->columnSpanFull(),
                                    ]),

                            ])->columnSpan(2),

                    ]),

                // Second Row - Delivery Note (Full Width)
                Grid::make(1)
                    ->schema([
                        Section::make('Delivery Note')
                            ->schema([
                                Textarea::make('delivery_note')
                                    ->label('')
                                    ->rows(6)
                                    ->maxLength(2000)
                                    ->placeholder('Enter delivery notes...')
                                    ->columnSpanFull(),
                            ]),

                    ]),
            ]);
    }
}
