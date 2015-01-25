<?php

class HistorymonthsController extends \BaseController {

	/**
	 * Display a listing of historymonths
	 *
	 * @return Response
	 */
	public function index()
	{
		$historymonths = Historymonth::all();

		return View::make('historymonths.index', compact('historymonths'));
	}

	/**
	 * Show the form for creating a new historymonth
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('historymonths.create');
	}

	/**
	 * Store a newly created historymonth in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Historymonth::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Historymonth::create($data);

		return Redirect::route('historymonths.index');
	}

	/**
	 * Display the specified historymonth.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$historymonth = Historymonth::findOrFail($id);

		return View::make('historymonths.show', compact('historymonth'));
	}

	/**
	 * Show the form for editing the specified historymonth.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$historymonth = Historymonth::find($id);

		return View::make('historymonths.edit', compact('historymonth'));
	}

	/**
	 * Update the specified historymonth in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$historymonth = Historymonth::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Historymonth::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$historymonth->update($data);

		return Redirect::route('historymonths.index');
	}

	/**
	 * Remove the specified historymonth from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Historymonth::destroy($id);

		return Redirect::route('historymonths.index');
	}

	public static function historic12(){ 
		$consumreals=Historymonth::all()->take(12);
		$a=array();
		$pics=array();
		$consums=array();
		$preu=0;
		$consumtotal=0;
		$preukwh=Consumreal::$preukwh;
		$pic=0;
		foreach ($consumreals as $consum)    {
			array_push($consums, [strtotime($consum->month)*1000,(float)$consum->consum*1000]);
			//array_push($pics, [(strtotime($consum->day)+10*3600)*1000,(float)$consum->pic*1000]);
			array_push($pics, [(strtotime($consum->month))*1000,(float)$consum->pic*1000]);
			$consumtotal=$consum->consum+$consumtotal;
			if($consum->pic>$pic)$pic=$consum->pic;
		};
		/* consum d'avui */
		$avuidata = (new DateTime())->format('Y-m-d');
		list($any2, $mes2, $dia2) = explode('-', $avuidata);
		$from=(new DateTime($any2.'-'.$mes2.'-'.$dia2.' 00:00:00'));
		$to=(new DateTime($any2.'-'.$mes2.'-'.$dia2.' 00:00:00'))->add(new DateInterval('PT24H00S'));
		$mesactual=(new DateTime())->format('Y-m');
		$consummesactual = DB::table('historydays')
		->select(DB::raw('SUM(consum) as consum, MAX(pic) as pic'))
		->whereRaw(DB::raw('(DATE_FORMAT(NOW(), "%Y-%m") like DATE_FORMAT(day, "%Y-%m"))'))
		->get();
		$avui['consum']=$consummesactual[0]->consum*1000;
		$avui['pic']=$consummesactual[0]->pic;
		$avui['mes']= strtotime($mesactual)*1000;
		array_push($consums, (object) array('x' => $avui['mes'],'y'=>(int)$avui['consum'],'color'=>'rgb(218, 233, 244)'));
		//	array_push($pics, [$avui['day']+36000000,$avui['pic']*1000]);
		$ara['preu']=ConsumrealsController::calculpreu($from,$to);
		$ara['pic']=(ConsumrealsController::maxpic($from,$to))/1000;
		$ara['consum']=$ara['preu']/$preukwh;
		if($avui['pic']<$ara['pic'])$avui['pic']=$ara['pic'];
		array_push($pics, [$avui['mes'],$avui['pic']*1000]);
		if($avui['pic']>$pic)$pic=$avui['pic'];
		if($pic<$ara['pic'])$pic=$ara['pic'];
		$a['avui']=$avui;
		$data = json_encode($consums);
		$pics = json_encode($pics);
		$a['consumtotal']=$consumtotal+($ara['consum']+$avui['consum'])/1000;
		$preu=$a['consumtotal']*$preukwh;
		$a['consums']=$consums;
		$a['data']=$data;
		$a['consumreals']=$consumreals;
		$a['pics']=$pics;
		$a['pic']=$pic;
		$a['preu']=$preu+$ara['preu'];	

