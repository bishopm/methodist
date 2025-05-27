<?php

namespace Bishopm\Methodist\Filament\Resources\PersonResource\RelationManagers;

use Bishopm\Methodist\Models\Circuit;
use Bishopm\Methodist\Models\Circuitrole;
use Bishopm\Methodist\Models\Person;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Livewire\Livewire;

class CircuitrolesRelationManager extends RelationManager
{
    protected static string $relationship = 'circuitroles';

    protected static ?string $title = 'Circuit roles';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('circuit_id')->label('Circuit')
                    ->options(Circuit::orderBy('circuit')->get()->pluck('circuit', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('status')->label('Status in this circuit')
                    ->multiple()
                    ->options([
                        'Guest' => 'Guest preacher',
                        'Leader' => 'Leader',
                        'Minister' => 'Circuit minister',
                        'Preacher' => 'Local preacher',
                        'Superintendent' => 'Superintendent minister',
                        'Supernumerary' => 'Supernumerary minister',
                    ])
                    ->statePath('status')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('circuit')
            ->columns([
                Tables\Columns\TextColumn::make('circuit.circuit'),
                Tables\Columns\TextColumn::make('status'),
            ])
            ->inverseRelationship('circuitrole')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('Add to a circuit')
                    ->form(fn (): array => [
                        Forms\Components\Select::make('circuit_id')->label('Circuit')
                            ->options(Circuit::orderBy('circuit')->get()->pluck('circuit', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('status')->label('Status in this circuit')
                            ->options([
                                'Guest' => 'Guest preacher',
                                'Leader' => 'Leader',
                                'Minister' => 'Circuit minister',
                                'Preacher' => 'Local preacher',
                                'Superintendent' => 'Superintendent minister',
                                'Supernumerary' => 'Supernumerary minister',
                            ])
                            ->multiple()
                            ->statePath('status'),
                    ])
                    ->action(function (array $data, RelationManager $livewire): void {
                        $person_id=$livewire->getOwnerRecord()->id;
                        Circuitrole::create([
                            'person_id'=>$person_id,
                            'circuit_id'=>$data['circuit_id'],
                            'status'=>$data['status']
                        ]);
                    })
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
