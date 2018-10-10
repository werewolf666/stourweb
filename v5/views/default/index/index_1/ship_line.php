
{if $channel['ship_line']['isopen']==1}
<div class="st-slideTab">
    <div class="st-tabnav">
        <h3 class="yl-bt">{$channel['ship_line']['channelname']}</h3>
        {st:dest action="query" flag="channel_nav" row="6" typeid="104" return="linedest"}
        {loop $linedest $ld}
        <span data-id="{$ld['id']}">{$ld['kindname']}</span>
        {/loop}
        <a href="{$cmsurl}ship/" class="more">{__('更多')}{$channel['ship_line']['channelname']}</a>
    </div>
    {loop $linedest $ld}
    <div class="st-tabcon">
        <ul class="st-cp-list">
            {st:ship action="query" flag="mdd" destid="$ld['id']" row="6" return="linelist"}
            {loop $linelist $l}
            <li>
                <div class="pic">
                    <img src="{Product::get_lazy_img()}" st-src="{Common::img($l['litpic'],285,194)}" alt="{$l['title']}"/>
                    <div class="buy"><a href="{$l['url']}" target="_blank" title="{$l['title']}">{__('立即预定')}</a></div>
                </div>
                <div class="js">
                    <a class="tit" href="{$l['url']}" target="_blank" title="{$l['title']}">{$l['title']}</a>
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
            {/loop}
        </ul>
    </div>

    {/loop}
    <div class="st-adimg">
        {st:ad action="getad" name="IndexRightShipAd1" pc="1" return="linead1"}
        {if !empty($linead1)}
        <a href="{$linead1['adlink']}" target="_blank"><img class="fl" src="{Product::get_lazy_img()}" st-src="{Common::img($linead1['adsrc'],279,610)}" alt="{$linead1['adname']}"/></a>
        {/if}
        {/st}
    </div>
</div><!--旅游线路结束-->
{st:ad action="getad" name="IndexBannerShipAd1" pc="1" return="linead2"}
{if !empty($linead2)}
<div class="st-list-sd">
    <a href="{$linead2['adlink']}" target="_blank"><img class="fl" src="{Product::get_lazy_img()}" st-src="{Common::img($linead2['adsrc'],1200,110)}" alt="{$linead2['adname']}"></a>
</div>
{/if}
{/if}