		$anyposterior =(new DateTime($mesactual))->add(new DateInterval('P1Y'));
		$anyanterior= (new DateTime($mesactual))->sub(new DateInterval('P1Y'));
		$a['anyposterior']=null;
		$a['anyanterior']=null;
		$a['anyactual']=(new DateTime($mesactual))->format('Y');;
		if($anyposterior<=(new DateTime()))
		{
			$a['anyposterior']=$anyposterior->format('Y');
		}else{
			$a['anyposterior']=$a['anyactual'];
		}
		if($anyanterior<(new DateTime())){
			$a['anyanterior']=$anyanterior->format('Y');
		}
		return $a;
	} 


	public static function historicany($date){ 
		$newd= (new DateTime())->format('Y');
		if($date==null){
			$any = $newd;

		}else{
			$any = $date;
		}

		$consumreals=Historymonth::whereBetween('month', array($any."-01-01 00:00:00",$any."-12-31 00:00:00"))->get();
		$a=array();
		$pics=array();
		$consums=array();
		$preu=0;
		$consumtotal=0;
		$preukwh=Consumreal::$preukwh;
		$pic=0;
		foreach ($consumreals as $consum)    {
			array_push($consums, [strtotime($consum->month)*1000,(float)$consum->consum*1000]);
			//array_push($pics, [(strtotime($consum->day)+10*3600)*1000,(float)$consum->pic*1000]);
			array_push($pics, [(strtotime($consum->month))*1000,(float)$consum->pic*1000]);
			$consumtotal=$consum->consum+$consumtotal;
			if($consum->pic>$pic)$pic=$consum->pic;
		};
		$fins=$any."-12-31 00:00:00";
		if((new DateTime($fins))>(new DateTime()))
		{
		/* consum d'avui */
				$avuidata = (new DateTime())->format('Y-m-d');
				list($any2, $mes2, $dia2) = explode('-', $avuidata);
				$from=(new DateTime($any2.'-'.$mes2.'-'.$dia2.' 00:00:00'));
				$to=(new DateTime($any2.'-'.$mes2.'-'.$dia2.' 00:00:00'))->add(new DateInterval('PT24H00S'));
				$mesactual=(new DateTime())->format('Y-m');
				$consummesactual = DB::table('historydays')
				->select(DB::raw('SUM(consum) as consum, MAX(pic) as pic'))
				->whereRaw(DB::raw('(DATE_FORMAT(NOW(), "%Y-%m") like DATE_FORMAT(day, "%Y-%m"))'))
				->get();
				$avui['consum']=$consummesactual[0]->consum*1000;
				$avui['pic']=$consummesactual[0]->pic;
				$avui['mes']= strtotime($mesactual)*1000;
				array_push($consums, (object) array('x' => $avui['mes'],'y'=>(int)$avui['consum'],'color'=>'rgb(218, 233, 244)'));
				//	array_push($pics, [$avui['day']+36000000,$avui['pic']*1000]);
				$ara['preu']=ConsumrealsController::calculpreu($from,$to);
				$ara['pic']=(ConsumrealsController::maxpic($from,$to))/1000;
				$ara['consum']=$ara['preu']/$preukwh;
				if($avui['pic']<$ara['pic'])$avui['pic']=$ara['pic'];
				array_push($pics, [$avui['mes'],$avui['pic']*1000]);
				if($avui['pic']>$pic)$pic=$avui['pic'];
				if($pic<$ara['pic'])$pic=$ara['pic'];
				$a['avui']=$avui;
				$consumtotal=$consumtotal+($avui['consum']+$ara['consum'])/1000;
				$preu=$preu+$ara['preu'];
		}

		$data = json_encode($consums);
		$pics = json_encode($pics);
		$a['consumtotal']=$consumtotal;
		$preu=$a['consumtotal']*$preukwh;
		$a['consums']=$consums;
		$a['data']=$data;
		$a['consumreals']=$consumreals;
		$a['pics']=$pics;
		$a['pic']=$pic;
		$a['preu']=$preu;
		$a['from']=	$any."-01-01 00:00:00";
		$anyposterior =(new DateTime($any.'-01-01'))->add(new DateInterval('P1Y'));
		$anyanterior= (new DateTime($any.'-01-01'))->sub(new DateInterval('P1Y'));
		$a['anyposterior']=null;
		$a['anyanterior']=null;
		$a['anyactual']=(new DateTime($any.'-01-01'))->format('Y');;
		if($anyposterior<=(new DateTime()))
		{
			$a['anyposterior']=$anyposterior->format('Y');
		}
		if($anyanterior<(new DateTime())){
			$a['anyanterior']=$anyanterior->format('Y');
		}
		return $a;
	} 

}
