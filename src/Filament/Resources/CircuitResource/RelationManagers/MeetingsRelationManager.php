<?php

namespace Bishopm\Methodist\Filament\Resources\CircuitResource\RelationManagers;

use Bishopm\Methodist\Models\Society;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MeetingsRelationManager extends RelationManager
{
    protected static string $relationship = 'meetings';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('description')
                    ->maxLength(199),
                Forms\Components\Hidden::make('circuit_id')
                    ->default(function () {
                        return $this->getOwnerRecord()->id;                       
                    })
                    ->required(),
                Forms\Components\DateTimePicker::make('meetingdate'),
                Forms\Components\Select::make('society_id')->label('Society')
                    ->options(Society::where('circuit_id',$this->getOwnerRecord()->id)->orderBy('society')->get()->pluck('society','id')),
                Forms\Components\Select::make('quarter')->label('Which quarter\'s plan does this appear on?')
                    ->options([
                        'previous'=>'Previous',
                        'current'=>'Current',
                        'next'=>'Next',
                    ])
                    ->default('previous'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('meetingdate'),
            ])
            ->filters([
                Filter::make('hide_older_meetings')
                    ->query(fn (Builder $query): Builder => $query->where('meetingdate', '>', date('Y-m-d H:i:00',strtotime('yesterday'))))
                    ->default()
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
