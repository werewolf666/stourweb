{if $channel['photo']['isopen']==1}
<div class="st-slideTab">
    <div class="st-tabnav">
        <h3 class="xc-bt">{$channel['photo']['channelname']}</h3>
        {st:dest action="query" flag="channel_nav" row="6" typeid="6" return="photodest"}
        {loop $photodest $pd}
        <span data-id="{$pd['id']}">{$pd['kindname']}</span>
        {/loop}
        <a href="{$cmsurl}photos/" class="more">{__('更多')}{$channel['photo']['channelname']}</a>
    </div>
    {loop $photodest $pd}
    <div class="st-tabcon">
        <div class="photo-block">
            <ul class="clearfix">
                {php}$photo_list = Model_photo::photo_list(array('order'=>2,'kindlist'=>$pd['id'])){/php}
                {php}$k=1;{/php}
                {loop $photo_list $photo}
                {if $k<5}
                <li {if $k==1} class="first" {/if}>
                <a href="{$cmsurl}photos/show_{$photo['aid']}.html" target="_blank" title="{$photo['title']}">
                    <img src="{Product::get_lazy_img()}" st-src="{if $k==1}{Common::img($photo['litpic'],408,237)}{else}{Common::img($photo['litpic'],244,237)}{/if}" alt="{$photo['title']}" />
                    <span class="tit">{$photo['title']}</span>
                </a>
                </li>
               {/if}
                {php}$k++;{/php}
                {/loop}
            </ul>
        </div>
    </div>
    {/loop}
</div>
<!--相册-->

{st:ad action="getad" name="IndexPhotos1" pc="1" return="photos1"}
{if !empty($photos1)}
<div class="st-list-sd">
    <a href="{$photos1['adlink']}" target="_blank"><img class="fl" src="{Product::get_lazy_img()}" st-src="{Common::img($photos1['adsrc'],1200,110)}" alt="{$photos1['adname']}"></a>
</div>
{/if}
{/st}
<!--广告-->
{/if}