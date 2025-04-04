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

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

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
                    ->relationship('circuit', 'circuit')
                    ->required(),
                Forms\Components\TextInput::make('address')
                    ->maxLength(199),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(199),
                Forms\Components\TextInput::make('website')
                    ->maxLength(199),
                Forms\Components\Hidden::make('latitude')
                    ->hiddenLabel(),
                Forms\Components\Hidden::make('longitude')
                    ->hiddenLabel(),
                Map::make('location')
                    ->afterStateUpdated(function (Set $set, ?array $state): void {
                        $set('latitude', $state['lat']);
                        $set('longitude', $state['lng']);
                    })
                    ->afterStateHydrated(function ($state, $record, Set $set): void {
                        $set('location', [
                            'lat' => $record->latitude,
                            'lng' => $record->longitude
                        ]);
                    })
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('society')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('circuit.reference')
                    ->label('No.')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('circuit.circuit')
                    ->sortable(),
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
            RelationManagers\ServicesRelationManager::class
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
