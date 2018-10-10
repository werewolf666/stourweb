<!DOCTYPE html>
<html>

<head script_table=Hyvz8B >
    <meta charset="UTF-8">
    <title>会员俱乐部</title>
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('style-new.css,member-club.css,mobilebone.css,swiper.min.css')}
    {Common::js('lib-flexible.js')}
	
</head>

<body>
<div id="clubHome" class="page out">
    <div class="header_top bar-nav">
        <a class="back-link-icon" href="{$cmsurl}member/index" data-ajax="false"></a>
        <h1 class="page-title-bar">会员俱乐部</h1>
    </div>
    <!-- 公用顶部 -->
    <div class="page-content">
        <div class="club-hd-box">
            <a class="vip-name" href="{$cmsurl}member/club/member_rank">{$rank['current_rank']['name']}</a>
            <a class="user-center" href="{$cmsurl}member/" data-ajax="false">
                <span class="user-hd-img">
                    <img src="{$member['litpic']}" alt="" title="" />
                    <i class="level">{$member['rank']}</i>
                </span>
                <span class="user-name">{$member['nickname']}</span>
            </a>
            <a class="integral" href="{$cmsurl}member/club/score">
                <span>可用积分：<em>{$member['jifen']}</em></span>
                {if isset($rank["range"][$rank['current']])}<span>距离升级还需：{php} echo $rank["range"][$rank['current']]-$rank['jifen'];{/php} </span>{/if}
            </a>
            <div class="earn-integral">
                <h3>我要赚积分</h3>
                <a class="more" href="{$cmsurl}member/club/member_task">查看更多<i class="more-arrow"></i></a>
                <div class="eran-list">
                    <ul class="clearfix">
                        {loop $strategy $s}
                        <li>
                            <div>
                                <i class="ico ico-{$label[$s['label']]}"></i>
                                <p>{$s['title']}</p>
                                <p class="num">+{$s['value']}</p>
                            </div>
                        </li>
                        {/loop}
                    </ul>
                </div>
            </div>
        </div>
        {st:ad action="getad" name="s_club_index_1"}
            {if $data['aditems']}
                <div class="swiper-container slide-img-block">
                    <div class="swiper-wrapper">
                        {loop $data['aditems'] $v}
                        <div class="swiper-slide">
                            <a class="pic" href="{$v['adlink']}"><img src="{Common::img($v['adsrc'],640,214)}" alt="{$v['adname']}" /></a>
                        </div>
                        {/loop}
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            {/if}
        {/st}
        <!-- 图片切换 -->
		{if St_Functions::is_system_app_install(107)}
        <div class="integral-mall">
            <div class="integral-mall-box">
                <div class="title">
                    <h3>超值兑换</h3>
                    <a class="link-mall" data-ajax="false" href="{$cmsurl}integral"></a>
                </div>
                <ul class="list clearfix">
                    {st:integral action="query" flag="order" row="6" return="integrals"}
                    {loop $integrals $v}
                    <li>
                        <a data-ajax="false" href="{$v['url']}">
                            <div class="pic"><img src="{Common::img($v['litpic'],348,210)}" alt="{$v['title']}" title="{$v['title']}"></div>
                            <p class="tit">{$v['title']}</p>
                            <p class="num"><em>{$v['need_jifen']}</em>积分</p>
                        </a>
                    </li>
                    {/loop}
                </ul>
            </div>
        </div>
        {/if}
        <div class="integral-ad-box">
            <ul class="clearfix">
                <li><a href="{$cmsurl}member/club/member_task"><img src="{$cmsurl}public/images/pic/mem-7.jpg" alt="" title="" /></a></li>
                <li><a href="{$cmsurl}member/club/member_rank"><img src="{$cmsurl}public/images/pic/mem-8.jpg" alt="" title="" /></a></li>
            </ul>
        </div>

    </div>
</div>
<!--绑定手机-->
<div id="bindPhone" class="page out" data-url="{$cmsurl}member/account/phone"  data-params="root=window&callback=callback_page"></div>
<!--绑定邮箱-->
<div id="bindMailbox" data-url="{$cmsurl}member/account/email"  data-params="root=window&callback=callback_page" class="page out"></div>
<!--个人资料-->
<div id="editData" class="page out" data-url="{$cmsurl}member/account/edit"  data-params="root=window&callback=callback_page"></div>
<!---->
<div id="myScore" class="page out" data-url="{$cmsurl}member/club/score" data-params="root=window&callback=callback_page"></div>
</body>
{Common::js('jquery.min.js,mobilebone.js,swiper.min.js,jquery.validate.min.js,jquery.layer.js,template.js,layer/layer.m.js')}
<script type="text/javascript" src="http://{$GLOBALS['main_host']}/res/js/jquery.validate.addcheck.js"></script>
<!--引入CSS-->
<link rel="stylesheet" type="text/css" href="http://{$GLOBALS['main_host']}/res/js/webuploader/webuploader.css">
<!--引入JS-->
<script type="text/javascript" src="http://{$GLOBALS['main_host']}/res/js/webuploader/webuploader.min.js"></script>
<script>
    var SITEURL = "{$cmsurl}";
    Mobilebone.evalScript = true;
    window.callback_page = function (pageInto, pageOut, response) {
        var contain_id = $(pageInto).attr('id');
        var url = $(pageInto).attr('data-url');
        $("#" + contain_id).load(url);
    };
    window.is_login = function (object) {
        var login_status = parseInt($('#islogin').val());
        if (!login_status) {
            window.location.href = "{$cmsurl}member/login";
            return true;
        } else {
            return false;
        }
    };
    $('.back-center').click(function () {
        window.location.href = SITEURL;
    });
    //轮播图
    var mySwiper = new Swiper ('.slide-img-block', {
        pagination: '.slide-img-block .swiper-pagination',
        observer:true,
        observeParents:true
    });
</script>
<link type="text/css" rel="stylesheet" href="{$cmsurl}public/mui/css/mui.picker.css" />
<link type="text/css" rel="stylesheet" href="{$cmsurl}public/mui/css/mui.poppicker.css" />
<script src="{$cmsurl}public/mui/js/mui.min.js"></script>
<script src="{$cmsurl}public/mui/js/mui.picker.js"></script>
<script src="{$cmsurl}public/mui/js/mui.poppicker.js"></script>
<script src="{$cmsurl}public/mui/js/city.data-3.js" type="text/javascript" charset="utf-8"></script>
</html>