<?php

namespace App\Filament\Resources\OrderCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class OrderCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Basic Information Section
                Fieldset::make('Category Information')
                    ->label('Category Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Category Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter category name'),
                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->maxLength(255)
                                    ->placeholder('auto-generated-from-name')
                                    ->unique(ignoreRecord: true),
                            ])->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Enter category description...')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),

                // Status Section
                Fieldset::make('Status')
                    ->label('Status')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
