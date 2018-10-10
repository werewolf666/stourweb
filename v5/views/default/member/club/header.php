<div class="header-info">
    <div class="wm-1200">
        <div class="login-info fl">
            您好，
            <a class="name">{$member['nickname']}</a>
            <a class="out" href="{Common::get_main_host()}/member/login/loginout">退出</a>
        </div>
        <div class="menu-info fr">
            <div class="menu-list">
                <a href="{$cmsurl}">网站首页</a>
                <i></i>
                <a href="{$cmsurl}member/">会员中心</a>
                <i></i>
                <a href="{$cmsurl}member/club/">会员俱乐部</a>
                <i></i>
                <a href="{$cmsurl}search/order">订单查询</a>
                <i></i>
            </div>
            <div class="website-dh">
                <span class="dh-tit">网站导航<i class="arrow-ico"></i></span>
                <ul class="dh-down">
                    {st:channel action="pc" row="20"}
                    {loop $data $row}
                    <li><a href="{$row['url']}">{$row['title']}</a></li>
                    {/loop}
                    {/st}
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- 顶部信息 -->

<div class="header-logo">
    <div class="wm-1200">
        {if !empty($GLOBALS['cfg_logo'])}
        <div class="lg-img"><a href="{$GLOBALS['cfg_logourl']}"><img src="{Common::img($GLOBALS['cfg_logo'],298,85)}" alt="{$GLOBALS['cfg_logotitle']}" /></a></div>
        {/if}
    </div>
</div>
<!-- logo -->
<div class="nav-club">
    <div class="menu-club">
        <ul class="clearfix">
            <li {if $default['selected']=='index'}class="active"{/if}><a href="{$cmsurl}member/club/">会员俱乐部</a></li>
            <li {if $default['selected']=='rank'}class="active"{/if}><a href="{$cmsurl}member/club/rank">我的等级</a></li>
            <li {if $default['selected']=='score'}class="active"{/if}>
                <a href="{$cmsurl}member/club/score">会员积分</a>
                <div class="down-menu">
                    <a href="{$cmsurl}member/club/makescore">赚取积分</a>
                    <a href="{$cmsurl}member/club/score">我的积分</a>
                </div>
            </li>
            {if St_Functions::is_normal_app_install('system_integral')}
            <li {if $default['selected']=='mall'}class="active"{/if}><a href="{$cmsurl}integral/">积分商城</a></li>
            {/if}
            {if $integralActivity}
            <li {if $default['selected']=='integral_activity'}class="active"{/if}>
            <a href="{$cmsurl}{$integralActivity[0]['url']}">积分活动</a>
            <div class="down-menu">
                {loop $integralActivity $v}
                <a href="{$cmsurl}{$v['url']}">{$v['title']}</a>
                {/loop}
            </div>
            </li>
            {/if}
        </ul>
    </div>
</div>
<!-- 主导航 -->
<script>
    $(function(){
        //主导航下拉菜单
        $(".menu-club li").hover(function(){
            $(this).children(".down-menu").show()
        },function(){
            $(this).children(".down-menu").hide()
        });
    })
</script>