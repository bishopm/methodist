<?php

namespace Bishopm\Methodist\Filament\Clusters\People\Resources;

use Bishopm\Methodist\Filament\Clusters\People;
use Bishopm\Methodist\Filament\Clusters\People\Resources\PersonResource\Pages;
use Bishopm\Methodist\Models\Circuit;
use Bishopm\Methodist\Models\Leader;
use Bishopm\Methodist\Models\Person;
use Bishopm\Methodist\Models\Society;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Livewire\Livewire;
use Outerweb\FilamentImageLibrary\Filament\Forms\Components\ImageLibraryPicker;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $cluster = People::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Personal details')
                    ->headerActions([
                        Action::make('Add as circuit leader')
                            ->visible(function ($record){
                                return $record and $record->leader ? false : true;
                            })
                            ->form([
                                Select::make('leader_roles')
                                    ->multiple()
                                    ->options([
                                        'Circuit Steward' => 'Circuit Steward',
                                        'Circuit Treasurer' => 'Circuit Treasurer',
                                        'Circuit Secretary' => 'Circuit Secretary'
                                    ]),
                                Select::make('leader_society_id')
                                    ->options(function ($record){
                                        return Society::where('circuit_id',$record->circuit_id)->orderBy('society')->get()->pluck('society', 'id');
                                    })                                        
                                    ->label('Society')
                                    ->searchable()
                                    ->required()
                            ])
                            ->action(function (array $data, Person $record, $livewire): void {
                                $record->leader()->create([
                                    'person_id' => $record->id,
                                    'roles' => $data['leader_roles'],
                                    'society_id' => $data['leader_society_id']
                                ]);
                                $record->save();
                                $record->refresh();
                                $livewire->refreshFormData([
                                    'roles',
                                    'society_id'
                                ]);
                            }),
                        Action::make('Add as preacher')
                            ->visible(function ($record){
                                return $record and $record->preacher ? false : true;
                            })
                            ->form([
                                Select::make('status')
                                    ->live()
                                    ->options([
                                        'note' => 'Preacher on note',
                                        'trial' => 'Preacher on trial',
                                        'preacher' => 'Local preacher',
                                        'emeritus' => 'Emeritus preacher'
                                    ]),
                                Forms\Components\TextInput::make('number')->label('Preacher number (optional)')
                                    ->numeric(),
                                Select::make('society_id')
                                    ->options(function ($record){
                                        return Society::where('circuit_id',$record->circuit_id)->orderBy('society')->get()->pluck('society', 'id');
                                    })                                        
                                    ->label('Society')
                                    ->searchable()
                                    ->required(),
                                Forms\Components\TextInput::make('induction')->label('Year of induction')
                                    ->readonly(function (Get $get){
                                        if (($get('status')=="preacher") or ($get('status')=="emeritus")){
                                            return false;
                                        } else {
                                            return true;
                                        }
                                    })
                            ])
                            ->action(function (array $data, Person $record, $livewire): void {
                                $record->preacher()->create([
                                    'person_id' => $record->id,
                                    'number' => $data['number'],
                                    'status' => $data['status'],
                                    'society_id' => $data['society_id'],
                                    'induction' => $data['induction']
                                ]);
                                $record->save();
                                $record->refresh();
                                $livewire->refreshFormData([
                                    'status',
                                    'number',
                                    'society_id',
                                    'induction'
                                ]);
                            }),
                        Action::make('Add as minister / deacon')
                            ->visible(function ($record){
                                return $record and $record->minister ? false : true;
                            })
                            ->action(function () {
                                // ...
                            }),
                    ])
                    ->schema([
                        Forms\Components\TextInput::make('firstname')->label('First name')
                            ->required()
                            ->maxLength(199),
                        Forms\Components\TextInput::make('surname')
                            ->required()
                            ->maxLength(199),
                        Forms\Components\TextInput::make('title')
                            ->maxLength(199),
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
                    ->visible(function ($record){
                        return $record and $record->preacher ? true : false;
                    })
                    ->headerActions([
                        Action::make('Remove as preacher')
                            ->requiresConfirmation()
                            ->action(function (array $data, Person $record): void {
                                $record->preacher()->delete();
                                $record->refresh();
                            })
                        ->color('danger')
                    ])
                    ->relationship('preacher')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'preacher' => 'Local preacher',
                                'trial' =>'Preacher on trial',
                                'note' => 'Preacher on note',
                                'emeritus' => 'Emeritus preacher'
                            ])
                            ->required(),
                        Forms\Components\Select::make('society_id')
                            ->options(function ($record){
                                return Society::where('circuit_id',$record->society->circuit_id)->orderBy('society')->get()->pluck('society','id');
                            })
                            ->label('Society')
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('number')->label('Preacher number (optional)')
                            ->numeric(),
                        Forms\Components\TextInput::make('induction')->label('Year of induction')
                    ])
                    ->columns(2),
                Section::make('Minister details')
                    ->visible(function ($record){
                        return $record and $record->minister ? true : false;
                    })
                    ->relationship('minister')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'Bishop' => 'Bishop',
                                'Deacon' => 'Deacon',
                                'Minister' => 'Minister',
                                'Supernumerary' => 'Supernumerary Minister'
                            ])
                            ->required(),
                        Forms\Components\Select::make('role')
                            ->options([
                                'Superintendent' => 'Superintendent'
                            ]),    
                        Forms\Components\Toggle::make('active')
                            ->required(),
                    ])
                    ->columns(2),
                Section::make('Leader details')
                    ->headerActions([
                        Action::make('Remove as circuit leader')
                            ->requiresConfirmation()
                            ->action(function (array $data, Person $record): void {
                                $record->leader()->delete();
                                $record->refresh();
                            })
                        ->color('danger')
                    ])
                    ->visible(function ($record){
                        return $record and $record->leader ? true : false;
                    })
                    ->relationship('leader')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->options([
                                'Circuit Secretary' => 'Circuit Secretary',
                                'Circuit Steward' => 'Circuit Steward',
                                'Circuit Treasurer' => 'Circuit Treasurer'
                            ])
                            ->multiple(),
                        Forms\Components\Select::make('society_id')
                            ->options(function ($record){
                                return Society::where('circuit_id',$record->society->circuit_id)->orderBy('society')->get()->pluck('society','id');
                            })
                            ->label('Society')
                            ->searchable()
                            ->required()
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('surname')
                    ->searchable(),
                Tables\Columns\TextColumn::make('firstname')
                    ->label('First name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('circuit.circuit')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('clergy')
                    ->icon(function ($record){
                        if ($record->minister){
                            return 'heroicon-o-x-circle';
                        } else {
                            return 'heroicon-o-check-circle';
                        }
                    })
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
            'index' => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePerson::route('/create'),
            'edit' => Pages\EditPerson::route('/{record}/edit'),
        ];
    }
}
