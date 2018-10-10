<div class="other_login">
    <h3><span>其他登录方式</span></h3>

    <p>
        {if $qqlogin}
        <a class="ico_qq" href="{$cmsurl}pub/thirdlogin/?type=qq&refer={urlencode($url)}"><em></em><span>QQ</span></a>
        {/if}
        {if $sinalogin}
        <a class="ico_wb"
           href="{$cmsurl}pub/thirdlogin/?type=weibo&refer={urlencode($url)}"><em></em><span>微博</span></a>
        {/if}
    </p>
</div>