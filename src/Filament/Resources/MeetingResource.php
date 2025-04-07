<?php

namespace Bishopm\Methodist\Filament\Resources;

use Bishopm\Methodist\Filament\Resources\MeetingResource\Pages;
use Bishopm\Methodist\Filament\Resources\MeetingResource\RelationManagers;
use Bishopm\Methodist\Models\Meeting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MeetingResource extends Resource
{
    protected static ?string $model = Meeting::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('circuit_id')
                    ->relationship('circuit', 'id')
                    ->required(),
                Forms\Components\DateTimePicker::make('meetingdate'),
                Forms\Components\TextInput::make('society_id')
                    ->numeric(),
                Forms\Components\TextInput::make('description')
                    ->maxLength(199),
                Forms\Components\TextInput::make('quarter')
                    ->maxLength(199),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('circuit.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('meetingdate')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('society_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quarter')
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
            'index' => Pages\ListMeetings::route('/'),
            'create' => Pages\CreateMeeting::route('/create'),
            'edit' => Pages\EditMeeting::route('/{record}/edit'),
        ];
    }
}
