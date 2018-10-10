<!doctype html>
<html>
<head  >
<meta charset="utf-8">
<title>{$seoinfo['seotitle']}-{$webname}</title>
    {if $seoinfo['keyword']}
    <meta name="keywords" content="{$seoinfo['keyword']}"/>
    {/if}
    {if $seoinfo['description']}
    <meta name="description" content="{$seoinfo['description']}"/>
    {/if}
    {$GLOBALS['cfg_indexcode']}
    {include "pub/varname"}
    {Common::css("base.css,sub/index.css",false)}
    {Common::js("jquery.min.js,base.js,common.js",false)}
</head>

<body>
  {request 'pub/header'}
  
  <div class="focus_img">
    <div id="_banners" class="banners">
        {st:ad action="getad" name="s_index_1" pc="1" return="ad"}
        {loop $ad['aditems'] $v}
      <div class="banner"><a href="{$v['adlink']}"><img src="{Common::img($v['adsrc'],1920,420)}" alt="{$v['adname']}" /></a></div>
        {/loop}
        {/st}
    </div>
    <div id="_focus" class="focus">
        {loop $ad['aditems'] $k}
      <a data-index="{php}echo $n-1;{/php}" href="javascript:void(0);" {if $n==1}class="on"{/if}><span class="bg-b"></span><span class="inner"></span></a>
        {/loop}
    </div>
  </div><!--滚动焦点图结束-->

  <div class="big pledge-content">
      <div class="wm-1200">

          <div class="pledge-block clearfix">
              <ul>
                  <li class="ico01">
                      <strong>{__('当地实力')}</strong>
                      <span>{__('专注当地旅游，汇聚实力')}</span>
                  </li>
                  <li class="ico02">
                      <strong>{__('安全保障')}</strong>
                      <span>{__('合法组团，安全诚信')}</span>
                  </li>
                  <li class="ico03">
                      <strong>{__('信心保障')}</strong>
                      <span>{__('出团量大，成团率高')}</span>
                  </li>
                  <li class="ico04">
                      <strong>{__('价格保障')}</strong>
                      <span>{__('甄选产品，高性价比')}</span>
                  </li>
                  <li class="ico05">
                      <strong>{__('品质服务')}</strong>
                      <span>{__('客户见证，以心换心')}</span>
                  </li>
              </ul>
          </div>

      </div>
  </div>

  <div class="big">
  	<div class="wm-1200">
    {if $channel['line']['isopen']==1}
      <div class="st-slideTab">
          <div class="st-tabnav">
            <h3>{__('旅游线路')}</h3>
            <a href="{$cmsurl}lines/" class="more">{__('更多线路')}</a>
          </div>		  			  
          <div class="st-adimg">
              {st:ad action="getad" name="s_index_2" pc="1" return="ads"}
              <a href="{$ads['adlink']}"><img class="fl" src="{Common::img($ads['adsrc'],279,610)}" alt="{$ads['adname']}" /></a>	   {/st}
          </div>		 	
          <div class="st-tabcon">
              <ul class="st-cp-list">
                  {st:line action="query" flag="mdd" destid="$GLOBALS['sys_webid']" row="6" return="linelist"}
                  {php $k=1;}
                  {loop $linelist $l}
                <li {if $k%3==0} class="mr_0" {/if}>
                    <div class="pic">
                    <img class="fl" src="{Common::img($l['litpic'],285,194)}" alt="{$l['title']}" width="285" height="194"/>
                    <div class="buy"><a href="{$l['url']}">{__('立即抢购')}</a></div>
                  </div>
                  <div class="js">
                    <a class="tit" href="{$l['url']}">{$l['title']}</a>
                    <p class="attr">
                        {loop $l['iconlist'] $ico}
                        <img src="{$ico['litpic']}" />
                        {/loop}
                    </p>
                    <p class="num">
                        {if !empty($l['storeprice'])}
                        <del>{__('原价')}：<i class="currency_sy">{Currency_Tool::symbol()}</i>{$l['sellprice']}</del>
                        {/if}
                      <span>
                        {if !empty($l['price'])}
                          <b><i class="currency_sy">{Currency_Tool::symbol()}</i>{$l['price']}</b>{__('元')}{__('起')}
                        {else}
                            {__('电询')}
                        {/if}
                      </span>
                    </p>
                  </div>
                </li>
                   {php $k++;}
                  {/loop}
                  {/st}
              </ul>
          </div>
      </div><!--热门线路--> {st:ad action="getad" name="s_index_3" pc="1" return="ads"}	 {if $ads}
      <div class="st-list-sd">
          <a href="{$ads['adlink']}"><img class="fl" src="{Common::img($ads['adsrc'],1200,110)}" alt="{$ads['adname']}" /></a>
      </div><!--广告-->	  {/if}  {/st}
    {/if}
    {if $channel['hotel']['isopen']==1}
      <div class="st-slideTab">
      	<div class="st-tabnav">
          <h3>{__('热门酒店')}</h3>
          <a href="{$cmsurl}hotels/" class="more">{__('更多酒店')}</a>
        </div>
        <div class="st-adimg">
            {st:ad action="getad" name="s_index_4" pc="1" return="ads"}
            <a href="{$ads['adlink']}"><img class="fl" src="{Common::img($ads['adsrc'],279,610)}" alt="{$ads['adname']}"/></a>
            {/st}
        </div>
        <div class="st-tabcon">
        	<ul class="st-cp-list">
            {st:hotel action="query" flag="mdd" destid="$GLOBALS['sys_webid']" row="6" return="hotellist"}
						{php $k=1;}
            {loop $hotellist $h}
						<li {if $k%3==0} class="mr_0" {/if}>
            	<div class="pic">
              	<img class="fl" src="{Common::img($h['litpic'],285,194)}"  width="285" height="194" alt="{h['title']}"/>
                <div class="buy"><a href="{$h['url']}">{__('立即预订')}</a></div>
              </div>
              <div class="js">
              	<a class="tit" href="{$h['url']}">{$h['title']}</a>
                <p class="num">
                    {if !empty($h['sellprice'])}
                    <del>{__('原价')}：<i class="currency_sy">{Currency_Tool::symbol()}</i>{$h['sellprice']}</del>
                    {/if}
                  <span>
                    {if !empty($h['price'])}
                      <b><i class="currency_sy">{Currency_Tool::symbol()}</i>{$h['price']}</b>{__('元')}{__('起')}
                    {else}
                        {__('电询')}
                    {/if}
                  </span>
                </p>
              </div>
            </li>
						{php $k++;}
            {/loop}
            {/st}
          </ul>
        </div>
      </div><!--热门酒店结束--> {st:ad action="getad" name="s_index_5" pc="1" return="ads"}	 {if $ads}
      <div class="st-list-sd">
         
          <a href="{$ads['adlink']}"><img class="fl" src="{Common::img($ads['adsrc'],1200,110)}" alt="{$ads['adname']}"/></a>
        
      </div><!--广告-->	  {/if}  {/st}
    {/if}
    {if $channel['spot']['isopen']==1}
      <div class="st-slideTab">
      	<div class="st-tabnav">
          <h3>{__('景点门票')}</h3>
          <a href="{$cmsurl}spots/" class="more">{__('更多景点')}</a>
        </div>
        <div class="st-tabcon">
        	<ul class="st-cp-list tuan-list">
                {st:spot action="query" flag="mdd" destid="$GLOBALS['sys_webid']" row="4" return="spotlist"}
                    {loop $spotlist $s}
                        <li {if $n%4==0}class="mr_0"{/if}>
                            <div class="pic">
                                <img class="fl" src="{Common::img($s['litpic'],285,194)}" width="285" height="194" alt="{$s['title']}" />
                                <div class="buy"><a href="{$s['url']}">{__('立即预订')}</a></div>
                            </div>
                            <div class="js">
                                <a class="tit" href="{$s['url']}">{$s['title']}</a>
                                <p class="num">
                                    {if !empty($s['sellprice'])}
                                    <del>{__('原价')}：<i class="currency_sy">{Currency_Tool::symbol()}</i>{$s['sellprice']}</del>
                                    {/if}
                                    <span>
                                    {if !empty($s['price'])}
                                      <b><i class="currency_sy">{Currency_Tool::symbol()}</i>{$s['price']}</b>{__('元')}{__('起')}
                                    {else}
                                        {__('电询')}
                                    {/if}
                                    </span>
                                </p>
                            </div>
                        </li>
                    {/loop}
                {/st}
          </ul>
        </div>
      </div><!--景点门票-->
{st:ad action="getad" name="s_index_6" pc="1" return="ads"}	{if $ads}
      <div class="st-list-sd">
          
          <a href="{$ads['adlink']}"><img class="fl" src="{Common::img($ads['adsrc'],1200,110)}" alt="{$ads['adname']}"/></a>
         
      </div><!--广告-->	  {/if}	   {/st}
        {/if}
        {if $channel['car']['isopen']==1}
      <div class="st-slideTab">
      	<div class="st-tabnav">
          <h3>{__('旅游租车')}</h3>
          <a href="{$cmsurl}cars/" class="more">{__('更多租车')}</a>
        </div>
        <div class="st-tabcon">
        	<ul class="st-car-list">
            {st:car action="query" flag="recommend" destid="$GLOBALS['sys_destid']"  row="5" return="carlist"}
            {loop $carlist $c}
          	<li {if $n%5==0}class="mr_0"{/if}>
            	<div class="pic">
              	<img class="fl" src="{Common::img($c['litpic'],224,152)}" width="224" height="152" alt="{$c['title']}"/>
                <div class="buy"><a href="{$c['url']}">{__('立即预订')}</a></div>
              </div>
              <div class="js">
              	<a class="tit" href="{$c['url']}">{$c['title']}</a>
                <p class="num">
                    {if !empty($c['sellprice'])}
                    <del>{__('原价')}：<i class="currency_sy">{Currency_Tool::symbol()}</i>{$c['sellprice']}</del>
                    {/if}
                    <span>
                        {if !empty($c['price'])}
                          <b><i class="currency_sy">{Currency_Tool::symbol()}</i>{$c['price']}</b>{__('元')}{__('起')}
                        {else}
                            {__('电询')}
                        {/if}
                    </span>
                </p>
              </div>
            </li>
            {/loop}
          </ul>
        </div>
      </div><!--旅游租车结束-->  {st:ad action="getad" name="s_index_7" pc="1" return="ads"}	  {if $ads}
      <div class="st-list-sd">
        
          <a href="{$ads['adlink']}"><img class="fl" src="{Common::img($ads['adsrc'],1200,110)}" alt="{$ads['adname']}"/></a>
        
      </div><!--广告-->	  {/if}  {/st}
    {/if}
    {if $channel['article']['isopen']==1}
      <div class="st-slideTab">
      	<div class="st-tabnav">
          <h3 class="gl-bt">{__('旅游攻略')}</h3>
          <span class="on">{__('最新攻略')}</span>
          <span>{__('推荐攻略')}</span>
          <a href="{$cmsurl}raiders/" class="more">{__('更多攻略')}</a>
        </div>
          <!--最新攻略-->
        <div class="st-tabcon">
        	<ul class="st-gl-list">
                {st:article action="query" flag="mdd_new" destid="$GLOBALS['sys_destid']" row="7" return="articlelist"}
                {loop $articlelist $a}
                {if $n==1}
          	<li class="ml_0">
            	<a class="fl" href="{$a['url']}"><i class="hot">HOT</i><img class="fl" src="{Common::img($a['litpic'],386,263)}"    width="386" height="263" alt="{$a['title']}"/></a>
              <a class="tit" href="{$a['url']}">{$a['title']}</a>
              <p class="txt">{if !empty($a['summary'])}{Common::cutstr_html($a['summary'],70)}{else}{Common::cutstr_html($a['content'],70)}{/if}</p>
            </li>
                {elseif $n<4}
          	<li>
            	<a class="fl" href="{$a['url']}"><i class="hot">HOT</i><img class="fl" src="{Common::img($a['litpic'],180,122)}"  width="180" height="122" alt="{$a['title']}"/></a>
              <div class="con">
                <a class="tit" href="{$a['url']}">{$a['title']}</a>
                <p class="txt">{if !empty($a['summary'])}{Common::cutstr_html($a['summary'],70)}{else}{Common::cutstr_html($a['content'],70)}{/if}</p>
              </div>
            </li>
             {elseif $n>3}
          	<li>
              <a class="tit" href="{$a['url']}">{$a['title']}</a>
              <p class="txt">{if !empty($a['summary'])}{Common::cutstr_html($a['summary'],70)}{else}{Common::cutstr_html($a['content'],70)}{/if}</p>
            </li>
                {/if}
            {/loop}
            {/st}
          </ul>
        </div>
          <!--推荐-->
          <div class="st-tabcon" style="display: none">
              <ul class="st-gl-list">
                  {st:article action="query" flag="mdd_order" destid="$GLOBALS['sys_destid']" return="articlelist"}
                  {loop $articlelist $a}
                  {if $n==1}
                  <li class="ml_0">
                      <a class="fl" href="{$a['url']}"><i class="hot">HOT</i><img class="fl" src="{Common::img($a['litpic'],386,298)}" alt="{$a['title']}" width="386" height="298" /></a>
                      <a class="tit" href="{$a['url']}">{$a['title']}</a>
                      <p class="txt">{if !empty($a['summary'])}{Common::cutstr_html($a['summary'],70)}{else}{Common::cutstr_html($a['content'],70)}{/if}</p>
                  </li>
                  {elseif $n<4}
                  <li>
                      <a class="fl" href="{$a['url']}"><i class="hot">HOT</i><img class="fl" src="{Common::img($a['litpic'],180,154)}" alt="{$a['title']}" width="180" height="154" /></a>
                      <div class="con">
                          <a class="tit" href="{$a['url']}">{$a['title']}</a>
                          <p class="txt">{if !empty($a['summary'])}{Common::cutstr_html($a['summary'],70)}{else}{Common::cutstr_html($a['content'],70)}{/if}</p>
                      </div>
                  </li>				  
                  {elseif $n>3}
                  <li {if $n==8}class="ml_0"{/if}>
                      <a class="tit" href="{$a['url']}">{$a['title']}</a>
                      <p class="txt">{if !empty($a['summary'])}{Common::cutstr_html($a['summary'],70)}{else}{Common::cutstr_html($a['content'],70)}{/if}</p>
                  </li>
                  {/if}
                  {/loop}
                  {/st}
              </ul>
          </div>
      </div><!--旅游攻略结束-->
       {st:ad action="getad" name="s_index_7" pc="1" return="ads"}		   {if $ads}
      <div class="st-list-sd">
         
          <a href="{$ads['adlink']}"><img class="fl" src="{Common::img($ads['adsrc'],1200,110)}" alt="{$ads['adname']}"/></a>
         
      </div><!--广告-->	  {/if} {/st}
      {/if}
    </div>
  </div>
  {request 'pub/footer'}
  {request "pub/flink"}

</body>
{Common::js("fcous.js,slideTabs.js")}
<script>
    $(function(){
        $('.st-slideTab').switchTab()
    })
</script>

</html>
