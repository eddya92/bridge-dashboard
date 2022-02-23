(function($) {
	"use strict";
	var data = [],
		totalPoints = 300;
	function getRandomData() {
		if (data.length > 0)
			data = data.slice(1);
		while (data.length < totalPoints) {
			var prev = data.length > 0 ? data[data.length - 1] : 50,
				y = prev + Math.random() * 10 - 5;
			if (y < 0) {
				y = 0;
			} else if (y > 100) {
				y = 100;
			}
			data.push(y);
		}
		var res = [];
		for (var i = 0; i < data.length; ++i) {
			res.push([i, data[i]])
		}
		return res;
	}
	var updateInterval = 30;
	$("#updateInterval").val(updateInterval).change(function () {
		var v = $(this).val();
		if (v && !isNaN(+v)) {
			updateInterval = +v;
			if (updateInterval < 1) {
				updateInterval = 1;
			} else if (updateInterval > 2000) {
				updateInterval = 2000;
			}
			$(this).val("" + updateInterval);
		}
	});
	var plot = $.plot("#real-time-update", [ getRandomData() ], {
		series: {
			shadowSize: 0
		},
		yaxis: {
			min: 0,
			max: 100
		},
		xaxis: {
			show: !1
		},
		background:{
			color:'#047afb'
		},
		grid: {
			borderWidth: 0
		},
		colors: [ CubaAdminConfig.primary ]
	});
	function update() {
		plot.setData([getRandomData()]);
		plot.draw();
		setTimeout(update, updateInterval);
	}
	update();
})(jQuery);
if ($("#flot-categories").length > 0) {
	var a = {
		color: CubaAdminConfig.primary,
		data: [
			["Jan", 25],
			["Feb", 8],
			["Mar", 4],
			["Apr", 13],
			["May", 17],
			["Jun", 9],
			["Jul", 5],
			["Aug", 11],
			["Sep", 17],
			["Oct", 8],
			["Nov", 26],
		]
	};
	$.plot("#flot-categories", [a], {
		series: {
			bars: {
				show: !0,
				barWidth: .8,
				align: "center",
				fillColor: {
					colors: [{
						opacity: 1
					}, {
						opacity: 1
					}]
				}
			}
		},
		xaxis: {
			mode: "categories",
			tickLength: 0
		},
		grid: {
			borderWidth: 0
		}
	})
}

if ($("#annotations-chart").length > 0) {
	for (var a = [], b = 0; b < 20; ++b) a.push([b, Math.sin(b)]);
	var c = [{
			data: a,
			label: "Pressure",
			color: CubaAdminConfig.primary
		}],
		d = [{
			color: "#ffffff",
			yaxis: {
				from: 1
			}
		}, {
			color: "#ffffff",
			yaxis: {
				to: -1
			}
		}, {
			color: "#ffffff",
			lineWidth: 1,
			xaxis: {
				from: 1,
				to: 1
			}
		}, {
			color: "#ffffff",
			lineWidth: 1,
			xaxis: {
				from: 9,
				to: 9
			}
		}],
		e = $("#annotations-chart"),
		f = $.plot(e, c, {
			bars: {
				show: !0,
				barWidth: .7,
				fill: 1
			},
			xaxis: {
				ticks: [],
				autoscaleMargin: .02
			},
			yaxis: {
				min: -2,
				max: 2
			},
			grid: {
				markings: d,
				borderWidth: 0
			}
		}),
		g = f.pointOffset({
			x: 2,
			y: -1.2
		});
	e.append("<div style='position:absolute;left:" + (g.left + 4) + "px;top:" + g.top + "px;color:#666;font-size:smaller'>Warming up</div>"), g = f.pointOffset({
		x: 8,
		y: -1.2
	}), e.append("<div style='position:absolute;left:" + (g.left + 4) + "px;top:" + g.top + "px;color:#666;font-size:smaller'>Actual measurements</div>");
	var h = f.getCanvas().getContext("2d");
	h.beginPath(), g.left += 4, h.moveTo(g.left, g.top), h.lineTo(g.left, g.top - 10), h.lineTo(g.left + 10, g.top - 5), h.lineTo(g.left, g.top), h.fillStyle = "#5e6db3", h.fill()
}

