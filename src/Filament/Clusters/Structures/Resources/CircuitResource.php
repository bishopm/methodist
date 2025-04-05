<?php

namespace Bishopm\Methodist\Filament\Clusters\Structures\Resources;

use Bishopm\Methodist\Filament\Clusters\Structures;
use Bishopm\Methodist\Filament\Clusters\Structures\Resources\CircuitResource\Pages;
use Bishopm\Methodist\Filament\Clusters\Structures\Resources\CircuitResource\RelationManagers;
use Bishopm\Methodist\Models\Circuit;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CircuitResource extends Resource
{
    protected static ?string $model = Circuit::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $cluster = Structures::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Edit Circuit')->columnSpanFull()->tabs([
                    Tab::make('Circuit')->columns(2)->schema([
                        Forms\Components\TextInput::make('circuit')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))   
                            ->maxLength(199),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(199),
                        Forms\Components\Select::make('district_id')
                            ->relationship('district', 'district')
                            ->required(),
                        Forms\Components\TextInput::make('reference')->label('Circuit number')
                            ->required()
                            ->numeric(),
                    ]),
                    Tab::make('Services')->columns(2)->schema([
                        Forms\Components\Select::make('midweeks')->label('Midweek services')
                            ->multiple()
                            ->options(function (){
                                return DB::table('midweeks')->select('midweek')->orderBy('midweek')->groupBy('midweek')->get()->pluck('midweek','midweek');
                            }),
                        Forms\Components\KeyValue::make('servicetypes')->label('Service types')
                            ->keyLabel('Abbreviation')
                            ->valueLabel('Description'),
                    ]),
                    Tab::make('Plan settings')->columns(2)->schema([
                        Forms\Components\Toggle::make('showphone')->label('Show phone numbers on plan'),
                        Forms\Components\Toggle::make('activated'),
                        Forms\Components\Select::make('plan_month')->label('First plan starts in this month')
                            ->options([
                                '1' => 'January',
                                '2' => 'February',
                                '3' => 'March'
                            ]),
                    ])
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')->label('No.')
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('circuit')
                    ->searchable(),
                Tables\Columns\TextColumn::make('district.district')
                    ->sortable(),
                Tables\Columns\IconColumn::make('activated')->label('Active')
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-o-x-circle',
                        '1' => 'heroicon-o-check-circle'
                    })
            ])->defaultSort('reference','asc')
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
            RelationManagers\SocietyRelationManager::class,
            RelationManagers\MeetingsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCircuits::route('/'),
            'create' => Pages\CreateCircuit::route('/create'),
            'plan' => Pages\PreachingPlan::route('/plan/{record}/{today?}'),
            'edit' => Pages\EditCircuit::route('/{record}/edit'),
        ];
    }
}
