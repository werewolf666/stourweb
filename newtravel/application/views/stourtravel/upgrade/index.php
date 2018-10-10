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
        <div class="list-top-set">
            <div class="list-web-pad"></div>
            <div class="list-web-ct">
                <table class="list-head-tb">
                    <tr>
                        <td>
                            <div class="w-set-tit bom-arrow">
                                {loop $version_info $key=>$info}
                                <span {if $n <= 1} class="on" {/if} data-partcode="{$key}" data-parttype="{$info['parttype']}" data-partcaption="{$info['caption']}"><s></s>{$info['caption']}</span>
                                {/loop}
                                <a href="javascript:;" class="refresh-btn" onclick="window.location.reload()">刷新</a>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="manage-nr">

            <div class="version_sj">
                <div class="now_version">
                    <p>当前版本：<span id="currentversion"></span></p>
                    <a class="normal-btn" id="nowgx_btn" href="javascript:;"
                       style="  margin-left:40px;float:left">立即更新</a>
                </div>

                <div class="version_list" style="overflow-y: hidden;">
                    <table width="100%" border="0" id="versionlist" style="position:relative">
                        <tr>
                            <th height="30" scope="col">状态</th>
                            <th scope="col">升级包版本</th>
                            <th scope="col">发布时间</th>
                            <th scope="col">大小</th>
                            <th scope="col">升级说明</th>
                            <th scope="col">升级状态</th>
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
var clicknum = 0;
var current_partcode = "";
var current_parttype = "";
var current_partcaption = "";
var is_exec_upgrade = false;
$(function () {
    //点击更新
    $("#nowgx_btn").click(function () {
        startUpgrade();
    })

    //切换
    $('.w-set-tit').find('span').click(function () {
        if (is_exec_upgrade)
            return;

        current_partcode = $(this).attr('data-partcode');
        current_parttype = $(this).attr('data-parttype');
        current_partcaption = $(this).attr('data-partcaption');

        $(this).addClass('on').siblings().removeClass('on');

        getCurrentVersion();
    })

    //新版检测
    var updatecheckurl = $('.w-set-tit .on').attr('data-parttype') == 'systempart' ?
        "upgrade/ajax_check_allSystemPart_update" :
        "upgrade/ajax_check_allAppPart_update/apppart/" + $('.w-set-tit .on').attr('data-partcode') + "?appname=" + $('.w-set-tit .on').attr('data-partcaption');
    $.ajax({
        url: SITEURL + updatecheckurl,
        dataType: 'json',
        success: function (data) {
            $(data).each(function () {
                if (this.newestVersioninfo != undefined && this.versioninfo.cVersion != this.newestVersioninfo.version) {
                    var spanobj = $('.w-set-tit').find("span[data-partcode='" + this.systemPartCode + "']");
                    spanobj.append("&nbsp;<img class=\"new-ico\" title=\"存在新的升级包\" src=\"/{$GLOBALS['cfg_backdir']}/public/images/N_03.gif\">")
                }
            })
        }});

    $('.w-set-tit .on').trigger("click");
})

function generateRequestUrl(paras) {
    return  SITEURL + "/" + paras + '/' + current_parttype + '/' + current_partcode + '?appname=' + current_partcaption;
}

