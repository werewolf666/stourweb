<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><stourweb_title/>-{$GLOBALS['cfg_webname']}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link type="text/css" rel="stylesheet" href="/phone/public/css/base.css">
    <script type="text/javascript" src="/phone/public/js/lib-flexible.js"></script>
    <script type="text/javascript" src="/phone/public/js/jquery.min.js"></script>
    <script type="text/javascript" src="/phone/public/js/template.js"></script>
</head>

<body>
{request "pub/header_new/typeid/0/ispaypage/1"}
<stourweb_pay_content/>
<div class="hold-bottom-bar">
    <div class="bottom-fixed">
        <a class="confirm_pay_btn" id="confirm_pay_btn" href="javascript:;"><stourweb_title/></a>
    </div>
</div>
</body>
<script>
    var login_status=0;
    $('#confirm_pay_btn').click(function(){
        if (status>0) {
            var data = $('#mobile_common_pay').find('a.on').attr('data');
            var payurl = $('#mobile_common_pay').find('a.on').attr('data-payurl');
            if (payurl == "") {
                window.location.href = '/payment/index/confirm/?' + data;
            } else {
                window.location.href = payurl + '/?' + data;
            }
        } else {
            window.location.href = login_status>0?'/phone/member#&myOrder':'/phone/member/login?redirecturl=<?php echo urlencode("/phone/member#&myOrder")?>';
        }
    });
    function is_login($obj){
        if($obj.bool<1){
            return;
        }
        login_status=$obj.islogin;
        var html='<div class="st_user_header_pic">'
            +'<img src="'+$obj.info.litpic+'" />'
            +'<p><a>'+$obj.info.nickname+'</a></p>'
            +'</div>'
            +'<div class="st_user_cz">'
            +'<a href="/phone/"><i class="ico_01"></i>首页</a>'
            +'<a href="/phone/member/order/list"><i class="ico_02"></i>我的订单<em>'+$obj.info.orderNum+'</em></a>'
            +'<a href="/phone/member/linkman"><i class="ico_03"></i>常用联系人</a>'
            +'<a class="cursor" id="logout"><i class="ico_04"></i>退出</a>'
            +'</div>';
        $('#login-html').html(html);
    }
</script>
<script type="text/javascript" src="/phone/member/login/ajax_islogin"></script>
</html>
