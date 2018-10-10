<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>404页面未找到-{$GLOBALS['cfg_webname']}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('amazeui.css,style.css,extend.css')}
    {Common::js('jquery.min.js,layer/layer.m.js')}
</head>

<body>
{request "pub/header/"}
<section>
    <div class="mid_content">
        <div class="no-content">
            <img src="{$cmsurl}public/images/nofound.png">

            <p></p>

            <div class="st_userSelect_cz">
                <h3></h3>

                <p style="width: 30%;margin: 0 auto">
                    <a href="{$cmsurl}">去首页</a>
                </p>
            </div>
        </div>
    </div>
    </div>
</section>

{request "pub/footer"}
</body>
</html>