function getCurrentVersion() {
    $.ajax({
        type: 'POST',
        url: generateRequestUrl("upgrade/ajax_current_version"),
        dataType: 'json',
        beforeSend: function () {
            ST.Util.showMsg('正在获取版本信息...', 6, 1000000);
        },
        success: function (data) {
            ST.Util.hideMsgBox();
            $('#currentversion').html(data.myversion);

            getVersionList();
        }
    })
}
//检测更新包
function getVersionList() {

    $.ajax({
        type: 'POST',
        url: generateRequestUrl('upgrade/ajax_version'),
        dataType: 'json',
        beforeSend: function () {
            ST.Util.showMsg('正在获取更新列表...', 6, 1000000);
        },
        success: function (data) {
            ST.Util.hideMsgBox();
            $('#versionlist tr').first().siblings().remove();

            var tr = '';
            $.each(data, function (i, row) {
                var status = row.status == '已更新' ? '已完成' : '<img class="jdimg" src="' + public_url + 'images/nojingdu.png" /><span class="tck_con errmsg" style="display:none"></span>';
                var mclass = row.status == '已更新' ? 'hasupgrade' : 'noupgrade';
                tr += '<tr class="' + mclass + '">';
                tr += '<td height="50" align="center"><span  class="gxstatus ' + mclass + '">' + row.status + '<span></td>';
                tr += '<td align="center">' + row.version + '</td>';
                tr += '<td align="center">' + row.pubdate + '</td>';
                tr += '<td align="center">' + row.filesize + '</td>';
                tr += '<td align="center"><img class="showdetail" data-version="' + row.version + '" data-pubdate="' + row.pubdate + '" src="' + public_url + 'images/sjsm.png" title="查看版本更新内容"/><div class="ver_desc">' + row.desc + '</div></td>'
                tr += '<td align="center" class="' + mclass + ' progress">' + status + '</td>'
                tr += '</tr>';
            })
            $('#versionlist').append(tr);
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

function upgradeui(enable) {
    if (enable) {
        clicknum = 0;
        is_exec_upgrade = false;
        $("#nowgx_btn").removeClass('nowgx_btn_click').addClass('normal-btn');
        var nogradearr = $("#versionlist").find('tr').find('.noupgrade');
        if (nogradearr.length > 0) {
            var lastv = nogradearr.last();
            lastv.find('.jdimg').attr('src', public_url + 'images/nojingdu.png');
            lastv.find('.errmsg').hide();
        }
    }
    else {
        is_exec_upgrade = true;
        $("#nowgx_btn").removeClass('normal-btn').addClass('nowgx_btn_click');
    }
}

function startUpgrade() {
    var noupgradenum = $("#versionlist").find("tr[class='noupgrade']").length;
    if (noupgradenum == 0) {
        ST.Util.showMsg('当前版本已经是最新版本', 4, 1000);
    } else {
        if (clicknum == 0) {
            upgradeui(false);
            clicknum++;
            ST.Util.confirmBox('系统升级', '升级前，您需要备份数据库吗?（备份可能花费几分钟时间，具体根据您系统配置和数据库大小而定）', function () {
                $.ajax({
                    type: 'POST',
                    url: generateRequestUrl('upgrade/ajax_backup_database'),
                    dataType: 'json',
                    beforeSend: function () {
                        ST.Util.showMsg('正在备份数据库...', 6, 1000000);
                    },
                    success: function (data) {
                        ST.Util.hideMsgBox();
                        if (data.status) { //备份数据库成功
                            execUpgrade('');
                        }
                        else {
                            ST.Util.showMsg('备份数据库失败,请检查', 5, 2000);
                            upgradeui(true);
                        }
                    }
                })
            }, function () {
                execUpgrade('');
            })
        }
    }
}

//升级
function execUpgrade(urlextparam) {
    $.ajax({
        type: 'POST',
        url: generateRequestUrl('upgrade/ajax_upgrade' + urlextparam),
        dataType: 'json',
        beforeSend: function () {
            var lastv = $("#versionlist").find("tr[class='noupgrade']").last();
            var scrolltop = lastv.offset().top - 30;
            lastv.find('.jdimg').attr('src', public_url + 'images/jingdu.gif');
            scrollToBottom(scrolltop);
        },
        success: function (data) {
            if (data.status == 1) {
                var lastv = $("#versionlist").find("tr[class='noupgrade']").last();
                $("#currentversion").html(data.currentversion);
                lastv.find('.gxstatus').html('已更新');
                lastv.find('.progress').html('已完成');
                lastv.find('.noupgrade').removeClass('noupgrade').addClass('hasupgrade');

                lastv.addClass('hasupgrade').removeClass('noupgrade');
                if ($("#versionlist").find("tr[class='noupgrade']").length >= 1) {//如果还有更新包则继续更新
                    execUpgrade('');
                } else {
                    upgradeui(true);
                    clearCache();
                }
            }
            else if (data.status == 2) {
                //需要用户确认
                ST.Util.confirmBox('系统升级', data.msg, function () {
                    execUpgrade('/confirmbetaversion/' + data.betaversion);
                }, function () {
                    upgradeui(true);
                    clearCache();
                })
            }
            else if (data.status == 3) {
                //依赖性检测失败，需要先升级内核补丁
                upgradeui(true);
                ST.Util.confirmBox('系统升级', data.msg, function () {
                    $(".w-set-tit span[data-partcode='{php echo Model_SystemParts::$coreSystemPartCode}']").trigger("click");
                }, function () {
                    clearCache();
                })
            }
            else {
                upgradeui(true);
                var lastv = $("#versionlist").find('tr').find('.noupgrade').last();
                lastv.find('.errmsg').html(data.msg);
                lastv.find('.errmsg').show();
                //如果是序列号验证失败,则弹出填写序列号窗口
                if (data.type == 'license_err') {
                    showBindBox();
                }
            }
        }
    })
}

function clearCache() {
    $.ajax(
        {
            type: "post",
            url: SITEURL + 'index/ajax_clearcache',
            beforeSend: function () {
                ST.Util.showMsg('正在清除缓存,请稍后...', 6, 60000);
            },
            success: function (data) {
                if (data == 'ok') {
                    ST.Util.showMsg('缓存清除成功', 4, 1000);
                    makeHtml();
                }
            }

        }
    );
}

function makeHtml() {
    $.ajax(
        {
            type: "post",
            url: SITEURL + 'index/ajax_makehtml',
            beforeSend: function () {
                ST.Util.showMsg('正在生成HTML,请稍后...', 6, 60000);
            },
            success: function (data) {
                if (data == 'ok') {
                    ST.Util.showMsg('生成HTML成功', 4, 1000);
                    ST.Util.showMsg('站点已升级至最新版!', 4, 1000);
                    window.location.reload();
                }
            }

        }
    );
}


//显示绑定页面
function showBindBox() {
    var url = SITEURL + "upgrade/bind";
    ST.Util.showBox('绑定授权ID', url, 560, 166, function () {
        upgradeui(true);
    });

}


//滚动到底部
function scrollToBottom(num) {
    $("html,body").animate({scrollTop: num}, 1000);
}

</script>
</body>
</html>
