<?php

namespace Bishopm\Methodist\Http\Controllers;

use Bishopm\Methodist\Models\Person;
use Bishopm\Methodist\Models\Circuit;
use Bishopm\Methodist\Models\Plan;
use Bishopm\Methodist\Models\Society;

class HomeController extends Controller
{

    public function home()
    {
        $data=array();
        return view('methodist::web.home',$data);
    }

    public function editplan($id)
    {
        // Dates
        $dates=$this->getdates($id,date('Y-m-d'));
        $societies=Society::with('services')->where('circuit_id',$id)->orderBy('society','asc')->get();
        $data=array();
        foreach ($societies as $soc){
            if (isset($soc->services[0])){
                foreach ($soc->services as $service){
                    foreach ($dates as $week){
                        $plan = Plan::where('service_id',$service->id)->where('servicedate',$week)->first();
                        if ($plan){
                            $data['rows'][$soc->society][date('H:i',strtotime($service->servicetime))][$week]['preacher']=$plan->person_id;
                            $data['rows'][$soc->society][date('H:i',strtotime($service->servicetime))][$week]['servicetype']=$plan->servicetype;
                            $data['rows'][$soc->society][date('H:i',strtotime($service->servicetime))][$week]['service_id']=$plan->service_id;
                        } else {
                            $data['rows'][$soc->society][date('H:i',strtotime($service->servicetime))][$week]['preacher']="";
                            $data['rows'][$soc->society][date('H:i',strtotime($service->servicetime))][$week]['servicetype']="";
                            $data['rows'][$soc->society][date('H:i',strtotime($service->servicetime))][$week]['service_id']=$service->id;
                        }
                    }
                }
                ksort($data['rows'][$soc->society]);
            }
        }
        ksort($data);
        $data['dates']=$dates;
        $data['servicetypes']=setting('general.servicetypes');
        asort($data['servicetypes']);
        $clergy=Person::withWhereHas('minister')->where('circuit_id',$id)->orderBy('surname')->get();
        $ministers=array();
        $supernumeraries=array();
        $preachers=array();
        foreach ($clergy as $minister){
            if ($minister->minister->status<>"Supernumery Minister"){
                $ministers[]=[
                    'id'=>$minister->id,
                    'name'=>substr($minister->firstname,0,1) . " " . $minister->surname
                ];
            } else {
                $supernumeraries[]=[
                    'id'=>$minister->id,
                    'name'=>substr($minister->firstname,0,1) . " " . $minister->surname
                ];
            }
        }
        $laypreachers=Person::withWhereHas('preacher')->where('circuit_id',$id)->orderBy('surname')->get();
        foreach ($laypreachers as $preacher){
            if ($preacher->preacher->active==1){
                    $preachers[]=[
                    'id'=>$preacher->id,
                    'name'=>substr($preacher->firstname,0,1) . " " . $preacher->surname
                ];
            }
        }
        $data['preachers']=array_merge($ministers,$supernumeraries,$preachers);
        return view('methodist::edit-plan',$data);
    }

    private function getdates($circuit_id,$today){
        $circuit=Circuit::find($circuit_id);
        $thismonth=intval(date('n',strtotime($today)));
        $thisyear=intval(date('Y',strtotime($today)));
        $yy=$thisyear;
        if ($circuit->plan_month==3){
            $plans[0]=[3,4,5];
            $plans[1]=[6,7,8];
            $plans[2]=[9,10,11];
            $plans[3]=[12,1,2];
            if ($thismonth<3){
                $yy=$thisyear-1;
            }
        } elseif ($circuit->plan_month==2){
            $plans[0]=[2,3,4];
            $plans[1]=[5,6,7];
            $plans[2]=[8,9,10];
            $plans[3]=[11,12,1];
            if ($thismonth<2){
                $yy=$thisyear-1;
            }
        } else {
            $plans[0]=[1,2,3];
            $plans[1]=[4,5,6];
            $plans[2]=[7,8,9];
            $plans[3]=[10,11,12];
        }
        foreach ($plans as $kk=>$pp){
            if (in_array($thismonth,$pp)){
                $plan=$plans[$kk];
            }
        }
        if ($plan[0]<10){
            $firstday = $yy . '-0' . $plan[0] . '-01';
        } else {
            $firstday = $yy . '-' . $plan[0] . '-01';
        }
        $lastday=date('Y-m-d',strtotime($firstday . " + 3 months"));
        $dow=intval(date('N',strtotime($firstday)));
        if ($dow==7){
            $firstsunday=$firstday;
        } else {
            $firstsunday=date("Y-m-d",strtotime($firstday)+86400*(7-$dow));
        }
        $dates[]=$firstsunday;
        for ($w=1;$w<15;$w++){
            if (in_array(intval(date('n',strtotime($firstsunday)+86400*7*$w)),$plan)){
                $dates[$w]=date("Y-m-d",strtotime($firstsunday)+86400*7*$w);
            }
        }
        /*$midweeks=Midweek::where('servicedate','>=',$firstday)->where('servicedate','<',$lastday)->orderBy('servicedate','ASC')->get();
        foreach ($midweeks as $mw){
            $dates[]=$mw->servicedate;
        }*/
        sort($dates);
        return $dates;
    }
}
