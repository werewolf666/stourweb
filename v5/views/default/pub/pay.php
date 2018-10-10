<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>
        <stourweb_title/>-{$webname}
    </title>
    <stourweb_header/>
    {Common::css("base.css")}
    {Common::js("jquery.min.js,base.js,common.js")}
</head>
<body>
{request 'pub/header'}
<div class="big">
    <div class="wm-1200">
        <stourweb_pay_content/>
    </div>
</div>
<stourweb_content/>
{request 'pub/footer'}
<stourweb_footer/>
</body>
</html>
