<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>订单中心-{$GLOBALS['cfg_webname']}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {php echo Common::css('amazeui.css,style.css,extend.css');}
    {php echo Common::js('jquery.min.js,amazeui.js,template.js,layer/layer.m.js');}
</head>

<body>
{request "pub/header/typeid/$typeid/isorder/1"}
<section>
    <div class="mid_content">
        <div class="no-content">
            <img src="{$GLOBALS['cfg_public_url']}images/nocon.png"/>
            <p>空空如也，什么都没有<br/>赶紧去预定，让生活充实起来吧！</p>
        </div>
    </div>
</section>
</body>
</html>
