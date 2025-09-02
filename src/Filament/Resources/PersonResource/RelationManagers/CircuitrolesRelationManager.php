<?php

namespace Bishopm\Methodist\Filament\Resources\PersonResource\RelationManagers;

use Bishopm\Methodist\Models\Circuit;
use Bishopm\Methodist\Models\Circuitrole;
use Bishopm\Methodist\Models\Person;
use Bishopm\Methodist\Models\Society;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
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
                    ->options(function (RelationManager $livewire){
                        $person = $livewire->getOwnerRecord();
                        if ($person->minister){
                            $options=[
                                'Guest' => 'Guest preacher',
                                'Minister' => 'Circuit minister',
                                'Superintendent' => 'Superintendent minister',
                                'Supernumerary' => 'Supernumerary minister'
                            ];
                        } elseif ($person->preacher){
                            $options=array_combine(setting('general.leadership_roles'),setting('general.leadership_roles'));
                            $options['Guest'] = 'Guest preacher';
                            $options['Preacher'] = 'Local preacher';
                        } else {
                            $options=array_combine(setting('general.leadership_roles'),setting('general.leadership_roles'));
                        }
                        return $options;
                    })
                    ->multiple()
                    ->statePath('status'),
                Forms\Components\Select::make('societies')->label('Societies')
                    ->options(function (Get $get){
                        $circuit=$get('circuit_id');
                        $options = Society::where('circuit_id',$circuit)->orderBy('society')->get()->pluck('society','id');
                        return $options;
                    })
                    ->multiple()
                    ->statePath('societies'),
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
                            ->options(Circuit::orderBy('reference')->get()->pluck('circuitname', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('status')->label('Status in this circuit')
                            ->options(function (RelationManager $livewire){
                                $person = $livewire->getOwnerRecord();
                                if ($person->minister){
                                    $options=[
                                        'Guest' => 'Guest preacher',
                                        'Minister' => 'Circuit minister',
                                        'Superintendent' => 'Superintendent minister',
                                        'Supernumerary' => 'Supernumerary minister'
                                    ];
                                } elseif ($person->preacher){
                                    $options=array_combine(setting('general.leadership_roles'),setting('general.leadership_roles'));
                                    $options['Guest']= 'Guest preacher';
                                    $options['Preacher']='Local preacher';
                                } else {
                                    $options=array_combine(setting('general.leadership_roles'),setting('general.leadership_roles'));
                                }
                                return $options;
                            })
                            ->multiple()
                            ->statePath('status'),
                    ])
                    ->action(function (array $data, RelationManager $livewire): void {
                        $person_id=$livewire->getOwnerRecord()->id;
                        Circuitrole::create([
                            'person_id'=>$person_id,
                            'circuit_id'=>$data['circuit_id'],
                            'status'=>$data['status'],
                            'societies'=>$data['societies']
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
