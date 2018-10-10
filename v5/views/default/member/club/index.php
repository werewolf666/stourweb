<!DOCTYPE html>
<html lang="en">
<head float_top=XIHwOs >
    <meta charset="UTF-8">
    <title>{__('俱乐部首页')}-{$webname}</title>
    {include "pub/varname"}
    {Common::css('header-club.css,club.css,base.css,extend.css',false)}
    {Common::js("jquery.min.js,base.js,common.js,SuperSlide.min.js")}
</head>
<body>
{request "member/club/header"}
<div class="home-slide" id="home-slide">
    <a class="prev" href="javascript:;"></a>
    <a class="next" href="javascript:;"></a>
    <div class="container-bd">
        <ul>
            {st:ad action="getad" name="s_club_index_1" pc="1" return="ad"}
            {loop $ad['aditems'] $v}
            <li class="banner"><a href="{$v['adlink']}" target="_blank"><img src="{Common::img($v['adsrc'],1920,450)}" alt="{$v['adname']}" /></a></li>
            {/loop}
            {/st}
        </ul>
    </div>
    <div class="container-hd">
        <ul>
            {loop $ad['aditems'] $k}
            <li></li>
            {/loop}
        </ul>
    </div>
    <div class="user-item-layer">
        <div class="info">
            <img class="hd-img" src="{$member['litpic']}" />
            <span class="name">Hi，<strong>{$member['nickname']}</strong></span>
            {if St_Functions::is_normal_app_install('member_sign')}
            <em id="sign_status"></em>
            {/if}
            <ul class="data">
                <li><?php Common::member_rank($member['mid'],array('return'=>'current'))?>
                    <em>{Common::member_rank($member['mid'],array('return'=>'current'))}</em>
                    <span>会员等级</span>
                </li>
                {if $nextGrade}
                <li>
                    <em>{$nextGrade['poor']}</em>
                    <span>距离升级还需</span>
                </li>
                {/if}
                <li>
                    <em>{$member['jifen']}</em>
                    <span>可用积分</span>
                </li>
            </ul>
        </div>
        <div class="jf-use">
            <dl>
                <dt>积分能做什么？</dt>
                <dd><i class="ico"></i>{$GLOBALS['cfg_exchange_jifen']}积分=&yen;1.00元</dd>
                <dd><i class="ico"></i>可兑换近千款精美礼品</dd>
            </dl>
        </div>
    </div>
</div>
<!-- 俱乐部首页滚动广告 -->

<div class="main-container grey-f7">
    <div class="wm-1200">
        {if St_Functions::is_normal_app_install('system_integral')}
           {request "integral/club"}
        {/if}
        <div class="mt20 mb35 clearfix">
            <div class="link-entry fl">
                <h3 class="column-bar clearfix">
                    <strong class="bar-tit">任务赚积分</strong>
                </h3>
                <div class="link-block">
                    <a href="/member/club/makescore"><img src="/res/images/club-jf.jpg" /></a>
                </div>
            </div>
            <!-- 任务赚积分 -->
            <div class="link-entry fr">
                <h3 class="column-bar clearfix">
                    <strong class="bar-tit">我的等级</strong>
                </h3>
                <div class="link-block">
                    <a href="/member/club/rank"><img src="/res/images/club-level.jpg" /></a>
                </div>
            </div>
            <!-- 我的等级 -->
        </div>

    </div>
</div>
<!-- footer -->
{request "pub/footer"}
<!-- footer end -->
<script>
    $(function(){
        //首页滚动广告
        $("#home-slide").slide({
            mainCell:".container-bd ul",
            titCell:".container-hd li",
            effect:"fold",
            interTime: 5000,
            delayTime: 1000,
            autoPlay:true
        });
    })
</script>
{if St_Functions::is_normal_app_install('member_sign')}
{request "sign/index"}
{/if}
</body>
</html>