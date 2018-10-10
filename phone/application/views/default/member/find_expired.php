<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>密码成功找回--{$webname}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {php echo Common::css('style.css,amazeui.css,extend.css');}
    <script type="text/javascript">
        var SITEURL = "{URL::site()}";
    </script>
</head>

<body>

<header>
    <div class="header_top">
        <div class="st_back"><a href="{$url}"></a></div>
        <h1 class="tit">找回密码</h1>
    </div>
</header>


<section>
    <div class="mid_content">

        <div class="st_user_box">

            <div class="st_login">
                <div class="st_success_ts">
                    <h3>已过有效验证时间，请重新找回密码!</a></h3>
                    <p>如有疑问请及时联系客服！</p>
                </div>
                <div class="st_userSelect_cz">
                    <h3>选择您的操作：</h3>
                    <p>
                        <a href="{$cmsurl}">去首页</a>
                        <a class="on" href="{$cmsurl}/member/find">找回密码</a>
                    </p>
                </div>
            </div>
            {request 'member/login/third'}
        </div>

    </div>
</section>


</body>
</html>
