@extends('layouts.login')

@section('title')
OpenEnergyMonitor
@stop

@section('content')
<div id="page-wrapper">
   @if(Session::has('message'))
   <div class="alert alert-success" style="display:block">
     {{ Session::get('message')}}
 </div>
 @endif
 <div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Històric ({{$mes}})</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
 <div class="row">
    <div class="col-lg-12">
<nav>
  <ul class="pagination">
    <li><a href="#"><span aria-hidden="true">&laquo; Mes anterior</span><span class="sr-only">Previous</span></a></li>
    <li><a href="#"><span aria-hidden="true">Mes posterior &raquo;</span><span class="sr-only">Next</span></a></li>
  </ul>
</nav>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-4 col-md-6">
        <div class="panel panel-green">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-money fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge" id="actualpreu">0</div>
                        <div>€</div>
                    </div>
                </div>
            </div>
            <a href="#">
                <div class="panel-footer">
                    <span class="pull-left">Cost mensual</span>
                    <!--<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>-->
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="panel panel-yellow">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-bar-chart fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge" id="consumdia">0</div>
                        <div>kW</div>
                    </div>
                </div>
            </div>
            <a href="#">
                <div class="panel-footer">
                    <span class="pull-left" >Consum total mensual</span>
                    <!--<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>-->
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="panel panel-red">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                    <i class="fa fa-line-chart fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge" id="pic">0</div>
                        <div>kWh</div>
                    </div>
                </div>
            </div>
            <a href="#">
                <div class="panel-footer">
                    <span class="pull-left">Pic consum mensual</span>
                    <!-- <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>-->
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> Gràfica diari
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body grafica-container">
                <div id="placeholder" class="demo-placeholder"></div>

            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>

    <!-- /.col-lg-4 -->
</div>
<!-- /.row -->



@stop


@section('javascript')

@stop