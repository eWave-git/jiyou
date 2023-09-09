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
});