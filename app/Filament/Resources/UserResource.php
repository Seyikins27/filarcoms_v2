<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use SolutionForest\FilamentAccessManagement\Resources\UserResource\RelationManagers\RolesRelationManager;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(strval(__('filament-access-management::filament-access-management.field.user.name')))
                        ->required(),

                    Forms\Components\TextInput::make('email')
                        ->required()
                        ->email()
                        ->unique(table: static::getModel(), ignorable: fn ($record) => $record)
                        ->label(strval(__('filament-access-management::filament-access-management.field.user.email'))),

                    Forms\Components\TextInput::make('password')
                        ->same('passwordConfirmation')
                        ->password()
                        ->maxLength(255)
                        ->required(fn ($component, $get, $livewire, $model, $record, $set, $state) => $record === null)
                        ->dehydrateStateUsing(fn ($state) => ! empty($state) ? Hash::make($state) : '')
                        ->label(strval(__('filament-access-management::filament-access-management.field.user.password'))),

                    Forms\Components\TextInput::make('passwordConfirmation')
                        ->password()
                        ->dehydrated(false)
                        ->maxLength(255)
                        ->label(strval(__('filament-access-management::filament-access-management.field.user.confirm_password'))),

                    Forms\Components\Select::make('roles')
                        ->multiple()
                        ->relationship('roles', 'name')
                        ->preload()
                        ->label(strval(__('filament-access-management::filament-access-management.field.user.roles'))),
                    Forms\Components\Toggle::make('can_publish')
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->label(strval(__('filament-access-management::filament-access-management.field.id'))),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label(strval(__('filament-access-management::filament-access-management.field.user.name'))),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->label(strval(__('filament-access-management::filament-access-management.field.user.email'))),

                Tables\Columns\IconColumn::make('email_verified_at')
                    ->options([
                        'heroicon-o-check-circle',
                        'heroicon-o-x-circle' => fn ($state): bool => $state === null,
                    ])
                    ->colors([
                        'success',
                        'danger' => fn ($state): bool => $state === null,
                    ])
                    ->label(strval(__('filament-access-management::filament-access-management.field.user.verified_at'))),

                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->label(strval(__('filament-access-management::filament-access-management.field.user.roles'))),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('Y-m-d H:i:s')
                    ->label(strval(__('filament-access-management::filament-access-management.field.user.created_at'))),
                Tables\Columns\ToggleColumn::make('can_publish')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
