{if $channel['car']['isopen']}
<div class="st-product-block">
    <h3 class="st-title-bar">
        <i class="line-icon"></i>
        <span class="title-txt">{$channel['car']['channelname']}</span>
    </h3>
    <ul class="st-list-block clearfix">
        {st:car action="query" flag="order" return="car_data"}
        {loop $car_data $row}
        <li>
            <a class="item" href="{$row['url']}">
                <div class="pic"><img src="{$defaultimg}" st-src="{Common::img($row['litpic'],330,225)}" alt="{$row['title']}"/></div>
                <div class="tit">{$row['title']}<span class="md">{$row['sellpoint']}</span></div>
                <div class="price">
                    {if !empty($row['price'])}
                    <span class="jg"><i class="currency_sy">{Currency_Tool::symbol()}</i><span class="num">{$row['price']}</span>起</span>
                    {else}
                    <span class="dx">电询</span>
                    {/if}
                </div>
            </a>
        </li>
        {/loop}
        {/st}
    </ul>
    <div class="st-more-bar">
        <a class="more-link" href="{$cmsurl}cars/all/">查看更多</a>
    </div>
</div>
{/if}
<!--热门租车-->