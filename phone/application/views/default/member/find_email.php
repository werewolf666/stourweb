<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>邮件找回密码-{$webname}</title>
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
            <form action="" data="{$email}">
                <div class="st_login">
                    <div class="st_txt_ts">我们向您的邮箱<span>{php} echo preg_replace('/(\w{4}).*@(.*?)/','$1****$2',$email);{/php}</span>发送了邮件，请您查收!</div>
                    <div class="st_userSelect_cz">
                        <h3>选择您的操作：</h3>
                        <p>
                            <a href="{$cmsurl}">去首页</a>
                            <a class="on" href="{$cmsurl}member/login">去登录</a>
                        </p>
                    </div>
                </div>
            </form>
            {request 'member/login/third'}
        </div>

    </div>
</section>


</body>
</html>
