{if $channel['outdoor']['isopen']==1}
<div class="st-slideTab">
    <div class="st-tabnav">
        <h3 class="hw-bt">户外活动</h3>
        {st:dest action="query" flag="channel_nav" row="4" typeid="114" return="linedest"}
        {loop $linedest $ld}
        <span data-id="{$ld['id']}">{$ld['kindname']}</span>
        {/loop}
        <a href="{$cmsurl}outdoor/" class="more">更多</a>
    </div>
    {loop $linedest $ld}
    <div class="st-tabcon">
        <div class="hw-item-block">
            <ul class="clearfix">
                {st:outdoor action="query" flag="mdd" destid="$ld['id']" row="4" return="list"}
                {loop $list $key $row}
                <li class="{if $key==3}last{/if} {if $row['bookstatus']==0 || $row['bookstatus']==4}end{/if}" >
                    <span class="label {if $row['bookstatus']==1}ing{elseif $row['bookstatus']==2}suc{elseif $row['bookstatus']==3}full{else}end{/if}">
                        {if $row['bookstatus']==1}报名中{elseif $row['bookstatus']==2}已成行{elseif $row['bookstatus']==3}已满员{else}已结束{/if}
                    </span>
                    <a class="pic" href="{$row['url']}" target="_blank"><img src="{Product::get_lazy_img()}" st-src="{Common::img($row['litpic'],285,200)}" alt="{$l['title']}" /></a>
                    <a class="bt" href="{$row['url']}" target="_blank">
                        {$row['title']}
                        {loop $row['iconlist'] $ico}
                        <img src="{Product::get_lazy_img()}" st-src="{$ico['litpic']}" />
                        {/loop}
                    </a>
                    <p class="info">
                        <span class="jr">{date('m月d日',$row['starttime'])}出发&nbsp;&nbsp;&nbsp;&nbsp;{$row['lineday']}天</span>
                        <span class="jg">{if $row['price']}{Currency_Tool::symbol()}<em>{$row['price']}</em>起{else}电询{/if}</span>
                    </p>
                </li>
                {/loop}
            </ul>
        </div>
    </div>
    {/loop}
</div>
{/if}