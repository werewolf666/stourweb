{if $channel['line']['isopen']==1}
<!--旅游线路开始-->
<div class="line_trip_box">
    <div class="product_box">
        <div class="con_list">
            <div class="st-tabnav">
                <h3>{$channel['line']['channelname']}</h3>
                {st:dest action="query" flag="channel_nav" row="6" typeid="1" return="linedest"}
                {loop $linedest $ld}
                <span>{$ld['kindname']}</span>
                {/loop}
                {/st}
                <a class="more" href="{$GLOBALS['cfg_basehost']}/lines/all">{__('更多')}</a>
            </div>
            {loop $linedest $ld}
            <div class="st-tabcon">
                <ul>
                    {st:line action="query" flag="mdd" destid="$ld['id']" row="6" return="linelist" }
                    {php}$k=1;{/php}
                    {loop $linelist $ll}

                    <li {if $k%3==0}class="mr_0"{/if}>
                        <a class="pic" href="{$ll['url']}" target="_blank" title="{$ll['title']}"><img src="{Product::get_lazy_img()}" st-src="{Common::img($ll['litpic'],294,200)}" alt="{$ll['title']}"/></a>
                        <a class="tit" href="{$ll['url']}" target="_blank" title="{$ll['title']}">{if $ll['color']}<span style="color:{$l['color']}">{$ll['title']}</span>{else}{$ll['title']}{/if}</a>
                          <span class="price">
                               {if !empty($ll['price'])}
                                   <i class="currency_sy">{Currency_Tool::symbol()}</i><b>{$ll['price']}</b>{__('起')}
                                  {else}
                                    {__('电询')}
                               {/if}
                          </span>
                    </li>
                    {php}$k++;{/php}
                    {/loop}

                </ul>
            </div>
            {/loop}
            {st:ad action="getad" name="Index3LineRightAd" pc="1" row="1" return="linead"}
            {if !empty($linead)}
            <div class="ad_img">
                <a href="{$linead['adlink']}"  class="fl clearfix" target="_blank"><img src="{Product::get_lazy_img()}" st-src="{Common::img($linead['adsrc'],240,566)}" alt="{$linead['adname']}"></a>
            </div>
            {/if}
        </div>
    </div>
</div>
<!--旅游线路结束-->
{/if}