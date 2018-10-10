{if $channel['hotel']['isopen']==1}
<div class="st-slideTab">
    <div class="st-tabnav">
        <h3 class="jd-bt">{$channel['hotel']['channelname']}</h3>
        {st:dest action="query" flag="channel_nav" row="6" typeid="2" return="hoteldest"}
        {loop $hoteldest $hd}
        <span data-id="{$hd['id']}">{$hd['kindname']}</span>
        {/loop}

        <a href="{$cmsurl}hotels/" class="more">{__('更多')}{$channel['hotel']['channelname']}</a>
    </div>
    {loop $hoteldest $hd}
    <div class="st-tabcon">
        <ul class="st-cp-list">
            {st:hotel action="query" flag="mdd" destid="$hd['id']" row="6" return="hotellist"}
            {loop $hotellist $h}
            <li>
                <div class="pic">
                    <img src="{Product::get_lazy_img()}" st-src="{Common::img($h['litpic'],285,194)}" alt="{$h['title']}"/>
                    <div class="buy"><a href="{$h['url']}" target="_blank" title="{$h['title']}">{__('立即预定')}</a></div>
                </div>
                <div class="js">
                    <a class="tit" href="{$h['url']}" target="_blank" title="{$h['title']}">{$h['title']}</a>
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
            {/st}

        </ul>
    </div>
    {/loop}

    <div class="st-adimg">
        {st:ad action="getad" name="IndexHotelAd1" return="hotelad1"}
        {if !empty($hotelad1)}
        <a href="{$hotelad1['adlink']}" target="_blank"><img class="fl" src="{Product::get_lazy_img()}" st-src="{Common::img($hotelad1['adsrc'],279,568)}" alt="{$hotelad1['adname']}" width="279" height="568"/></a>
        {/if}
        {/st}

    </div>
</div><!--热门酒店结束-->
{st:ad action="getad" name="IndexHotelAd2" return="hotelad2"}
{if !empty($hotelad2)}
<div class="st-list-sd">
    <a href="{$hotelad2['adlink']}" target="_blank"><img class="fl" src="{Product::get_lazy_img()}" st-src="{Common::img($hotelad2['adsrc'],1200,110)}" alt="{$hotelad2['adname']}"></a>
</div>
{/if}
{/if}