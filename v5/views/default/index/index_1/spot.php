{if $channel['spot']['isopen']==1}
<div class="st-slideTab">
    <div class="st-tabnav">
        <h3 class="mp-bt">{$channel['spot']['channelname']}</h3>
        {st:dest action="query" flag="channel_nav" row="6" typeid="5" return="spotdest"}
        {loop $spotdest $sd}
        <span data-id="{$sd['id']}">{$sd['kindname']}</span>
        {/loop}
        <a href="/spots/" class="more">{__('更多')}{$channel['spot']['channelname']}</a>
    </div>
    {loop $spotdest $sd}
    <div class="st-tabcon">
        <ul class="st-cp-list">
            {st:spot action="query" flag="mdd" destid="$sd['id']" row="6" return="spotlist"}
            {loop $spotlist $s}
            <li>
                <div class="pic">
                    <img src="{Product::get_lazy_img()}" st-src="{Common::img($s['litpic'],285,194)}" alt="{$s['title']}"/>
                    <div class="buy"><a href="{$s['url']}" target="_blank" title="{$s['title']}">{__('立即预定')}</a></div>
                </div>
                <div class="js">
                    <a class="tit" href="{$s['url']}" target="_blank" title="{$s['title']}">{$s['title']}</a>
                    <p class="attr">
                        {loop $s['iconlist'] $ico}
                        <img src="{$ico['litpic']}" />
                        {/loop}
                    </p>
                    <p class="num">
                        {if !empty($s['sellprice'])}
                        <del>{__('原价')}：<i class="currency_sy">{Currency_Tool::symbol()}</i>{$s['sellprice']}</del>
                        {/if}
                            <span>
                                {if !empty($s['price'])}
                                  <b><i class="currency_sy">{Currency_Tool::symbol()}</i>{$s['price']}</b>{__('起')}
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
    {/loop}

    <div class="st-adimg">
        {st:ad action="getad" name="IndexSpotAd1" return="spotad1"}
        {if !empty($spotad1)}
        <a href="{$spotad1['adlink']}" target="_blank"><img class="fl" src="{Product::get_lazy_img()}" st-src="{Common::img($spotad1['adsrc'],279,610)}" alt="{$spotad1['adname']}"/></a>
        {/if}
        {/st}

    </div>
</div><!--景点门票结束-->
{st:ad action="getad" name="IndexSpotAd2" return="spotad2"}
{if !empty($spotad2)}
<div class="st-list-sd">
    <a href="{$spotad2['adlink']}" target="_blank"><img class="fl" src="{Product::get_lazy_img()}" st-src="{Common::img($spotad2['adsrc'],1200,110)}" alt="{$spotad2['adname']}"></a>
</div>
{/if}

{/if}