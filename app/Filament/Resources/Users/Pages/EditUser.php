<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\Reservation;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Hidden for yourself and for anyone with reservations (keeps sales history)
            DeleteAction::make()
            ->hidden(fn (Model $record) => $record->id === \Illuminate\Support\Facades\Auth::id()
                || Reservation::where('user_id', $record->id)->exists()),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $isAdmin = $data['is_admin'] ?? null;
        unset($data['is_admin']);

        $record->fill($data);

        if ($isAdmin !== null) {
            $record->is_admin = (bool) $isAdmin;
        }

        $record->save();

        return $record;
    }
}