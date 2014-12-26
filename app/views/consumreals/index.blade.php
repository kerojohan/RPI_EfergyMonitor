@extends('layouts.master')

@section('title')
OpenEnergyMonitor
@stop

@section('content')

<h1>OpenEnergyMonitor</h1>

<hr/>

<hr/>

@if(Session::has('message'))
<div class="alert alert-success" style="display:block">
{{ Session::get('message')}}
</div>
@endif

<table class="table">
      <thead>
        <tr>
          <th>ID</th>
		  <th>dia</th>
		  <th>consums</th>
		  <th>pic</th>
        </tr>
      </thead>
      <tbody>
      @foreach($consumreals as $key =>$consum)
        <tr>

          <td>{{$consum->id}}</td>
          <td>{{$consum->day}}</td>
          <td>{{$consum->consum}}</td>
          <td>{{$consum->pic}}</td>
         
        </tr>
	@endforeach
      </tbody>
    </table>


@stop