<?php

namespace App\Filament\Resources\Reservations\Tables;

use App\Models\Reservation;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReservationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('items.product'))
            ->columns([
                TextColumn::make('id')->label('#')->sortable()->searchable(),
                TextColumn::make('contact_name')->label('Customer')->searchable(),
                TextColumn::make('contact_phone')->label('Phone')->searchable(),
                TextColumn::make('items_list')
                    ->label('Items')
                    ->state(fn (Reservation $r) => $r->items
                        ->map(fn ($i) => ($i->product?->name ?? 'Product') . ' × ' . $i->quantity)
                        ->implode(', '))
                    ->wrap(),
                TextColumn::make('amount_total')->label('Total')->money('PHP')->sortable(),
                TextColumn::make('payment_method')
                    ->label('Payment')
                    ->formatStateUsing(fn ($state) => $state === 'online' ? 'QR Ph' : 'In store'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending', 'picked_up' => 'success',
                        'ready'                => 'info',
                        'awaiting_payment'     => 'warning',
                        'cancelled', 'expired' => 'danger',
                        default                => 'gray',
                    }),
                TextColumn::make('pickup_by')->label('Pick up by')->dateTime('M d, g:i A')->sortable(),
                TextColumn::make('reserved_at')->label('Reserved')->dateTime('M d, g:i A')->sortable(),
            ])
            ->defaultSort('reserved_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options([
                    'awaiting_payment' => 'Awaiting payment',
                    'pending'          => 'Reserved',
                    'ready'            => 'Ready for pickup',
                    'picked_up'        => 'Picked up',
                    'cancelled'        => 'Cancelled',
                    'expired'          => 'Expired',
                ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('ready')
                        ->label('Ready')
                        ->icon('heroicon-o-archive-box')
                        ->color('info')
                        ->visible(fn (Reservation $r) => $r->status === 'pending')
                        ->action(fn (Reservation $r) => $r->update(['status' => 'ready'])),

                    Action::make('picked_up')
                        ->label('Picked up')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalDescription('Use this after the customer has taken the items and paid.')
                        ->visible(fn (Reservation $r) => in_array($r->status, ['pending', 'ready'], true))
                        ->action(fn (Reservation $r) => $r->update([
                            'status'      => 'picked_up',
                            'amount_paid' => $r->amount_total,
                        ])),

                    Action::make('cancel')
                        ->label('Cancel')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalDescription('The items go back to stock.')
                        ->visible(fn (Reservation $r) => in_array($r->status, ['awaiting_payment', 'pending', 'ready'], true))
                        ->action(fn (Reservation $r) => $r->staffCancel()),

                    EditAction::make()->label('View'),
                ])
                    ->label('Actions')
                    ->icon('heroicon-m-chevron-down')
                    ->iconPosition('after')
                    ->button()
                    ->color('gray'),
            ]);
    }
}