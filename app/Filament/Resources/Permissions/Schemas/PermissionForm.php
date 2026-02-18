<?php

namespace App\Filament\Resources\Permissions\Schemas;

use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PermissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Permission Details')
                    ->label('Permission Details')
                    ->schema([
                        TextInput::make('name')
                            ->label('Permission Name')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g., edit users'),
                        TextInput::make('guard_name')
                            ->label('Guard Name')
                            ->default('web')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('web'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
