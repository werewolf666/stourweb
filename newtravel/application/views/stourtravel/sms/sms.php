<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title color_background=zm8iDl >思途CMS{$coreVersion}短信接口</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,upgrade.css,base_new.css'); }
</head>

<body>

    <table class="content-tab">
        <tr>
            <td width="119px" class="content-lt-td" valign="top">
                {template 'stourtravel/public/leftnav'}
                <!--右侧内容区-->
            </td>
            <td valign="top" class="content-rt-td" style="overflow:auto;">
                <div class="cfg-header-bar">
                    <div class="cfg-header-tab">
                        {loop $providerlist $provider}
                        <span class="item {if $n <= 1}on{/if}" data-providerid="{$provider['id']}" data-providercfgurl="{$provider['config_url']}" ><s></s>{$provider['name']}</span>
                        {/loop}
                    </div>
                    <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                </div>
                <div class="manage-nr">
                    <div class="version_sj">
                        <div class="version_list" style=" height: 845px">

                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>


<script language="JavaScript">
var public_url = "{$GLOBALS['cfg_public_url']}";
var basehost = "{$GLOBALS['cfg_basehost']}";
$(function () {
    //切换
    $('.cfg-header-tab').find('span').click(function () {
        var providerid = $(this).attr('data-providerid');
        var providercfgurl = basehost + $(this).attr('data-providercfgurl')+"?provider_id="+providerid;
        var html = "<iframe src='" + providercfgurl  + "' width='100%' height='100%' frameborder='0px'></iframe>";

        $(this).addClass('on').siblings().removeClass('on');

        $(".version_list").html(html);
    })

    $('.cfg-header-tab .on').trigger("click");
})

</script>


</body>
</html>
