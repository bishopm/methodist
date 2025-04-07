<?php

namespace Bishopm\Methodist\Filament\Clusters\Settings\Resources;

use Bishopm\Methodist\Filament\Clusters\Settings;
use Bishopm\Methodist\Filament\Clusters\Settings\Resources\UserResource\Pages;
use Bishopm\Methodist\Filament\Clusters\Settings\Resources\UserResource\RelationManagers;
use Bishopm\Methodist\Models\Circuit;
use Bishopm\Methodist\Models\District;
use Bishopm\Methodist\Models\Society;
use Bishopm\Methodist\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $cluster = Settings::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('roles')->multiple()->relationship('roles', 'name'),
                Forms\Components\Select::make('districts')->multiple()
                    ->options(District::orderBy('district')->get()->pluck('district', 'id'))
                    ->searchable(),
                Forms\Components\Select::make('circuits')->multiple()
                    ->options(Circuit::orderBy('circuit')->get()->pluck('circuit', 'id'))
                    ->searchable(),
                Forms\Components\Select::make('societies')->multiple()
                    ->options(Society::orderBy('society')->get()->pluck('society', 'id'))
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Impersonate::make()
                    ->redirectTo('/admin'),
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
