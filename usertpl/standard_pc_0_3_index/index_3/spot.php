{if $channel['spot']['isopen']==1}
<!--景点门票开始-->
<div class="spot_trip_box">
    <div class="product_box">
        <div class="con_list">
            <div class="st-tabnav">
                <h3>{$channel['spot']['channelname']}</h3>
                {st:dest action="query" flag="channel_nav" row="6" typeid="5" return="spotdest"}
                {loop $spotdest $sd}
                <span>{$sd['kindname']}</span>
                {/loop}
                {/st}
                <a class="more" href="{$GLOBALS['cfg_basehost']}/spots/all">{__('更多')}</a>
            </div>
            {loop $spotdest $sd}
            <div class="st-tabcon">
                <ul>
                    {st:spot action="query" flag="mdd" destid="$sd['id']" row="6" return="slist"}
                    {php}$sindex=1;{/php}
                    {loop $slist $s}
                    <li {if $sindex%3==0}class="mr_0"{/if}>
                        <a class="pic" href="{$s['url']}" target="_blank" title="{$s['title']}"><img src="{Product::get_lazy_img()}" st-src="{Common::img($s['litpic'],294,200)}" alt="{$s['title']}"/></a>
                        <a class="tit" href="{$s['url']}" target="_blank" title="{$s['title']}">{$s['title']}</a>
                              <span class="price">
                                   {if !empty($s['price'])}
                                   <i class="currency_sy">{Currency_Tool::symbol()}</i><b>{$s['price']}</b>{__('起')}
                                  {/if}
                              </span>
                    </li>
                    {php}$sindex++;{/php}
                    {/loop}
                    {/st}

                </ul>
            </div>
            {/loop}
            {st:ad action="getad" name="Index3SpotRightAd" pc="1" row="1" return="spotad"}
            {if !empty($spotad)}
            <div class="ad_img"><a href="{$spotad['adlink']}" target="_blank"><img class="fl" src="{Product::get_lazy_img()}" st-src="{Common::img($spotad['adsrc'],240,566)}" alt="{$spotad['adname']}"/></a></div>
            {/if}
        </div>
    </div>
</div>
<!--景点门票结束-->
{/if}