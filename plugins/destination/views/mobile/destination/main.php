<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{$seoinfo['seotitle']}-{$GLOBALS['cfg_webname']}</title>
{if $seoinfo['keyword']}
<meta name="keywords" content="{$seoinfo['keyword']}" />
{/if}
{if $seoinfo['description']}
<meta name="description" content="{$seoinfo['description']}" />
{/if}
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
{Common::css('base.css,destination.css')}
{Common::js('lib-flexible.js,jquery.min.js,template.js')}

</head>
<body>

  	{request "pub/header_new/typeid/$typeid"}


    <div class="dest-hd">
        {if $destinfo['litpic']}
        <a href="{$cmsurl}photos/{$destinfo['pinyin']}" class="pic">
            <img src="{Common::img($destinfo['litpic'],750,510)}" title="{$destinfo['kindname']}"/>
            <div class="dest-msg">
                <span class="ch">{$destinfo['kindname']}</span>
                <span class="en">{strtoupper($destinfo['pinyin'])}</span>
            </div>
            <div class="photo-link"><i class="icon"></i>{$destinfo['picnum']}</div>
        </a>
        {/if}
    </div>
    <!--目的地介绍-->

    <div class="dest-menu clearfix">
        {st:channel action="destchannel" destpinyin="$destinfo['pinyin']" row="100"}
        {loop $data $row}
        <a class="item" href="{$row['url']}">
            <span class="icon"><img src="{$row['ico']}"/></span>
            <span class="name">{$row['title']}</span>
        </a>
        {/loop}
        {/st}

    </div>
    <!--目的地导航-->


    {request "pub/footer"}

</body>
</html>
