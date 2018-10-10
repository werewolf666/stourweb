{st:hotel action="query" flag="theme" themeid="$info['id']" row="6"}
{if !empty($data)}
<div class="theme-container">
    <h3 class="theme-tit"><i class="hotel-icon"></i>推荐酒店</h3>
    <div class="theme-wrap">
        <ul class="theme-list clearfix">
            {loop $data $row}
            <li>
                <a class="item-a" href="{$row['url']}">
                    <span class="pic"><img src="{$defaultimg}" st-src="{Common::img($row['litpic'],248,168)}" alt="{$row['title']}"></span>
                    <span class="bt">{$row['title']}<em>{$row['sellpoint']}</em></span>
                    <span class="jg">
                        {if $row['price'] > 0}
                        <i class="currency_sy">{Currency_Tool::symbol()}</i><em>{$row['price']}</em>起
                        {else}
                        <em>电询</em>
                        {/if}
                    </span>
                </a>
            </li>
            {/loop}
        </ul>
        <div class="more-item"><a class="more-link" href="{$cmsurl}hotels/all">查看更多</a></div>
    </div>
</div>
{/if}