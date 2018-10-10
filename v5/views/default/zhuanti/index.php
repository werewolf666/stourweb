<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{$seoinfo['seotitle']}-{$webname}</title>
    {if $seoinfo['keyword']}
    <meta name="keywords" content="{$seoinfo['keyword']}" />
    {/if}
    {if $seoinfo['description']}
    <meta name="description" content="{$seoinfo['description']}" />
    {/if}
    {include "pub/varname"}
    {Common::css('theme.css,header.css,base.css')}
    {Common::js('jquery.min.js,base.js,common.js')}
</head>

<body>
<div class="web-top">
    <div class="wm-1200">
        <div class="notice-txt">{$GLOBALS['cfg_gonggao']}</div>

        <div class="top-login">
            <span id="loginstatus">
            </span>
            <a class="dd" href="{$cmsurl}search/order"><i></i>订单查询</a>
            <dl class="dh">
                <dt><i></i>网站导航</dt>
                <dd>
                    {st:channel action="pc" row="20"}
                    {loop $data $row}
                    <a href="{$row['url']}">{$row['title']}</a>
                    {/loop}
                    {/st}
                </dd>
            </dl>

        </div>
        <div class="scroll-order">
            <ul>
                {st:comment action="query" flag="all" row="3"}
                {loop $data $row}
                <li>{$row['nickname']}{$row['pltime']}评论了{$row['productname']}</li>
                {/loop}
                {/st}
            </ul>
        </div>
    </div>
