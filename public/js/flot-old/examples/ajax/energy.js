var G_vmlCanvasManager;
var day_array = new Array('day.abbr.sunday','day.abbr.monday','day.abbr.tuesday','day.abbr.wednesday','day.abbr.thursday','day.abbr.friday','day.abbr.saturday');
var month_array = new Array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
var month_abbr_array = new Array('month.abbr.jan', 'month.abbr.feb', 'month.abbr.mar', 'month.abbr.apr', 'month.abbr.may', 'month.abbr.jun', 'month.abbr.jul', 'month.abbr.aug', 'month.abbr.sep', 'month.abbr.oct', 'month.abbr.nov', 'month.abbr.dec');
var season_array = new Array('#f7f5f2', '#f7f5f2', '#f7f5f2', '#d3e9b5', '#d3e9b5', '#d3e9b5', '#ffed99', '#ffed99', '#ffed99', '#fbc7b3', '#fbc7b3', '#fbc7b3');
var period = 'day';
var units = 'kwh';

// DATA HANDLING AND MANIPULATION

function pad(number,length) {
    var str = '' + number;
    while (str.length < length)
        str = '0' + str;
    return str;
}

function formatPower(v) { 
  if (v >= 1000)
		return (v / 1000) + "kW";
	else
		return v + "W";
}

function parse_date_data(dataset, entry, period) {
	var resultset = [];
	var last_val = 0;
	var fill_count = 0;
	for(key in dataset) {
		var row = dataset[key];
		
		if (period === 'day' && row[entry] === 0 && fill_count < 10) {
			resultset.push( [parseInt(key), last_val] );
			fill_count++;
		} else {
			if(row[entry] == "undef") {
				row = null;
				resultset.push( [parseInt(key), 0] );
			} else {
				fill_count = 0;
				last_val = row[entry];
				resultset.push( [parseInt(key), row[entry]] );
			}
		}
	}
	return resultset;
}

function average(dataset, numpoints, skip_nulls) {
	var output = [];
	var avg_points = Math.floor(dataset.length / numpoints);
	if(numpoints >=  dataset.length) {
		return dataset;
	}
	var avg_c = 0, avg_x = 0, avg_y = 0, count = 0;
	output.push(dataset[0]);
	for(row in dataset) {
		count++;
		data = dataset[row];
		if(data[1] == null) {
			if(skip_nulls) {
				continue;
			}
			if(avg_c >= 1) {
				output.push([(avg_x / avg_c), (avg_y / avg_c)]);
			}
			avg_c = 0;
			avg_x = 0;
			avg_y = 0;
			output.push(data);
		} else {
			avg_x += data[0];
			avg_y += data[1];
			if(++avg_c == avg_points || count == dataset.length) {
				output.push([(avg_x / avg_c), (avg_y / avg_c)]);
				avg_c = 0;
				avg_x = 0;
				avg_y = 0;
			}
		}
	}
	output.push(dataset[dataset.length - 1]);
	return output;
}

function rolling_mean(dataset, rolling_length) {
	var rolling_stack = [];
	var count = 0;
	for(row in dataset) {
		var data = dataset[row];
		var timestamp = data[0];
		var value = data[1];
		if(value != null) {
			rolling_stack.push(parseFloat(value));
			if(rolling_stack.length > rolling_length) {
				rolling_stack.shift();
			}
			dataset[row][1] = function(r_stack) {
				var avg = 0;
				var stack_length = r_stack.length;
				for(entry in r_stack) {
					avg += r_stack[entry];
				}
				return avg / stack_length;
			}(rolling_stack)
		}
	}
	return dataset;
}

// EASING ALGO

function easeInOutCirc (t, b, c, d) {
	if ((t/=d/2) < 1) return -c/2 * (Math.sqrt(1 - t*t) - 1) + b;
	return c/2 * (Math.sqrt(1 - (t-=2)*t) + 1) + b;
}

// GENERAL SETTINGS

