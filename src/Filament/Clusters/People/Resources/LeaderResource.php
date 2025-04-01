<?php

namespace Bishopm\Methodist\Filament\Clusters\People\Resources;

use Bishopm\Methodist\Filament\Clusters\People;
use Bishopm\Methodist\Filament\Clusters\People\Resources\LeaderResource\Pages;
use Bishopm\Methodist\Models\Leader;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeaderResource extends Resource
{
    protected static ?string $model = Leader::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = People::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('person_id')
                    ->relationship('person', 'title')
                    ->required(),
                Forms\Components\TextInput::make('roles'),
                Forms\Components\Select::make('society_id')
                    ->relationship('society', 'id'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('person.firstname')
                    ->sortable(),
                Tables\Columns\TextColumn::make('person.surname')
                    ->sortable(),
                Tables\Columns\TextColumn::make('society.society')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('person.circuit.circuit')
                    ->searchable(),
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
            'index' => Pages\ListLeaders::route('/'),
            'create' => Pages\CreateLeader::route('/create'),
            'edit' => Pages\EditLeader::route('/{record}/edit'),
        ];
    }
}
