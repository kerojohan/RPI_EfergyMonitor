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
           <!--     <button  type="button" class="btn btn-default pull-right" > Anual</button>
            <button  type="button" class="btn btn-default pull-right" > Mensual</button>
            <button  type="button" class="btn btn-primary pull-right" > Diari</button>
        -->


        <h1 class="page-header">Històric</h1>
 
      <!--  <ul class="pagination pull-right">
            <li><a href="#"><span aria-hidden="true">&laquo; Mes anterior</span><span class="sr-only">Previous</span></a></li>
            <li><a href="#"><span aria-hidden="true">Mes posterior &raquo;</span><span class="sr-only">Next</span></a></li>
        </ul>-->
    </div>
    <!-- /.col-lg-12 -->
</div>
<div class="row">
    <div class="col-lg-12">
    <div class="btn-toolbar" role="toolbar" id="timesel">
          <div class="btn-group">
            <!--<a type="button" class="btn btn-default" aria-label="Left Align" href="{{URL::to('/historic/any')}}"><strong>Any actual</strong></a>-->
            @if($dades['anyanterior']!=null)   
            <a type="button" class="btn btn-default" aria-label="Center Align" href="{{URL::to('/historic/any/'.$dades['anyanterior'])}}"><strong>{{$dades['anyanterior']}}</strong></a>
            @endif
            @if($dades['anyposterior']!=null) 
            <a type="button" class="btn btn-default" aria-label="Right Align" href="{{URL::to('/historic/any/'.$dades['anyposterior'])}}"><strong>{{$dades['anyposterior']}}</strong></a>
            @endif
            <a type="button" class="btn btn-default" aria-label="Justify" href="{{URL::to('/historic/12months')}}"><strong>Últims 12 mesos</strong></a>
        </div>
    </div>
    </div>
</div>

    <div class="row">
    <div class="col-lg-12">
    <!--           <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>-->
    <div id="title" style=" clear: both;
    text-align: center; margin-top:20px"><strong>{{$dades['anyactual']}}</strong></div>
    <div id="placeholder" style="min-width: 310px; height: 300px; margin: 0 auto; margin-bottom: 25px;" ></div>
    <div id="chartLegend"></div>
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
                    <span class="pull-left">Cost mes</span>
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
                        <div class="huge" id="consummes">{{round($dades['consumtotal'],2)}}</div>
                        <div>kW</div>
                    </div>
                </div>
            </div>
            <a href="#">
                <div class="panel-footer">
                    <span class="pull-left" >Consum mes</span>
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
                        <div class="huge" id="pic">{{round($dades['pic'],2)}}</div>
                        <div>kWh</div>
                    </div>
                </div>
            </div>
            <a href="#">
                <div class="panel-footer">
                    <span class="pull-left">Pic consum mes</span>
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
        <table class="table">
          <thead>
            <tr>
              <th>Dia</th>
              <th>Consum</th>
              <th>Pic</th>
          </tr>
      </thead>
      <tbody>
          @foreach($dades['consumreals'] as $key =>$consum)
          <tr>
              <td><a href="{{ URL::to('historic/mes/'. date('Y-m',strtotime($consum->month))) }}">{{date('Y-m',strtotime($consum->month))}}</a></td>
              <td>{{$consum->consum}} kW</td>
              <td>{{$consum->pic}} KWh</td>
          </tr>
          @endforeach
                   @if(isset($dades['avui']['mes']))
            <tr>
              <td><a href="{{ URL::to('/')}}">{{date('Y-m',$dades['avui']['mes']/1000)}}</a></td>
              <td>{{round($dades['avui']['consum']/1000,2)}} kW</td>
              <td>{{round($dades['avui']['pic'],2)}} kWh</td>
          </tr>
          @endif
      </tbody>
  </table>
</div>
</div>

@stop

