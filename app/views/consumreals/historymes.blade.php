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
                <button  type="button" class="btn btn-default pull-right" > Anual</button>
            <button  type="button" class="btn btn-default pull-right" > Mensual</button>
            <button  type="button" class="btn btn-primary pull-right" > Diari</button>
        <h1 class="page-header">Històric ({{$mes}})</h1>
      <!--  <ul class="pagination pull-right">
            <li><a href="#"><span aria-hidden="true">&laquo; Mes anterior</span><span class="sr-only">Previous</span></a></li>
            <li><a href="#"><span aria-hidden="true">Mes posterior &raquo;</span><span class="sr-only">Next</span></a></li>
        </ul>-->
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
                        <div class="huge" id="actualpreu">{{round($preu,2)}}</div>
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
                        <div class="huge" id="consummes">{{$consumtotal}}</div>
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
                        <div class="huge" id="pic">{{$pic}}</div>
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
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> Gràfica mes
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body grafica-container">
                <div id="placeholder" class="demo-placeholder"></div>
                <div id="chartLegend"></div>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>

    <!-- /.col-lg-4 -->
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
          @foreach($consumreals as $key =>$consum)
          <tr>
              <td>{{$consum->day}}</td>
              <td>{{$consum->consum}}</td>
              <td>{{$consum->pic}}</td>

          </tr>
          @endforeach
      </tbody>
  </table>
</div>
</div>

@stop

@section('javascript')
<script type="text/javascript">
    function formatDate(d) {
        var ss = d.getDate()
        if ( ss < 10 ) ss = '0' + ss

            var hh = d.getMonth() +1;
        if ( hh < 10 ) hh = '0' + hh

            var min = d.getFullYear();

        return min+'-'+hh+'-'+ss;
    }

    $(function() {
        timezoneJS.timezone.zoneFileBasePath = "{{  URL::asset('js/flot/examples/axes-time-zones/tz')}}";
        timezoneJS.timezone.defaultZoneFile = [];
        timezoneJS.timezone.init({async: false});
        var data=new Date('{{$mes}}-01 00:00:00');
        var dataplot=[];
        var optionsplot ={

            xaxis: {
                mode: 'time',
                        //timeformat: "%h%p",
                        timezone: "Europe/Madrid",
                        color: '#717073',
                        min: data.getTime(),
                       // max: data.getTime()+86400000
                   },
                   yaxis: {
                    label: 'kW',
                    ticks: 10,
                    min: 0,
                    color: '#717073'
                },
                grid: {
                    borderWidth: 2,
                    borderColor: '#717073',
                    color: '#c9cbcc',
                    backgroundColor: { colors: ["#f7f5f2", "#f7f5f2"] },
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
                opacity: 0.80
            }).appendTo("body");
            $("#placeholder").bind("plothover", function (event, pos, item) {
                var str = "(" + pos.x.toFixed(2) + ", " + (pos.y.toFixed(2)) + "KWh)";
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

            $("#placeholder").bind("plotclick", function (event, pos, item) {
              if (item) {
                 window.location.href = "/energymonitor/public/historic/dia/"+formatDate((new Date((item.datapoint[0]))));
             }
         });

            $.plot("#placeholder", [ {
                label:"Consum",
                data: {{$data}},
                bars: {
                    show: true,
                    barWidth: 1000 * 60 * 60 * 24 *0.8,
                    align: "center",
                }

            },{
                label: "Màxima petició de consum (pic)",
                color:"red",
                data: {{$pics}},
                lines: { show: true, fill: false }
            }], optionsplot);


        });
</script>
@stop