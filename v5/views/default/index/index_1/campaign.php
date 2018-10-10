{if $channel['campaign']['isopen']==1}
<div class="st-slideTab">
    {st:attr action="query" flag="grouplist" row="1" typeid="105" return="grouplist"}
    {st:attr action="query" flag="childitem" typeid="105" groupid="$grouplist[0]['id']" return="attrlist"}
    <div class="st-tabnav">
        <h3 class="hd-bt">{$channel['campaign']['channelname']}</h3>
        {loop $attrlist $attr}
        <span>{$attr['attrname']}</span>
        {/loop}
        <a href="{$cmsurl}campaign/" class="more">{__('更多')}{$channel['campaign']['channelname']}</a>
    </div>

    {loop $attrlist $attr}
    <div class="st-tabcon">
        <div class="hd-item-block">
            <ul class="clearfix">
                {st:campaign action="query" flag="attr" row="4"  attrid="$attr['id']" bookstatus="2,3" return="list"}
                {loop $list $key $row}
                <li class="{if $row['bookstatus']==3}end{/if}{if $key==3} last{/if}">
                    <span class="jd">{if $row['bookstatus']==2}{__('报名中')}{elseif $row['bookstatus']==3}{__('已结束')}{/if}</span>
                    <a class="pic" href="{$row['url']}" target="_blank" title="{$row['title']}"><img src="{Product::get_lazy_img()}" st-src="{Common::img($row['litpic'],285,200)}" alt="{$row['title']}" /></a>
                    <a class="bt" href="{$row['url']}" target="_blank" title="{$row['title']}">
                        {$row['title']}
                        {loop $row['iconlist'] $icon}
                        <img src="{$icon['litpic']}"/>
                        {/loop}
                    </a>
                    <p class="info">
                        <span class="jr">{__('已加入')}{$row['joining_number']}/{if $row['number']==-1}{__('不限')}{else}{$row['number']}{/if}</span>
                        {if $row['price']}
                        <span class="jg"><em><i class="currency_sy">{Currency_Tool::symbol()}</i>{$row['price']}</em>{__('起')}</span>
                        {else}
                        <span class="jg"><em>{__('电询')}</em></span>
                        {/if}
                    </p>
                </li>
                {/loop}
            </ul>
        </div>
    </div>
    {/loop}


</div>
{/if}