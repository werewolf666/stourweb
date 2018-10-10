<!doctype html>
<html>

<head html_div=tyvz8B >
    <meta charset="utf-8">
    <title>{$seoinfo['seotitle']}-{$webname}</title>
    {if $seoinfo['keyword']}
    <meta name="keywords" content="{$seoinfo['keyword']}"/>
    {/if}
    {if $seoinfo['description']}
    <meta name="description" content="{$seoinfo['description']}"/>
    {/if}
    <meta name="author" content="{$webname}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="icon" href="{$GLOBALS['cfg_m_main_url']}/favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="{$GLOBALS['cfg_m_main_url']}/favicon.ico" type="image/x-icon"/>
    {Common::css('base.css,swiper.min.css,index.css')}
    {Common::js('lib-flexible.js,swiper.min.js,jquery.min.js,template.js,layer/layer.m.js,delayLoading.min.js')}
</head>

<body>

    {request "pub/header_new/typeid/0/isindex/1"}

    <div class="swiper-container st-focus-banners">
        <ul class="swiper-wrapper">
            {st:ad action="getad" name="s_index_1"}
                {loop $data['aditems'] $v}
                    <li class="swiper-slide">
                        <a class="item" href="{$v['adlink']}"><img class="swiper-lazy" data-src="{Common::img($v['adsrc'],750,320)}"></a>
                        <div class="swiper-lazy-preloader"></div>
                    </li>
                {/loop}
            {/st}
        </ul>
        <div class="swiper-pagination"></div>
    </div>
    <!-- 轮播图 -->

    <div class="st-search">
        <div class="st-search-box">
            <div class="st-search-down clearfix">
                <span  class="st-down-select"  ></span>
                <span class="st-down-ico"><i class=""></i></span>
            </div>
            <input type="text" class="st-search-text" id="keyword" placeholder="搜索目的地/攻略/线路"/>
            <input type="button" class="st-search-btn" value=""/>
        </div>
    </div>
    <!-- 搜索 -->

    <div class="st-home-menu">
        {st:channel action="getchannel" row="100"}
            {loop $data $row}
                <a class="menu-item" href="{$row['url']}">
                    <span class="icon"><img src="{$row['ico']}"/></span>
                    <span class="text">{$row['title']}</span>
                </a>
            {/loop}
        {/st}
    </div>
    <!-- 主导航 -->

    <div class="st-sale-box">
        <h3 class="st-title-bar">
            <i class="line-icon"></i>
            <span class="title-txt">特价优惠</span>
        </h3>
        <div class="st-sale-con">
            <div class="st-advpic-l">
                {st:ad action="getad" name="s_index_2" row="1"}
                    {if !empty($data)}
                        <a class="item" href="{$data['adlink']}"><img src="{$defaultimg}" st-src="{Common::img($data['adsrc'],375,340)}" title="{$data['adname']}"></a>
                    {/if}
                {/st}
            </div>
            <div class="st-advpic-r">
                {st:ad action="getad" name="s_index_3" row="1"}
                    {if !empty($data)}
                        <a class="item" href="{$data['adlink']}"><img src="{$defaultimg}" st-src="{Common::img($data['adsrc'],375,170)}" title="{$data['adname']}"></a>
                    {/if}
                {/st}
                {st:ad action="getad" name="s_index_4" row="1"}
                    {if !empty($data)}
                        <a class="item" href="{$data['adlink']}"><img src="{$defaultimg}" st-src="{Common::img($data['adsrc'],375,170)}" title="{$data['adname']}"></a>
                    {/if}
                {/st}
            </div>
        </div>
    </div>
    <!--特价优惠-->

    {st:channel action="getchannel" row="100"}
        {loop $data $row}
            {if  $row['m_issystem'] && in_array($row['m_typeid'],array(1,2,3,4,5,6,8,11,13,101,104,105,114))}
                {include 'index/'.Model_Model::all_model($row['m_typeid'],'maintable')}
            {/if}
        {/loop}
    {/st}

   

    {request "pub/code"}
    {request "pub/footer"}
    {if !empty($GLOBALS['cfg_m_phone'])}
        <a class="call-phone" href="tel:{$GLOBALS['cfg_m_phone']}"></a>
    {/if}


    <!--全局搜索弹出框-->
    <div class="search-sx-box">
        <div class="search-sx">
            {php}$searchModel = Model_Model::get_wap_search_model();{/php}
            <ul>
                {loop $searchModel $m}
                <li  {if $m['issystem']==1}  data-pinyin="{$m['pinyin']}"  {else}  data-pinyin="general/index/{$m['pinyin']}" {/if} ><span>{$m['modulename']}</span><i></i></li>
                {/loop}
            </ul>
        </div>
    </div>

</body>

</html>

<script>
    $(function () {

        //首页滚动广告
        var mySwiper = new Swiper('.st-focus-banners', {
            autoplay: 5000,
            pagination : '.swiper-pagination',
            lazyLoading : true,
            observer: true,
            observeParents: true
        });

        //全局搜索弹出框
        $(".st-search-down").click(function(){
            $(".search-sx-box").show();
        });
        $(".search-sx-box").click(function(){
            $(this).hide();
        });
         $(".search-sx li").click(function(event){
            $(this).addClass("on").siblings().removeClass("on");
            var words=$(this).text();
            var pinyin = $(this).attr('data-pinyin');
            $(".st-down-select").text(words);
            $(".st-down-select").attr('data-pinyin',pinyin);
        });
         $('.search-sx li:first').trigger('click');


        //全局搜索
        $('.st-search-btn').click(function () {
            var keyword = $.trim($("#keyword").val());
            if (keyword == '') {
                layer.open({
                    content: '{__("error_keyword_not_empty")}',
                    btn: ['{__("OK")}']
                });
                return false;
            } else {
                var pinyin = $(".st-down-select").attr('data-pinyin');
                url = SITEURL + 'query/'+pinyin+'?keyword=' + encodeURIComponent(keyword);
                window.location.href = url;
            }
        });

        //团购时间
        $('.st-tuan-list').find('.count').each(function (index, element) {
            show_count(element);
        });
        function show_count(node) {
            var endTime = $(node).attr('end-time') * 1000;
            var startTime = $(node).attr('start-time') * 1000;
            var timer_rt = window.setInterval(function () {

                var time;
                var now = new Date();
                now = now.getTime();
                if (startTime > now) {
                    time = startTime - now;
                    $(node).find('.sy').html('开始时间');
                } else if (endTime > now) {
                    time = endTime - now;
                    $(node).find('.sy').html('结束时间');
                } else {
                    $(node).find('.sy').html('已结束');
                    $(node).parents('li').remove();
                    clearInterval(timer_rt);
                }
                time = parseInt(time / 1000);
                var day = Math.floor(time / (60 * 60 * 24));
                var hour = Math.floor((time - day * 24 * 60 * 60) / 3600);
                var minute = Math.floor((time - day * 24 * 60 * 60 - hour * 3600) / 60);
                var html = '';
                if (day > 0) {
                    html += day + '天';
                }
                if (hour > 0) {
                    html += hour + '时';
                }
                if (minute > 0) {
                    html += minute + '分';
                }
                $(node).find('.time').html(html);
            }, 1000);
        }
    })
</script>