<?php

class ConsumrealsController extends \BaseController {

	/**
	 * Display a listing of consumreals
	 *
	 * @return Response
	 */
	public function index()
	{
		$consumreals = Consumreal::all();

		return View::make('consumreals.index', compact('consumreals'));
	}

	/**
	 * Show the form for creating a new consumreal
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('consumreals.create');
	}

	/**
	 * Store a newly created consumreal in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Consumreal::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Consumreal::create($data);

		return Redirect::route('consumreals.index');
	}

	/**
	 * Display the specified consumreal.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$consumreal = Consumreal::findOrFail($id);

		return View::make('consumreals.show', compact('consumreal'));
	}

	/**
	 * Show the form for editing the specified consumreal.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$consumreal = Consumreal::find($id);

		return View::make('consumreals.edit', compact('consumreal'));
	}

	/**
	 * Update the specified consumreal in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$consumreal = Consumreal::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Consumreal::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$consumreal->update($data);

		return Redirect::route('consumreals.index');
	}

	/**
	 * Remove the specified consumreal from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Consumreal::destroy($id);

		return Redirect::route('consumreals.index');
	}

	public static function consumdia($dia,$mes,$any){  
		$a=array();
		$from=(new DateTime($any.'-'.$mes.'-'.$dia.' 00:00:00'));
		$to=(new DateTime($any.'-'.$mes.'-'.$dia.' 00:00:00'))->add(new DateInterval('PT24H00S'));
		$consumreals = Consumreal::whereBetween('date', array($from, $to))->get();
		$preukwh=Consumreal::$preukwh;
		$preu=0;
		$cont=0;
		$anterior=0;
		$sumatemps=0;
		foreach ($consumreals as $consum)    {
			if($cont>0){
				if($cont<(count($consumreals)-1) ){
					$tempsfrom=$consum->date;
				}else{
					if((strtotime((new DateTime())->format('Y-m-d H:i:s'))-strtotime($tempsfrom))>3600){
						$tempsfrom=($to->format('Y-m-d H:i:s'));
					}else{
						$tempsfrom=$consum->date;
					}
				}
				$preu=$preu+($preukwh*($consum->value/1000)*((strtotime($tempsfrom)-strtotime($anterior))/3600));
				$sumatemps=$sumatemps+((strtotime($tempsfrom)-strtotime($anterior)));
				$anterior=$consum->date;
			}else{
				$anterior=$from->format('Y-m-d H:i:s');
			}
			$cont++;
		};
		$pic = Consumreal::whereBetween('date', array($from, $to))->max('value');
		$a['preu']=$preu;
		$a['consum']=$preu/$preukwh;
		$a['pic']=$pic/1000;
		return $a;
	}

	public static function consumdiajson($dia,$mes,$any){  
		$consums=array();
		$a=array();
		$from=(new DateTime($any.'-'.$mes.'-'.$dia.' 00:00:00'));
		$to=(new DateTime($any.'-'.$mes.'-'.$dia.' 00:00:00'))->add(new DateInterval('PT24H00S'));
		$consumreals = Consumreal::whereBetween('date', array($from, $to))->get();
		$preukwh=Consumreal::$preukwh;
		$preu=0;
		$cont=0;
		$anterior=0;
		$sumatemps=0;
		foreach ($consumreals as $consum)    {
			array_push($consums, [strtotime($consum->date)*1000,(int)$consum->value]);
			if($cont>0){
				if($cont<(count($consumreals)-1) ){
					$tempsfrom=$consum->date;
				}else{
					if((strtotime((new DateTime())->format('Y-m-d H:i:s'))-strtotime($tempsfrom))>3600){
						$tempsfrom=($to->format('Y-m-d H:i:s'));
					}else{
						$tempsfrom=$consum->date;
					}
				}
				$preu=$preu+($preukwh*($consum->value/1000)*((strtotime($tempsfrom)-strtotime($anterior))/3600));
				$sumatemps=$sumatemps+((strtotime($tempsfrom)-strtotime($anterior)));
				$anterior=$consum->date;
			}else{
				$anterior=$from->format('Y-m-d H:i:s');
			}
			$cont++;
		};
		$pic = Consumreal::whereBetween('date', array($from, $to))->max('value');
		$a['consum']=$preu/$preukwh;
		$a['pic']=$pic/1000;
		$inici = strtotime($from->format('Y-m-d'));
		array_unshift($consums, [$inici.'000'+0,$consums[0][1]]);
		$fi = strtotime(date('Y-m-d', strtotime($from->format('Y-m-d') . " +1 days")));
		array_push($consums, [$fi.'000'+0,$consums[(count($consums)-1)][1]]);
		$a['data']=$consums;
		return json_encode($a);
	}


	public static function consumsreallast(){ 
		$a=self::consumptiondata('last');
	    return $a;
	}

	public static function consumsreal(){ 
		$a=self::consumptiondata();
	   	return $a;
	}
	public static function consumptiondata($parameter=null){
		$a=array();
	    $consums=array();
	    $a['label']="consums";    
	    $ara=(new DateTime());
	    $from = $ara->sub(new DateInterval('PT24H00S'))->format('Y-m-d H:i:s');
	    $to=(new DateTime())->format('Y-m-d H:i:s');
		if($parameter=='last'){
		   $consum = Consumreal::whereBetween('date', array($from, $to))->orderBy('date', 'desc')->first();
		   array_push($consums, [strtotime($consum->date)*1000,(int)$consum->value]);
		}else{
		   $consumreals = Consumreal::whereBetween('date', array($from, $to))->get();
		   foreach ($consumreals as $consum)    {
		     array_push($consums, [strtotime($consum->date)*1000,(int)$consum->value]);
		   };
		}
	    $a['data']=$consums;
	    return $a;
	}
}
