<!doctype html>
<html>
<head head_ul=Lyvz8B >
<meta charset="utf-8">
<title>{$info['servername']}-{$webname}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('base.css,help.css')}
    {Common::js('lib-flexible.js,jquery.min.js,delayLoading.min.js')}
</head>

<body>

{request "pub/header_new/typeid/0"}
     
    <div class="st-help-block">
        <h3>{$info['servername']}</h3>
        <div class="st-help-show">
            {Product::strip_style($info['content'])}
        </div>
    </div><!--aboutus-->

  {request "pub/footer"}

</body>
</html>
