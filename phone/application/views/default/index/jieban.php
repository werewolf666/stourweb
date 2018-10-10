{if $channel['jieban']['isopen']}
<div class="st-product-block">
    <h3 class="st-title-bar">
        <i class="line-icon"></i>
        <span class="title-txt">{$channel['jieban']['channelname']}</span>
    </h3>
    <ul class="st-jieban-list">
        {st:jieban action="query" row="2" flag="new" return="jieban_data"}
        {loop $jieban_data $row}
        <li>
            <a class="item" href="{$row['url']}">
                <img src="{$defaultimg}" st-src="{Common::img($row['litpic'],690,345)}"/>
                <span class="info">
                    <span class="day"><b>{$row['day']}</b>日游</span>
                    <span class="date">{$row['startdate']}出发</span>
                </span>
            </a>
            <div class="tit">{$row['title']}</div>
            <div class="txt">{Common::cutstr_html($row['description'],20)}</div>
            <div class="type">
                {loop $row['attrlist'] $v}
                <span class="attr">{$v['attrname']}</span>
                {/loop}
            </div>
            <div class="join">
                <span class="bm">已有<b>{$row['joinnum']}</b>人报名</span>
                <span class="ck"><i class="icon"></i>{$row['shownum']}</span>
                <a class="link" href="{$row['url']}"><i class="icon"></i>立即加入</a>
            </div>
        </li>
        {/loop}
        {/st}
    </ul>
    <div class="st-more-bar">
        <a class="more-link" href="{$cmsurl}jieban/">查看更多</a>
    </div>
</div>
<!--结伴-->
{/if}