</div><!--顶部-->


    {if !empty($info['logo'])}
      <div class="st-theme-head-box" style=" background:url({$info['logo']}) center no-repeat"></div><!-- 顶部背景 -->
    {else}
      <div class="st-theme-head-box" style=" background:url(/res/images/theme-top-bg.jpg) center no-repeat"></div><!-- 顶部背景 -->
    {/if}

  <div class="big" {if !empty($info['bgimage'])}style=" background:url({$info['bgimage']}) repeat"{else}style=" background:#dce9fa"{/if}>
  	<div class="wm-1200">

    	<div class="st-main-page">

        <div class="st-theme-center">
        	<h3 class="con-tit">{$info['ztname']}</h3>
          <div class="con-txt">
          	{$info['jieshao']}
          </div>
        </div><!-- 专题简介 -->
        {st:line action="query" flag="theme" themeid="$info['id']" row="6" return="linelist"}
        {if count($linelist)>0}
            <div class="st-theme-cpbox">
        	<div class="cp-tit"><strong>推荐线路</strong><a class="g_more" target="_blank" href="/lines/all-0-0-4-0-0-0-1">查看更多</a></div>
          <div class="cp-conbox">

          	    <div class="adv-imgbox">
                    {st:ad action="getad" name="zt_line_ad" row="1" pc="1" return="linead"}
                    {if !empty($linead)}
                    <a target="_blank" href="{$linead['adlink']}"><img src="{Common::img($linead['adsrc'],229,613)}" alt="{$linead['adname']}"/></a>
                    {/if}
                </div>

            <div class="cp-conlist">
            	<ul>
                    {loop $linelist $l}
              	      <li>
                	<a class="show-link" href="{$l['url']}" target="_blank">
                    <div class="pic">
                        {if $l['saveprice']>0}
                        <span class="sub"><em>立减</em><strong>{$l['saveprice']}元</strong></span>
                        {/if}
                        <img src="{Common::img($l['litpic'],280,190)}" alt="{$l['title']}" /></div>
                    <div class="bt">{$l['title']}</div>
                    <div class="cz">
                      <span class="jg"><b><i class="currency_sy">{Currency_Tool::symbol()}</i>{$l['price']}</b>起</span>
                      <span class="now-gm">立即购买</span>
                    </div>
                  </a>
                </li>
                    {/loop}
              </ul>
            </div>
          </div>
        </div><!-- 产品列表 -->
        {/if}
        {st:hotel action="query" flag="theme" themeid="$info['id']" row="6" return="hotellist"}
        {if count($hotellist)>0}
            <div class="st-theme-cpbox">
        	<div class="cp-tit"><strong>推荐酒店</strong><a class="g_more" target="_blank" href="/hotels/all-0-0-4-0-0-1">查看更多</a></div>
          <div class="cp-conbox">
            <div class="cp-conlist">
            	<ul>
                 {loop $hotellist $h}
              	<li>
                	<a class="show-link" href="{$h['url']}" target="_blank">
                    <div class="pic"><img src="{Common::img($h['litpic'],280,190)}" alt="{$h['title']}" /></div>
                    <div class="bt">{$h['title']}</div>
                    <div class="cz">
                      <span class="jg"><b><i class="currency_sy">{Currency_Tool::symbol()}</i>{$h['price']}</b>起</span>
                      <span class="now-gm">立即购买</span>
                    </div>
                  </a>
                </li>
                {/loop}
              </ul>
            </div>
              {st:ad action="getad" name="zt_hotel_ad" row="1" pc="1" return="hotelad"}

                <div class="adv-imgbox">
                    {if !empty($hotelad)}
                    <a target="_blank" href="{$hotelad['adlink']}"><img src="{Common::img($hotelad['adsrc'],229,613)}" alt="{$hotelad['adname']}"/></a>{/if}
                </div>

          </div>
        </div><!-- 产品列表 -->
        {/if}

          {st:car action="query" flag="theme" themeid="$info['id']" row="6" return="carlist"}
            {if count($carlist)>0}
            <div class="st-theme-cpbox">
                <div class="cp-tit"><strong>推荐车辆</strong><a class="g_more" target="_blank" href="/cars/all-0-4-0-1">查看更多</a></div>
                <div class="cp-conbox">
                    {st:ad action="getad" name="zt_car_ad" row="1" pc="1" return="carad"}

                    <div class="adv-imgbox">
                        {if !empty($carad)}
                        <a target="_blank" href="{$carad['adlink']}"><img src="{Common::img($carad['adsrc'],229,613)}" alt="{$carad['adname']}"/></a>
                        {/if}
                    </div>

                    <div class="cp-conlist">
                        <ul>
                            {loop $carlist $c}
                            <li>
                                <a class="show-link" href="{$c['url']}" target="_blank">
                                    <div class="pic">
                                        <img src="{Common::img($c['litpic'],280,190)}" alt="{$c['title']}" /></div>
                                    <div class="bt">{$c['title']}</div>
                                    <div class="cz">
                                        <span class="jg"><b><i class="currency_sy">{Currency_Tool::symbol()}</i>{$c['price']}</b>起</span>
                                        <span class="now-gm">立即购买</span>
                                    </div>
                                </a>
                            </li>
                            {/loop}
                        </ul>
                    </div>
                </div>
            </div><!-- 产品列表 -->
            {/if}

            {st:spot action="query" flag="theme" themeid="$info['id']" row="6" return="spotlist"}
            {if count($spotlist)>0}
            <div class="st-theme-cpbox">
                <div class="cp-tit"><strong>推荐景点</strong><a class="g_more"  target="_blank" href="/spots/all-0-4-0-1">查看更多</a></div>
                <div class="cp-conbox">
                    <div class="cp-conlist">
                        <ul>
                            {loop $spotlist $s}
                            <li>
                                <a class="show-link" href="{$s['url']}" target="_blank">
                                    <div class="pic"><img src="{Common::img($s['litpic'],280,190)}" alt="{$s['title']}" /></div>
                                    <div class="bt">{$s['title']}</div>
                                    <div class="cz">
                                        <span class="jg"><b><i class="currency_sy">{Currency_Tool::symbol()}</i>{$s['price']}</b>起</span>
                                        <span class="now-gm">立即购买</span>
                                    </div>
                                </a>
                            </li>
                            {/loop}
                        </ul>
                    </div>
                    {st:ad action="getad" name="zt_spot_ad" row="1" pc="1" return="spotad"}

                    <div class="adv-imgbox">
                        {if !empty($spotad)}
                        <a target="_blank" href="{$spotad['adlink']}"><img src="{Common::img($spotad['adsrc'],229,613)}" alt="{$spotad['adname']}"/></a>
                        {/if}
                    </div>

                </div>
            </div><!-- 产品列表 -->
            {/if}

        {st:article action="query" flag="theme" themeid="$info['id']" row="4" return="arclist"}
        {if count($arclist)>0}
            <div class="st-theme-cpbox">
        	<div class="cp-tit"><strong>推荐攻略</strong><a class="g_more"  target="_blank" href="/raiders/all">查看更多</a></div>
          <div class="gl-conbox">
          	<ul>
                {loop $arclist $a}
            	    <li>
                        <a href="{$a['url']}" target="_blank">
                          <div class="pic"><img src="{Common::img($a['litpic'],269,182)}" alt="{$a['title']}" /></div>
                          <div class="bt">{$a['title']}</div>
                          <div class="nr">{Common::cutstr_html($a['content'],30)}</div>
                        </a>
                    </li>
                {/loop}
            </ul>
          </div>
        </div><!-- 产品列表 -->
        {/if}

      </div>

    </div>

{request 'pub/footer'}
{request "pub/flink"}

</body>
</html>
<script>
    $(function(){


        //登陆状态
        $.ajax({
            type:"POST",
            async:false,
            url:SITEURL+"member/login/ajax_is_login",
            dataType:'json',
            success:function(data){
                if(data.status){
                        $txt = '<a class="dl" style="padding:0" href="javascript:;">你好,</a>';
                    $txt+= '<a class="dl" href="{Common::get_main_host()}/member/">'+data.user.nickname+'</a>';
                    $txt+= '<a class="dl" href="{Common::get_main_host()}/member/login/loginout">退出</a>';
                    //$txt+= '<a class="dl" href="{$cmsurl}member">个人中心</a>';
                }else{
                    $txt = '<a class="dl" href="{Common::get_main_host()}/member/login">登录</a>';
                    $txt+= '<a class="zc" href="{Common::get_main_host()}/member/register">免费注册</a>';
                }
                $("#loginstatus").html($txt);
            }
        })



    })
</script>
