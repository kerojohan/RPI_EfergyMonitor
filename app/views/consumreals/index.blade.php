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
		  <th>date</th>
		  <th>timestamp</th>
		  <th>value</th>
        </tr>
      </thead>
      <tbody>
      @foreach($consumreals as $key =>$consum)
        <tr>

          <td>{{$consum->id}}</td>
          <td>{{$consum->date}}</td>
          <td>{{$consum->timestamp}}</td>
          <td>{{$consum->value}}</td>
         
        </tr>
	@endforeach
      </tbody>
    </table>


@stop