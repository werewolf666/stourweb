{if $channel['line']['isopen']==1}
<div class="st-product-block">
    <h3 class="st-title-bar">
        <i class="line-icon"></i>
        <span class="title-txt">{$channel['line']['channelname']}</span>
    </h3>
    <ul class="st-list-block clearfix">
        {st:line action="query" flag="order" row="4" return="line_data"}
        {loop $line_data $row}
        <li>
            <a class="item" href="{$row['url']}">
                <div class="pic"><img src="{$defaultimg}" st-src="{Common::img($row['litpic'],330,225)}" alt="{$row['title']}"/></div>
                <div class="tit double">{if $row['color']}<span style="color:{$row['color']}">{$row['title']}</span>{else}{$row['title']}{/if}<span class="md">{$row['sellpoint']}</span>
                </div>
                <div class="price">
                    {if !empty($row['price'])}
                    <span class="jg"><i class="currency_sy">{Currency_Tool::symbol()}</i><strong class="num">{$row['price']}</strong>起</span>
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
        <a class="more-link" href="{$cmsurl}lines/all/">查看更多</a>
    </div>
</div>
{/if}
<!--热门线路-->