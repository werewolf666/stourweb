<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{$info['title']}-{$GLOBALS['cfg_webname']}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('amazeui.css,style.css,extend.css')}
    {Common::js('jquery.min.js,layer/layer.m.js')}
</head>

<body>
{request "pub/header/"}
<section>
    <div class="mid_content">
        <div class="success_page">
            <div class="suc_box">
                <div class="success_pic"><img src="{$cmsurl}public/images/error.png"/></div>
                <p><?php echo $info['msg']; ?></p>
                <a class="back" href="{$cmsurl}">首页</a>&nbsp;
                {if $referurl}
                <a class="back" href="{$referurl}">返回上一页</a>
                {/if}
            </div>
        </div>
    </div>
</section>
{request "pub/footer"}
</body>
</html>