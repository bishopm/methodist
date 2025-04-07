<?php

namespace Bishopm\Methodist\Filament\Resources;

use Bishopm\Methodist\Filament\Resources\DistrictResource\Pages;
use Bishopm\Methodist\Filament\Resources\DistrictResource\RelationManagers;
use Bishopm\Methodist\Models\Circuit;
use Bishopm\Methodist\Models\District;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
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
                Forms\Components\TextInput::make('location')
                    ->maxLength(199),
                Forms\Components\TextInput::make('bishop')
                    ->maxLength(199),
                Forms\Components\TextInput::make('secretary')
                    ->maxLength(199),
                Forms\Components\TextInput::make('latitude')
                    ->hiddenLabel(),
                Forms\Components\TextInput::make('longitude')
                    ->hiddenLabel(),
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
                    })
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
            ])
            ->modifyQueryUsing(function (Builder $query){
                $user=Auth::user();
                if (!$user->hasRole('Super Admin')){
                    if ($user->districts){
                        return $query->whereIn('id',$user->districts);
                    } else if ($user->circuits) {
                        $districts=Circuit::whereIn('id',$user->circuits)->select('district_id')->get()->pluck('district_id');
                        return $query->whereIn('id',$districts);
                    } else {
                        return $query->where('id',0);
                    }
                }
            })
            ->filters([
                //
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
