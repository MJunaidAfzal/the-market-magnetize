<?php

namespace App\Filament\Resources\LeadSources\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class LeadSourceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Basic Information')
                    ->label('Basic Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Source Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Facebook, Website, Google Ads')
                            ->columnSpanFull()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (empty($state)) {
                                    return;
                                }
                                $set('slug', \Str::slug($state));
                            }),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g., facebook, website, google-ads')
                            ->columnSpanFull(),
                    ]),
                Fieldset::make('Additional Details')
                    ->label('Additional Details')
                    ->schema([
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('Describe this lead source...')
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
