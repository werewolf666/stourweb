{if $channel['article']['isopen']}
<div class="st-product-block">
    <h3 class="st-title-bar">
        <i class="line-icon"></i>
        <span class="title-txt">{$channel['article']['channelname']}</span>
    </h3>
    <ul class="st-article-list">
        {st:article action="query" flag="order" row="3" return="article_data"}
        {loop $article_data $row}
        <li>
            <a class="item" href="{$row['url']}">
                <div class="pic"><img src="{$defaultimg}" st-src="{Common::img($row['litpic'],235,160)}" alt="{$row['title']}"/></div>
                <div class="info">
                    <p class="tit">{$row['title']}</p>
                    <p class="txt">{Common::cutstr_html($row['summary'],20)}</p>
                    <p class="data">
                        <span class="mdd"><i class="icon"></i>{$row['finaldest']['kindname']}</span>
                        <span class="num"><i class="icon"></i>{$row['shownum']}</span>
                    </p>
                </div>
            </a>
        </li>
        {/loop}
        {/st}
    </ul>
    <div class="st-more-bar">
        <a class="more-link" href="{$cmsurl}raiders/all/">查看更多</a>
    </div>
</div>
<!--攻略-->
{/if}