if ($("#flot-basic-chart").length > 0) {
	for (var a = [], b = 0; b < 14; b += .5) a.push([b, Math.sin(b)]);
	var c = {
			color: CubaAdminConfig.secondary,
			data: [
				[0, 3],
				[4, 8],
				[8, 5],
				[9, 13]
			]
		},
		d = {
			color: CubaAdminConfig.primary ,
			data: [
				[0, 12],
				[7, 0],
				null,
				[0, 2.5],
				[12, 10.5]
			]
		};
	$.plot("#flot-basic-chart", [a, c, d], {
		grid: {
			borderWidth: 0
		},
		bars: {
			show: !0,
			lineWidth: 0,
			fill: !0,
			fillColor: {
				colors: [{
					opacity: 1
				}, {
					opacity: 1
				}]
			}
		},
		colors: ["#51bb25", "#51bb25" ,"#51bb25" ,"#51bb25"]
	})
}

$(function() {
	var datasets = {
		"usa": {
			label: "gen",
			data: [[1, 483994], [2, 479060], [3, 457648], [4, 401949], [5, 424705], [6, 402375], [7, 377867], [8, 357382], [9, 337946], [10, 336185], [11, 328611], [12, 329421]]
		}
	};
	var i = 0;
	$.each(datasets, function(key, val) {
		val.color = i;
		++i;
	});
	var choiceContainer = $("#choices");
	$.each(datasets, function(key, val) {
		choiceContainer.append("<div class='checkbox checkbox-primary m-squar'><input type='checkbox' name='" + key +
			"' checked='checked' id='id" + key + "'></input>" +
			"<label for='id" + key + "'>"
			+ val.label + "</label></div>");
	});
	choiceContainer.find("input").click(plotAccordingToChoices);
	function plotAccordingToChoices() {
		var data = [];
		choiceContainer.find("input:checked").each(function () {
			var key = $(this).attr("name");
			if (key && datasets[key]) {
				data.push(datasets[key]);
			}
		});
		if (data.length > 0) {
			$.plot("#toggling-series-flot", data, {
				yaxis: {
					min: 0
				},
				xaxis: {
					tickDecimals: 0
				},
				grid: {

					borderWidth: 0
				},
				colors: [CubaAdminConfig.primary, "#f8d62b" ,"#a927f9" ,"#51bb25" , CubaAdminConfig.secondary , "#dc3545" ,"#f8d62b"]

			});
		}
	}
	plotAccordingToChoices();
});

$(function() {
	function a() {
		$("#stacking-flot-chart").length > 0 && $.plot("#stacking-flot-chart", j, {
			series: {
				stack: f,
				lines: {
					show: h,
					fill: !0,
					steps: i
				},
				bars: {
					show: g,
					barWidth: .6
				}
			},
			grid: {
				borderWidth: 0
			}
		})
	}
	for (var b = [], c = 0; c <= 10; c += 1) b.push([c, parseInt(30 * Math.random(), 30)]);
	for (var d = [], c = 0; c <= 10; c += 1) d.push([c, parseInt(30 * Math.random(), 30)]);
	for (var e = [], c = 0; c <= 10; c += 1) e.push([c, parseInt(30 * Math.random(), 30)]);
	var f = 0,
		g = !0,
		h = !1,
		i = !1,
		j = [{
			color: CubaAdminConfig.secondary ,
			data: b
		}, {
			color: CubaAdminConfig.primary ,
			data: d
		}, {
			color: "#51bb25",
			data: e
		}];
	a(), $(".stackControls button").click( function(b) {
		b.preventDefault(), f = "With stacking" == $(this).text() || null, a()
	}), $(".graphControls button").on("click", function(b) {
		b.preventDefault(), g = $(this).text().indexOf("Bars") != -1, h = $(this).text().indexOf("Lines") != -1, i = $(this).text().indexOf("steps") != -1, a()
	})
});

