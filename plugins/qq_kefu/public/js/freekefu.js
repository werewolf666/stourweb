
(function(window,$){
    //发送客服请求
    function send_freekefu(callback,phoneSelector) {
        var selector=!phoneSelector?'#freekefu_phone':phoneSelector
        var phone = $(selector).val();
        var regPartton = /^[\d-]{6,}$/;
        if (!regPartton.test(phone)) {
            var msg='电话号码格式错误';
            if(typeof(callback)=='function')
                callback({status:false,msg:msg})
            else
            {
                alert(msg);
            }
            return;
        }
        $.ajax(
            {
                type: "post",
                data: {phone:phone},
                dataType: 'json',
                url: "http://" + window.location.host + "/plugins/qq_kefu/index/ajax_add_freekefu",
                success: function (data) {
                    var isCallback=typeof(callback)=='function';
                    if(isCallback)
                    {
                        callback(data);
                    }
                    else{
                        alert(data.msg);
                    }
                }
            }
        );
    }

    var _Freekefu={};
    _Freekefu.send_freekefu=send_freekefu;
    window.Freekefu=_Freekefu;
})(window,jQuery)
	
	
