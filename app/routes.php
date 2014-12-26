<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
  $dia = (new DateTime())->format('d-m-Y');
  //return View::make('hello')->with(compact('dia'));
  return View::make('consumreals.consumreal')->with(compact('dia'));
});

Route::get('/historic/dia/{data?}', function($date = null)
{
  if($date==null){
    $dia = (new DateTime())->format('Y-m-d');

  }else{
    $dia = $date;
  }

   $json_string = file_get_contents('/var/www/energymonitor/public/history/'.$dia.'.json');
          $content = json_decode($json_string);
          $preukwh=0.141019;
      
      $consum= $content->consum;
      $preu=$consum * $preukwh;
      $pic=$content->pic;
      $php_array = $content->data;
      $data = json_encode($php_array);
      $totselsdies=Historyday::paginate(1);
  //$mes = (new DateTime())->format('m-Y');
  //return View::make('hello')->with(compact('dia'));
  return View::make('consumreals.history')->with(compact('totselsdies','dia','consum','preu','data','pic'));
});

Route::get('/historic/{mes?}', function($date=null)
{
  if($date==null){
    $mes = (new DateTime())->format('Y-m');

  }else{
    $mes = $date;
  }
    $consumreals=Historyday::where('day', 'LIKE', "$mes%")->get();

  $pics=array();
  $consums=array();
  $preu=0;
  $consumtotal=0;
  $preukwh=0.141019;
  $pic=0;
  foreach ($consumreals as $consum)    {
    array_push($consums, [strtotime($consum->day)*1000,(float)$consum->consum*1000]);
    array_push($pics, [strtotime($consum->day)*1000,(float)$consum->pic*1000]);
        //$consums[$consum->timestamp]="[".$consum->value."]";
    $consumtotal=$consum->consum+$consumtotal;
    if($consum->pic>$pic)$pic=$consum->pic;
    
  };
  $preu=$consumtotal*$preukwh;
  $pics = json_encode($pics);
  $data = json_encode($consums);
  //return View::make('hello')->with(compact('dia'));
  return View::make('consumreals.historymes')->with(compact('data','consumreals','mes','pics','preu','consumtotal','pic'));
});


Route::get('/show', function()
{
  return View::make('consumreals.show');
});

Route::resource('consumreals', 'ConsumrealsController');
Route::get('/consumsrealjson', function()
{
 $a=array();
 $consums=array();
 $a['label']="consums";  	
 $ara=(new DateTime());
 $from = $ara->sub(new DateInterval('PT24H00S'))->format('Y-m-d H:i:s');
 $to=(new DateTime())->format('Y-m-d H:i:s');
 $consumreals = Consumreal::whereBetween('date', array($from, $to))->get();
    	//$consumreals = Consumreal::all(['date','value'])->;
 foreach ($consumreals as $consum)    {
   array_push($consums, [strtotime($consum->date)*1000,(int)$consum->value]);
      	//$consums[$consum->timestamp]="[".$consum->value."]";
 };
 $a['data']=$consums;
 return Response::json($a);
});
Route::get('/consumsrealjson/{offset}', function($offset)
{
  $a=array();
  $consums=array();
  $a['label']="consums";    
  $ara=(new DateTime());
  $from = $ara->sub(new DateInterval('PT24H00S'))->format('Y-m-d H:i:s');
  $to=(new DateTime())->format('Y-m-d H:i:s');
  $consumreals = Consumreal::whereBetween('date', array($from, $to))->take(10000000)->skip($offset)->get();
      //$consumreals = Consumreal::all(['date','value'])->;
  foreach ($consumreals as $consum)    {
    array_push($consums, [strtotime($consum->date)*1000,(int)$consum->value]);
        //$consums[$consum->timestamp]="[".$consum->value."]";
  };
  $a['data']=$consums;
  return Response::json($a);
});
Route::get('/consumsrealjsonlast', function()
{
  $a=array();
  $consums=array();
  $a['label']="consums";    
  $ara=(new DateTime());
  $from = $ara->sub(new DateInterval('PT24H00S'))->format('Y-m-d H:i:s');
  $to=(new DateTime())->format('Y-m-d H:i:s');
  $consum = Consumreal::whereBetween('date', array($from, $to))->orderBy('date', 'desc')->first();
      //$consumreals = Consumreal::all(['date','value'])->;
  array_push($consums, [strtotime($consum->date)*1000,(int)$consum->value]);
        //$consums[$consum->timestamp]="[".$consum->value."]";

  $a['data']=$consums;
  return Response::json($a);
});

Route::get('/consumsrealsdiajson/{dia}/{mes}/{any}', function($dia,$mes,$any)
{
  $a=ConsumrealsController::consumdia($dia,$mes,$any); 
  return Response::json($a);
});

Route::get('/consumdiajson/{dia}/{mes}/{any}', function($dia,$mes,$any)
{
  $a=array();
  $consums=array();
  $a['label']="consums";    
  $from=(new DateTime($any.'-'.$mes.'-'.$dia.' 00:00:00'));
  $to=(new DateTime($any.'-'.$mes.'-'.$dia.' 00:00:00'))->add(new DateInterval('PT24H00S'));
  $consumreals = Consumreal::whereBetween('date', array($from, $to))->get();
  foreach ($consumreals as $consum)    {
    array_push($consums, [strtotime($consum->date)*1000,(int)$consum->value]);
        //$consums[$consum->timestamp]="[".$consum->value."]";
  };
  $a['data']=$consums;
  return Response::json($a);
});

Route::get('/consumdia/{dia}/{mes}/{any}', function($dia,$mes,$any)
{
 $dia = $any.'-'.$mes.'-'.$dia;
 return View::make('consumreals.consumdia')->with(compact('dia'));;
});