$(function() {
	function drawArrow(ctx, x, y, radius){
		ctx.beginPath();
		ctx.moveTo(x + radius, y + radius);
		ctx.lineTo(x, y);
		ctx.lineTo(x - radius, y + radius);
		ctx.stroke();
	}
	function drawSemiCircle(ctx, x, y, radius){
		ctx.beginPath();
		ctx.arc(x, y, radius, 0, Math.PI, false);
		ctx.moveTo(x - radius, y);
		ctx.lineTo(x + radius, y);
		ctx.stroke();
	}
	var data1 = [
		[1,1,.5,.1,.3],
		[2,2,.3,.5,.2],
		[3,3,.9,.5,.2],
		[1.5,-.05,.5,.1,.3],
		[3.15,1.,.5,.1,.3],
		[2.5,-1.,.5,.1,.3]
	];

	var data1_points = {
		show: true,
		radius: 5,
		errorbars: "xy",
		xerr: {show: true, asymmetric: true, upperCap: "-", lowerCap: "-"},
		yerr: {show: true, color: "red", upperCap: "-"}
	};

	var data2 = [
		[.7,3,.2,.4],
		[1.5,2.2,.3,.4],
		[2.3,1,.5,.2]
	];

	var data2_points = {
		show: true,
		radius: 5,
		errorbars: "y",
		yerr: {show:true, asymmetric:true, upperCap: drawArrow, lowerCap: drawSemiCircle}
	};

	var data3 = [
		[1,2,.4],
		[2,0.5,.3],
		[2.7,2,.5]
	];

	var data3_points = {
		radius: 0,
		errorbars: "y",
		yerr: {show:true, upperCap: "-", lowerCap: "-", radius: 5}
	};

	var data4 = [
		[1.3, 1],
		[1.75, 2.5],
		[2.5, 0.5]
	];

	var data4_errors = [0.1, 0.4, 0.2];
	for (var i = 0; i < data4.length; i++) {
		data4_errors[i] = data4[i].concat(data4_errors[i])
	}

	var data = [
		{color: "#f73164", points: data1_points, data: data1, label: "data1"},
		{color: "#a927f9",  points: data2_points, data: data2, label: "data2"},
		{color: "#f8d62b", lines: {show: true}, points: data3_points, data: data3, label: "data3"},
		{color: "#7366ff", bars: {show: true, align: "center", barWidth: 0.25}, data: data4, label: "data4"},
		{color: "#51bb25", points: data3_points, data: data4_errors}
	];

	$.plot($("#error-flot-chart"), data , {
		legend: {
			position: "sw",
			show: true
		},
		series: {
			lines: {
				show: false
			}
		},
		xaxis: {
			min: 0.6,
			max: 3.1
		},
		yaxis: {
			min: 0,
			max: 3.5
		},
		zoom: {
			interactive: true
		},
		pan: {
			interactive: true
		},
		grid: {
			borderWidth: 0
		}
	});
});

