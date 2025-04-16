<?php

namespace Bishopm\Methodist\Filament\Resources;

use Bishopm\Methodist\Filament\Resources\PersonResource\Pages;
use Bishopm\Methodist\Models\Circuit;
use Bishopm\Methodist\Models\Leader;
use Bishopm\Methodist\Models\Minister;
use Bishopm\Methodist\Models\Person;
use Bishopm\Methodist\Models\Preacher;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Personal details')
                    ->headerActions([
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
                    ])
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->hiddenOn('edit')
                            ->options([
                                'Clergy' => [
                                    'deacon' => 'Deacon',
                                    'minister' => 'Minister',
                                ],
                                'Lay' => [
                                    'preacher' => 'Preacher',
                                    'leader' => 'Not a preacher',
                                ]
                            ])
                            ->default('preacher')
                            ->required(),
                        Forms\Components\TextInput::make('firstname')->label('First name')
                            ->required()
                            ->maxLength(199),
                        Forms\Components\TextInput::make('surname')
                            ->required()
                            ->maxLength(199),
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
                        Forms\Components\Select::make('circuit_id')
                            ->label('Circuit')
                            ->options(Circuit::orderBy('circuit')->get()->pluck('circuit', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('guestcircuits')->label('Also preaches in these circuits')
                            ->multiple()
                            ->options(Circuit::orderBy('circuit')->get()->pluck('circuit', 'id'))
                            ->searchable(),
                        Forms\Components\Select::make('society_id')
                            ->visible(function ($record){
                                if ($record){
                                    $min=Minister::where('person_id',$record->id)->first();
                                    if ($min){
                                        return false;
                                    } else {
                                        return true;
                                    }
                                }
                            })
                            ->label('Society')
                            ->options(function ($record){
                                if ($record){
                                    return Society::where('circuit_id',$record->circuit_id)->orderBy('society')->get()->pluck('society', 'id');
                                } else {
                                    return Society::orderBy('society')->get()->pluck('society', 'id');
                                }
                            })
                            ->searchable(),
                        Forms\Components\Select::make('leadership')
                            ->visible(function ($record){
                                if ($record){
                                    $min=Minister::where('person_id',$record->id)->first();
                                    if ($min){
                                        return false;
                                    } else {
                                        return true;
                                    }
                                }
                            })
                            ->label('Leadership roles')
                            ->multiple()
                            ->options(array_combine(setting('general.leadership_roles'),setting('general.leadership_roles'))),
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
                        Forms\Components\TextInput::make('number')->label('Preacher number (optional)')
                            ->numeric(),
                        Forms\Components\TextInput::make('induction')->label('Year of induction'),
                        Forms\Components\Select::make('leadership')
                            ->visible(function ($record){
                                $pre=Preacher::where('person_id',$record->id)->first();
                                if ($pre){
                                    return false;
                                } else {
                                    return true;
                                }
                            })
                            ->label('Leadership roles')
                            ->multiple()
                            ->options(array_combine(setting('general.preacher_leadership_roles'),setting('general.preacher_leadership_roles'))),
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
                        Forms\Components\Select::make('leadership')
                            ->multiple()
                            ->options([
                                'Superintendent' => 'Superintendent'
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
                Tables\Columns\TextColumn::make('surname')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('firstname')
                    ->label('First name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('circuit.circuit')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('society.society')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('minister')->label('Clergy')
                    ->icon('heroicon-o-check-circle')
            ])
            ->defaultSort('surname')
            ->modifyQueryUsing(function (Builder $query){
                $user=Auth::user();
                if (!$user->hasRole('Super Admin')){
                    if ($user->districts){
                        $circuits=Circuit::whereIn('district_id',$user->districts)->get()->pluck('id');
                        return $query->whereIn('circuit_id',$circuits);
                    } else if ($user->circuits){
                        return $query->whereIn('circuit_id',$user->circuits);
                    } else if ($user->societies) {
                        return $query->whereIn('society_id',$user->societies);
                    }
                }
            })

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
