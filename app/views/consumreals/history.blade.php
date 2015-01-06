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
        <h1 class="page-header">Històric ({{$dades['dia']}}) <button onclick="javascript:window.location.href='javascript:history.back()'" type="button" class="btn btn-primary pull-right" > Tornar</button></h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel-body grafica-container">

            <div id="placeholder" class="demo-placeholder"><img style="top:50%;position:relative;left:50%" src="{{asset('img/ajax-loader.gif')}}"></div>

        </div>
    </div>
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
                        <div class="huge" id="actualpreu">{{round($dades['preu'],2)}}</div>
                        <div>€</div>
                    </div>
                </div>
            </div>
            <a href="#">
                <div class="panel-footer">
                    <span class="pull-left">Cost dia</span>
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
                        <div class="huge" id="consumdia">{{round($dades['consum'],2)}}</div>
                        <div>kW</div>
                    </div>
                </div>
            </div>
            <a href="#">
                <div class="panel-footer">
                    <span class="pull-left" >Consum dia</span>
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
                        <div class="huge" id="pic">{{round($dades['pic'],3)}}</div>
                        <div>kWh</div>
                    </div>
                </div>
            </div>
            <a href="#">
                <div class="panel-footer">
                    <span class="pull-left">Pic consum dia</span>
                    <!-- <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>-->
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
</div>
<!-- /.row -->
<!-- <div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> Gràfica dia
            </div>
            <div class="panel-body grafica-container">
                <div id="placeholder" class="demo-placeholder"></div>
            </div>
        </div>
    </div>

</div>
 -->



@stop

@section('javascript')
<script type="text/javascript">
            function formatDate(d) {
                var ss = d.getSeconds()
                if ( ss < 10 ) ss = '0' + ss

                    var hh = d.getHours()
                if ( hh < 10 ) hh = '0' + hh

                    var min = d.getMinutes()
                if ( min < 10 ) min = '0' + min

                    return hh+':'+min+':'+ss;
            }

            $(function() {
        timezoneJS.timezone.zoneFileBasePath = "{{  URL::asset('js/flot/examples/axes-time-zones/tz')}}";
        timezoneJS.timezone.defaultZoneFile = [];
        timezoneJS.timezone.init({async: false});
        var data=new Date('{{$dades["dia"]}} 00:00:00');
                var dataplot=[];
                var optionsplot ={
                    xaxis: {
                        mode: 'time',
                        //timeformat: "%h%p",
                timezone: "Europe/Madrid",
                        color: '#717073',
                        min: data.getTime(),
                        max: data.getTime()+86400000
                    },
                    yaxis: {
                        label: 'kW',
                        ticks: 10,
                        min: 0,
                        color: 'rgb(192, 192, 192)',
                        axisMargin: 10,
                        autoscaleMargin: 0.05
                    },
                    grid: {
                        borderWidth: 2,
                        borderColor: 'rgb(192, 192, 192)',
                        //backgroundColor: { colors: ["#f7f5f2", "#f7f5f2"] },
                        clickable: true,
                        hoverable: true
                    },
                    legend : {
                        show: true
                    }
                };

                $("<div id='tooltip'></div>").css({
                    position: "absolute",
                    display: "none",
                    border: "1px solid #fdd",
                    padding: "2px",
                    "background-color": "#fee",
                    opacity: 0.80
                }).appendTo("body");
                $("#placeholder").bind("plothover", function (event, pos, item) {
                    var str = "(" + pos.x.toFixed(2) + ", " + (pos.y.toFixed(2))/1000 + "KWh)";
                    if (item) {
                        var x = formatDate((new Date((item.datapoint[0])))),
                        y = item.datapoint[1].toFixed(2)/1000+"KWh";
                        $("#tooltip").html(x + "<br>" + y)
                        .css({top: item.pageY+5, left: item.pageX+5})
                        .fadeIn(200);
                    } else {
                        $("#tooltip").hide();
                    }   
                });

                $.plot("#placeholder", [{{$dades['data']}}], optionsplot);
          

            });
            </script>
@stop