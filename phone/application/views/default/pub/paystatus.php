<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{$info['title']}-{$GLOBALS['cfg_webname']}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {php echo Common::css('amazeui.css,style.css,extend.css');}
    {php echo Common::js('jquery.min.js,amazeui.js');}

</head>
<body>
<div class="mid_content">

    <div class="success_page">
        <div class="suc_box">
            {if $info['sign']>9}
            <div class="success_pic"><img src="{$cmsurl}public/images/success.png"/></div>
            {else}
            <div class="success_pic"><img src="{$cmsurl}public/images/error.png"/></div>
            {/if}
            <p>{$info['msg']}</p>
            <a class="back" href="{$cmsurl}">首页</a>&nbsp;<a class="back" href="{$cmsurl}member/#&myOrder">订单中心</a>
        </div>
    </div>
</div>
</body>
</html>