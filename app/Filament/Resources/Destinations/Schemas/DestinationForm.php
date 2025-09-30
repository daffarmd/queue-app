<?php

namespace App\Filament\Resources\Destinations\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DestinationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Destination Name')
                    ->placeholder('Enter destination name'),

                TextInput::make('code')
                    ->required()
                    ->maxLength(10)
                    ->unique(ignoreRecord: true)
                    ->label('Destination Code')
                    ->placeholder('Enter destination code (e.g., ICU, ER)')
                    ->helperText('Short code used for queue identification'),

                Textarea::make('description')
                    ->nullable()
                    ->maxLength(500)
                    ->rows(3)
                    ->label('Description')
                    ->placeholder('Enter optional description'),
            ]);
    }
}
