<!--热门酒店开始-->
{if $channel['hotel']['isopen']==1}
<div class="hotel_trip_box">
    <div class="trip_tit">{$channel['hotel']['channelname']}</div>
    <div class="product_box">
        <div class="sidle_hot">
            <h3>{__('推荐')}{$channel['hotel']['channelname']}</h3>
            <ul>
                {st:hotel action="query" flag="order" row="6" return="rechotel"}
                {loop $rechotel $h}
                <li>
                    <s></s>
                    <div class="pic"><a href="{$h['url']}" target="_blank" title="{$h['title']}"><img class="lazyimg" src="{Product::get_lazy_img()}" st-src="{Common::img($h['litpic'],88,60)}" alt="{$h['title']}"></a></div>
                    <p class="tit"><a href="{$h['url']}" target="_blank" title="{$h['title']}">{$h['title']}</a></p>
                    <p class="jg">{if $h['price']>0}<i class="currency_sy">{Currency_Tool::symbol()}</i>{$h['price']}{else}{__('电询')}{/if}</p>
                </li>
                {/loop}
                {/st}


            </ul>
        </div>
        <div class="con_list">
            <div class="st-tabnav">
                {st:dest action="query" flag="channel_nav" row="6" typeid="2" return="hoteldest"}
                {loop $hoteldest $hd}
                <span>{$hd['kindname']}</span>
                {/loop}
                <a class="more" href="{$GLOBALS['cfg_basehost']}/hotels/all/">{__('更多')}</a>
            </div>
            {loop $hoteldest $hd}

            <div class="st-tabcon" style="display: block;">
                <ul>
                    {st:hotel action="query" flag="mdd" destid="$hd['id']" row="8" return="hlist"}
                    {php}$hindex=1;{/php}
                    {loop $hlist $h}
                    <li {if $hindex%4==0}class="mr_0"{/if}>
                        <a class="pic" href="{$h['url']}" target="_blank" title="{$h['title']}"><img class="lazyimg" alt="{$h['title']}" src="{Product::get_lazy_img()}" st-src="{Common::img($h['litpic'],180,122)}"></a>
                        <a class="tit" href="{$h['url']}" target="_blank" title="{$h['title']}">{$h['title']}</a>
                              <span class="price">
                                  {if !empty($h['price'])}
                                   <i class="currency_sy">{Currency_Tool::symbol()}</i><b>{$h['price']}</b>{__('起')}
                                  {else}
                                    {__('电询')}
                                  {/if}
                              </span>
                    </li>
                    {php}$hindex++;{/php}
                    {/loop}
                    {/st}
                </ul>
            </div>

            {/loop}


            {st:ad action="getad" name="Index2HotelRightAd" pc="1" return="hotelad"}
            {if !empty($hotelad)}
            <div class="ad_img">
                <a href="{$hotelad['adlink']}" style="margin-bottom:10px;" class="fl clearfix" target="_blank"><img src="{Product::get_lazy_img()}" st-src="{Common::img($hotelad['adsrc'],200,424)}" alt="{$hotelad['adname']}"></a>
            </div>
            {/if}
        </div>
    </div>
</div>
{/if}
<!--热门酒店结束-->