{Common::css('header.css')}
{Model_Fenxiao::save_fxcode()}
<div class="header_top">
    {if $showlogo==1}
        <!--检测城市站点是否安装-->
        {if !St_Functions::is_normal_app_install('city_site')}
            <a class="header_logo" href="{$GLOBALS['cfg_m_main_url']}"><img src="{Common::img($GLOBALS['cfg_m_logo'])}" height="30" alt="{$webname}"/></a>
        {else}
            {request "city/header"}
        {/if}
    {else}
    <a class="back-link-icon" {$backurl}  data-ajax="false"></a>
    {/if}
    <h1 class="page-title-bar">{$channelname}</h1>
    <div class="st-top-menu">
        {if !$isindex}
        <span class="st-user-menu"></span>
        <div class="header-menu-bg"></div>
        <div class="st-down-bar">
            <ul>
                <li><a href="{$cmsurl}" data-ajax="false"><i class="icon home-ico"></i>首页</a></li>
                <li><a href="{$cmsurl}search" data-ajax="false"><i class="icon search-ico"></i>搜索</a></li>
                <li><a href="{$cmsurl}member" data-ajax="false"><i class="icon center-ico"></i>个人中心</a></li>
            </ul>
        </div>
        {else}
        <a class="st-user-center" href="{$cmsurl}member"></a>
        {/if}
    </div>
</div>


<script>
    var SITEURL = "{URL::site()}";
    $(function(){
        //头部下拉导航
        $(".st-user-menu").on("click",function(){
            $(".header-menu-bg,.st-down-bar").show();
            $("body").css("overflow","hidden")
        });
        $(".header-menu-bg").on("click",function(){
            $(".header-menu-bg,.st-down-bar").hide();
            $("body").css("overflow","auto")
        })
		$('body').append('<script'+' type="text/javascript" src="'+SITEURL+'member/login/ajax_islogin"></'+'script>');
    })

	function is_login($obj){
        var fx_url="content={urlencode($info['title'])}";
        if($obj['islogin']==1){
            if($obj['info']['fx_member']){
                if(window.location.href.indexOf('/show_')!=-1)
                {
                    var btn = $($obj['info']['fx_btn'].replace('[fx_url]', fx_url));
                    $('.bom_fixed').append(btn);
                    btn.attr('data-ajax',false);
                    btn.addClass('now-sell');
                }
            }
        }
    }
</script>
<!--微信分享-->
{include 'pub/share'}
