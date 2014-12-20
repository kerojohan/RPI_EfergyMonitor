<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>EnergyMonitor</title>
	<link href="js/flot/examples/examples.css" rel="stylesheet" type="text/css">
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="../../excanvas.min.js"></script><![endif]-->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script language="javascript" type="text/javascript" src="js/flot/jquery.flot.js"></script>
	<script language="javascript" type="text/javascript" src="js/flot/jquery.flot.time.js"></script>
	<script language="javascript" type="text/javascript" src="js/flot/examples/axes-time-zones/date.js"></script>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
	<style>
		@import url(//fonts.googleapis.com/css?family=Lato:700);

		body {
			margin:0;
			font-family:'Lato', sans-serif;
			text-align:center;
			color: #999;
		}

		.welcome {
			width: 300px;
			height: 200px;
			position: absolute;
			left: 50%;
			top: 10px;
			margin-left: -150px;
			margin-top: -100px;
			opacity: 0.9;
		}

		a, a:visited {
			text-decoration:none;
		}

		h1 {
			font-size: 32px;
			margin: 16px 0 0 0;
		}
		.demo-container{
			background-image: url("https://openclipart.org/image/300px/svg_to_png/202623/Green_Engery.png");
			background-repeat:no-repeat;
			background-position: center; 
		}
	</style>
	<script type="text/javascript">

		var	dataplot=[];
		var optionsplot ={
			xaxis: {
				mode: 'time',
				timezone: "Europe/Madrid",
						//timeformat: "%h%p",
						color: '#717073',
						min: (new Date()).getTime()-86400000,
						//min: (new Date()).getTime()-43200000,
						//min: (new Date()).getTime()-21600000,
						//min: (new Date()).getTime()-10800000,
						//min: (new Date()).getTime()-5400000,
						//min: (new Date()).getTime()-2700000,
						//min: (new Date()).getTime()-1350000,

						max: (new Date()).getTime()
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
						show: true
					}
				};

				function preudiari(){
					var date=new Date();
					$.getJSON('/energymonitor/public/consumsrealsdiajson/'+date.getDate()+'/'+(date.getMonth()+1)+'/'+date.getFullYear(), function(response) {
						preu=response.preu;
						consumdia=response.consum;
						$('#actualpreu').text(preu.toFixed(2)+"€");
						$('#consumdia').text(consumdia.toFixed(2)+"kW");
					});

					setTimeout(preudiari, 60000);
				}

				function fetchData(){
	//console.log("fetchData");
	var d = []; 

	$.getJSON('/energymonitor/public/consumsrealjsonlast', function(response) {
		d=response.data;

		if(d.length >0){
			dataplot=dataplot.concat([[((new Date()).getTime()),(d[d.length-1][1])]]);
			$('#actualconsum').text((d[d.length-1][1])/1000+"kWh");
			optionsplot.xaxis.min= (new Date()).getTime()-86400000,
	 //optionsplot.xaxis.min= (new Date()).getTime()-1350000,
	 optionsplot.xaxis.max = (new Date()).getTime();
	 $.plot("#placeholder", [dataplot], optionsplot);	
	//console.log("redibuixant");
}

});
/*
$.getJSON('/energymonitor/public/consumsrealjson/'+dataplot.length, function(response) {
	d=response.data;

if(d.length >0){
	//console.log(dataplot.length);
	dataplot=dataplot.concat(d);
	$('#actualconsum').text((d[d.length-1][1])/1000+"kWh");
	 optionsplot.xaxis.min= (new Date()).getTime()-86400000,
	 optionsplot.xaxis.max = (new Date()).getTime();
	$.plot("#placeholder", [dataplot], optionsplot);	
	//console.log("redibuixant");
}

});*/
setTimeout(fetchData, 10000);
}

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
			timezoneJS.timezone.zoneFileBasePath = "js/flot/examples/axes-time-zones/tz";
		timezoneJS.timezone.defaultZoneFile = [];
		timezoneJS.timezone.init({async: false});
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
	$.getJSON('/energymonitor/public/consumsrealjson', function(response) {
		dataplot=response.data;
				//console.log(d);
				$.plot("#placeholder", [dataplot], optionsplot);
			});

	preudiari();
	fetchData();
});


</script>

<script type="text/javascript">
	google.load("visualization", "1.1", {packages:["calendar"]});
	google.setOnLoadCallback(drawChart);

	function drawChart() {
		var dataTable = new google.visualization.DataTable();
		dataTable.addColumn({ type: 'date', id: 'Date' });
		dataTable.addColumn({ type: 'number', id: 'consum' });
		dataTable.addRows([
          // Many rows omitted for brevity.
          [ new Date(), 400 ],
          [ new Date(2014, 9, 7), 200 ]

          ]);

		var chart = new google.visualization.Calendar(document.getElementById('calendar_basic'));

		var options = {
			title: "History",
			height: 350,
		};
		   function selectHandler() {
          var selectedItem = chart.getSelection()[0];
          if (selectedItem.row) {
            var topping = dataTable.getValue(selectedItem.row, 0);
            alert('The user selected ' + topping);
          }
        }
  google.visualization.events.addListener(chart, 'select', selectHandler);

		chart.draw(dataTable, options);
	}


</script>
</head>
<body>


	<div id="header">
		<h2>EnergyMonitor</h2>
	</div>

	<div id="content">

		<div class="row">


			<div  class="col-md-4 well">		
				<p>Consum actual</p>
				<h1 id="actualconsum">0kWh</h1>
			</div>

			<div class="col-md-4 well">
				<p>Cost {{$dia}}</p>
				<h1 id="actualpreu">0€</h1>
			</div>
			<div class="col-md-4 well">
				<p>Consum dia {{$dia}}</p>
				<h1 id="consumdia">0kW</h1>
			</div>
		</div>

   		<!--<div class="welcome">
		<img src="https://openclipart.org/image/300px/svg_to_png/202623/Green_Engery.png"/>
	</div>-->
	<div >
		<div class="demo-container">
			<div id="placeholder" class="demo-placeholder"></div>
		</div>

	</div>
	<div class="row">
		<div id="calendar_basic" style="width:1000px;height: 175px;"></div>
	</div>
</div>


</body>
</html>
