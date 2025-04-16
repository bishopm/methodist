<?php

namespace Bishopm\Methodist\Filament\Resources\CircuitResource\RelationManagers;

use Bishopm\Methodist\Models\Circuit;
use Bishopm\Methodist\Models\Person;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class PersonsRelationManager extends RelationManager
{
    protected static string $relationship = 'persons';

    public $record;

    protected static ?string $title = 'People';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')->label('Which best describes the person you are adding?')
                    ->live()
                    ->options([
                        'circuitminister'=>'Circuit minister',
                        'guest'=>'Guest preacher',
                        'preacher'=>'Local preacher',
                        'leader'=>'Lay leader'
                    ]),
                Forms\Components\Select::make('person_id')->label('Select from existing names')
                    ->hidden(function (Get $get) {
                        if (($get('status')=='circuitminister') or ($get('status')=='guest')){
                            return false;
                        } else {
                            return true;
                        }
                    })
                    ->searchable()
                    ->options(function (){
                        $persons=DB::table('persons')->select('id','surname','firstname')->orderBy('surname')->orderBy('firstname')->get();
                        $options=array();
                        foreach ($persons as $person){
                            $options[$person->id] = $person->surname . ", " . $person->firstname;
                        }
                        return $options;
                    }),
                Forms\Components\Section::make('Add a new person')
                    ->hidden(function (Get $get) {
                        if (($get('status')=='circuitminister') or ($get('status')=='')){
                            return true;
                        } else {
                            return false;
                        }
                    })
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('surname'),
                        Forms\Components\TextInput::make('firstname'),
                        Forms\Components\Select::make('title')
                            ->options([
                                'Dr'=>'Dr',
                                'Mr'=>'Mr',
                                'Mrs'=>'Mrs',
                                'Ms'=>'Ms',
                                'Prof'=>'Prof',
                                'Rev'=>'Rev'
                            ]),
                        Forms\Components\TextInput::make('phone')
                            ->afterStateHydrated(function (Forms\Components\TextInput $component, $state) {
                                $component->state(str_replace(" ","",$state));
                            })
                            ->maxLength(199),
                        ]),
                    Forms\Components\Hidden::make('circuit_id')
                        ->default(function ($livewire){
                            return $livewire->getOwnerRecord()->id;
                        })
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('surname')
            ->columns([
                Tables\Columns\TextColumn::make('surname')->searchable(),
                Tables\Columns\TextColumn::make('firstname')->label('First name'),
                Tables\Columns\TextColumn::make('status')
            ])
            ->filters([
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data) {
                        if ($data['status']=="circuitminister"){
                            dd("changing minister circuit to " . $data['circuit_id']);
                        } elseif ($data['status']=="preacher"){
                            dd('Creating person and designating as preacher');
                        } elseif ($data['status']=="guest"){
                            if (!$data['person_id']){
                                $person=Person::create([
                                    'surname' => $data['surname'],
                                    'firstname' => $data['firstname'],
                                    'title' => $data['title'],
                                    'phone' => $data['phone']
                                ]);
                            } else {
                                $person=Person::find($data['person_id']);
                            }
                            if (!$person->guestcircuits){
                                $person->guestcircuits=$data['circuit_id'];
                                $person->save();
                            } elseif (!in_array($data['circuit_id'],$person->guestcircuits)){
                                $gc=json_decode($person->guestcircuits);
                                $gc[]=$data['circuit_id'];
                                $person->guestcircuits=$gc;
                                $person->save();
                            }
                            return $person;
                        } elseif ($data['status']=="preacher"){
                            dd('Creating person and adding leader status');
                        }
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
