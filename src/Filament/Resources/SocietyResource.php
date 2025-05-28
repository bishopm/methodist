<?php

namespace Bishopm\Methodist\Filament\Resources;

use Bishopm\Methodist\Filament\Resources\SocietyResource\Pages;
use Bishopm\Methodist\Filament\Resources\SocietyResource\RelationManagers;
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
use Illuminate\Support\Str;

class SocietyResource extends Resource
{
    public static array|string $routeMiddleware = ['checkperms'];
    
    protected static ?string $model = Society::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('society')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('circuit.reference')
                    ->label('No.')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('circuit.circuit')
                    ->sortable(),
            ])
            ->recordUrl(fn (Society $record): string => SocietyResource::getUrl('view', ['record' => $record]))
            ->modifyQueryUsing(function (Builder $query){
                $user=Auth::user();
                if (!$user->hasRole('Super Admin')){
                    if ($user->societies){
                        return $query->whereIn('id',$user->societies);
                    } else if ($user->circuits) {
                        return $query->whereIn('circuit_id',$user->circuits);
                    }
                }
            })
            ->filters([
                Filter::make('hide_inactive_societies')
                    ->query(fn (Builder $query): Builder => $query->whereHas('circuit', function($q) { $q->where('active',1); }))
                    ->default()
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
                    })
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
            RelationManagers\ServicesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSocieties::route('/'),
            'create' => Pages\CreateSociety::route('/create'),
            'view' => Pages\ViewSociety::route('/{record}'),
            'edit' => Pages\EditSociety::route('/{record}/edit'),
        ];
    }
}
