<?php

namespace Bishopm\Methodist\Filament\Resources;

use Bishopm\Methodist\Filament\Resources\DistrictResource\Pages;
use Bishopm\Methodist\Filament\Resources\DistrictResource\RelationManagers;
use Bishopm\Methodist\Models\Circuit;
use Bishopm\Methodist\Models\District;
use Bishopm\Methodist\Models\Person;
use Bishopm\Methodist\Models\Society;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DistrictResource extends Resource
{
    protected static ?string $model = District::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-europe-africa';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('district')
                    ->required()
                    ->maxLength(199),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(199),
                Forms\Components\Hidden::make('location'),
                Forms\Components\Hidden::make('latitude'),
                Forms\Components\Hidden::make('longitude'),
                Map::make('location')
                    ->afterStateUpdated(function (Set $set, ?array $state): void {
                        $set('latitude', $state['lat']);
                        $set('longitude', $state['lng']);
                    })
                    ->afterStateHydrated(function ($state, $record, Set $set): void {
                        $set('location', [
                            'lat' => $record->latitude,
                            'lng' => $record->longitude
                        ]);
                    }),
                Forms\Components\RichEditor::make('contact')->label('District office details'),
                Forms\Components\Select::make('bishop')
                    ->options( function () {
                        $persons = Person::whereHas('minister')->orderBy('surname')->orderBy('firstname')->get();
                        foreach ($persons as $person){
                            $options[$person->id]=$person->surname . ", " . $person->firstname;
                        }
                        return $options;
                    })
                    ->searchable(),
                Forms\Components\Toggle::make('active')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('district')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\IconColumn::make('active')
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-o-x-circle',
                        '1' => 'heroicon-o-check-circle'
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success'
                    })
            ])
            ->modifyQueryUsing(function (Builder $query){
                $user=Auth::user();
                if (!$user->hasRole('Super Admin')){
                    if ($user->districts){
                        return $query->whereIn('id',$user->districts);
                    } else if ($user->circuits) {
                        $districts=Circuit::whereIn('id',$user->circuits)->select('district_id')->get()->pluck('district_id');
                        return $query->whereIn('id',$districts);
                    } else if ($user->societies) {
                        $circuits=Society::whereIn('id',$user->societies)->select('circuit_id')->get()->pluck('circuit_id');
                        $districts=Circuit::whereIn('id',$circuits)->select('district_id')->get()->pluck('district_id');
                        return $query->whereIn('id',$districts);
                    } else {
                        return $query->where('id',0);
                    }
                }
            })
            ->filters([
                Filter::make('hide_inactive_districts')
                    ->query(fn (Builder $query): Builder => $query->where('active', 1))
                    ->default()
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            RelationManagers\CircuitsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDistricts::route('/'),
            'create' => Pages\CreateDistrict::route('/create'),
            'view' => Pages\ViewDistrict::route('/{record}'),
            'edit' => Pages\EditDistrict::route('/{record}/edit'),
        ];
    }
}
