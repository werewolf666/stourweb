{if $channel['article']['isopen']==1}
<!--文章开始-->
<div class="article_trip_box">
    <div class="article_con">
        <div class="trip_tit">
            <h3>{$channel['article']['channelname']}</h3>
            <div class="st-tabnav">
                {st:dest action="query" flag="channel_nav" row="6" typeid="4" return="articledest"}
                {loop $articledest $ad}
                <span>{$ad['kindname']}</span>
                {/loop}
                {/st}
            </div>
            <a class="more" href="{$GLOBALS['cfg_basehost']}/raiders/all">{__('更多')}</a>
        </div>
        {loop $articledest $ad}
        <div class="st-tabcon">

            <div class="first">
                {st:article action="query" flag="mdd" destid="$ad['id']" row="9" return="articlelist"}
                {if !empty($articlelist[0])}
                <div class="pic">
                            <a target="_blank" href="{$articlelist[0]['url']}" title="{$articlelist[0]['title']}"><img class="lazyimg" alt="{$articlelist[0]['title']}" src="{Product::get_lazy_img()}" st-src="{Common::img($articlelist[0]['litpic'],370,259)}"></a>
                </div>
                <p class="tit"><a target="_blank" href="{$articlelist[0]['url']}" title="{$articlelist[0]['title']}">{$articlelist[0]['title']}</a></p>
                <p class="txt">{if !empty($articlelist[0]['summary'])}{Common::cutstr_html($articlelist[0]['summary'],70)}{else}{Common::cutstr_html($articlelist[0]['content'],70)}{/if}</p>
                {/if}
            </div>
            <div class="second">
                <ul>
                    {php}$aindex=1;{/php}
                    {loop $articlelist $arc}
                    {if $aindex>1}
                    <li {if $aindex%2==0} class="mr_0"{/if}>
                    <p class="tit"><a href="{$arc['url']}" target="_blank" title="{$arc['title']}">{$arc['title']}</a></p>
                    <p class="txt">
                        {if !empty($arc['summary'])}{Common::cutstr_html($arc['summary'],50)}{else}{Common::cutstr_html($arc['content'],50)}{/if}
                    </p>
                    </li>
                    {/if}
                    {php}$aindex++;{/php}
                    {/loop}
                </ul>
            </div>
        </div>
        {/loop}

    </div>
</div>
<!--文章结束-->
{/if}