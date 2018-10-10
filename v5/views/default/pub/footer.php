{Common::css('footer.css')}
{Common::load_skin()}
<div class="st-brand">
    <div class="wm-1200">

        <div class="st-serve">
            <dl class="ico01 bor_0">
                <dt></dt>
                <dd>
                    <em>阳光价格</em>
                    <span>同类产品，保证低价</span>
                </dd>
            </dl>
            <dl class="ico02">
                <dt></dt>
                <dd>
                    <em>阳光行程</em>
                    <span>品质护航，透明公开</span>
                </dd>
            </dl>
            <dl class="ico03">
                <dt></dt>
                <dd>
                    <em>阳光服务</em>
                    <span>专属客服，快速响应</span>
                </dd>
            </dl>
            <dl class="ico04">
                <dt></dt>
                <dd>
                    <em>救援保障</em>
                    <span>途中意外，保证援助</span>
                </dd>
            </dl>
        </div>

    </div>
</div><!--品牌介绍-->

{request 'pub/help'}

<div class="st-footer">
    <div class="wm-1200">

        <div class="st-foot-menu">
            {st:footnav action="pc" row="10"}
            {loop $data $row}
            <a href="{$row['url']}" target="_blank" rel="nofollow">{$row['title']}</a>
            {/loop}
            {/st}
        </div>
        <!--底部导航-->

        <div class="st-foot-edit">
            {$GLOBALS['cfg_footer']}
        </div>
        <!--网站底部介绍-->
        <div class="support">技术支持：<a href="http://www.stourweb.com/" target="_blank">思途CMS</a></div>
        <p>{stripslashes($GLOBALS['cfg_tongjicode'])}{$GLOBALS['cfg_html_kefu']}</p>
    </div>
</div>
<script src="/plugins/qq_kefu/public/js/qqkefu.js"></script>
