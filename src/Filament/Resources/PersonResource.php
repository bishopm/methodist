<?php

namespace Bishopm\Methodist\Filament\Resources;

use Bishopm\Methodist\Filament\Resources\PersonResource\Pages;
use Bishopm\Methodist\Filament\Resources\PersonResource\RelationManagers;
use Bishopm\Methodist\Models\Circuit;
use Bishopm\Methodist\Models\Minister;
use Bishopm\Methodist\Models\Person;
use Bishopm\Methodist\Models\Preacher;
use Bishopm\Methodist\Models\Society;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->roles[0]->name === "Super Admin";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Personal details')
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
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state=="minister"){
                                    $set('title', "Rev");
                                }
                            })
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
                        Forms\Components\FileUpload::make('image')
                            ->image(),
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
                                'emeritus' => 'Emeritus preacher',
                                'guest' => 'Guest preacher'
                            ])
                            ->required(),
                        Forms\Components\Select::make('society_id')
                            ->label('Society')
                            ->options(Society::orderBy('society')->get()->pluck('society', 'id'))
                            ->searchable(),
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
                            ->label('Preacher leadership roles')
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
                                'Deacon' => 'Deacon',
                                'Minister' => 'Minister',
                                'Superintendent' => 'Superintendent minister',
                                'Supernumerary' => 'Supernumerary Minister'
                            ])
                            ->required(),
                        Forms\Components\Select::make('leadership')
                            ->label('District leadership roles')
                            ->multiple()
                            ->options(array_combine(setting('general.minister_leadership_roles'),setting('general.minister_leadership_roles'))),
                        Forms\Components\TextInput::make('ordained')->numeric(),
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
                Tables\Columns\TextColumn::make('circuitroles.status')->label('Status')
                    ->formatStateUsing(function ($state){
                        if (is_array(json_decode($state))){
                            return implode(json_decode($state));
                        } else {
                            return $state;
                        }
                    })
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
            RelationManagers\CircuitrolesRelationManager::class,
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
