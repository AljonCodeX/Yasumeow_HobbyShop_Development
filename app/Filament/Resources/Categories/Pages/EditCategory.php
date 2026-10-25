<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Models\Game;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (DeleteAction $action, Model $record) {
                    if ($record->products()->exists() || Game::where('category_id', $record->id)->exists()) {
                        Notification::make()
                            ->title("Can't delete this category")
                            ->body('It still has products or games. Move or delete them first.')
                            ->danger()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }
}