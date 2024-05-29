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
                    root.fps = 50;

                    const myTheme = am5.Theme.new(root);

                    myTheme.rule("AxisLabel", ["minor"]).setAll({
                        dy:1
                    });

                    myTheme.rule("Grid", ["x"]).setAll({
                        strokeOpacity: 0.05
                    });

                    myTheme.rule("Grid", ["x", "minor"]).setAll({
                        strokeOpacity: 0.05
                    });

                    myTheme.rule("Label",["y"]).setAll({            //  240526
                        //fill: am5.color(0x000000),
                        fontSize: "0.7em"
                    });
                    
                    myTheme.rule("Label",["x"]).setAll({            //  240526
                        //fill: am5.color(0x000000),
                        fontSize: "0.7em"
                    });

                    myTheme.rule("Graphics",["line","series","stroke"]).setAll({            //  240526 데이터 선의 두께, 표현등에 대한 변화
                        strokeWidth: 5,
                        //strokeDasharray: [10, 5]                                         // 대쉬추가
                    });


                    // Set themes
                    // https://www.amcharts.com/docs/v5/concepts/themes/
                    root.setThemes([
                        am5themes_Animated.new(root),
                        am5themes_Kelly.new(root),                                          // 고대비 색상 사용
                        myTheme
                    ]);


                    // Create chart
                    // https://www.amcharts.com/docs/v5/charts/xy-chart/
                    var chart = root.container.children.push(am5xy.XYChart.new(root, {
                        panX: true,
                        panY: true,
                        wheelX: "panX",
                        wheelY: "zoomX",
                        maxTooltipDistance: 0,
                        pinchZoomX:true,
                        height : 600                    //전체 650에서 그래프 600
                        
                    }));

                    // Create axes
                    // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
                    var xAxis = chart.xAxes.push(
                        am5xy.DateAxis.new(root, {
                            maxDeviation: 0.5,
                            groupData: true,
                            baseInterval: {
                                maxDeviation: 0.1,
                                groupData: false,
                                timeUnit: "minute",     // "day" | "millisecond"
                                count: 1
                            },
                            tooltipDateFormat: "MM-dd HH:mm",
                            renderer: am5xy.AxisRendererX.new(root, {
                                minGridDistance: 100, pan:"zoom"
                            }),
                            tooltip: am5.Tooltip.new(root, {})
                        })
                    );

                    var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                        renderer: am5xy.AxisRendererY.new(root, {})
                    }));

                    $.each(obj.fields, function (key1, value1) {
                        var _data = []
                        $.each(data, function (key, value) {
                            _data.push({date:data[key]['date'], value:data[key][value1.field]})
                        })

                        var series = chart.series.push(am5xy.LineSeries.new(root, {
                            name: value1.name,
                            xAxis: xAxis,
                            yAxis: yAxis,
                            valueYField: "value",
                            valueXField: "date",
                            // fill: am5.color(0x095256),                     // 240526
                            // stroke: am5.color(0xff0000),                     // 240526
                            minBulletDistance : 5,
                            legendValueText: "{valueY}",
                            tooltip: am5.Tooltip.new(root, {
                                pointerOrientation: "horizontal",
                                labelText: "[bold]{name}[/] : {valueY}"
                            })
                        }));

                        series.bullets.push(function () {                               // 240526 원형 마크 추가
                            return am5.Bullet.new(root, {
                                sprite: am5.Circle.new(root, {
                                radius: 8,
                                fill: series.get("fill"),
                                stroke: root.interfaceColors.get("background"),
                                strokeWidth: 1,
                                })
                            });
                        });

                        series.data.setAll(_data);

                        // Make stuff animate on load
                        // https://www.amcharts.com/docs/v5/concepts/animations/
                        series.appear();

                    })

                    // Add cursor
                    // https://www.amcharts.com/docs/v5/charts/xy-chart/cursor/
                    var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                        behavior: "none"
                    }));
                    cursor.lineY.set("visible", false);


                    // Add scrollbar
                    // https://www.amcharts.com/docs/v5/charts/xy-chart/scrollbars/
                    // chart.set("scrollbarX", am5.Scrollbar.new(root, {
                    //     orientation: "horizontal"
                    // }));

                    // Add legend
                    // https://www.amcharts.com/docs/v5/charts/xy-chart/legend-xy-series/
                    //var legend = chart.rightAxesContainer.children.push(am5.Legend.new(root, { //240525
                    var legend = chart.bottomAxesContainer.children.push(am5.Legend.new(root, {
                        layout : root.horizontalLayout,
                        //layout: root.verticalLayout,
                        height: 50,
                        width: 1400,
                        paddingTop: 10,
                        paddingLeft: 10,
                        //height: am5.percent(100)
                    }));

                    // When legend item container is hovered, dim all the series except the hovered one
                    legend.itemContainers.template.events.on("pointerover", function(e) {
                        var itemContainer = e.target;

                        // As series list is data of a legend, dataContext is series
                        var series = itemContainer.dataItem.dataContext;

                        chart.series.each(function(chartSeries) {
                            if (chartSeries != series) {
                                chartSeries.strokes.template.setAll({
                                    strokeOpacity: 0.15,
                                    stroke: am5.color(0x000000)
                                });
                            } else {
                                chartSeries.strokes.template.setAll({
                                    strokeWidth: 3
                                });
                            }
                        })
                    })

                    // When legend item container is unhovered, make all series as they are
                    legend.itemContainers.template.events.on("pointerout", function(e) {
                        var itemContainer = e.target;
                        var series = itemContainer.dataItem.dataContext;

                        chart.series.each(function(chartSeries) {
                            chartSeries.strokes.template.setAll({
                                strokeOpacity: 1,
                                strokeWidth: 1,
                                stroke: chartSeries.get("fill")
                            });
                        });
                    })

                    legend.itemContainers.template.set("width", am5.p100);
                    legend.valueLabels.template.setAll({
                        width: am5.p100,
                        textAlign: "right"
                    });

                    // It's is important to set legend data after all the events are set on template, otherwise events won't be copied
                    legend.data.setAll(chart.series.values);

                    // Make stuff animate on load
                    // https://www.amcharts.com/docs/v5/concepts/animations/
                    chart.appear(1000, 100);
                });
            }
        })
    }

});