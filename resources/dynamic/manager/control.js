$(function () {
    $("[name='relay']").click(function () {
        let val = "";
        if ($(this).is(":checked")) {
            val = 1;
        } else {
            val = 0;
        }

        $.ajax({
            url:'/manager/get_control_relay_change',
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
            url:'/manager/get_control_temperature_change',
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

    $("[name='ch']").click(function () {
        let ch1 = $("[name="+$(this).data('idx')+"_ch1]").is(":checked") ? 1 : 0;
        let ch2 = $("[name="+$(this).data('idx')+"_ch2]").is(":checked") ? 1 : 0;
        let ch3 = $("[name="+$(this).data('idx')+"_ch3]").is(":checked") ? 1 : 0;
        let ch4 = $("[name="+$(this).data('idx')+"_ch4]").is(":checked") ? 1 : 0;

        $.ajax({
            url:'/manager/get_control_4ch_change',
            type:'post',
            data: {
                control_idx:$(this).data('idx'), ch1:ch1, ch2:ch2, ch3:ch3, ch4:ch4,
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