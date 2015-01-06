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
  $dades['pic']=0;
  $dades['consum']=0;
  $dades['ultim']=0;
  $dades['preu']=0;
  /*list($any2, $mes2, $dia2) = explode('-', $dia);
  $dades = ConsumrealsController::consumdia($dia2,$mes2,$any2); 
  $ultim=Consumreal::all()->last();
  $dades['ultim']=$ultim->value / 1000;*/
  return View::make('consumreals.consumreal')->with(compact('dia','dades'));
});
Route::get('/show', function()
{
  return View::make('consumreals.show');
});
Route::resource('consumreals', 'ConsumrealsController');
Route::get('/consumdia/{dia}/{mes}/{any}', function($dia,$mes,$any)
{
 $dia = $any.'-'.$mes.'-'.$dia;
 return View::make('consumreals.consumdia')->with(compact('dia'));;
});


Route::group(array('prefix' => 'historic'), function()
{
  Route::get('/dia/{data?}', function($date = null)
  {
    $dades=HistorydaysController::getdaydata($date);
    return View::make('consumreals.history')->with(compact('dades'));
  });

  Route::get('/{mes?}', function($date=null)
  {
    $dades=HistorydaysController::historicmes($date); 
    return View::make('consumreals.historymes')->with(compact('dades'));
  });

});

/*
* Apartat de funcions que generen informació als serveis web
*/
Route::group(array('prefix' => 'json'), function()
{
  Route::get('/consumsreallast', function()
  {
    $dades=ConsumrealsController::consumsreallast(); 
    return Response::json($dades);
  });

  Route::get('/consumsrealsdia/{dia}/{mes}/{any}', function($dia,$mes,$any)
  {
    $dades=ConsumrealsController::consumdia($dia,$mes,$any); 
    return Response::json($dades);
  });

  Route::get('/consumsreal', function()
  {
   $dades=ConsumrealsController::consumsreal();
   return Response::json($dades);
 });
});



