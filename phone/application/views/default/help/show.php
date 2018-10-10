<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{$info['title']}-{$GLOBALS['cfg_webname']}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('base.css,help.css')}
    {Common::js('lib-flexible.js,jquery.min.js,template.js,delayLoading.min.js')}
</head>

<body>

{request "pub/header_new/typeid/0/isshowpage/1/definetitle/".urlencode('帮助详情')}


    <div class="st-help-block">
        <h3>{$info['title']}</h3>
        <div class="st-help-show">
           {Product::strip_style($info['body'])}
        </div>
    </div>
    <!-- 帮助详情 -->

{request "pub/footer"}
</body>
</html>
