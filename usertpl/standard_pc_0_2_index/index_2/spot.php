<!--景点门票开始-->
{if $channel['spot']['isopen']==1}
<div class="spot_trip_box">
    <div class="trip_tit">{$channel['spot']['channelname']}</div>
    <div class="product_box">
        <div class="sidle_hot">
            <h3>{__('推荐')}{$channel['spot']['channelname']}</h3>
            <ul>
                {st:spot action="query" flag="order" row="6" return="recspot"}
                {loop $recspot $s}
                <li>
                    <s></s>
                    <div class="pic"><a href="{$s['url']}" target="_blank" title="{$s['title']}"><img class="lazyimg" src="{Product::get_lazy_img()}" st-src="{Common::img($s['litpic'],88,60)}" alt="{$s['title']}"></a></div>
                    <p class="tit"><a href="{$s['url']}" target="_blank" title="{$s['title']}">{$s['title']}</a></p>
                    <p class="jg">{if $s['price']>0}<i class="currency_sy">{Currency_Tool::symbol()}</i>{$s['price']}{else}{__('电询')}{/if}</p>
                </li>
                {/loop}
                {/st}
            </ul>
        </div>
        <div class="con_list">
            <div class="st-tabnav">
                {st:dest action="query" flag="channel_nav" row="6" typeid="5" return="spotdest"}
                {loop $spotdest $sd}
                <span>{$sd['kindname']}</span>
                {/loop}
                <a class="more" href="{$GLOBALS['cfg_basehost']}/spots/all">{__('更多景点')}</a>
            </div>
            {loop $spotdest $sd}
            <div class="st-tabcon" style="display: block;">
                <ul>
                    {st:spot action="query" flag="mdd" destid="$sd['id']" row="8" return="slist"}
                    {php}$s=1;{/php}
                    {loop $slist $h}
                    <li {if $s%4==0}class="mr_0"{/if}>
                        <a class="pic" href="{$h['url']}" target="_blank" title="{$h['title']}"><img class="lazyimg" alt="{$h['title']}" src="{Product::get_lazy_img()}" st-src="{Common::img($h['litpic'],180,122)}"></a>
                        <a class="tit" href="{$h['url']}" target="_blank" title="{$h['title']}">{$h['title']}</a>
                              <span class="price">
                                  {if !empty($h['price'])}
                                   <i class="currency_sy">{Currency_Tool::symbol()}</i><b>{$h['price']}</b>{__('起')}
                                  {/if}
                              </span>
                    </li>
                    {php}$s++{/php}
                    {/loop}
                    {/st}
                </ul>
            </div>
            {/loop}

            {st:ad action="getad" name="Index2SpotRightAd" pc="1" return="spotad"}
            {if !empty($spotad)}
            <div class="ad_img">
                <a href="{$spotad['adlink']}" style="margin-bottom:10px;" class="fl clearfix" target="_blank"><img src="{Product::get_lazy_img()}" st-src="{Common::img($spotad['adsrc'],200,424)}" alt="{$spotad['adname']}"></a>
            </div>
            {/if}
        </div>
    </div>
</div>
{/if}
<!--景点门票结束-->