$(function () {

    function isMobile(){
        var UserAgent = navigator.userAgent;
        if (UserAgent.match(/iPhone|iPod|Android|Windows CE|BlackBerry|Symbian|Windows Phone|webOS|Opera Mini|Opera Mobi|POLARIS|IEMobile|lgtelecom|nokia|SonyEricsson/i) != null || UserAgent.match(/LG|SAMSUNG|Samsung/) != null) {
            return true;
        }else{
            return false;
        }
    }
    function get_pushid() {
        window.ReactNativeWebView.postMessage('');
        window.document.addEventListener('message', function (data) {
            if (data.data) {
                $.ajax({
                    url:'/manager/dashboard/set_push_id',
                    type:'post',
                    data: {
                        subscription_id:data.data
                    },
                    dataType: "json",
                    success:function(obj){
                        if (obj.success) {
                            console.log(obj.success)
                        }
                    }
                })
            }
        })
    }

    if (isMobile()) {
        get_pushid();
    }

    $("button[class='nav-link']").click(function () {
        console.log($(this).data('idx'))

        $("#dynamicTbody").empty();
        $.ajax({
            url:'/manager/dashboard/getWidgetItems',
            type:'post',
            data: {
                widget_idx:$(this).data('idx'),
            },
            dataType: "json",
            success:function(obj){
                if (obj.success) {

                    var html = '';
                    $.each(obj.board_type, function (key, value) {
                        key = key+1;
                        html += "<tr>";
                        var che = '';
                        if (value.display == 'Y') { che = 'checked'; }
                        html += "<td>"+"<input type='checkbox' class='form-check-input' "+che+" name='data"+key+"_display' value='Y' />"+"</td>";
                        html += "<td>"+"<input type='text' class='form-control ps-0' value='"+value.name+"' name='data"+key+"_name' />"+"</td>";
                        html += "<td>"+"<select class='form-select' name='data"+key+"_symbol'>";

                        $.each(obj.symbols, function (key1, value1) {
                            var sel = '';
                            if (value.symbol == value1.symbol) { sel = 'selected'; }
                            html += "<option value='"+value1.idx+"' "+sel+">"+value1.symbol+"</option>";
                        })
                        html +=  "</select>"+"</td>";
                        html += "</tr>";
                    })
                    $("#dynamicTbody").append(html);

                }
            }
        })

        $("[name='idx']").val($(this).data('idx'));
        $("[name='widget_name']").val($(this).data('title'));

        setTimeout(() => $("#modal-widget").modal("show"), 1000);

    });

    $("[name='modal_submit']").click(function () {
        $.ajax({
            url:'/manager/dashboard/widgetNameChange',
            type:'post',
            data:$("[name='frm']").serialize(),
            dataType: "json",
            success:function(obj){
                if (obj.success) {
                    location.reload();
                }
            }
        })
    })


    if (document.getElementById('chartdiv')) {

        $.ajax({
            url:'/manager/dashboard/getChart',
            type:'post',
            data: {
                widget_idx:$("#chartdiv").data('idx')
            },
            dataType: "json",
            success:function(obj){
                var data = []
                var field = []
                $.each(obj.obj, function (key, value) {
                    // console.log(value.date1);
                    var d = value.dates.split(' ');
                    var y = d[0].split('-');
                    var h = d[1].split(':');
                    data.push({
                        date: new Date(y[0], y[1]-1, y[2],h[0],h[1],h[2]).getTime(),

                    });

                    $.each(obj.fields, function (key1, value1) {
                        // console.log(kk+'||'+obj.obj[key][kk])
                        data[key][value1.field] = obj.obj[key][value1.field];
                    })

                })

                // console.log(data)
                $.each(obj.fields, function (key, value) {
                    field.push({
                        fieldDate: value.field,
                        fieldName: value.name,
                    });
                })


                am5.ready(function() {

                    // Create root element
                    // https://www.amcharts.com/docs/v5/getting-started/#Root_element
                    var root = am5.Root.new("chartdiv");

                    // Set themes
                    // https://www.amcharts.com/docs/v5/concepts/themes/
                    root.setThemes([
                        am5themes_Animated.new(root)
                    ]);

                    // Create chart
                    // https://www.amcharts.com/docs/v5/charts/xy-chart/
                    var chart = root.container.children.push(
                        am5xy.XYChart.new(root, {
                            focusable: true,
                            panX: true,
                            panY: true,
                            wheelX: "panX",
                            wheelY: "zoomX",
                            pinchZoomX:true
                        })
                    );

                    var easing = am5.ease.linear;
                    chart.get("colors").set("step", 3);

                    // Create axes
                    // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
                    var xAxis = chart.xAxes.push(
                        am5xy.DateAxis.new(root, {
                            maxDeviation: 0.5,
                            groupData: true,
                            baseInterval: {
                                maxDeviation: 0.1,
                                groupData: false,
                                timeUnit: "minute",
                                count: 1
                            },
                            tooltipDateFormat: "MM-dd HH:mm",
                            renderer: am5xy.AxisRendererX.new(root, {
                                minGridDistance: 100, pan:"zoom"
                            }),
                            tooltip: am5.Tooltip.new(root, {})
                        })
                    );

                    var legend = chart.children.push(
                        am5.Legend.new(root, {
                            x: am5.p50,
                            centerX: 0,
                            centerY: am5.m50,
                            y: am5.percent(98),
                        })
                    );


                    function createAxisAndSeries(fieldDate, fieldNamw, opposite) {
                        var yRenderer = am5xy.AxisRendererY.new(root, {
                            opposite: opposite
                        });
                        var yAxis = chart.yAxes.push(
                            am5xy.ValueAxis.new(root, {
                                // visible: false,
                                maxDeviation: 1,
                                maxPrecision: 0,
                                renderer: yRenderer
                            })
                        );

                        if (chart.yAxes.indexOf(yAxis) > 0) {
                            yAxis.set("syncWithAxis", chart.yAxes.getIndex(0));
                        }

                        // Add series
                        // https://www.amcharts.com/docs/v5/charts/xy-chart/series/
                        var series = chart.series.push(
                            am5xy.LineSeries.new(root, {
                                name: fieldNamw,
                                xAxis: xAxis,
                                yAxis: yAxis,
                                valueYField: fieldDate,
                                valueXField: "date",
                                tooltip: am5.Tooltip.new(root, {
                                    pointerOrientation: "horizontal",
                                    labelText: "[bold]{name}[/] : {valueY}"
                                })
                            })
                        );

                        //series.fills.template.setAll({ fillOpacity: 0.2, visible: true });
                        series.strokes.template.setAll({ strokeWidth: 3 });

                        yRenderer.grid.template.set("strokeOpacity", 0.05);
                        yRenderer.labels.template.set("fill", series.get("fill"));
                        yRenderer.setAll({
                            stroke: series.get("fill"),
                            strokeOpacity: 1,
                            opacity: 1
                        });

                        // Set up data processor to parse string dates
                        // https://www.amcharts.com/docs/v5/concepts/data/#Pre_processing_data
                        series.data.processor = am5.DataProcessor.new(root, {
                            dateFormat: "yyyy-MM-dd",
                            dateFields: ["date"]
                        });

                        series.data.setAll(data);
                        legend.data.setAll(chart.series.values);

                    }

                    // Add cursor
                    // https://www.amcharts.com/docs/v5/charts/xy-chart/cursor/
                    var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                        xAxis: xAxis,
                        behavior: "none"
                    }));
                    cursor.lineY.set("visible", false);

                    // add scrollbar
                    chart.set("scrollbarX", am5.Scrollbar.new(root, {
                        orientation: "horizontal"
                    }));


                    $.each(field, function (key, value) {
                        if (key % 2) {
                            createAxisAndSeries(value.fieldDate, value.fieldName, true);
                        } else {
                            createAxisAndSeries(value.fieldDate, value.fieldName, true);
                        }
                    })

                    // Make stuff animate on load
                    // https://www.amcharts.com/docs/v5/concepts/animations/
                    chart.appear(1000, 10);


                }); // end am5.ready()


            }
        })
    }

});