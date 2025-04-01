<?php

namespace Bishopm\Methodist\Filament\Clusters\Structures\Resources;

use Bishopm\Methodist\Filament\Clusters\Structures;
use Bishopm\Methodist\Filament\Clusters\Structures\Resources\SocietyResource\Pages;
use Bishopm\Methodist\Filament\Clusters\Structures\Resources\SocietyResource\RelationManagers;
use Bishopm\Methodist\Models\Society;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SocietyResource extends Resource
{
    protected static ?string $model = Society::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Structures::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('society')
                    ->required()
                    ->maxLength(199),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(199),
                Forms\Components\Select::make('circuit_id')
                    ->relationship('circuit', 'id')
                    ->required(),
                Forms\Components\TextInput::make('address')
                    ->maxLength(199),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(199),
                Forms\Components\TextInput::make('website')
                    ->maxLength(199),
                Map::make('location')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('society')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('circuit.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('website')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListSocieties::route('/'),
            'create' => Pages\CreateSociety::route('/create'),
            'edit' => Pages\EditSociety::route('/{record}/edit'),
        ];
    }
}
