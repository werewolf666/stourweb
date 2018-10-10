<!--签证开始-->
{if $channel['visa']['isopen']==1}
<div class="visa_trip_box">
    <div class="trip_tit">{$channel['visa']['channelname']}<a class="more" href="{$GLOBALS['cfg_basehost']}/visa/">{__('更多')}</a></div>
    <div class="product_box">
        <div class="visa_con_list">
            <ul>
                {st:visa action="query" flag="order" row="8" return="visalist"}
                {loop $visalist $v}
                <li>
                    <a class="fl" href="{$v['url']}" target="_blank" title="{$v['title']}"><span class="pic"><img class="lazyimg" src="{Product::get_lazy_img()}" alt="{$v['title']}" st-src="{Common::img($v['litpic'],92,62)}"></span></a>
                    <p>
                        <a href="{$v['url']}" target="_blank" title="{$v['title']}">{$v['title']}</a>
                          <span>
                              {if $v['price']}
                                    <i class="currency_sy">{Currency_Tool::symbol()}</i><b>{$v['price']}</b>
                                {else}
                                   {__('电询')}
                                {/if}
                          </span>
                    </p>
                </li>
                {/loop}
                {/st}
            </ul>
        </div>
    </div>
</div>
{/if}
<!--签证结束-->