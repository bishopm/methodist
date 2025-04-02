<?php

namespace Bishopm\Methodist\Filament\Clusters\Structures\Resources\CircuitResource\Pages;

use Bishopm\Methodist\Filament\Clusters\Structures\Resources\CircuitResource;
use Bishopm\Methodist\Models\District;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\DB;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Support\HtmlString;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Actions\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

class EditPlan extends Page implements HasForms
{
    use InteractsWithRecord, InteractsWithForms, InteractsWithTable;

    protected static string $resource = CircuitResource::class;

    protected static string $view = 'methodist::edit-plan';

    protected ?string $subheading = 'Subtitle';

    public ?array $data;

    public $count;

    public $credits, $smss;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->form->fill();
    }

    protected function getHeaderActions(): array
    {
        $schema = $this->constructPreviewSchema();
        if (intval(date('n')) & 1){
            $monthadd="+1 month";
            $rosterlabel = date('M') . ' and ' . date('M',strtotime($monthadd));
            $firstdate = date('Y-m-01');
        } else {
            $monthadd="-1 month";
            $rosterlabel = date('M',strtotime($monthadd)) . ' and ' . date('M');
            $firstdate = date('Y-m-01',strtotime('-1 month'));
        }
        return [
            Action::make('prev')
                ->action(fn () => self::changeMonth('prev'))
                ->icon('heroicon-m-backward')
                ->iconButton(),
            Action::make('next')
                ->action(fn () => self::changeMonth('next'))
                ->icon('heroicon-m-forward')
                ->iconButton(),
            Action::make('report')->label('View Plan')
                ->url(fn (): string => route('reports.plan', [
                    'id' => $this->record,
                    'year' => date('Y',strtotime($this->data['firstofmonth'])), 
                    'month' => date('m',strtotime($this->data['firstofmonth']))
                ]))
        ];
    }

    protected function constructPreviewSchema(){
        $record=$this->record;
        /*$rosterdate =date('Y-m-d',strtotime('next ' . $record->dayofweek));
        $schema=[Placeholder::make('PreviewDate')->content(new HtmlString('<b>' . date('l d F Y',strtotime($rosterdate)) . '</b>'))->label('')];
        $rosteritems = Rosteritem::with('individuals','rostergroup.group')->where('rosterdate',$rosterdate)->whereHas('rostergroup', function ($q) use ($record) {
            $q->where('roster_id',$record->id);
        })->get();
        $data['ridata']=array();
        $messages = "";
        foreach ($rosteritems as $ri){
            foreach ($ri->individuals as $indiv){
                if ($indiv->cellphone){
                    $msg = $indiv->firstname . ", " . $record->message . " (" . $ri->rostergroup->group->groupname . ")";
                    if ($ri->rostergroup->extrainfo==="yes"){
                        if ($ri->rostergroup->extrainfo=="reading"){
                            $servicetimes=setting('general.services');
                            foreach ($servicetimes as $service){
                                if (strpos($ri->rostergroup->group->groupname,$service)){
                                    $stime = $service;
                                }
                            }
                            $reading = Service::where('servicedate',$rosterdate)->where('servicetime',$stime)->first();
                            if ((isset($reading->reading)) and (str_contains($ri->rostergroup->group->groupname, 'Readers'))){
                                $msg = $msg . " Reading: " . $reading->reading;
                            }
                        }
                    }
                    $this->data['ridata'][$ri->rostergroup->group->groupname][$indiv->cellphone]=$msg;
                    $this->data['allmessages'][$indiv->cellphone]=$msg;
                    $messages = $messages . $indiv->cellphone . ": " . $msg . "<br>";
                }
            }
        }
        $schema[] = Placeholder::make('Bulksms Credits')->label('')->content(function (){
            $this->smss = new BulksmsService(setting('services.bulksms_clientid'), setting('services.bulksms_api_secret'));
            $this->credits = $this->smss->get_credits();
            return "Available BulkSMS credits: " . $this->credits;
        });
        $schema[] = Placeholder::make('Preview')->content(new HtmlString($messages))->label('');
        return $schema;*/
    }

    protected function changeMonth($start){
        if ($start=="prev"){
            $this->data['firstofmonth'] = date('Y-m-d', strtotime($this->data['firstofmonth'] . " -1 month"));
        } else {
            $this->data['firstofmonth'] = date('Y-m-d', strtotime($this->data['firstofmonth'] . " +1 month"));
        }        
        $weeks = $this->getWeeks($this->data['firstofmonth']);
        foreach ($weeks as $n=>$week){
            $wkvar = 'week'.$n;
            $this->data[$wkvar]=$week;
        }
        foreach ($this->data['rgs'] as $rg){
            foreach ($weeks as $w=>$wk){
                $vv = 'select_' . $w . "_" . $rg;
                $this->data[$vv] = $this->getIndivs($rg,$wk);
            }
        }
    }

    protected function getWeeks($firstofmonth){
        $thismonth=date('Y-m',strtotime($firstofmonth));
        for ($i=1;$i<7;$i++){
            if (date('l',strtotime($thismonth . "-" . $i)) == $this->record->dayofweek) {
                $weeks[]=date('Y-m-d',strtotime($thismonth . "-" . $i));
            }
        }
        for ($j=1;$j<=4;$j++){
            $weeks[]=date('Y-m-d',strtotime($weeks[0] . ' + ' . $j . ' week'));
        }
        $this->data['prev']=date('M Y',strtotime($thismonth . '-01 -1 month'));
        $this->data['next']=date('M Y',strtotime($thismonth . '-01 +1 month'));
        $this->data['columns']=6;
        return $weeks;
    }

    private static function updateIndivs($state, $rosterdate, $rostergroup){
        /*$ri = Rosteritem::where('rosterdate',$rosterdate)->where('rostergroup_id',$rostergroup)->first();
        if (isset($ri->id)){
            DB::table('individual_rosteritem')->where('rosteritem_id',$ri->id)->delete();
        } else {
            $ri = Rosteritem::create([
                'rostergroup_id' => $rostergroup,
                'rosterdate' => $rosterdate
            ]);
        }
        if (is_array($state)){
            foreach ($state as $indiv){
                DB::table('individual_rosteritem')->insert([
                    'individual_id' => $indiv,
                    'rosteritem_id' => $ri->id
                ]);
            }
        } else {
            DB::table('individual_rosteritem')->insert([
                'individual_id' => $state,
                'rosteritem_id' => $ri->id
            ]);
        }*/
    }

    public function getIndivs($rg, $wk){
        /*$rosteritem=Rosteritem::with('individuals')->where('rosterdate',$wk)->where('rostergroup_id',$rg)->first();
        $ridat=array();
        if ($rosteritem){
            if (isset($rosteritem->individuals)){
                foreach ($rosteritem->individuals as $indiv){
                    $ridat[]=$indiv->id;
                }
            }
        }
        return $ridat;*/
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(District::query())
            ->columns([
                TextColumn::make('district'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public function form(Form $form): Form
    {
        $schema = array();
        $this->subheading = $this->record->roster;
        if (!isset($this->data['firstofmonth'])){
            $this->data['firstofmonth'] = date('Y-m-01');
        }
        /*$rostergroups = Rostergroup::with('group.individuals')->where('roster_id',$this->record->id)->get()->sortBy('group.groupname');
        $weeks=$this->getWeeks($this->data['firstofmonth']);
        foreach ($weeks as $ndx=>$label){
            if ($ndx==0){
                $schema[] = Placeholder::make('blank')->label('');
            }
            $this->data['week' . $ndx] = $label;
            $schema[] = TextInput::make('week' . $ndx)->label('')
                            ->live()
                            ->default($label)
                            ->readonly();
        }
        foreach ($rostergroups as $ndx=>$rg) {
            $this->data['rgs'][]=$rg->id;
            $schema[] = Placeholder::make($rg->group->groupname)->content($rg->group->groupname)->label('');
            $members=[];
            foreach ($rg->group->individuals as $indiv){
                $members[$indiv->id] = $indiv->firstname . " " . $indiv->surname;
            }
            foreach ($weeks as $wno=>$week){
                $onduty=array();
                $onduty=$this->getIndivs($rg->id,$week);
                if ($rg->maxpeople==1){
                    if (isset($onduty)){
                        $ind = $onduty;
                    } else {
                        $ind=0;
                    }
                    $schema[] = Select::make('select_' . $wno . "_" . $rg->id)
                        ->label('')
                        ->options($members)
                        ->default($ind)
                        ->live()
                        ->placeholder('')
                        ->afterStateUpdated(fn ($state) => self::updateIndivs($state, $week, $rg->id));
                } else {
                    if (isset($onduty)){
                        $ind = $onduty;
                    } else {
                        $ind=[];
                    }
                    $schema[] = Select::make('select_' . $wno . "_" . $rg->id)
                        ->label('')
                        ->multiple()
                        ->options($members)
                        ->maxItems($rg->maxpeople)
                        ->default($ind)
                        ->live()
                        ->placeholder('')
                        ->afterStateUpdated(fn ($state) => self::updateIndivs($state, $week, $rg->id));
                }
            }
        }
            */
        return $form
            ->schema($schema)
            ->columns(1)
            ->statePath('data');
    }

}