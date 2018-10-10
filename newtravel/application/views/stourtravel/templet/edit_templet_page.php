<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>模板页面编辑-思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,plist.css,base_new.css'); }
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js,jquery.buttonbox.js,choose.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
    <style>
        .cursor{ cursor: pointer}
        .mall .pro-search{ float: left; margin: 3px 0 0 5px;}
    </style>
</head>
<body style="overflow:hidden" class="mall">
<table class="content-tab">
    <tr>
        <td width="119px" class="content-lt-td" valign="top">
            {template 'stourtravel/public/leftnav'}
            <!--右侧内容区-->
        </td>
        <td valign="top" class="content-rt-td" style="overflow:hidden">

            <div class="st-table-content">
                <div class="cfg-header-bar clearfix">

                    <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload();">刷新</a>

                </div>

                <ul class="info-item-block">
                    <li>
                        <span class="item-hd">模板包名称：</span>
                        <div class="item-bd">
                            <input type="text" id="templetname" name="templetname" value="{$templetname}" class="input-text w300">
                            <div class="item-section lh-20 mt-10 c-red">
                                <p></p>
                            </div>
                        </div>
                    </li>
                    <li>
                        {request 'filemanager/index/folder/'.$folder.'/ismobile/'.$ismobile.'/embedpage/1/menuid/194'}
                    </li>

                </ul>

                <div>
                    <a href="javascript:;" class="btn btn-primary  radius ml-20 size-L" id="comfirm_btn" onclick="save_templet()">保存</a>
                </div>
            </div>

        </td>
    </tr>
</table>
</body>
<script language="JavaScript">
    function save_templet() {
        if ($("#templetname").val() == "") {
            $("#templetname").siblings(".item-section").find("p").html("模板包名称不允许为空");
            $("#templetname").addClass("b-error");
            return;
        }
        $("#templetname").removeClass("b-error");
        $("#templetname").siblings(".item-section").find("p").html("");


        ST.Util.showMsg("正在保存模板包信息...", 6, 1000000);
        $.ajax({
            type: 'post',
            url: SITEURL + "templet/ajax_save_templet_page",
            data: {system_part_code: {$system_part_code}, templetname: $("#templetname").val()},
            dataType: 'json',
            success: function (rs) {
                ST.Util.hideMsgBox();
                if (rs.status === 1) {
                    ST.Util.showMsg("保存成功", 4);
                } else {
                    ST.Util.showMsg(rs.msg, 5, 3000);
                }
            }
        });

    }

</script>
</html><script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.1510&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
