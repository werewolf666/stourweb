<!doctype html>
<html>
<head padding_top=XIHwOs >
    <meta charset="utf-8">
    <title>404页面未找到-{$GLOBALS['cfg_webname']}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('base.css')}
    {Common::js('lib-flexible.js,swiper.min.js,jquery.min.js,template.js')}
    
</head>

<body>
{request "pub/header_new/typeid/$typeid"}
<section>
    <div class="mid_content">
        <div class="no-content">
            <img src="{$cmsurl}public/images/nofound.png">
            <div class="st_userSelect_cz">
                <a href="{$cmsurl}">返回首页</a>
            </div>
        </div>
    </div>
    </div>
</section>

{request "pub/footer"}
</body>
</html>