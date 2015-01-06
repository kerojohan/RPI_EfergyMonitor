<?php

class HistorydaysController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /historydays
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /historydays/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /historydays
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /historydays/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /historydays/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /historydays/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /historydays/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public static function getdaydata($date){
	    if($date==null){
	      $dia = (new DateTime())->format('Y-m-d');
	    }else{
	      $dia = $date;
	    }
	    $dades=array();
	    $json_string = file_get_contents('/var/www/energymonitor/public/history/'.$dia.'.json');
	    $content = json_decode($json_string);
	    $preukwh=Consumreal::$preukwh;          
	    $consum= $content->consum;
	    $preu=$consum * $preukwh;
	    $pic=$content->pic;
	    $php_array = $content->data;
	    $data = json_encode($php_array);
	    $totselsdies=Historyday::paginate(1);
	    $dades['totselsdies']=$totselsdies;
	    $dades['dia']=$dia;
	    $dades['consum']=$consum;
	    $dades['preu']=$preu;
	    $dades['data']=$data;
	    $dades['pic']=$pic;
	    return $dades;
	}

	public static function historicmes($date=null){ 
		$newd= (new DateTime())->format('Y-m');
		if($date==null){
			$mes = $newd;

		}else{
			$mes = $date;
		}
		$consumreals=Historyday::where('day', 'LIKE', "$mes%")->get();
		$a=array();
		$pics=array();
		$consums=array();
		$preu=0;
		$consumtotal=0;
		$preukwh=Consumreal::$preukwh;
		$pic=0;
		foreach ($consumreals as $consum)    {
			array_push($consums, [strtotime($consum->day)*1000,(float)$consum->consum*1000]);
			//array_push($pics, [(strtotime($consum->day)+10*3600)*1000,(float)$consum->pic*1000]);
			array_push($pics, [(strtotime($consum->day)+10*3600)*1000,(float)$consum->pic*1000]);
			$consumtotal=$consum->consum+$consumtotal;
			if($consum->pic>$pic)$pic=$consum->pic;
		};

		$avui['day']="";
		$avui['consum']=0;
		$avui['pic']=0;
		if($mes==$newd){
			$avuidata = (new DateTime())->format('Y-m-d');
			list($any2, $mes2, $dia2) = explode('-', $avuidata);
			$avui = ConsumrealsController::consumdia($dia2,$mes2,$any2); 
			$avui['consum']*=1000;
			$avui['dia']=$avuidata;
			$consumtotal=$consumtotal+($avui['consum']/1000);
			if($pic==0) $pic=$avui['pic'];
			$avui['day']= strtotime((new DateTime())->format('Y-m-d'))*1000;
			array_push($consums, (object) array('x' => $avui['day'],'y'=>(int)$avui['consum'],'color'=>'rgb(218, 233, 244)'));
		//	array_push($pics, [$avui['day']+36000000,$avui['pic']*1000]);
			array_push($pics, [$avui['day']+10*3600*1000,$avui['pic']*1000]);
		}
		$data = json_encode($consums);
		$preu=$consumtotal*$preukwh;
		$pics = json_encode($pics);
		$a['avui']=$avui;
		$a['consumtotal']=$consumtotal;
		$a['mes']=$mes;
		$a['consums']=$consums;
		$a['data']=$data;
		$a['consumreals']=$consumreals;
		$a['pics']=$pics;
		$a['pic']=$pic;
		$a['preu']=$preu;
		$mesposterior =(new DateTime($mes))->add(new DateInterval('P1M'));
		$mesanterior= (new DateTime($mes))->sub(new DateInterval('P1M'));
		$a['mesposterior']=null;
		$a['mesanterior']=null;
		if($mesposterior<=(new DateTime()))
		{
			$a['mesposterior']=$mesposterior->format('Y-m');
		}
		if($mesanterior<(new DateTime())){
			$a['mesanterior']=$mesanterior->format('Y-m');
		}
			
		
		return $a;
	} 
}