<?php

namespace Bishopm\Methodist\Filament\Clusters\Settings\Resources;

use Bishopm\Methodist\Filament\Clusters\Settings;
use Bishopm\Methodist\Filament\Clusters\Settings\Resources\LectionResource\Pages;
use Bishopm\Methodist\Filament\Clusters\Settings\Resources\LectionResource\RelationManagers;
use Bishopm\Methodist\Models\Lection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LectionResource extends Resource
{
    protected static ?string $model = Lection::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Settings::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('year')
                    ->maxLength(191),
                Forms\Components\TextInput::make('lection')
                    ->label('Description')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('ot')
                    ->label('Old Testament')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('psalm')
                    ->label('Psalm')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('nt')
                    ->label('New Testament')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('gospel')
                    ->label('Gospel')
                    ->required()
                    ->maxLength(191),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lection')
                    ->label('Description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ot')
                    ->label('Old Testament')
                    ->searchable(),
                Tables\Columns\TextColumn::make('psalm')
                    ->label('Psalm')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nt')
                    ->label('New Testament')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gospel')
                    ->label('Gospel')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('year')->label('Year')
                ->options([
                    'A' => 'Year A',
                    'B' => 'Year B',
                    'C' => 'Year C'
                ])
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
            'index' => Pages\ListLections::route('/'),
            'create' => Pages\CreateLection::route('/create'),
            'edit' => Pages\EditLection::route('/{record}/edit'),
        ];
    }
}
