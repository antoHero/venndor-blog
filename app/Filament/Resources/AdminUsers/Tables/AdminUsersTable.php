<?php

namespace App\Filament\Resources\AdminUsers\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AdminUsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('mobile')
                    ->label('Mobile')
                    ->searchable(),
                TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'admin' ? 'primary' : 'gray'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('mobile_verified_at')
                    ->label('Verified')
                    ->dateTime()
                    ->placeholder('Never signed in')
                    ->sortable(),
            ])
            ->defaultSort('name')
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'editor' => 'Editor',
                        'admin' => 'Admin',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
