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
	return View::make('hello');
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
    /*
    {
    "label": "Europe (EU27)",
    "data": [[1999, 3.0], [2000, 3.9]]
}

*/
	return Response::json($a);
});
