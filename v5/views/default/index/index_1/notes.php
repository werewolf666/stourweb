{if $channel['notes']['isopen']==1}
<div class="st-slideTab">
    <div class="st-tabnav">
        <h3 class="jb-bt">{$channel['notes']['channelname']}</h3>
        <a href="{$cmsurl}notes"  class="more">更多{$channel['notes']['channelname']}</a>
    </div>
    <div class="st-tabcon">
        <div class="travel-notes">
            <ul class="clearfix">
                {st:notes action="query" flag="order" row="4"}
                {loop $data $row}
                <li>
<!--                    <i class="attr">精华</i>-->
                    <a href="{$row['url']}" target="_blank" class="pic" title="{$row['title']}">
                        <img src="{Product::get_lazy_img()}" st-src="{Common::img($row['litpic'],285,185)}" alt="{$row['title']}" />
                    </a>
                    <div class="nr">
                        <div class="msg"><img src="{$row['memberpic']}" /><span class="name">{$row['nickname']}</span></div>
                        <a href="{$row['url']}" target="_blank" class="bt" title="{$row['title']}">{$row['title']}</a>
                        <p class="txt">{$row['description']}</p>
                    </div>
                </li>
                {/loop}
                {/st}
            </ul>
        </div>
    </div>
</div>
<!-- 游记 -->

{st:ad action="getad" name="IndexNotes1" pc="1" return="photos1"}
{if !empty($photos1)}
<div class="st-list-sd">
    <a href="{$photos1['adlink']}" target="_blank"><img class="fl" src="{Product::get_lazy_img()}" st-src="{Common::img($photos1['adsrc'],1200,110)}" alt="{$photos1['adname']}"></a>
</div>
{/if}
{/st}
{/if}