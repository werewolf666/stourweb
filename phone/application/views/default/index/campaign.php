{if $channel['campaign']['isopen']==1}
<div class="st-product-block">
    <h3 class="st-title-bar">
        <i class="line-icon"></i>
        <span class="title-txt">{$channel['campaign']['channelname']}</span>
    </h3>
    <ul class="st-campaign-list clearfix">
        {st:campaign action="query" flag="order" row="4"}
        {loop $data $row}
        <li>
            <a class="item" href="{$row['url']}">
                <div class="pic">
                    <img src="{$defaultimg}" st-src="{Common::img($row['litpic'],330,225)}" alt="{$row['title']}"/>
                    {if $row['bookstatus']==1}
                    <span class="label will">未开始</span>
                    {elseif $row['bookstatus']==2}
                    <span class="label ing">报名中</span>
                    {else}
                    <span class="label end">已结束</span>
                    {/if}
                </div>
                <div class="tit">{$row['title']}</div>
                <div class="loac"><i class="icon"></i>{$row['finaldest_name']} | 已加入{$row['joining_number']}/{if $row['number']=='-1'}不限{else}{$row['number']}{/if}</div>
                <div class="price">
                    {if !empty($row['price'])}
                    <span class="jg">{Currency_Tool::symbol()}<strong class="num">{$row['price']}</strong>起</span>
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
        <a class="more-link" href="{$cmsurl}campaign/all/">查看更多</a>
    </div>
</div>
{/if}
<!--热门活动-->