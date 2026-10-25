<?php

namespace App\Filament\Resources\Games\Pages;

use App\Filament\Resources\Games\GameResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditGame extends EditRecord
{
    protected static string $resource = GameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (DeleteAction $action, Model $record) {
                    if ($record->events()->exists()) {
                        Notification::make()
                            ->title("Can't delete this game")
                            ->body('It has events. Turn off "Show on website" instead.')
                            ->danger()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }
}