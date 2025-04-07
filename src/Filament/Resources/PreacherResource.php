<?php

namespace Bishopm\Methodist\Filament\Resources;

use Bishopm\Methodist\Filament\Resources\PreacherResource\Pages;
use Bishopm\Methodist\Models\Circuit;
use Bishopm\Methodist\Models\Preacher;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PreacherResource extends Resource
{
    protected static ?string $model = Preacher::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Personal details')
                    ->relationship('person')
                    ->schema([
                        Forms\Components\TextInput::make('firstname')
                            ->label('First name')
                            ->required()
                            ->maxLength(199),
                        Forms\Components\TextInput::make('surname')
                            ->required()
                            ->maxLength(199),
                        Forms\Components\Select::make('title')
                            ->options([
                                'Dr' => 'Dr',
                                'Mr' => 'Mr',
                                'Mrs' => 'Mrs',
                                'Ms' =>'Ms',
                                'Rev' => 'Rev',
                                'Prof' => 'Prof'
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(199),
                        Forms\Components\Select::make('circuit_id')
                            ->label('Circuit')
                            ->options(Circuit::orderBy('circuit')->get()->pluck('circuit', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->columns(2),
                Section::make('Preacher details')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'preacher' => 'Local preacher',
                                'trial' =>'Preacher on trial',
                                'note' => 'Preacher on note',
                                'emeritus' => 'Emeritus preacher'
                            ])
                            ->required(),
                        Forms\Components\Select::make('role')
                            ->options([
                                'sos' => 'Supervisor of Studies',
                                'secretary' => 'Secretary'
                            ]),    
                        Forms\Components\Toggle::make('active')
                            ->required(),
                    ])
                    ->columns(2),
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
                Tables\Columns\TextColumn::make('person.circuit.circuit')
                    ->searchable(),
                Tables\Columns\TextColumn::make('society.society')
                    ->sortable(),
                Tables\Columns\IconColumn::make('active')
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-o-x-circle',
                        '1' => 'heroicon-o-check-circle'
                    }),
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
            'index' => Pages\ListPreachers::route('/'),
            'create' => Pages\CreatePreacher::route('/create'),
            'edit' => Pages\EditPreacher::route('/{record}/edit'),
        ];
    }
}
