{if $channel['car']['isopen']==1}
<div class="st-slideTab">
    <div class="st-tabnav">
        <h3 class="zc-bt">{$channel['car']['channelname']}</h3>
        <a href="{$cmsurl}cars/" class="more">{__('更多')}{$channel['car']['channelname']}</a>
    </div>

    <div class="st-tabcon">
        <ul class="st-car-list">
            {st:car action="query" flag="recommend"  row="5" return="carlist"}
            {loop $carlist $c}
            <li {if $n%5==0}class="mr_0"{/if}>
                <div class="pic">
                    <img src="{Product::get_lazy_img()}" st-src="{Common::img($c['litpic'],224,152)}" alt="{$c['title']}"/>
                    <div class="buy"><a href="{$c['url']}" target="_blank" title="{$c['title']}">{__('立即预定')}</a></div>
                </div>
                <div class="js">
                    <a class="tit" href="{$c['url']}" target="_blank" title="{$c['title']}">{$c['title']}</a>
                    <p class="num">
                        {if !empty($c['sellprice'])}
                        <del>{__('原价')}：<i class="currency_sy">{Currency_Tool::symbol()}</i>{$c['sellprice']}</del>
                        {/if}
                            <span>
                                {if !empty($c['price'])}
                                  <b><i class="currency_sy">{Currency_Tool::symbol()}</i>{$c['price']}</b>{__('起')}
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

</div><!--旅游租车结束-->
<!--car ad-->
{st:ad action="getad" name="IndexCarAd1" return="carad1"}
{if !empty($carad1)}
<div class="st-list-sd">
    <a href="{$carad1['adlink']}" target="_blank"><img class="fl" src="{Product::get_lazy_img()}" st-src="{Common::img($carad1['adsrc'],1200,110)}" alt="{$carad1['adname']}"></a>
</div>
{/if}

{/if}