<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,jqtransform.css,base_new.css,templet_manage.css'); }
    {php echo Common::getScript('jquery.upload.js');}
</head>
<style>


</style>
<body style="overflow: hidden">

<ul class="info-item-block">
    <li>
        <span class="item-hd"><label class="c-red va-m mr-5">*</label>模板包名称{Common::get_help_icon('templet_upload_templet_name')}：</span>
        <div class="item-bd">
            <input type="text" id="templetname" name="templetname" class="input-text">
            <div class="item-section lh-20 mt-10 c-red">
                <p></p>
            </div>
        </div>
    </li>
    <li>
        <span class="item-hd"><label class="c-red va-m mr-5">*</label>模板文件{Common::get_help_icon('templet_upload_templet_installer')}：</span>
        <div class="item-bd">
            <a href="javascript:;" class="btn btn-primary radius size-S" onclick="upload_templet()">上传文件</a>
            <span id="filename" name="filename" class="item-text ml-10"></span>
            <div class="item-section lh-20 c-red">
                <p></p>
            </div>
        </div>
    </li>
    <li class="clearfix text-c mt-10">
        <a href="javascript:;" class="btn radius" id="cancel_btn" onclick="currentd.close().remove();">取消</a>
        <a href="javascript:;" class="btn btn-primary  radius ml-20" id="comfirm_btn" onclick="save_templet()">确定</a>
    </li>


</ul>


<script language="JavaScript">

    var currentd = parent.window.d;
    //上传模板
    function upload_templet() {
        // 上传方法
        $.upload({
            // 上传地址
            url: SITEURL + 'templet/ajax_upload_templet',
            // 文件域名字
            fileName: 'filedata',
            fileType: 'zip',
            // 上传之前回调,return true表示可继续上传
            onSubmit: function () {
                $("#filename").text("");
                $("#filename").siblings(".item-section").find("p").html("");
                ST.Util.showMsg("正在上传模板文件...", 6, 1000000);
                return true;
            },
            // 上传之后回调
            onComplate: function (data) {
                ST.Util.hideMsgBox();
                data = $.parseJSON(data);
                //如果上传成功
                if (data.status === 1) {
                    $("#filename").text(data.data);
                }
                else {
                    ST.Util.showMsg(data.msg, 5, 3000);
                }
            }
        });
    }

    function save_templet() {
        if ($("#templetname").val() == "") {
            $("#templetname").siblings(".item-section").find("p").html("模板包名称不允许为空");
            $("#templetname").addClass("b-error");
            return;
        }
        $("#templetname").removeClass("b-error");
        $("#templetname").siblings(".item-section").find("p").html("");

        if ($("#filename").text() == "") {
            $("#filename").siblings(".item-section").find("p").html("请先上传模板文件");
            return;
        }
        $("#filename").siblings(".item-section").find("p").html("");


        ST.Util.showMsg("正在保存模板文件...", 6, 1000000);
        $.ajax({
            type: 'post',
            url: SITEURL + "templet/ajax_save_templet",
            data: {filename: $("#filename").text()},
            dataType: 'json',
            success: function (rs) {
                ST.Util.hideMsgBox();
                if (rs.status === 1) {
                    var upgrade_task_list = new Array();
                    var upgrade_task = {
                        system_part_code: "",
                        name: $("#templetname").val(),
                        upgrade_code: "",
                        app_number: "custom_templet",
                        system_part_type: "3",
                        installer_url: rs.data
                    };
                    upgrade_task_list.push(upgrade_task);

                    ST.Util.showMsg("正在准备执行模板安装...", 6, 1000000);
                    $.ajax({
                        type: 'post',
                        url: SITEURL + "upgrade/ajax_ready_upgrade",
                        data: {upgrade_task_list: upgrade_task_list},
                        dataType: 'json',
                        success: function (rs) {
                            ST.Util.hideMsgBox();
                            if (rs.status === 1) {
                                var url = SITEURL + "upgrade/install";
                                //var currentd = parent.window.d;
                                ST.Util.showBox("模板安装", url, 450, 300, function () {
                                    ST.Util.responseDialog({data:rs},true);
                                    //currentd.close().remove();
                                });
                            } else {
                                ST.Util.showMsg(rs.msg, 5, 3000);
                            }
                        }
                    });

                } else {
                    ST.Util.showMsg(rs.msg, 5, 3000);
                }
            }
        });

    }

</script>

</body>
</html><script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.1601&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
