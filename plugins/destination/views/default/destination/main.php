<!doctype html>
<html>
<head border_margin=5TKwOs >
<meta charset="utf-8">
    <title>{$seoinfo['seotitle']}-{$GLOBALS['cfg_webname']}</title>
    {if $seoinfo['keyword']}
    <meta name="keywords" content="{$seoinfo['keyword']}" />
    {/if}
    {if $seoinfo['description']}
    <meta name="description" content="{$seoinfo['description']}" />
    {/if}
    {include "pub/varname"}
    {Common::css('dest.css,base.css,extend.css')}
    {Common::js('jquery.min.js,base.js,common.js,slideTabs.js,delayLoading.min.js')}

</head>

<body>
{request "pub/header"}
          
  <div class="big">
  	<div class="wm-1200">
      <div class="st-guide">
          <a href="{$cmsurl}">{$GLOBALS['cfg_indexname']}</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;{$info['kindname']}
      </div><!--面包屑-->
      
    	<div class="st-main-page">
        
		  <div class="dest-sum-box">
        	<div class="dest-sum-pic">
          	<div class="img"><img src="{Common::img($info['litpic'],550,372)}" alt="{$info['kindname']}" /></div>
                {if ST_Functions::is_system_app_install(6)}
            <span class="num morepic" style="cursor: pointer" data-url="{$GLOBALS['cfg_basehost']}/photos/{$info['pinyin']}/"><i></i>{__('查看更多图片')}</span>
                {/if}
          </div>
        	<div class="dest-sum-txt">
          	<h3>{$info['kindname']}</h3>
            <div class="nr" style="overflow-y:auto"><div>{St_Functions::cut_html_str($info['jieshao'],'360','...')}{if mb_strlen($info['jieshao'])>360}<a class="more" href="javascript:;">[{__('展开介绍')}]</a>{/if}</div><div class="hide">{St_Functions::cut_html_str($info['jieshao'],100000,'')}<a class="more" href="javascript:;">[{__('收缩介绍')}]</a></div></div>
          </div>
        </div><!-- 目的地概括 -->
        
        <div class="hot-dest-box">
        <strong>{__('周边目的地')}：</strong>
          <div class="hotlist">
            {st:destination action="query" flag="same" pid="$info['pid']" row="100" return="samedest"}
              {loop $samedest $next}
          	    <a href="{$GLOBALS['cfg_basehost']}/{$next['pinyin']}/">{$next['kindname']}</a>
              {/loop}
          </div>
        </div><!-- 目的地推荐 -->
        
        <div class="st-dest-cplist">
        	<div class="st-tabnav">
                {if ST_Functions::is_system_app_install(1)}
                {st:line action="query" flag="mdd" destid="$info['id']" row="8" return="linelist"}
                {if $linelist}
          	   <span data-url="{$GLOBALS['cfg_basehost']}/lines/{$info['pinyin']}/">{$channel['line']['channelname']}</span>
                {/if}
                {/if}
                {if ST_Functions::is_system_app_install(2)}
                {st:hotel action="query" flag="mdd" destid="$info['id']" row="8" return="hotellist"}
                {if $hotellist}
                <span data-url="{$GLOBALS['cfg_basehost']}/hotels/{$info['pinyin']}/">{$channel['hotel']['channelname']}</span>
                {/if}
                {/if}
                {if ST_Functions::is_system_app_install(3)}
                {st:car action="query" flag="recommend"  row="8" return="carlist"}
                {if $carlist}
                <span data-url="{$GLOBALS['cfg_basehost']}/cars/">{$channel['car']['channelname']}</span>
                {/if}
                {/if}
                {if ST_Functions::is_system_app_install(5)}
                {st:spot action="query" flag="mdd" destid="$info['id']" row="8" return="spotlist"}
                {if $spotlist}
                <span data-url="{$GLOBALS['cfg_basehost']}/spots/{$info['pinyin']}/">{$channel['spot']['channelname']}</span>
                {/if}
                {/if}
                {if ST_Functions::is_system_app_install(105)}
                {st:campaign action="query" flag="mdd" destid="$info['id']" row="8" bookstatus="2,3" return="list"}
                {if $list}
                <span data-url="{$GLOBALS['cfg_basehost']}/campaign/{$info['pinyin']}/">{$channel['campaign']['channelname']}</span>
                {/if}
                {/if}
                {if ST_Functions::is_system_app_install(104)}
                {st:ship action="query" flag="mdd" destid="$info['id']" row="8" return="shiplist"}
                {if $shiplist}
                <span data-url="{$GLOBALS['cfg_basehost']}/ship/{$info['pinyin']}/">{$channel['ship_line']['channelname']}</span>
                {/if}
                {/if}

            <a href="{$GLOBALS['cfg_basehost']}/lines/{$info['pinyin']}" class="more productmore" target="_blank">{__('更多')}</a>
            </div>
            {if ST_Functions::is_system_app_install(1)}

            {st:line action="query" flag="mdd" destid="$info['id']" row="8" return="linelist"}
            {if $linelist}
            <div class="st-tabcon">
            <ul class="st-cp-list">

                {php}$k=1;{/php}
                {loop $linelist $l}
                 <li {if $k%4==0}class="mr_0"{/if}>
                <div class="pic">
                  <img src="{Product::get_lazy_img()}" st-src="{Common::img($l['litpic'],285,194)}" alt="{$l['title']}"/>
                  <div class="buy"><a href="{$l['url']}" target="_blank">{__('立即预定')}</a></div>
                </div>
                <div class="js">
                  <a class="tit" href="{$l['url']}" target="_blank">{$l['title']}</a>
                  <p class="attr">
                      {loop $l['iconlist'] $ico}
                        <img src="{Product::get_lazy_img()}" st-src="{$ico['litpic']}" />
                      {/loop}

                  </p>
                  <p class="num">
                      {if !empty($l['storeprice'])}
                      <del>{__('原价')}：<i class="currency_sy">{Currency_Tool::symbol()}</i>{$l['storeprice']}</del>
                      {/if}
                            <span>
                                {if !empty($l['price'])}
                                  <b><i class="currency_sy">{Currency_Tool::symbol()}</i>{$l['price']}</b>{__('起')}
                                {else}
                                    {__('电询')}
                                {/if}
                            </span>
                  </p>
                </div>
              </li>
                {php}$k++{/php}
                {/loop}

            </ul>
            </div><!-- 线路 -->
            {/if}
            {/if}
            {if ST_Functions::is_system_app_install(2)}
            {st:hotel action="query" flag="mdd" destid="$info['id']" row="8" return="hotellist"}
            {if $hotellist}
            <div class="st-tabcon">
            <ul class="st-cp-list">

                {loop $hotellist $h}
                  <li {if $n%4==0}class="mr_0"{/if}>
                    <div class="pic">
                      <img src="{Product::get_lazy_img()}" st-src="{Common::img($h['litpic'],285,194)}" alt="{$h['title']}"/>
                      <div class="buy"><a href="{$h['url']}" target="_blank">{__('立即预定')}</a></div>
                    </div>
                    <div class="js">
                      <a class="tit" href="{$h['url']}" target="_blank">{$h['title']}</a>
                      <p class="num">
                          {if !empty($h['sellprice'])}
                          <del>{__('原价')}：<i class="currency_sy">{Currency_Tool::symbol()}</i>{$h['sellprice']}</del>
                          {/if}
                            <span>
                                {if !empty($h['price'])}
                                  <b><i class="currency_sy">{Currency_Tool::symbol()}</i>{$h['price']}</b>{__('起')}
                                {else}
                                    {__('电询')}
                                {/if}
                            </span>
                      </p>
                    </div>
                  </li>
                {/loop}

            </ul>
          </div><!-- 酒店 -->
            {/if}
            {/if}
            {if ST_Functions::is_system_app_install(3)}
            {st:car action="query" flag="recommend"  row="8" return="carlist"}
            {if $carlist}
            <div class="st-tabcon">
            <ul class="st-cp-list">
                {loop $carlist $c}
                  <li {if $n%4==0}class="mr_0"{/if}>
                <div class="pic">
                  <img src="{Product::get_lazy_img()}" st-src="{Common::img($c['litpic'],285,194)}" alt="{$c['title']}"/>
                  <div class="buy"><a href="{$c['url']}" target="_blank">{__('立即预定')}</a></div>
                </div>
                <div class="js">
                  <a class="tit" href="{$c['url']}" target="_blank">{$c['title']}</a>
                  <p class="num">
                    <em> {__('满意度')}:{$c['satisfyscore']}</em>
                            <span>
                                {if !empty($c['price'])}
                                  <b><i class="currency_sy">{Currency_Tool::symbol()}</i>{$c['price']}</b>{__('起')}
                                {else}
                                   {__('电询')}
                                {/if}
                            </span>
                  </p>
                </div>
              </li>
                {/loop}
            </ul>
            </div><!-- 租车 -->
            {/if}
            {/if}
            {if ST_Functions::is_system_app_install(5)}
            {st:spot action="query" flag="mdd" destid="$info['id']" row="8" return="spotlist"}
            {if $spotlist}
            <div class="st-tabcon">
            <ul class="st-cp-list">

                {loop $spotlist $s}
                  <li {if $n%4==0}class="mr_0"{/if}>
                    <div class="pic">
                      <img src="{Product::get_lazy_img()}" st-src="{Common::img($s['litpic'],285,194)}" alt="{$s['title']}"/>
                      <div class="buy"><a href="{$s['url']}" target="_blank"> {__('立即预定')}</a></div>
                    </div>
                    <div class="js">
                      <a class="tit" href="{$s['url']}" target="_blank">{$s['title']}</a>
                      <p class="num">
                          {if !empty($s['sellprice'])}
                          <del> {__('票面价')}：<i class="currency_sy">{Currency_Tool::symbol()}</i>{$s['sellprice']}</del>
                          {/if}
                            <span>
                                {if !empty($s['price'])}
                                  <b><i class="currency_sy">{Currency_Tool::symbol()}</i>{$s['price']}</b> {__('起')}
                                {else}
                                     {__('电询')}
                                {/if}
                            </span>
                      </p>
                    </div>
                  </li>
                {/loop}
            </ul>
            </div><!-- 景点 -->
            {/if}
            {/if}
            {if ST_Functions::is_system_app_install(105)}
            {st:campaign action="query" flag="mdd" destid="$info['id']" row="8" bookstatus="2,3" return="list"}
            {if $list}
            <div class="st-tabcon" style="display: block;">
                <ul class="hd-tab-block clearfix">

                    {loop $list $key $row}
                    <li  class="{if ($key+1)%4==0}mr_0{/if} {if $row['bookstatus']==3}end{/if}">
                        <span class="jd">{if $row['bookstatus']==2}报名中{elseif $row['bookstatus']==3}已结束{/if}</span>
                        <a class="pic" href="{$row['url']}" target="_blank"><img src="{Product::get_lazy_img()}" st-src="{Common::img($row['litpic'],285,194)}" alt="{$row['title']}" width="285" height="194"></a>
                        <a class="bt" href="{$row['url']}" target="_blank">
                            {$row['title']}
                            {loop $row['iconlist'] $icon}
                            <img src="{$icon['litpic']}"/>
                            {/loop}
                        </a>
                        <p class="info">
                            <span class="jr">已加入{$row['joining_number']}/{if $row['number']==-1}不限{else}{$row['number']}{/if}</span>
                            {if $row['price']}
                            <span class="jg"><em><i class="currency_sy">{Currency_Tool::symbol()}</i>{$row['price']}</em>起</span>
                            {else}
                            <span class="jg"><em>电询</em></span>
                            {/if}
                        </p>
                    </li>
                    {/loop}
                </ul>
            </div>
            {/if}
            {/if}
            {if ST_Functions::is_system_app_install(104)}
            {st:ship action="query" flag="mdd" destid="$info['id']" row="8" return="shiplist"}
            {if $shiplist}
            <div class="st-tabcon" style="display: block;">
                <ul class="ship-tab-block clearfix">

                    {loop $shiplist $key $ship}
                    <li class="{if ($key+1)%4==0}mr_0{/if}">
                        <div class="pic">
                            <a href="{$ship['url']}" target="_blank"><img src="{Product::get_lazy_img()}" st-src="{Common::img($ship['litpic'],283,193)}" width="285" height="194" alt="{$ship['title']}"></a>
                        </div>
                        <div class="txt">
                            <p class="bt"><a href="{$ship['url']}" target="_blank">{$ship['title']}</a></p>
                            <p class="date">{if !empty($ship['starttime'])}{date('m月d日',$ship['starttime'])}{/if}</p>
                            <p class="clearfix">
                                {if $ship['price']}
                                <span class="jg"><em><i class="currency_sy">{Currency_Tool::symbol()}</i><strong>{$ship['price']}</strong></em>起/人</span>
                                {else}
                                <span class="jg"><em><strong>电询</strong></em></span>
                                {/if}
                                <span class="start">{$ship['startcity_name']}</span>
                            </p>
                        </div>
                    </li>
                    {/loop}
                </ul>
            </div>

            {/if}
            {/if}
        </div><!-- 目的地产品列表 -->
              {if ST_Functions::is_system_app_install(4)}
        <div class="dest-hot-article">
        	<div class="st-tabnav">
          	<span class="on"> {__('热门')}{$channel['article']['channelname']}</span>
            <a class="more" href="/raiders/{$info['pinyin']}" target="_blank"> {__('更多')}</a>
          </div>
          <div class="st-tabcon">
          	<ul>
                {st:article action="query" flag="mdd" destid="$info['id']" row="4" return="articlelist"}
                {loop $articlelist $a}
            	    <li {if $n%2==0}class="mr_0"{/if}>
                        
                            <div class="pic"><a href="{$a['url']}" target="_blank"><img src="{Product::get_lazy_img()}" st-src="{Common::img($a['litpic'],183,124)}" alt="{$a['title']}" /></a></div>
                          <div class="nr">
                            <p class="bt"><a href="{$a['url']}" target="_blank">{$a['title']}</a></p>
                            <p class="txt">{Common::cutstr_html($a['summary'],70)}</p>
                            <p class="sj">
                                <span class="date">{Common::mydate('Y-m-d',$a['addtime'])}</span>
                              <span class="num">{$a['shownum']}</span>
                            </p>
                          </div>
                    </li>
            	{/loop}
            </ul>
          </div>
        </div><!-- 热门攻略 -->
            {/if}
              {if ST_Functions::is_system_app_install(101)}
            {st:notes action="destionation" destid="$info['id']" destpy="$info['pinyin']" row="6" return="notes"}
            <div class="travel-notes">
                <div class="st-tabnav">
                    <span class="on"> {__('精品游记')}</span>
                </div>
                <div class="st-tabcon">
                    <ul class="tn-list" id="notes_content">
                        {loop $notes['data'] $v}
                            <li>
                                <div class="pic"><a href="{$v['url']}" target="_blank"><img src="{Common::img($v['litpic'],220,150)}" alt="{$v['title']}" width="220" height="150" /></a></div>
                                <div class="nr">
                                    <h3 class="tit"><a href="{$v['url']}" target="_blank">{$v['title']}</a></h3>
                                    <p class="txt">{$v['description']}</p>
                                    <p class="info">
                                        <span class="name"><img src="{Common::img($v['member']['litpic'],27,27)}" />{$v['member']['nickname']}</span>
                                        <span class="date">{date('Y-m-d H:i:s',$v['modtime'])}</span>
                                        <span class="read">{$v['shownum']} {__('人')} {__('已')} {__('阅读')}<i class="ico"></i></span>
                                    </p>
                                </div>
                            </li>
                        {/loop}
                    </ul>
                </div>
                <div class="main_mod_page clear" id="notes_page" data="{'destid':{$info['id']},destpy:'{$info['pinyin']}'}">
                    {$notes['page']}
                </div>
                {Common::js('template.js')}
                <script id="notes_content_template" type="text/html">
                    {{each data as $v}}
                    <li>
                        <div class="pic"><a href="{{$v['url']}}" target="_blank"><img src="{{$v.litpic}}" alt="{{$v.title}}" width="220" height="150" /></a></div>
                        <div class="nr">
                            <h3 class="tit"><a href="{{$v.url}}" target="_blank">{{$v.title}}</a></h3>
                            <p class="txt">{{$v['description']}}</p>
                            <p class="info">
                                <span class="name"><img src="{{$v.member.litpic}}" />{{$v.member.nickname}}</span>
                                <span class="date">{{$v.modtime}}</span>
                                <span class="read">{{$v.shownum}}{__('人')} {__('已')} {__('阅读')}<i class="ico"></i></span>
                            </p>
                        </div>
                    </li>
                    {{/each}}
                </script>
                <script>
                 $(function(){
                     $('#notes_page').find('a').live('click',function(){
                         $('#notes_content').html('<img src="/res/images/loading.gif" style="display:block;width:28px;height:28px;margin:160px auto 157px auto;">');
                         var url=$(this).attr('data');
                         var data=eval("("+$('#notes_page').attr('data')+")");
                         $.get(url,data,function(list){
                             $('#notes_content').html(template('notes_content_template', list));
                             $('#notes_page').html(list['page']);
                         },'json')
                         return false;
                     });
                 })
                </script>
                <!-- 翻页 -->
            </div>
            <!-- 精品游记 -->
            {/if}
      </div>
    	
    </div>
  </div>
  
{request "pub/footer"}
<script>
    $(function(){
        $('.st-dest-cplist').switchTab({
            effect:'fade'
        })

        $(".st-tabnav span").click(function(){
            var url = $(this).attr('data-url');
            $('.productmore').attr('href',url);
        })
        //更多图片
        $(".morepic").click(function(){
            var url = $(this).attr('data-url');
            location.href = url;
        })
        //展开详情
        $('.dest-sum-txt').find('.more').click(function(){
            $(this).parent().addClass('hide').siblings('div').removeClass('hide');
        });

    })
</script>

</body>
</html>
