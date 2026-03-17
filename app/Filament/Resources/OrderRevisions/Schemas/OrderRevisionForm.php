<?php

namespace App\Filament\Resources\OrderRevisions\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class OrderRevisionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Hidden order_id field
                Hidden::make('order_id'),

                // First Row - Revision Files (Full Width)
                Grid::make(1)
                    ->schema([
                        FileUpload::make('revision_files')
                            ->label('Revision Files')
                            ->multiple()
                            ->disk('public')
                            ->directory('order-revisions')
                            ->preserveFilenames()
                            ->downloadable()
                            ->openable()
                            ->nullable()
                            ->placeholder('Upload revision files'),
                    ]),

                // Second Row - Revision Note (Full Width)
                Grid::make(1)
                    ->schema([
                        Textarea::make('revision_note')
                            ->label('Revision Note')
                            ->rows(6)
                            ->maxLength(2000)
                            ->placeholder('Describe the revision changes or notes...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
