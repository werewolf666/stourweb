{if $channel['guide']['isopen']==1}

{st:dest action="query" flag="channel_nav" row="8" typeid="106" return="linedest"}
<div class="st-slideTab">
    <div class="st-tabnav">
        <h3 class="dy-bt">推荐导游</h3>
        {loop $linedest $ld}
         <span>{$ld['kindname']}</span>
        {/loop}
        <a href="/guide/all" class="more">更多</a>
    </div>
    {loop $linedest $k $ld}
        {st:guide action="service_by_dest"  row="4" destid="$ld['id']" return="service"}
        <div class="st-tabcon">
            <div class="dragoman-block">
                <ul class="clearfix">
                    {loop $service $k $s}	
					<li class="{if $k==3}last{/if}">
                                <a class="item-a" href="{$s['url']}" target="_blank" title="{$s['title']}">
                                    <div class="pic">
                                        <img src="{Common::img($s['litpic'],285,194)}" alt="{$g['title']}" />
											{if $s['finaldest_name']}<span class="mdd"><i class="icon"></i>{$s['finaldest_name']}</span>{/if}
                                    </div>
                                    <div class="info">
                                        <p class="tit">{$s['title']}</p>
                                        <p class="clearfix mt5">
                                            <span class="name fl"><img src="{Common::img($s['member_litpic'],44,44)}">服务导游：{$s['truename']}</span>
                                            <span class="jg fr">{if $s['price']}{Currency_Tool::symbol()}<em>{$s['price']}</em>/天{else}{/if}</span>
                                        </p>
                                    </div>
                                </a>
                            </li>
                    {/loop}
                </ul>
            </div>
        </div>
    {/loop}
</div>
{/if}
<!-- 导游 -->