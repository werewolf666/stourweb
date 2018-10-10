{if $channel['car']['isopen']==1}
<!--旅游租车开始-->
<div class="car_trip_box">
    <div class="product_box">
        <div class="car_con_list">
            <div class="st-tabnav">
                <h3>{$channel['car']['channelname']}</h3>

                <a class="more" href="{$GLOBALS['cfg_basehost']}/cars/all" target="_blank">{__('更多')}</a>
            </div>
            <div class="st-tabcon">
                <ul>
                    {st:car action="query" flag="recommend"  row="4" return="carlist"}
                    {loop $carlist $c}
                    <li {if $n%4==0}class="mr_0"{/if}>
                        <a class="pic" href="{$c['url']}" target="_blank" title="{$c['title']}"><img src="{Product::get_lazy_img()}" st-src="{Common::img($c['litpic'],220,150)}" alt="{$c['title']}"/></a>
                        <a class="tit" href="{$c['url']}" target="_blank" title="{$c['title']}">{$c['title']}</a>
                          <span class="price">
                              {if $c['price']}
                                    <i class="currency_sy">{Currency_Tool::symbol()}</i><b>{$c['price']}{if !empty($c['unit'])}/{$c['unit']}{/if}</b>
                                {else}
                                   {__('电询')}
                                {/if}
                          </span>
                    </li>
                    {/loop}
                    {/st}
                </ul>
            </div>
            {st:ad action="getad" name="Index3CarRightAd" pc="1" return="carad"}
            {if !empty($carad)}
            <div class="ad_img"><a href="{$carad['adlink']}" target="_blank"><img class="fl" src="{Product::get_lazy_img()}" st-src="{Common::img($carad['adsrc'],240,216)}" alt="{$carad['adname']}"/></a></div>
            {/if}
            {/st}
        </div>
    </div>
</div>
<!--旅游租车结束-->
{/if}