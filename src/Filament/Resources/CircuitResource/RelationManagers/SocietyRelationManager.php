<?php

namespace Bishopm\Methodist\Filament\Resources\CircuitResource\RelationManagers;

use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class SocietyRelationManager extends RelationManager
{
    protected static string $relationship = 'societies';

    public $disabled;

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('society')
                ->required()
                ->maxLength(199)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
            Forms\Components\TextInput::make('slug')
                ->required()
                ->maxLength(199),
            Forms\Components\Select::make('circuit_id')
                ->relationship('circuit', 'circuit')
                ->searchable()
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
                    if ($record){
                        $set('location', [
                            'lat' => $record->latitude,
                            'lng' => $record->longitude
                        ]);
                    }
                })
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('society')
            ->columns([
                Tables\Columns\TextColumn::make('society'),
                Tables\Columns\TextColumn::make('services.servicetime')
            ])
            ->defaultSort('society','asc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->hidden(false),
                Tables\Actions\EditAction::make()
                    ->hidden(function ($record) {
                        $user=Auth::user();
                        if (!$user->hasRole('Super Admin')){
                            if (($user->circuits) and (in_array($record->circuit_id,$user->circuits))) {
                                return false;
                            } else if (($user->societies) and (in_array($record->id,$user->societies))){
                                return false;
                            } else {
                                return true;
                            }
                        } else {
                            return false;
                        }
                    }),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
