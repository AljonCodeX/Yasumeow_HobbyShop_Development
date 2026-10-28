<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (DeleteAction $action, Model $record) {
                    $hasPlayers = $record->status !== 'cancelled'
                        && $record->registrations()->where('status', 'registered')->exists();

                    if ($hasPlayers || $record->results()->exists()) {
                        Notification::make()
                            ->title("Can't delete this event")
                            ->body('It has registered players or results. Set the event to Cancelled first, then delete.')
                            ->danger()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }
}