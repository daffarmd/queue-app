<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Service Name'),

                TextInput::make('code')
                    ->required()
                    ->maxLength(3)
                    ->extraInputAttributes(['style' => 'text-transform: uppercase'])
                    ->label('Service Code')
                    ->helperText('3-letter code (e.g., GEN, PHR, LAB)'),

                Textarea::make('description')
                    ->nullable()
                    ->rows(3)
                    ->label('Description'),
            ]);
    }
}