$(function() {
	var data = [],
		series = Math.floor(Math.random() * 6) + 3;
	for (var i = 0; i < series; i++) {
		data[i] = {
			label: "Series" + (i + 1),
			data: Math.floor(Math.random() * 100) + 1
		}
	}
	$.plot('#default-pie-flot-chart', data, {
		series: {
			pie: {
				show: true
			}
		},
		colors: [ CubaAdminConfig.primary , "#51bb25" ,"#f8d62b" ,"#a927f9" ,"#dc3545", "#6c757d" , CubaAdminConfig.secondary ]
	});
	$.plot('#default-pie-legend-flot-chart', data, {
		series: {
			pie: {
				show: true
			}
		},
		legend: {
			show: false
		},
		colors: [CubaAdminConfig.primary , "#51bb25" ,"#f8d62b" ,"#a927f9" ,"#dc3545", "#6c757d" , CubaAdminConfig.secondary]
	});
	$.plot('#hidden-label-radius-flot-chart', data, {
		series: {
			pie: {
				show: true,
				radius: 1,
				label: {
					show: true,
					radius: 2/3,
					threshold: 0.1
				}
			}
		},
		legend: {
			show: false
		},
		colors: [CubaAdminConfig.primary , "#51bb25" ,"#f8d62b" ,"#a927f9" ,"#dc3545", "#6c757d" , CubaAdminConfig.secondary]
	});
	$.plot('#default-pie-flot-chart-hover', data, {
		series: {
			pie: {
				show: true
			}
		},
		grid: {
			hoverable: true,
			clickable: true
		},
		colors: [CubaAdminConfig.primary , "#51bb25" ,"#f8d62b" ,"#a927f9" ,"#dc3545", "#6c757d" , CubaAdminConfig.secondary]
	});
	$.plot('#custom-label1pie', data, {
		series: {
			pie: {
				show: true,
				radius: 1,
				label: {
					show: true,
					radius: 1,
					background: {
						opacity: 0.8
					}
				}
			}
		},
		legend: {
			show: false
		},
		colors: [CubaAdminConfig.primary , "#51bb25" ,"#f8d62b" ,"#a927f9" ,"#dc3545", "#6c757d" , CubaAdminConfig.secondary]
	});
	$.plot('#label-radius-flot-chart', data, {
		series: {
			pie: {
				show: true,
				radius: 1,
				label: {
					show: true,
					radius: 3/4,
					background: {
						opacity: 0.8
					}
				}
			}
		},
		legend: {
			show: false
		},
		colors: [CubaAdminConfig.primary , "#51bb25" ,"#f8d62b" ,"#a927f9" ,"#dc3545", "#6c757d" , CubaAdminConfig.secondary]
	});
	$.plot('#title-pie-flot-chart', data, {
		series: {
			pie: {
				show: true,
				radius: 1,
				tilt: 0.5,
				label: {
					show: true,
					radius: 1,
					background: {
						opacity: 0.8
					}
				},
				combine: {
					color: '#ddd',
					threshold: 0.1
				}
			}
		},
		legend: {
			show: false
		},
		colors: [CubaAdminConfig.primary , "#51bb25" ,"#f8d62b" ,"#a927f9" ,"#dc3545", "#6c757d" , CubaAdminConfig.secondary]
	});
	$.plot('#dount-hole-flot-chart', data, {
		series: {
			pie: {
				innerRadius: 0.5,
				show: true
			}
		},
		colors: [CubaAdminConfig.primary , "#51bb25" ,"#f8d62b" ,"#a927f9" ,"#dc3545", "#6c757d" , CubaAdminConfig.secondary]
	});
});

if ($("#multiple-real-timeupdate ").length > 0) {
	var a = [],
		b = 300,
		c = function() {
			for (a.length > 0 && (a = a.slice(1)); a.length < b;) {
				var c = a.length > 0 ? a[a.length - 1] : 50,
					d = c + 10 * Math.random() - 5;
				d < 0 ? d = 0 : d > 100 && (d = 100), a.push(d)
			}
			for (var e = [], f = 0; f < a.length; ++f) e.push([f, a[f]]);
			return e
		},
		d = [],
		b = 300,
		e = function() {
			for (d.length > 0 && (d = d.slice(1)); d.length < b;) {
				var a = d.length > 0 ? d[d.length - 1] : 50,
					c = a + 10 * Math.random() - 5;
				c < 0 ? c = 0 : c > 100 && (c = 100), d.push(c)
			}
			for (var e = [], f = 0; f < d.length; ++f) e.push([f, d[f]]);
			return e
		},
		f = 30,
		g = 30;
	g && !isNaN(+g) && (f = +g, f < 1 ? f = 1 : f > 2e3 && (f = 2e3), $(this).val("" + f));
	var h = {
			color: "#047afb",
			data: c()
		},
		i = {
			color: "#000000",
			data: e()
		},
		j = $.plot("#multiple-real-timeupdate", [h, i], {
			series: {
				shadowSize: 0
			},
			yaxis: {
				min: 0,
				max: 100
			},
			xaxis: {
				show: !1
			},
			grid: {
				borderWidth: 0
			},
			colors: [ CubaAdminConfig.primary , CubaAdminConfig.secondary ]
		}),
		k = function() {
			j.setData([c(), e()]), j.draw(), setTimeout(k, f)
		};
	k()
}
