{if $channel['visa']['isopen']==1}
<div class="st-slideTab">
    <div class="st-tabnav">
        <h3 class="qz-bt">{$channel['visa']['channelname']}</h3>
        <a href="{$cmsurl}visa/" class="more">{__('更多')}{$channel['visa']['channelname']}</a>
    </div>
    <div class="st-tabcon">
        <ul class="st-visa-list">
            {st:visa action="query" flag="order" row="5" return="visalist"}
            {loop $visalist $v}
            <li {if $n%5==0} class="mr_0" {/if}>
            <a class="fl" href="{$v['url']}" target="_blank" title="{$v['title']}">
                <div class="country"><em><img src="{Product::get_lazy_img()}" st-src="{Common::img($v['litpic'],140,95)}"/></em></div>
                <span class="tit">{$v['title']}</span>
            </a>
            <p class="num">
                {if !empty($v['sellprice'])}
                <del>{__('原价')}：<i class="currency_sy">{Currency_Tool::symbol()}</i>{$v['sellprice']}</del>
                {/if}
                {if !empty($v['price'])}
                <span><b><i class="currency_sy">{Currency_Tool::symbol()}</i>{$v['price']}</b></span>
                {else}
                <span><b>{__('电询')}</b></span>
                {/if}
            </p>
            </li>
            {/loop}
        </ul>
    </div>
</div><!--签证办理结束-->
<!--visa ad-->
{st:ad action="getad" name="IndexVisaAd1" return="visaad1"}
{if !empty($visaad1)}
<div class="st-list-sd">
    <a href="{$visaad1['adlink']}" target="_blank"><img class="fl" src="{Product::get_lazy_img()}" st-src="{Common::img($visaad1['adsrc'],1200,110)}" alt="{$visaad1['adname']}"></a>
</div>
{/if}
{/if}