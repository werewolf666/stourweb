<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><stourweb_title/>-{$GLOBALS['cfg_webname']}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

<link type="text/css" href="/phone/public/css/amazeui.css" rel="stylesheet" />
<link type="text/css" href="/phone/public/css/style.css" rel="stylesheet" />
<link type="text/css" href="/phone/public/css/extend.css" rel="stylesheet" />
<script type="text/javascript" src="/phone/public/js/jquery.min.js"></script>
<script type="text/javascript" src="/phone/public/js/amazeui.js"></script>
<script type="text/javascript" src="/phone/public/js/template.js"></script>
<script type="text/javascript" src="/phone/public/js/layer/layer.m.js"></script>


</head>

<body>
{request "pub/header/typeid/0/iscommontitle/1"}
<stourweb_content/>

</body>
<script>
function is_login($obj){
    if($obj.bool<1){
      return;
    }
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
