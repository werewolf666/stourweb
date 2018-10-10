{if $channel['tuan']['isopen']==1}
<div class="st-slideTab">
    <div class="st-tabnav">
        <h3 class="tg-bt">{$channel['tuan']['channelname']}</h3>
        {st:dest action="query" flag="channel_nav" row="6" typeid="13" return="tuandest"}
        {loop $tuandest $td}
        <span data-id="{$td['id']}">{$td['kindname']}</span>
        {/loop}

        <a href="{$cmsurl}tuan/" class="more">{__('更多')}{$channel['tuan']['channelname']}</a>
    </div>
    {loop $tuandest $td}
    <div class="st-tabcon">
        <ul class="st-cp-list tuan-list">
            {st:tuan action="query" flag="mdd" destid="$td['id']" row="8" return="tuanlist"}
            {php}$k=1;{/php}
            {loop $tuanlist $t}
            <li {if $k%4==0}class="mr_0"{/if}>
                <div class="pic">
                    <img src="{Product::get_lazy_img()}" st-src="{Common::img($t['litpic'],285,194)}" alt="{$t['title']}"/>
                    <div class="buy"><a href="{$t['url']}" target="_blank" title="{$t['title']}">{__('立即预定')}</a></div>
                </div>
                <div class="js">
                    <a class="tit" href="{$t['url']}" target="_blank" title="{$t['title']}">{$t['title']}</a>
                    <p class="attr">
                        {loop $t['iconlist'] $ico}
                        <img src="{$ico['litpic']}" />
                        {/loop}
                    </p>
                    <p class="num">
                        {if !empty($t['sellprice'])}
                        <del>{__('原价')}：<i class="currency_sy">{Currency_Tool::symbol()}</i>{$t['sellprice']}</del>
                        {/if}
                            <span>
                                {if !empty($t['price'])}
                                  <b><i class="currency_sy">{Currency_Tool::symbol()}</i>{$t['price']}</b>{__('起')}
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
    </div>
    {/loop}
</div><!--特价团购结束-->
{st:ad action="getad" name="IndexTuanAd1" return="tuanad1"}
{if !empty($tuanad1)}
<div class="st-list-sd">
    <a href="{$tuanad1['adlink']}" target="_blank"><img class="fl" src="{Product::get_lazy_img()}" st-src="{Common::img($tuanad1['adsrc'],1200,110)}" alt="{$tuanad1['adname']}"></a>
</div>
{/if}
{/if}