<?php

namespace App\Filament\Resources\Results;

use App\Filament\Resources\Results\Pages\EditResult;
use App\Filament\Resources\Results\Pages\ListResults;
use App\Filament\Resources\Results\Schemas\ResultForm;
use App\Filament\Resources\Results\Tables\ResultsTable;
use App\Filament\Resources\Results\RelationManagers\ResultsRelationManager;
use App\Models\Event;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ResultResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $slug = 'results';

    protected static ?string $navigationLabel = 'Results';

    protected static ?string $modelLabel = 'result';

    protected static ?string $pluralModelLabel = 'results';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static string|UnitEnum|null $navigationGroup = 'Events';

    protected static ?int $navigationSort = 3;

    // Only completed events show up here
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status', 'completed')
            ->withCount([
                'registrations as players_count' => fn (Builder $q) => $q->where('status', 'registered'),
                'results',
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return ResultForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ResultsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [ResultsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListResults::route('/'),
            'edit'  => EditResult::route('/{record}/edit'),
        ];
    }
}