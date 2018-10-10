{if $channel['notes']['isopen']}
<div class="st-product-block">
    <h3 class="st-title-bar">
        <i class="line-icon"></i>
        <span class="title-txt">{$channel['notes']['channelname']}</span>
    </h3>
    <ul class="st-travelnotes-list">
        {st:notes action="query" flag="order" row="3"}
        {loop $data $row}
        <li>
            <a class="item" href="{$row['url']}">
                <div class="pic">
                    <img src="{$defaultimg}" st-src="{Common::img($row['litpic'],235,160)}" alt="{$row['title']}"/>
                </div>
                <div class="info">
                    <p class="tit">{$row['title']}</p>
                    <p class="data clearfix">
                        <span class="phone fl">{Common::cutstr_html($row['nickname'],16)}</span>
                        <span class="num fr"><i class="icon"></i>{$row['shownum']}</span>
                    </p>
                </div>
            </a>
        </li>
        {/loop}
        {/st}
    </ul>
    <div class="st-more-bar">
        <a class="more-link" href="{$cmsurl}notes/all/">查看更多</a>
    </div>
</div>
<!--游记-->
{/if}