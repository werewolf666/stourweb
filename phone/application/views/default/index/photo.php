{if $channel['photo']['isopen']}
<div class="st-product-block">
    <h3 class="st-title-bar">
        <i class="line-icon"></i>
        <span class="title-txt">{$channel['photo']['channelname']}</span>
    </h3>
    <ul class="st-photo-list clearfix">
        {php}$photo_list = Model_photo::photo_list(array('order'=>2)){/php}
        {php}$k=1;{/php}
        {loop $photo_list $photo}
        {if $k<7}
        <li>
            <a class="item" href="{$cmsur}photos/show_{$photo['aid']}.html">
                <img src="{$defaultimg}" st-src="{Common::img($photo['litpic'],220,150)}" alt="{$photo['title']}"/>
            </a>
        </li>
        {/if}
        {php}$k++;{/php}
        {/loop}
    </ul>
    <div class="st-more-bar">
        <a class="more-link" href="{$cmsurl}photos/all/">查看更多</a>
    </div>
</div>
<!--相册-->
{/if}