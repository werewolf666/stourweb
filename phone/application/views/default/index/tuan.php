{if $channel['tuan']['isopen']}
<div class="st-product-block">
    <h3 class="st-title-bar">
        <i class="line-icon"></i>
        <span class="title-txt">{$channel['tuan']['channelname']}</span>
    </h3>
    <ul class="st-tuan-list">
        {st:tuan action="query" flag="new" status="1" row="2" return="tuan_data"}
        {loop $tuan_data $v}
        <li>
            <a class="item {if $info['starttime']>time()}begin{/if}" href="{$v['url']}">
                <div class="pic">
                    <img src="{$defaultimg}" st-src="{Common::img($v['litpic'],690,345)}"/>
                    {if $v['totalnum'] != 0}
                    <span class="count" start-time="{$v['starttime']}" end-time="{$v['endtime']}">
                        <span class="sy"></span>
                        <span class="time"></span>
                    </span>
                    {/if}
                    {if $v['totalnum'] == 0}
                    <span class="sold-out"></span>
                    {/if}
                </div>
                <p class="tit">{$v['title']}</p>
                <p class="txt">{$v['sellpoint']}</p>
                <p class="price clearfix">
                    <span class="yhj"><i class="currency_sy">{Currency_Tool::symbol()}</i><b>{$v['price']}</b></span>
                    {if !empty($v['sellprice'])}
                    <span class="del">原价：{$v['sellprice']}元</span>
                    {/if}
                    <span class="num fr">已售：{$v['sellnum']}</span>
                </p>
            </a>
        </li>
        {/loop}
        {/st}
    </ul>
    <div class="st-more-bar">
        <a class="more-link" href="{$cmsurl}tuan/all/">查看更多</a>
    </div>
</div>
<!--限时特价-->
{/if}