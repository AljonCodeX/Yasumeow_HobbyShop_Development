<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
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
