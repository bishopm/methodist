<?php

namespace Bishopm\Methodist\Filament\Clusters\People\Resources;

use Bishopm\Methodist\Filament\Clusters\People;
use Bishopm\Methodist\Filament\Clusters\People\Resources\MinisterResource\Pages;
use Bishopm\Methodist\Models\Minister;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MinisterResource extends Resource
{
    protected static ?string $model = Minister::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = People::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('firstname')
                    ->required()
                    ->maxLength(199),
                Forms\Components\TextInput::make('surname')
                    ->required()
                    ->maxLength(199),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(199),
                Forms\Components\TextInput::make('title')
                    ->maxLength(199),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(199),
                Forms\Components\TextInput::make('circuit_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('active')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('role')
                    ->maxLength(199),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('person.firstname')->label('First name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('person.surname')->label('Surname')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('person.circuit.circuit')
                    ->sortable(),
                Tables\Columns\IconColumn::make('active')
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-o-x-circle',
                        '1' => 'heroicon-o-check-circle'
                    }),
                Tables\Columns\TextColumn::make('role')
                    ->searchable()
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMinisters::route('/'),
            'create' => Pages\CreateMinister::route('/create'),
            'edit' => Pages\EditMinister::route('/{record}/edit'),
        ];
    }
}
