<!--旅游租车开始-->
{if $channel['car']['isopen']==1}
<div class="car_trip_box">
    <div class="trip_tit">{$channel['car']['channelname']}<a class="more" href="{$GLOBALS['cfg_basehost']}/cars/all">{__('更多')}</a></div>
    <div class="product_box">
        <div class="car_con_list">

            <div class="st-tabcon" style="display: block;">
                <ul>
                    {st:car action="query" flag="recommend"  row="5" return="carlist"}
                    {loop $carlist $c}
                    <li {if $n%5==0}class="mr_0"{/if}>
                        <div class="pic"><a href="{$c['url']}" target="_blank" title="{$c['title']}"><img class="lazyimg" alt="{$c['title']}" src="{Product::get_lazy_img()}" st-src="{Common::img($c['litpic'],180,122)}"></a></div>
                        <a class="tit" href="{$c['url']}" target="_blank" title="{$c['title']}">{$c['title']}</a>
                            <span class="price">
                                {if $c['price']}
                                    <i class="currency_sy">{Currency_Tool::symbol()}</i><b>{$c['price']}</b>
                                {else}
                                   {__('电询')}
                                {/if}
                            </span>
                    </li>
                    {/loop}
                    {/st}


                </ul>
            </div>

        </div>
        {st:ad action="getad" name="Index2CarRightAd" pc="1" return="carad"}
        {if !empty($carad)}
        <div class="ad_img">
            <a href="{$carad['adlink']}" style="margin-bottom:10px;" class="fl clearfix" target="_blank"><img src="{Product::get_lazy_img()}" st-src="{Common::img($carad['adsrc'],200,202)}" alt="{$carad['adname']}"></a>
        </div>
        {/if}
    </div>
</div>
{/if}
<!--旅游租车结束-->