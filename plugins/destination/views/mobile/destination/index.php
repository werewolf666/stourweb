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
{Common::css('base.css,swiper.min.css,destination.css')}
{Common::js('lib-flexible.js,swiper.min.js,jquery.min.js,template.js')}
</head>
<body>

  	{request "pub/header_new/typeid/$typeid"}

    <div class="swiper-container st-focus-banners" >
        <ul class="swiper-wrapper">
            {st:ad action="getad" name="s_dest_index_1"}
            {loop $data['aditems'] $v}
            <li class="swiper-slide">
                <a class="item" href="{$v['adlink']}"><img class="swiper-lazy" data-src="{Common::img($v['adsrc'],750,260)}"></a>
                <div class="swiper-lazy-preloader"></div>
            </li>
            {/loop}
            {/st}
        </ul>
        <div class="swiper-pagination ad-pagination"></div>
    </div>
    <!--轮播图-->

    <div class="st-product-block">
        <h3 class="st-title-bar">
            <i class="line-icon"></i>
            <span class="title-txt">热门目的地</span>
        </h3>
        <ul class="hot-dest-list clearfix">
            {st:destination action="query" flag="hot" offset="0" row="6"}
            {loop $data $row}
            <li>
                <a class="item" href="{$cmsurl}{$row['pinyin']}">
                    <img src="{Common::img($row['litpic'],210,142)}" alt="{$row['kindname']}" />
                    <span class="tit">{$row['kindname']}</span>
                </a>
            </li>
            {/loop}
            {/st}
        </ul>
    </div>
    <!--热门目的地-->

    <div class="dest-list-box">
        {st:destination action="query" flag="next" typeid="12" offset="0" row="9999" pid="0"}
        {loop $data $row}
        <h3><i class="icon"></i><a href="{$cmsurl}{$row['pinyin']}">{$row["kindname"]}</a></h3>
        <div class="con">
            {st:destination action="query" flag="next" typeid="12" offset="0" row="9999" pid="$row['id']" return="data1"}
            {loop $data1 $row1}
            <dl>
                <dt><a href="{$cmsurl}{$row1['pinyin']}">{$row1["kindname"]}</a></dt>
                <dd>
                    {st:destination action="query" typeid="12" flag="next" offset="0" row="9999" pid="$row1['id']" return="data2"}
                    {loop $data2 $row2}
                    <a href="{$cmsurl}{$row2['pinyin']}">{$row2["kindname"]}</a>
                    {/loop}
                    {/st}
                </dd>
            </dl>
            {/loop}
            {/st}
        </div>
        {/loop}
        {/st}
        <!--目的地列表-->
    </div>

    {request "pub/footer"}

    <script>
        $(function(){

            //目的地栏目页滚动广告
            var adSwiper = new Swiper('.st-focus-banners', {
                autoplay: 5000,
                pagination : '.ad-pagination',
                lazyLoading : true,
                observer: true,
                observeParents: true
            });

        })
    </script>

</body>
</html>
