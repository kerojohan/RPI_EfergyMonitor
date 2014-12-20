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

Route::get('/history', function()
{
  $mes = (new DateTime())->format('m-Y');
  //return View::make('hello')->with(compact('dia'));
  return View::make('consumreals.history')->with(compact('mes'));
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
