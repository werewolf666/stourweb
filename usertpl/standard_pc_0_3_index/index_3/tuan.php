{if $channel['tuan']['isopen']==1}
<!--团购开始-->
<div class="tuan_trip_box">
    <div class="trip_tit"><h3>{$channel['tuan']['channelname']}</h3><a class="more" href="{$GLOBALS['cfg_basehost']}/tuan/" target="_blank">更多</a></div>
    <div class="product_box">
        <div class="tuan_con_list">
            <div class="st-tabcon">
                <ul>
                    {st:tuan action="query" flag="new"  row="4" return="tuanlist"}
                    {loop $tuanlist $t}
                    <li {if $n%4==0}class="mr_0"{/if}>
                        <span class="dz_ico"><b>{$t['discount']}{__('折')}</b>{__('优惠')}</span>
                        <a class="pic" href="{$t['url']}" target="_blank" title="{$t['title']}"><img src="{Product::get_lazy_img()}" st-src="{Common::img($t['litpic'],220,150)}" alt="{$t['title']}"/></a>
                        <a class="tit" href="{$t['url']}" target="_blank" title="{$t['title']}">{$t['title']}</a>
                              <span class="price">
                                  {if $t['price']}
                                    <i class="currency_sy">{Currency_Tool::symbol()}</i><b>{$t['price']}</b>
                                  {else}
                                       {__('电询')}
                                  {/if}
                              </span>
                    </li>
                    {/loop}
                    {/st}
                </ul>
            </div>
            {st:ad action="getad" name="Index3TuanRightAd" pc="1" row="1" return="tuanad"}
            {if !empty($tuanad)}
            <div class="ad_img"><a href="{$tuanad['adlink']}" target="_blank"><img class="fl" src="{Product::get_lazy_img()}" st-src="{Common::img($tuanad['adsrc'],240,216)}" alt="{$tuanad['adname']}"/></a></div>
            {/if}
            {/st}
        </div>
    </div>
</div>
<!--团购结束-->
{/if}
