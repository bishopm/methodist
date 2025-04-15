<?php

namespace Bishopm\Methodist\Filament\Resources\CircuitResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PersonsRelationManager extends RelationManager
{
    protected static string $relationship = 'persons';

    public $record;

    protected static ?string $title = 'People';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('surname')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('surname')
            ->columns([
                Tables\Columns\TextColumn::make('surname'),
                Tables\Columns\TextColumn::make('firstname')->label('First name'),
                Tables\Columns\TextColumn::make('status')
            ])
            ->filters([
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
