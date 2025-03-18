<?php

namespace Bishopm\Methodist\Filament\Clusters\Structures\Resources;

use Bishopm\Methodist\Filament\Clusters\Structures;
use Bishopm\Methodist\Filament\Clusters\Structures\Resources\CircuitResource\Pages;
use Bishopm\Methodist\Filament\Clusters\Structures\Resources\CircuitResource\RelationManagers;
use Bishopm\Methodist\Models\Circuit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CircuitResource extends Resource
{
    protected static ?string $model = Circuit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Structures::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('circuit')
                    ->required()
                    ->maxLength(199),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(199),
                Forms\Components\Select::make('district_id')
                    ->relationship('district', 'id')
                    ->required(),
                Forms\Components\TextInput::make('reference')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('plan_month')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('contact')
                    ->maxLength(199),
                Forms\Components\TextInput::make('showphone')
                    ->tel()
                    ->maxLength(10),
                Forms\Components\TextInput::make('activated')
                    ->maxLength(10),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('circuit')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('district.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('plan_month')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact')
                    ->searchable(),
                Tables\Columns\TextColumn::make('showphone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('activated')
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
            'index' => Pages\ListCircuits::route('/'),
            'create' => Pages\CreateCircuit::route('/create'),
            'edit' => Pages\EditCircuit::route('/{record}/edit'),
        ];
    }
}
