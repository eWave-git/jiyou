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

            }
        })
    }

});