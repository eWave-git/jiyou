
$(function () {
    //Date range picker with time picker
    $('#sdateAtedate').daterangepicker({
        locale: {
            // "format": 'YYYY-MM-DD HH:mm:ss',     // 일시 노출 포맷
            "format": 'YYYY-MM-DD',     // 일시 노출 포맷
            "applyLabel": "확인",                    // 확인 버튼 텍스트
            "cancelLabel": "취소",                   // 취소 버튼 텍스트
            "daysOfWeek": ["일", "월", "화", "수", "목", "금", "토"],
            "monthNames": ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"]
        },
        timePicker: false,                        // 시간 노출 여부
        showDropdowns: true,                     // 년월 수동 설정 여부
        autoApply: true,                         // 확인/취소 버튼 사용여부
        timePicker24Hour: true,                  // 24시간 노출 여부(ex> true : 23:50, false : PM 11:50)
    })


    getChart();
    function getChart() {

        $.ajax({
            url:'/manager/inquiry/getMyChart',
            type:'post',
            data: {
                device:$("[name='device']").val(),
                board:$("[name='board']").val(),
                sdateAtedate:$("input[name='sdateAtedate']").val(),
                graph_interval:$("[name='graph_interval']").val(),
            },
            dataType: 'json',
            success:function(obj) {

                $.each(obj.obj, function (key, value) {
                    // console.log(obj.obj[key]['tag_name'])
                    // console.log(obj.obj[key]['config'])
                    var myChart = new Chart(document.getElementById(obj.obj[key]['tag_name']), obj.obj[key]['config']);

                })

            }
        });


    }
});
