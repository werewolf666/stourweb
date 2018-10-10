{if $channel['ship_line']['isopen']==1}
<div class="st-product-block">
    <h3 class="st-title-bar">
        <i class="line-icon"></i>
        <span class="title-txt">{$channel['ship_line']['channelname']}</span>
    </h3>
    <ul class="st-ship-list clearfix">
        {st:ship action="query" row="4" return="subdata"}
        {loop $subdata $ship}
        <li>
            <a class="item" href="{$ship['url']}">
                <div class="pic">
                    <img src="{Common::img($ship['litpic'],330,225)}" alt="{$ship['title']}" />
                    {if !empty($ship['starttime'])}<span class="date">出发时间：{date('Y-m-d',$ship['starttime'])}</span>{/if}
                </div>
                <div class="tit">{$ship['title']}</div>
                <div class="info clearfix">
                    <span class="price fl">{if !empty($ship['price'])}{Currency_Tool::symbol()}<strong class="num">{$ship['price']}</strong>起{else}电询{/if}</span>
                    <span class="loac fr"><i class="icon"></i>{if !empty($ship['finaldest_name'])}{$ship['finaldest_name']}{/if}</span>
                </div>
            </a>
        </li>
        {/loop}
    </ul>
    <div class="st-more-bar">
        <a class="more-link" href="{$cmsurl}ship/all/">查看更多</a>
    </div>
</div>
{/if}
<!--热门活动-->