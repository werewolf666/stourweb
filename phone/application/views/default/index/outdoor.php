{if $channel['outdoor']['isopen']==1}
<div class="st-product-block">
    <h3 class="st-title-bar">
        <i class="line-icon"></i>
        <span class="title-txt">{$channel['outdoor']['channelname']}</span>
    </h3>
    <ul class="st-outdoor-list">
        {st:outdoor action="query" flag="order" row="4" return="outdoor_list"}
                    {loop $outdoor_list $row}
                    <li class="{if $row['bookstatus']==1}in{elseif $row['bookstatus']==2}finish{elseif $row['bookstatus']==3}full{else}end{/if}">
                        <a href="{$row['url']}" data-ajax="false">
                            <div class="pic">
                                <div class="tips"><i></i>{$row['bookstatus_name']}</div>
                                <div class="img"><img src="{Common::img($row['litpic'],450,225)}" alt="" title="{$row['title']}" /></div>
                            </div>
                            <div class="info">{$row['title']}</div>
                            <div class="des clearfix">
                                <p class="num">{date('m月d日',$row['starttime'])},{$row['lineday']}天</p>
                                <p class="price">
                                    {if $row['price']}
                                    <i>{Currency_Tool::symbol()}</i><strong>{$row['price']}</strong>起
                                    {else}
                                    <strong>电询</strong>
                                    {/if}
                                </p>
                            </div>
                        </a>
                    </li>
                    {/loop}
    </ul>
    <div class="st-more-bar">
        <a class="more-link" href="/phone/outdoor/all/">查看更多</a>
    </div>
</div>
{/if}