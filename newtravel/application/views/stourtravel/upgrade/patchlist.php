<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>系统升级-思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,upgrade.css'); }

</head>

<body>
<!--顶部-->
<table class="content-tab">
<tr>
    <td width="119px" class="content-lt-td" valign="top">
        {template 'stourtravel/public/leftnav'}
        <!--右侧内容区-->
    </td>
    <td valign="top" class="content-rt-td" style="overflow:auto;">


        <div class="manage-nr">

            <div class="version_sj">
                <div class="now_version">
                    <p>当前版本：<span id="currentversion"></span></p>
                </div>

                <div class="version_list" style="overflow-y: hidden;">
                    <table width="100%" border="0" id="versionlist" style="position:relative">
                        <tr>
                            <th height="30" scope="col">状态</th>
                            <th scope="col">升级包版本</th>
                            <th scope="col">发布时间</th>
                            <th scope="col">大小</th>
                            <th scope="col">升级说明</th>
                        </tr>

                    </table>
                </div>

            </div>

    </td>
</tr>


<!--左侧导航区-->

<!--右侧内容区-->

<script language="JavaScript">
var public_url = "{$GLOBALS['cfg_public_url']}";

var current_partcode = "{$system_part_code}";
$(function () {
    getVersionList();
})

function generateRequestUrl(paras) {
    return  SITEURL + "/" + paras;
}

//检测更新包
function getVersionList() {

    $.ajax({
        type: 'POST',
        url: generateRequestUrl('upgrade/ajax_get_patch_list'),
        dataType: 'json',
        data: {system_part_code: current_partcode},
        beforeSend: function () {
            ST.Util.showMsg('正在获取更新包列表...', 6, 1000000);
        },
        success: function (data) {
            ST.Util.hideMsgBox();
            $('#versionlist tr').first().siblings().remove();

            if(data.status != 1)
            {
                ST.Util.showMsg("查找更新包信息失败 "+data.msg, 5, 3000);
                return;
            }

            var tr = '';
            $.each(data.data, function (i, row) {
                var mclass = row.upgrade_status == '已更新' ? 'hasupgrade' : 'noupgrade';
                tr += '<tr class="' + mclass + '">';
                tr += '<td height="50" align="center"><span  class="gxstatus ' + mclass + '">' + row.upgrade_status + '<span></td>';
                tr += '<td align="center">' + row.version + '</td>';
                tr += '<td align="center">' + row.pubdate + '</td>';
                tr += '<td align="center">' + row.filesize + '</td>';
                tr += '<td align="center"><img class="showdetail" data-version="' + row.version + '" data-pubdate="' + row.pubdate + '" src="' + public_url + 'images/sjsm.png" title="查看版本更新内容"/><div class="ver_desc">' + row.desc + '</div></td>'
                tr += '</tr>';
            })
            $('#versionlist').append(tr);
            $('#currentversion').html(data.current_version);
            addEvent();
        }
    })
}

function addEvent() {
    $('.showdetail').click(function () {
        var content = $(this).parent().find('.ver_desc').html();
        var version = $(this).attr('data-version');
        var pubdate = $(this).attr('data-pubdate');
        var html = "<style>.version_num_con{color:#565656;float:left;width:750px}.version_num_con dl{ float:left;width:100%;border-bottom:1px solid #dcdcdc}.version_num_con dl dt{float:left;width:11%;height:40px;line-height:40px}.version_num_con dl dd{width:89%;float:left;line-height:40px;}.ver_desc{display: none}</style>";

        html += '<div class="version_num_con">';
        html += '<dl>';
        html += '<dt>版本号：</dt>';
        html += '<dd>' + version + '</dd>';
        html += '</dl>';
        html += '<dl>';
        html += '<dt>发布时间：</dt>';
        html += '<dd>' + pubdate + '</dd>';
        html += '</dl>';
        html += '<dl>';
        html += '<dt>升级内容：</dt>';
        html += '<dd>';
        html += content;
        html += '</dd>';
        html += '</dl>';
        html += '</div>';

        ST.Util.messagBox('查看版本详细信息', html);
    })
}

</script>
</body>
</html>