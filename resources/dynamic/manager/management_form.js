
$(function () {
    $("#control_device").change(function () {

        $.ajax({
            url:'/manager/management/get_control_type',
            type:'post',
            data: {
                device_idx:$(this).val()
            },
            dataType: "json",
            success:function(obj){
                if (obj.success) {
                    if (obj.obj['control_type'] == 'R') {
                        $("#div_relay").show();
                        $("#div_temperature").hide();
                        $("#temperature").val('');
                    } else if (obj.obj['control_type'] == 'T') {
                        $("#div_relay").hide();
                        $("#div_temperature").show();
                        $("#temperature").val(obj.temperature);
                    } else {
                        $("#div_relay").hide();
                        $("#div_temperature").hide();
                    }
                }
            }
        })
    });
});
