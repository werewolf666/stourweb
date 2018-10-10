<!--团购开始-->
{if $channel['tuan']['isopen']==1}
<div class="tuan_trip_box">
    <div class="trip_tit">{$channel['tuan']['channelname']}<a class="more" href="{$GLOBALS['cfg_basehost']}/tuan/" target="_blank">{__('更多特价')}</a></div>
    <div class="product_box">
        <div class="tuan_con_list">
            <div class="st-tabcon">
                <ul>
                    {st:tuan action="query" flag="new"  row="5" return="tuanlist"}
                    {loop $tuanlist $t}
                    <li {if $n%5==0}class="mr_0"{/if}>
                        <span class="dz_ico"><b>{$t['discount']}{__('折')}</b>{__('优惠')}</span>
                        <a class="pic" href="{$t['url']}" target="_blank" title="{$t['title']}"><img class="lazyimg" alt="{$t['title']}" src="{Product::get_lazy_img()}" st-src="{Common::img($t['litpic'],180,122)}"></a>
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
            {st:ad action="getad" name="Index2TuanRightAd" pc="1" return="tuanad"}
            {if !empty($tuanad)}
            <div class="ad_img">
                <a href="{$tuanad['adlink']}" style="margin-bottom:10px;" class="fl clearfix" target="_blank"><img src="{Product::get_lazy_img()}" st-src="{Common::img($tuanad['adsrc'],200,202)}" alt="{$tuanad['adname']}"></a>
            </div>
            {/if}
        </div>
    </div>
</div>
{/if}
<!--团购结束-->