<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (DeleteAction $action, Model $record) {
                    if ($record->reservationItems()->exists()) {
                        Notification::make()
                            ->title("Can't delete this product")
                            ->body('It is part of existing reservations. Set its stock to 0 instead.')
                            ->danger()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }
}