@section('javascript')
<script type="text/javascript">
    function formatDate(d) {
            var hh = d.getMonth() +1;
        if ( hh < 10 ) hh = '0' + hh

            var min = d.getFullYear();

        return min+'-'+hh;
    }

    $(function() {
        timezoneJS.timezone.zoneFileBasePath = "{{  URL::asset('js/flot/examples/axes-time-zones/tz')}}";
        timezoneJS.timezone.defaultZoneFile = [];
        timezoneJS.timezone.init({async: false});
        var data=new Date("{{$dades['from']}}");
        var data2 = new Date(new Date(data).setMonth(data.getMonth()+11));
               // data2.setDate(data2.getDate()-1);
               var dataplot=[];
               var optionsplot ={

                xaxis: {

                    mode: 'time',
                        //timeformat: "%h%p",
                        timezone: "Europe/Madrid",
                       // color: '#717073',
                        min: data.getTime()-3600*12*1000*29,
                        max: data2.getTime()+3600*12*1000*29,
                         tickColor: "#fff",
                        autoscaleMargin: 0.05
                    },
                    yaxis: {
                        label: 'kW',
                        ticks: 10,
                        min: 0,
                        color: 'rgb(192, 192, 192);',
                        axisMargin: 10,
                        autoscaleMargin: 0.05
                    },
                    grid: {
                        borderWidth: 0.1,
                        borderColor: '#fff',
                        color: 'rgb(192, 192, 192);',
                        //backgroundColor: { colors: ["#f7f5f2", "#f7f5f2"] },
                        clickable: true,
                        hoverable: true
                    },
                    legend : {
                        show: true,

                    }
                };

                $("<div id='tooltip'></div>").css({
                    position: "absolute",
                    display: "none",
                    border: "1px solid #fdd",
                    padding: "2px",
                    "background-color": "#fee",
                    opacity: 0.75
                }).appendTo("body");
                $("#placeholder").bind("plothover", function (event, pos, item) {
                    var str = "(" + pos.x.toFixed(0) + ", " + (pos.y.toFixed(0)*1000) + " Wh)";
                    if (item) {
                        var x = formatDate((new Date((item.datapoint[0])))),
                        y = (item.datapoint[1]).toFixed(0)+" Wh";
                        $("#tooltip").html(x + "<br>" + y)
                        .css({top: item.pageY+5, left: item.pageX+5})
                        .fadeIn(200);
                    } else {
                        $("#tooltip").hide();
                    }   
                });

                $("#placeholder").bind("plotclick", function (event, pos, item) {
                  if (item) {
                   window.location.href = "/energymonitor/public/historic/mes/"+formatDate((new Date((item.datapoint[0]))));
               }
           });
                $.plot("#placeholder", [ {
                    label:"Consum",
                    data: {{$dades['data']}},
                    color:'#7cb5ec',
                    stack: 1,
                    bars: {
                        show: true,
                        barWidth: 1000 * 60 * 60 * 24 *29*0.8,
                        align: "center",
                        fill:1
                    }

                }
                   @if(isset($dades['avui']['mes']))
                ,{
                    data:[[{{$dades['avui']['mes']}},{{$dades['avui']['consum']}}]],
                    stack: 1,
                    color:'rgb(218, 233, 244)',
                    bars: {
                        show: true,
                        barWidth: 1000 * 60 * 60 * 24 *29 *0.8,
                        align: "center",
                        fill:1,
                    },
                    clickable: true,

                }
                    @endif
                ,{
                    color:'rgb(217, 83, 79)',
                    label: "Màxima petició de consum (pic)",
                    color:"rgb(217, 83, 79)",
                    data: {{$dades['pics']}},
                    clickable: false,
                    splines: {
                        show: true,
                        tension: 0.3,
                        lineWidth: 3,
                        fill: false 
                    },
                        points: {
                        radius: 3,
                        show: true,
                        fill: true,
                        fillColor:'rgb(217, 83, 79)',
                    },
                }], optionsplot);


});
</script>
@stop