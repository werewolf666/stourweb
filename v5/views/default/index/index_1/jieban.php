{if $channel['jieban']['isopen']==1}
<div class="st-slideTab">
    <div class="st-tabnav">
        <h3 class="jb-bt">{$channel['jieban']['channelname']}</h3>
        <a href="{$cmsurl}jieban"  class="more">更多{$channel['jieban']['channelname']}</a>
    </div>
    <div class="st-tabcon">
        <div class="mate-block">
            <ul class="clearfix">
                {st:jieban action="query" flag="order" row="4"}
                {loop $data $row}
                <li>
                    <a href="{$row['url']}" class="pic" target="_blank" title="{$row['title']}">
                        <img src="{Product::get_lazy_img()}" st-src="{Common::img($row['litpic'],253,172)}" alt="{$row['title']}"/>
                    </a>
                    <a href="{$row['url']}" class="bt" target="_blank" title="{$row['title']}">{$row['title']}</a>
                    <p class="txt">{$row['description']}</p>
                    <div class="user">
                        <img src="{$row['memberpic']}" />
                        <span class="name">{$row['nickname']}</span></div>
                    <div class="date clearfix">
                        <span class="day">剩余时间：<strong>{$row['leftday']}</strong>天</span>
                        <span class="how">已有<strong>{$row['joinnum']}</strong>人加入</span>
                    </div>
                </li>
                {/loop}
                {/st}

            </ul>
        </div>
    </div>
</div>

<!-- 结伴 -->
{/if}