$(function () {
    $('.btn_del').click(function () {
        if(confirm("삭제 하시겠습니까?")){
            location.href = "/manager/group_alarm/"+$(this).data('idx')+"/delete";
        }
    });

    $("[name='activation']").click(function () {
        if(confirm("변경 하시겠습니까?")) {
            let val = "";

            if ($(this).is(":checked")) {
                val = 'Y';
            } else {
                val = 'N';
            }

            $.ajax({
                url:'/manager/group_alarm/setGroupActiveChange',
                type:'post',
                data: {
                    idx:$(this).data('idx'), active:val
                },
                dataType: "json",
                success:function(obj){
                    if (obj.success) {
                        window.location.reload(true);
                    }
                }
            })
        } else {
            $(this).prop('checked', !$(this).is(":checked")).change()
        }
    });

});