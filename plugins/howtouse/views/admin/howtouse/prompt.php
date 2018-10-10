<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>操作指引提示框-思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,plist.css'); }
    {php echo Common::css_plugin('guide.css','howtouse'); }

</head>
<body style="overflow:hidden" class="mall">
<div class="novice-guide-layer-box">
    <div class="novice-guide-title-icon"></div>
    <div class="novice-guide-content">
        <h4 class="tit">欢迎使用思途CMS</h4>
        <p class="txt">通过新手教程，助您快速上手，使用本系统！</p>
    </div>
    <div class="novice-guide-read">
        <a class="guide-link" href="javascript:showHowtouse();">开始学习</a>
        <a class="close-btn" href="javascript:close();">关闭</a>
    </div>
    <div class="check-block">
        <span id="checkLable" class="check-lable"><i class="check-icon"></i>不再提醒</span>
    </div>
</div>
</body>
<script>
    $(function () {

        $("#checkLable").click(function () {
            var _this = $(this);
            var $checkIcon = _this.children(".check-icon");
            if ($checkIcon.hasClass("checked")) {
                $checkIcon.removeClass("checked")
            }
            else {
                $checkIcon.addClass("checked")
            }
        })

    })

    function showHowtouse() {
        savePromptOption('{$first_usage_guide_menu['title']}', '{$cmsurl}howtouse/admin/index/index/menuid/{$first_usage_guide_menu['id']}');
    }
    function savePromptOption(jumpTitle, jumpUrl) {
        var url = SITEURL + "config/ajax_saveconfig";
        var frmdata = "webid=0";
        if ($("#checkLable").children(".check-icon").hasClass("checked")) {
            frmdata += "&cfg_howtouse_prompt_disabled=1";
            parent.show_newbie_guide_menu_tips();
        }
        else {
            frmdata += "&cfg_howtouse_prompt_disabled=0";
        }

        $.ajax({
            type: 'POST',
            url: url,
            dataType: 'json',
            data: frmdata,
            success: function (data) {

                if (jumpTitle != "" && jumpUrl != "") {
                    ST.Util.addTab(jumpTitle, jumpUrl);
                }

                ST.Util.closeBox();

            }
        })
    }
    function close() {
        savePromptOption('', '');
    }
</script>
</html>