$(function () {
    $("[name='relay']").click(function () {
        let val = "";
        if ($(this).is(":checked")) {
            val = 1;
        } else {
            val = 0;
        }

        $.ajax({
            url:'/manager/management/get_control_relay_change',
            type:'post',
            data: {
                control_idx:$(this).data('idx'), field:$(this).data('field'), val:val
            },
            dataType: "json",
            success:function(obj){
                if (obj.success) {
                    window.location.reload(true);
                }
            }
        })
    });

    $("[name='temperature']").click(function () {
        let idx = "temperature_"+$(this).data('idx');

        $.ajax({
            url:'/manager/management/get_control_temperature_change',
            type:'post',
            data: {
                control_idx:$(this).data('idx'), val:$("[name="+idx+"]").val()
            },
            dataType: "json",
            success:function(obj){
                if (obj.success) {
                    alert('저장 하였습니다.');
                    window.location.reload(true);
                }
            }
        })
    });

});