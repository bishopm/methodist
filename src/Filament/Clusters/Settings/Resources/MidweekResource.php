<?php

namespace Bishopm\Methodist\Filament\Clusters\Settings\Resources;

use Bishopm\Methodist\Filament\Clusters\Settings;
use Bishopm\Methodist\Filament\Clusters\Settings\Resources\MidweekResource\Pages;
use Bishopm\Methodist\Filament\Clusters\Settings\Resources\MidweekResource\RelationManagers;
use Bishopm\Methodist\Models\Midweek;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MidweekResource extends Resource
{
    protected static ?string $model = Midweek::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'midweek service';  

    protected static ?string $cluster = Settings::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('midweek')->label('Service name')
                    ->required()
                    ->maxLength(199),
                Forms\Components\DatePicker::make('servicedate')->label('Date')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('midweek')->label('Service name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('servicedate')->label('Date')
                    ->date()
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMidweeks::route('/'),
            'create' => Pages\CreateMidweek::route('/create'),
            'edit' => Pages\EditMidweek::route('/{record}/edit'),
        ];
    }
}
