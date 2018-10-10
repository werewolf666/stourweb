
{st:flink action="query"}
{if !empty($data) && $isindex==1}
<div class="st-link">
    <div class="wm-1200">

        <div class="st-link-list">
            <dl>
                <dt>{__('友情链接')}：</dt>
                <dd>

                    {loop $data $row}
                    <a href="{$row['url']}" {if $row['is_follow']==0}rel="nofollow"{/if} target="_blank">{$row['title']}</a>
                    {/loop}
                </dd>
            </dl>
        </div>

    </div>
</div>
{/if}
{/st}