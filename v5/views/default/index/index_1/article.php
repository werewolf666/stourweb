{if $channel['article']['isopen']==1}
<div class="st-slideTab">
    <div class="st-tabnav">
        <h3 class="gl-bt">{$channel['article']['channelname']}</h3>
        <span>{__('最新')}{$channel['article']['channelname']}</span>
        {st:dest action="query" flag="channel_nav" row="6" typeid="4" return="articledest"}
        {loop $articledest $ad}
        <span data-id="{$td['id']}">{$ad['kindname']}</span>
        {/loop}

        <a href="{$cmsurl}raiders/" class="more">{__('更多')}{$channel['article']['channelname']}</a>
    </div>
    {st:article action="query" flag="order" row="7" return="articlelist"}
    <div class="st-tabcon">
        <ul class="st-gl-list">
            {loop $articlelist $a}
            {if $n==1}
            <li class="first">
                <div class="pic"><a href="{$a['url']}" title="{$a['title']}"><i class="hot">hot</i><img src="{Product::get_lazy_img()}" st-src="{Common::img($a['litpic'],386,263)}" alt="{$a['title']}"/></a></div>
                <a class="tit" href="{$a['url']}" target="_blank" title="{$a['title']}">{$a['title']}</a>
                <p class="txt">{if !empty($a['summary'])}{Common::cutstr_html($a['summary'],70)}{else}{Common::cutstr_html($a['content'],70)}{/if}</p>
            </li>
            {elseif $n<4}
            <li>
                <div class="pic"><a href="{$a['url']}" title="{$a['title']}"><i class="hot">hot</i><img src="{Product::get_lazy_img()}" st-src="{Common::img($a['litpic'],180,122)}" alt="{$a['title']}"/></a></div>
                <div class="con">
                    <a class="tit" href="{$a['url']}" title="{$a['title']}">{$a['title']}</a>
                    <p class="txt">{if !empty($a['summary'])}{Common::cutstr_html($a['summary'],70)}{else}{Common::cutstr_html($a['content'],70)}{/if}</p>
                </div>
            </li>
            {elseif $n>3}
            <li>
                <a class="tit" href="{$a['url']}" target="_blank">{$a['title']}</a>
                <p class="txt">{if !empty($a['summary'])}{Common::cutstr_html($a['summary'],70)}{else}{Common::cutstr_html($a['content'],70)}{/if}</p>
            </li>
            {/if}
            {/loop}

        </ul>
    </div>
    {loop $articledest $ad}
    <div class="st-tabcon">
        <ul class="st-gl-list">
            {st:article action="query" flag="mdd" destid="$ad['id']" row="7" return="articlelist"}
            {loop $articlelist $a}
            {if $n==1}
            <li class="ml_0 first">
                <a class="fl" href="{$a['url']}" title="{$a['title']}"><i class="hot">hot</i><img class="fl" src="{Common::img($a['litpic'],386,298)}" alt="{$a['title']}" width="386" height="298" /></a>
                <a class="tit" href="{$a['url']}" target="_blank" title="{$a['title']}">{$a['title']}</a>
                <p class="txt">{if !empty($a['summary'])}{Common::cutstr_html($a['summary'],70)}{else}{Common::cutstr_html($a['content'],70)}{/if}</p>
            </li>
            {elseif $n<4}
            <li>
                <a class="fl" href="{$a['url']}" title="{$a['title']}"><i class="hot">hot</i><img class="fl" src="{Common::img($a['litpic'],386,298)}" alt="{$a['title']}" width="180" height="154" /></a>
                <div class="con">
                    <a class="tit" href="{$a['url']}" title="{$a['title']}">{$a['title']}</a>
                    <p class="txt">{if !empty($a['summary'])}{Common::cutstr_html($a['summary'],70)}{else}{Common::cutstr_html($a['content'],70)}{/if}</p>
                </div>
            </li>



            {elseif $n>3}
            <li>
                <a class="tit" href="{$a['url']}" target="_blank" title="{$a['title']}">{$a['title']}</a>
                <p class="txt">{if !empty($a['summary'])}{Common::cutstr_html($a['summary'],70)}{else}{Common::cutstr_html($a['content'],70)}{/if}</p>
            </li>
            {/if}
            {/loop}

        </ul>
    </div>
    {/loop}


</div><!--旅游攻略结束-->
{st:ad action="getad" name="IndexArticleAd1" return="articlead1"}
{if !empty($articlead1)}
<div class="st-list-sd">
    <a href="{$articlead1['adlink']}" target="_blank"><img class="fl" src="{Common::img($articlead1['adsrc'],1200,110)}" alt="{$articlead1['adname']}"></a>
</div>
{/if}
{/if}