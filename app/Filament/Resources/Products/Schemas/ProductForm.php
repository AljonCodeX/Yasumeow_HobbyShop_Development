<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->prefix('₱'),
                TextInput::make('stock_qty')
                    ->label('Stock')
                    ->required()
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->default(0)
                    ->disabled(fn (string $operation) => $operation === 'edit')
                    ->dehydrated(fn (string $operation) => $operation !== 'edit')
                    ->helperText(fn (string $operation) => $operation === 'edit'
                        ? 'To add stock, use Restock in Reports > Inventory.'
                        : null),
                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('products')
                    ->columnSpanFull(),
                Toggle::make('is_featured')
                    ->label('Featured')
                    ->default(false),
            ]);
    }
}