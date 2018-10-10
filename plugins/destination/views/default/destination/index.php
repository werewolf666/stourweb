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
    {include "pub/varname"}
    {Common::css('dest.css,base.css,extend.css')}
    {Common::js('jquery.min.js,base.js,common.js,slideTabs.js,delayLoading.min.js')}

<script>
	$(function(){
		$('.st-tabdest').switchTab()
	})
</script>
</head>
<body>

  {request "pub/header"}
  <div class="dest-home-top">
      {st:ad action="getad" name="s_dest_index_1" pc="1" return="ad"}
      {if !empty($ad)}
      <a href="{if empty($ad['adlink'])}javascript:;{else}{$ad['adlink']}{/if}" class="dest-pic" target="_blank"><img  src="{Product::get_lazy_img()}" st-src="{Common::img($ad['adsrc'],1920,386)}" alt="{$ad['adname']}"></a>
      {else}
      <img class="dest-pic" src="{$GLOBALS['cfg_public_url']}images/dest-page-banner.jpg" />
      {/if}

    <div class="dset-searchBox">
      <div class="searchCon">
        <input type="text" class="dest-text destname" placeholder="{__('我要去')}..." />
        <input type="button" class="dest-btn" />
      </div>
    </div>
  </div><!-- 目的地首页搜索 -->
  <div class="big">
  	<div class="wm-1200">

    	<div class="st-main-page">
				
		<div class="hot-dest-advimg">
        	<h3>{__('热门目的地')}</h3>
          <div class="adv-conlist">
              {st:destination action="query" flag="hot" row="7" typeid="12"}
                {loop $data $row}
          	    <a class="pic0{$n}" href="{$GLOBALS['cfg_basehost']}/{$row['pinyin']}/" target="_blank">{if $n<4}<span class="img-t"><img src="{Product::get_lazy_img()}" st-src="{Common::img($row['litpic'],386,263)}" alt="{$row['kindname']}" /></span>{else}<span class="img-b"><img src="{Product::get_lazy_img()}" st-src="{Common::img($row['litpic'],285,194)}" alt="{$row['kindname']}" /></span>{/if}<span class="bt"><strong>{$row['kindname']}</strong></span></a>
                {/loop}
          </div>
        </div><!-- 目的地照片墙 -->
            <!--栏目介绍-->
            {if !empty($seoinfo['jieshao'])}
            <div class="st-comm-introduce">
                <div class="st-comm-introduce-txt">
                    {$seoinfo['jieshao']}
                </div>
            </div>
            {/if}
        
        <div class="whole-destlist">
        	<h3>{__('全部目的地')}</h3>
          <div class="st-tabdest">
          	<div class="st-tabnav">
              {st:destination action="query" flag="top" row="50" isindex="1" return="destlist"}
                {loop $destlist $dest}
                 <span>{$dest['kindname']}</span>
                {/loop}
            </div>
            {loop $destlist $d}

                <div class="st-tabcon">
                    {st:destination action="query" flag="next" typeid="12" isindex="1" pid="$d['id']" return="secondmdd" row="100"}
                    {loop $secondmdd $sd}
            	<dl>
              	<dt><a href="{$GLOBALS['cfg_basehost']}/{$sd['pinyin']}/">{$sd['kindname']}</a></dt>
                <dd>
                    {st:destination action="query" flag="next" typeid="12" isindex="1" pid="$sd['id']" return="thirdmdd" row="100"}
                     {loop $thirdmdd $nd}
                	  <a href="{$GLOBALS['cfg_basehost']}/{$nd['pinyin']}/">{$nd['kindname']}</a>
                     {/loop}
                    {/st}

                </dd>
              </dl>
                    {/loop}
            </div>

            {/loop}
          </div>
        </div>
        
      </div>
    	
    </div>
  </div>
  
  {request "pub/footer"}
  {request "pub/flink"}
  {Common::js('result/result.js',0)}
  <link type="text/css" href="{$GLOBALS['cfg_public_url']}js/result/result.css" rel="stylesheet" />
  <script>
      $(function(){
          $('.destname').Result({url:SITEURL+'destination/ajax_dest_by_pinyin',extraParams:{typeid:0,parents:true}});

          //搜索进目的地
          $(".dest-btn").click(function(){
              var destname = $(".destname").val();
              if(destname==''){
                    $(".destname").focus();
              }else{
                  var url = SITEURL+'destination/search?destname='+encodeURIComponent(destname);
                  location.href= url;
              }
          })

      })
  </script>
</body>
</html>