var general_graph_settings = {
	xaxis: {
		mode: 'time',
		timeformat: "%h%p",
		color: '#717073'
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

var graph_24_settings = jQuery.extend(true, {}, general_graph_settings);
graph_24_settings.xaxis.tickSize = [1,'hour'];
graph_24_settings.xaxis.label = lang['hours'];
graph_24_settings.xaxis.tickFormatter = function (val, axis) { 
  h = new Date(val).getUTCHours(); 
  if (h%2==0) { 
    return h<10?'0'+h+':00':h+':00';
  } else { 
    return '';
  }
};
graph_24_settings.series = {
  stack: false,
  lines: { lineWidth: 1, show: true, fill: false },
  shadowSize: 0
};

/*
graph_24_settings.grid.markings = function (axes) {
  var markings = [];
  var d = new Date(axes.xaxis.min);
  d.setHours(0);
  d.setMinutes(0);
  d.setSeconds(0);

  var start_time = d.getTime();

  while(start_time < axes.xaxis.max) {
    markings.push({ xaxis: { from: start_time - (1000 * 60 * 60 * 5), to: start_time + (1000 * 60 * 60 * 6) } , color: "#dfdfde"});
    start_time += (1000 * 60 * 60 * 24);
	}
  return markings;
};
*/

var graph_month_settings = jQuery.extend(true, {}, general_graph_settings);
graph_month_settings.xaxis.tickSize = [1,'day'];
graph_month_settings.xaxis.label = lang['days'];
graph_month_settings.xaxis.tickFormatter = function (val, axis) { 
  return lang[day_array[new Date(val).getUTCDay()]];
};


//graph_month_settings.series = {
//                stack: false,
//                lines: { show: false },
//                bars: { show: false, barWidth: 1, steps: 24 }
//            };
graph_month_settings.series = {
                stack: false
            };

var graph_year_settings = jQuery.extend(true, {}, general_graph_settings);
graph_year_settings.xaxis.ticks = 12;
graph_year_settings.xaxis.label = lang['months'];
graph_year_settings.xaxis.tickFormatter = function (val, axis) { 
  return lang[month_abbr_array[new Date(val).getUTCMonth()]];
};


function animate_value(object, start, end, current_frame, total_frames, delay) {
	$(object).text('£'+(easeInOutCirc (current_frame, start, (end - start), total_frames)).toFixed(2));
	if(current_frame != total_frames) {
		setTimeout('animate_value("' + object +'",' + start +', ' + end +', ' + (++current_frame) +', ' + total_frames +', ' + delay +')', delay);
	}

}

var offset = new Date().getTimezoneOffset();
var period_settings = {
	day: {
		action : 'getDay?getPreviousPeriod=0&cache=false&offset='+offset,
		settings: graph_24_settings
	},
	month: {
		action : 'getMonth?getPreviousPeriod=0&offset='+offset,
		settings: graph_month_settings
	},
	year: {
		action : 'getYear?getPreviousPeriod=0&offset='+offset,
		settings: graph_year_settings
	}
}

function initialise_instant_view() {
	if(typeof global_settings.instant_view == 'undefined' || global_settings.instant_view == 'dial' || global_settings.instant_view == '') {
		instant_reading = dial.set_element('#instant_usage').setup().set_values().render();
	} else if(global_settings.instant_view == 'meter') {
		instant_reading = meter.set_element('#instant_usage').setup().set_values().render();
	} else if(global_settings.instant_view == 'ibars') {
		instant_reading = ibars.set_element('#instant_usage').setup().set_values();
	} else if(global_settings.instant_view == 'arrow') {
		instant_reading = arrow.set_element('#instant_usage').setup().set_values();
	}

	$('#instant_usage').fadeIn();
}

function showTooltip(x, y, contents) {
	$('<div id="tooltip">' + contents + '</div>').css( {
		position: 'absolute',
		display: 'none',
		'z-index': 4000,
		top: y - 60,
		left: x ,
		border: '1px solid #f8971d',
		padding: '5px 10px',
		'font-size': '16px',
		'line-height': '20px',
		'background-color': '#fff',
		'-webkit-box-shadow': '-2px 3px 5px rgba(50, 50, 50, 0.75)',
    '-moz-box-shadow':    '-2px 3px 5px rgba(50, 50, 50, 0.75)',
    'box-shadow':         '-2px 3px 5px rgba(50, 50, 50, 0.75)'
	}).appendTo("body").fadeIn(200);
}

function doGraph() {
  var settings = period_settings[period];
	$("#demand_graph").html('');
	$.getJSON('/proxy/' + settings.action, function(response) {
		var local_settings = settings;
		if(response.status == 'ok') {
			var current_data = parse_date_data(response.data, 0, period);

			if(period == 'day') {
			  last_ts = ''
		    for(key in current_data) {
		      last_ts = current_data[key][0];
		    }
			  $.plot($("#demand_graph"), [{data:current_data,color:'#8CC63F'}] , local_settings.settings);
        d2 = new Date(last_ts*1);
        date2_str = lang[month_array[d2.getUTCMonth()]]+' '+d2.getUTCDate()+' '+d2.getUTCFullYear();
        $('.current_date').html(lang['today'] + ' - ' + date2_str);
				$('h2.bar').html(lang['energy.demand']);
				$('#yaxis1').html('kW');
				$('#xaxis').html(lang['hours']);
				$('.kwh-button').html('kW');
				$('#yaxis2').html(lang['now']);
				$('.graph-key').hide();
				$('#key-24h').show(); 
				$('.graph-sums').hide();
			}
			if(period == 'month') {
			  first_ts = '';
			  last_ts = '';
			  max_key = '';
			  max_val = 0;
			  min_val = 0;
			  min_key = '';
			  avg_val = 0;
			  running_total = 0;
			  num_points = 0; 
			  
			  if (units == 'cost') {
			    $('#yaxis1').html(global_settings.cur_major);
			    var multiplier = global_settings.cost_per_kwh / 100;
			  } else if (units == 'co2') {
			    $('#yaxis1').html('kg<br/>CO<sub>2</sub>');
			    var multiplier = 0.5;
			  } else {
			    $('#yaxis1').html('kWh');
			    var multiplier = 1;
			  }

			  
			  for(key in current_data) {
	        var row = current_data[key];
	        if(first_ts == '') {
	          first_ts = row[0];
	        }
	        last_ts = row[0];
	        if(row[1] != null && row[1] != 0) {
            num_points++;
            row[1] = row[1] * multiplier;
            if (min_val == 0 || row[1] < min_val) {
              min_val = row[1];
              min_key = key;
            }
            if (max_val == 0 || row[1] > max_val) {
              max_val = row[1];
              max_key = key;
            }
            
            running_total = running_total * 1 + row[1];          
            current_data[key] = row; 
	        }
	      }
	      avg_val = running_total / num_points;
        regular_data = [ ];
        min_data = [ ];
        max_data = [ ];

        for(key in current_data) {
          if (key == max_key) {
            max_data[key] = current_data[key];
          } else if (key == min_key){
            min_data[key] = current_data[key];
          } else {
            regular_data[key] = current_data[key];
          }
        }
        avg_data = [[first_ts - (12 * 60 * 60 * 1000), avg_val], [last_ts + (12 * 60 * 60 * 1000), avg_val]];
      
				plots = [
			    {data:avg_data, lines: { lineWidth: 1, show: true, fill: false  },shadowSize: 0, color:'#717073', yaxis: 2},
			    {data:regular_data, bars: {align: 'center', barWidth: (24 * 52 * 60 * 1000), show: true,	lineWidth: 0,	fill: true, fillColor: {colors: ["#717073", "#717073"]},	align: "center"	}, color:'#717073'},
			    {data:min_data, bars: {align: 'center', barWidth: (24 * 52 * 60 * 1000), show: true,	lineWidth: 0,	fill: true, fillColor: {colors: ["#8cc63f", "#8cc63f"]},	align: "center"	}, color:'#8cc63f'},
			    {data:max_data, bars: {align: 'center', barWidth: (24 * 52 * 60 * 1000), show: true,	lineWidth: 0,	fill: true, fillColor: {colors: ["#f25d23", "#f25d23"]},	align: "center"	}, color:'#f25d23'}
			  ];
			  
			  if (units == 'cost') {
			    $('#yaxis1').html(global_settings.cur_major);
			    var multiplier = global_settings.cost_per_kwh / 100;
			  } else if (units == 'co2') {
			    $('#yaxis1').html('kg<br/>CO<sub>2</sub>');
			    var multiplier = 0.5;
			  } else {
			    $('#yaxis1').html('kWh');
			    var multiplier = 1;
			  }
			  
			  graph_month_settings.yaxes = [{label: 'kW',	ticks: 10,min: 0, max: max_val * 1.1,	color: '#717073'}, {
  min: 0, max: max_val * 1.1, position: "right", ticks: [[avg_val, 'Avg']]}];
  
				$.plot($("#demand_graph"), plots, local_settings.settings);
          d1 = new Date(first_ts*1);
        d2 = new Date(last_ts*1);
        date1_str = lang[month_array[d1.getUTCMonth()]]+' '+d1.getUTCDate()+' '+d1.getUTCFullYear();
        date2_str = lang[month_array[d2.getUTCMonth()]]+' '+d2.getUTCDate()+' '+d2.getUTCFullYear();
        $('.current_date').html(date1_str + ' - ' + date2_str);
				$('h2.bar').html(lang['history.usage']);
				$('.kwh-button').html('kWh');
				$('#yaxis2').html('');
				$('.graph-key').hide();
				$('#key-month').show();
				$('.graph-sums').show();
				$('#xaxis').html('');
			}
			if(period == 'year') {
			  $('h2.bar').html(lang['history.usage']);
				first_ts = '';
			  last_ts = '';
			  max_key = '';
			  max_val = 0;
			  min_val = 0;
			  min_key = '';
			  avg_val = 0;
			  running_total = 0;
			  num_points = 0; 
			  
			  if (units == 'cost') {
			    $('#yaxis1').html(global_settings.cur_major);
			    var multiplier = global_settings.cost_per_kwh / 100;
			  } else if (units == 'co2') {
			    $('#yaxis1').html('kg<br/>CO<sub>2</sub>');
			    var multiplier = 0.5;
			  } else {
			    $('#yaxis1').html('kWh');
			    var multiplier = 1;
			  }
			  
			  var markings = [];
			  prev_ts=  0;			  
			  
			  for(key in current_data) {
	        var row = current_data[key];
	        if(first_ts == '') {
	          first_ts = row[0];
	        }
	        last_ts = row[0];
	        
	        if (prev_ts == 0) {
	          prev_ts = row[0] - (16 * 24 *60 *60 *1000);
	        } 
	    //    month = new Date(prev_ts).getUTCMonth();
	    //    markings.push({ xaxis: { from: prev_ts, to: last_ts } , color: season_array[month]});
	        prev_ts = row[0];
	        
	     /*   row[1] = 10000 * multiplier;
            running_total = running_total * 1 + row[1];          
            current_data[key] = row; */
	        if(row[1] != null && row[1] != 0) {
            num_points++;
            row[1] = row[1] * multiplier;
            if (min_val == 0 || row[1] < min_val) {
              min_val = row[1];
              min_key = key;
            }
            if (max_val == 0 || row[1] > max_val) {
              max_val = row[1];
              max_key = key;
            }
            running_total = running_total * 1 + row[1];          
            current_data[key] = row; 
	        }
	      }
	      avg_val = running_total / num_points;
        regular_data = [ ];
        min_data = [ ];
        max_data = [ ];

        for(key in current_data) {
          if (key == max_key) {
            max_data[key] = current_data[key];
          } else if (key == min_key){
            min_data[key] = current_data[key];
          } else {
            regular_data[key] = current_data[key];
          }
        }
        avg_data = [[first_ts - (16 * 24 * 60 * 60 * 1000), avg_val], [last_ts + (16 * 24 * 60 * 60 * 1000), avg_val]];  
        month = new Date(last_ts + (16 * 24 * 60 * 60 * 1000)).getUTCMonth();
	    //  markings.push({ xaxis: { from: prev_ts, to: last_ts + (16 * 24 * 60 * 60 * 1000) } , color: season_array[month]});
        
				plots = [
			    {data:avg_data, lines: { lineWidth: 1, show: true, fill: false  },shadowSize: 0, color:'#717073', yaxis: 2},
			    {data:regular_data, bars: {align: 'center', barWidth: (30 * 24 * 52 * 60 * 1000), show: true,	lineWidth: 0,	fill: true, fillColor: {colors: ["#717073", "#717073"]},	align: "center"	}, color:'#717073'},
			    {data:min_data, bars: {align: 'center', barWidth: (30 * 24 * 52 * 60 * 1000), show: true,	lineWidth: 0,	fill: true, fillColor: {colors: ["#8cc63f", "#8cc63f"]},	align: "center"	}, color:'#8cc63f'},
			    {data:max_data, bars: {align: 'center', barWidth: (30 * 24 * 52 * 60 * 1000), show: true,	lineWidth: 0,	fill: true, fillColor: {colors: ["#f25d23", "#f25d23"]},	align: "center"	}, color:'#f25d23'}
			  ];
			  graph_year_settings.yaxes = [{label: 'kW',	ticks: 10,min: 0, max: max_val * 1.1,	color: '#717073'}, {
  min: 0, max: max_val * 1.1, position: "right", ticks: [[avg_val, 'Avg']]}];
        graph_year_settings.grid.markings = markings;
				$.plot($("#demand_graph"), plots, local_settings.settings);
        d1 = new Date(first_ts*1);
        d2 = new Date(last_ts*1);
        date1_str = lang[month_array[d1.getUTCMonth()]]+' '+d1.getUTCFullYear();
        date2_str = lang[month_array[d2.getUTCMonth()]]+' '+d2.getUTCFullYear();
        $('.current_date').html(date1_str + ' - ' + date2_str);
				$('h2.bar').html(lang['history.usage']);
				$('.kwh-button').html('kWh');
				$('#yaxis2').html('');
				$('.graph-key').hide();
				$('#key-year').show();
				$('.graph-sums').show();
				$('#xaxis').html('');
			}
		}
	});
}

// GENERAL DOC READY INITIALISATIONS

$(document).ready( function () {

  $(".datepicker").datepicker({dateFormat: "dd/mm/yy" });

	$('.dynamic_multi_select').each(function (i, e) {		setup_dynamic_select($ .attr('id'));});

  $("#report-link").attr("href", "/reports");

	$('a[rel="external"]').click(function(e){
		e.preventDefault();
		window.open(this.href, 'picture');
	});

	$('.action_set_time_period').click( function(e) {
		e.preventDefault();
		period = $(this).data('time-period');
		$(this).addClass('active').siblings().removeClass('active');
		doGraph();
	});
	
	$('.action_set_units').click( function(e) {
		e.preventDefault();
		units = $(this).data('unit');
		$(this).addClass('active').siblings().removeClass('active');
		doGraph();
	});
	
	$('.flag-sel').hover(
	  function() {
	  $('.selected-country').html($(this).find('span').text());
	},
	  function() {
	    $('.selected-country').html('&nbsp;');
	});
	
	var previousPoint = null;

	$("#demand_graph").bind("plothover", function (event, pos, item) {
		if (item) {
			if (previousPoint != item.datapoint) {
				previousPoint = item.datapoint;
				$("#tooltip").remove();
	 			var x = item.datapoint[0],
				y = item.datapoint[1];
				var myDate = new Date(x);
				if (units == 'cost') {
          if (period == 'month') {
				    showTooltip(item.pageX, item.pageY, "<span class='pu-date'>"+lang[month_array[myDate.getUTCMonth()]] + " " + myDate.getUTCDate() + "</span><br/>" + global_settings.cur_prefix + y.toFixed(2)+ global_settings.cur_suffix);
				  } else if (period == 'year') {
				    showTooltip(item.pageX, item.pageY, "<span class='pu-date'>"+lang[month_array[myDate.getUTCMonth()]] + "</span><br/>" + global_settings.cur_prefix + y.toFixed(2)+ global_settings.cur_suffix);
				  }
			  } else if (units == 'co2') {
          if (period == 'month') {
				    showTooltip(item.pageX, item.pageY, "<span class='pu-date'>"+lang[month_array[myDate.getUTCMonth()]] + " " + myDate.getUTCDate() + "</span><br/>" + y.toFixed(2) + 'kg/CO2');
				  } else if (period == 'year') {
				    showTooltip(item.pageX, item.pageY, "<span class='pu-date'>"+lang[month_array[myDate.getUTCMonth()]] + "</span><br/>" + y.toFixed(2) + 'kg/CO2');
				  }
			  } else {
          if (period == 'day') {
				    showTooltip(item.pageX, item.pageY, "<span class='pu-date'>"+pad(myDate.getUTCHours(),2) + ":" + pad(myDate.getUTCMinutes(), 2) + "</span><br/>" + y.toFixed(2) + 'kW');
				  } else if (period == 'month') {
				    showTooltip(item.pageX, item.pageY, "<span class='pu-date'>"+lang[month_array[myDate.getUTCMonth()]] + " " + myDate.getUTCDate() + "</span><br/>" + y.toFixed(2) + 'kWh');
				  } else if (period == 'year') {
				    showTooltip(item.pageX, item.pageY, "<span class='pu-date'>"+lang[month_array[myDate.getUTCMonth()]] + "</span><br/>" + y.toFixed(2) + 'kWh');
				  }
			  }

			}
		} else {
			$("#tooltip").remove();
			previousPoint = null;            
		}

	});
	
	// initialise graph (24 hours)
	$('.action_set_time_period.default').click();
	
	$('#instant_usage .show-settings').live('click', function(e) {
		e.preventDefault();
		instant_reading.stop_refresh();
		$('#instant_usage').fadeOut('default', function() {
			instant_settings.set_element('#instant_usage').setup();
			$(this).fadeIn();
		});

	});
	initialise_instant_view();
	
	// initialise cost widget
	cost.set_element('#cost').getMonth();
	
	// initialise budget widget
	budget.set_element('#budget').setup();
	
	// initialise weather widget
	weather.set_element('#weather').setup().set_values();

});
