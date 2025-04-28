<?php

namespace Bishopm\Methodist\Filament\Resources\CircuitResource\RelationManagers;

use Bishopm\Methodist\Models\Circuitrole;
use Bishopm\Methodist\Models\Person;
use Bishopm\Methodist\Models\Society;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class PersonsRelationManager extends RelationManager
{
    protected static string $relationship = 'persons';

    public $record;

    protected static ?string $title = 'People';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('firstname')->label('First name'),
                        Forms\Components\TextInput::make('surname'),
                        Forms\Components\Select::make('title')
                            ->options([
                                'Dr'=>'Dr',
                                'Mr'=>'Mr',
                                'Mrs'=>'Mrs',
                                'Ms'=>'Ms',
                                'Prof'=>'Prof',
                                'Rev'=>'Rev'
                            ]),
                        Forms\Components\TextInput::make('phone'),
                        Forms\Components\Select::make('status')->label('Status in this circuit')
                            ->formatStateUsing(function ($state){
                                if ($state){
                                    return json_decode($state);
                                }
                            })
                            ->live()
                            ->multiple()
                            ->statePath('status')
                            ->options([
                                'Guest' => 'Guest preacher',
                                'Leader' => 'Leader',
                                'Minister' => 'Circuit minister',
                                'Preacher' => 'Local preacher',
                                'Supernumerary' => 'Supernumerary minister',
                            ]),
                        Forms\Components\Select::make('leadership')->label('Leadership roles')
                            ->visible(function (Get $get){
                                $status=$get('status');
                                if ($status==null){
                                    $status=[];
                                }
                                if ((in_array('Minister',$status)) or (in_array('Supernumerary',$status))){
                                    return false;
                                } else {
                                    return true;
                                }
                            })
                            ->multiple()
                            ->options(setting('general.leadership_roles')),
                        Forms\Components\Select::make('society_id')->label('Society')
                            ->options(function ($livewire){
                                return Society::where('circuit_id',$livewire->getOwnerRecord()->id)->orderBy('society')->get()->pluck('society','id');
                            })
                            ->visible(function (Get $get){
                                $status=$get('status');
                                if ($status==null){
                                    $status=[];
                                }
                                if ((in_array('Minister',$status)) or (in_array('Supernumerary',$status))){
                                    return false;
                                } else {
                                    return true;
                                }
                            })
                    ]),
                Forms\Components\Section::make('Clergy')->relationship('minister')->columns(2)
                    ->hiddenOn('create')
                    ->visible(function ($record){
                        if ($record->minister){
                            return true;
                        } else {
                            return false;
                        }
                    })
                    ->schema([
                        Forms\Components\Select::make('leadership')->label('Leadership roles')
                            ->multiple()
                            ->options(setting('general.minister_leadership_roles')),
                        Forms\Components\Toggle::make('active')
                            ->onColor('success'),
                    ]),
                Forms\Components\Section::make('Preacher')->relationship('preacher')
                    ->description('This section relates only to preachers')
                    ->hiddenOn('create')
                    ->columns(2)
                    ->visible(function ($record){
                        if ($record->preacher){
                            return true;
                        } else {
                            return false;
                        }
                    })
                    ->schema([
                        Forms\Components\Select::make('leadership')->label('Leadership roles')
                            ->multiple()
                            ->options(array_combine(setting('general.preacher_leadership_roles'),setting('general.preacher_leadership_roles'))),
                        Forms\Components\Select::make('status')
                            ->live()
                            ->options([
                                'note' => 'Preacher on note',
                                'trial' => 'Preacher on trial',
                                'preacher' => 'Local preacher',
                                'emeritus' => 'Emeritus preacher'
                            ]),
                        Forms\Components\TextInput::make('number')->label('Preacher number (optional)')
                            ->numeric(),
                        Forms\Components\TextInput::make('induction')->label('Year of induction')
                            ->readonly(function (Get $get){
                                if (($get('status')=="preacher") or ($get('status')=="emeritus")){
                                    return false;
                                } else {
                                    return true;
                                }
                            }),
                        Forms\Components\Toggle::make('active')
                            ->onColor('success'),
                        ]),
                Forms\Components\Placeholder::make('circuitroles')->label('Roles in other circuits')
                    ->hiddenOn('create')
                    ->visible(function ($record){
                        if (count($record->circuitroles)>1){
                            return true;
                        } else {
                            return false;
                        }
                    })
                    ->content(function ($record, RelationManager $livewire){
                        $dat="";
                        $circuit = $livewire->getOwnerRecord()->id;
                        foreach ($record->circuitroles as $role){
                            if ($role->circuit_id <> $circuit){
                                $dat.=$role->circuit->reference . " " . $role->circuit->circuit . " (" . implode(", ",$role->status) . ")<br>";
                            }
                        }
                        return new HtmlString($dat);
                    }),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('surname')
            ->recordTitleAttribute('surname')
            ->columns([
                Tables\Columns\TextColumn::make('surname')->searchable(),
                Tables\Columns\TextColumn::make('firstname')->label('First name')->searchable(),
                Tables\Columns\TextColumn::make('status')->searchable()
                ->formatStateUsing(function ($state){
                    return implode(', ',json_decode($state));
                })
            ])
            ->filters([
            ])
            ->headerActions([
                Tables\Actions\Action::make('transfer')->label('Transfer a minister or add as guest')
                    ->form([
                        Forms\Components\Grid::make('transfergrid')
                            ->schema([
                                Forms\Components\Select::make('person_id')->label('Existing names')
                                    ->live()
                                    ->options(function ($livewire){
                                        $circuitid=$livewire->getOwnerRecord()->id;
                                        $persons = Person::whereHas('circuits', function ($q) use ($circuitid) { $q->where('circuit_id','<>',$circuitid); })->orderBy('surname')->orderBy('firstname')->get();
                                        foreach ($persons as $person){
                                            $options[$person->id]=$person->surname . ", " . $person->firstname;
                                        }
                                        return $options;
                                    })
                                    ->searchable(),
                                Forms\Components\Select::make('status')->label('Status in this circuit')
                                    ->formatStateUsing(function ($state){
                                        if ($state){
                                            return json_decode($state);
                                        }
                                    })
                                    ->live()
                                    ->multiple()
                                    ->statePath('status')
                                    ->options([
                                        'Guest' => 'Guest preacher',
                                        'Leader' => 'Leader',
                                        'Minister' => 'Circuit minister',
                                        'Preacher' => 'Local preacher',
                                        'Supernumerary' => 'Supernumerary minister',
                                    ])
                            ])->columns(2)
                    ])
                    ->action(function (array $data, RelationManager $livewire){
                        $circuit_id=$livewire->getOwnerRecord()->id;
                        Circuitrole::create([
                            'person_id'=>$data['person_id'],
                            'circuit_id'=>$circuit_id,
                            'status'=>$data['status']
                        ]);
                    }),
                    Tables\Actions\CreateAction::make()->label('Add a new person')
                        ->using(function (array $data, RelationManager $livewire){
                            $circuit_id=$livewire->getOwnerRecord()->id;
                            $person=Person::create([
                                'firstname' => $data['firstname'],
                                'surname' => $data['surname'],
                                'title' => $data['title'],
                                'phone' => $data['phone'],
                                'leadership' => $data['leadership'],
                                'society_id' => $data['society_id']
                            ]);
                            $circuitrole=Circuitrole::create([
                                'person_id'=>$person->id,
                                'circuit_id'=>$circuit_id,
                                'status'=>$data['status']
                            ]);
                            return $person;
                        })
                ])
            ->actions([
                Tables\Actions\EditAction::make()->modalHeading('Edit person'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
