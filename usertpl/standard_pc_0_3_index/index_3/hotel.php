{if $channel['hotel']['isopen']==1}
<!--热门酒店开始-->
<div class="hotel_trip_box">
    <div class="product_box">
        <div class="con_list">
            <div class="st-tabnav">
                <h3>{$channel['hotel']['channelname']}</h3>
                {st:dest action="query" flag="channel_nav" row="6" typeid="2" return="hoteldest"}
                {loop $hoteldest $hd}
                <span>{$hd['kindname']}</span>
                {/loop}
                {/st}
                <a class="more" href="{$GLOBALS['cfg_basehost']}/hotels/all/">{__('更多')}</a>
            </div>
            {loop $hoteldest $hd}
            <div class="st-tabcon">
                <ul>
                    {st:hotel action="query" flag="mdd" destid="$hd['id']" row="6" return="hlist"}
                    {php}$hindex=1;{/php}
                    {loop $hlist $h}
                    <li {if $hindex%3==0}class="mr_0"{/if}>
                        <a class="pic" href="{$h['url']}" target="_blank" title="{$h['title']}"><img src="{Product::get_lazy_img()}" st-src="{Common::img($h['litpic'],294,200)}" alt="{$h['title']}"/></a>
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
            {st:ad action="getad" name="Index3HotelRightAd" pc="1" row="1" return="hotelad"}
            {if !empty($hotelad)}
            <div class="ad_img"><a href="{$hotelad['adlink']}" target="_blank"><img class="fl" src="{Product::get_lazy_img()}" st-src="{Common::img($hotelad['adsrc'],240,566)}" alt="{$hotelad['adname']}"/></a></div>
            {/if}
            {/st}
        </div>
    </div>
</div>
<!--热门酒店结束-->
{/if}