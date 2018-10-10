<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>系统模块升级管理-思途CMS<?php echo $coreVersion;?></title>
    <?php echo  Stourweb_View::template('stourtravel/public/public_min_js');  ?>
    <?php echo Common::getCss('style.css,base.css,base2.css,plist.css,mall.css,upgrade.css'); ?>
    <?php echo Common::getScript("template.js"); ?>
    <?php echo Common::getCss('uploadify.css','js/uploadify/'); ?>
</head>
<body>
<table class="content-tab">
<tr>
<td width="119px" class="content-lt-td"  valign="top">
    <?php echo  Stourweb_View::template('stourtravel/public/leftnav');  ?>
    <!--右侧内容区-->
</td>
<td valign="top" class="content-rt-td">
<!--面包屑-->
    <div class="list-top-set">
        <div class="list-web-pad"></div>
        <div class="list-web-ct">
            <table class="list-head-tb">
                <tbody>
                <tr>
                    <td class="head-td-lt">
                    </td>
                    <td class="head-td-rt">
                        <a href="javascript:;" class="refresh-btn" onclick="window.location.reload()">刷新</a>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="manage">
        <div id="core_system_upgrade_info" class="manage-top">
        </div>
        <div id="app_upgrade_info" class="manage-bot">
        </div>
        <div id="templet_upgrade_info" class="manage-bot">
        </div>
    </div>
</td>
</tr>
</table>
<scirpt id="core_system_upgrade" type="text/html" style="display: none">
    <p class="welcome">
        欢迎使用思途CMS旅游电商管理系统！</p>
    <p>
        <label>
            当前系统版本：</label><span class="system-version">V{{core_system_data.system_part.cVersion}}</span>
        {{if core_system_data.last_upgrade_info != undefined}}
            &nbsp;&nbsp;<label>
                官方最新：</label><span class="new-system">V{{core_system_data.last_upgrade_info.Version}}</span>
        {{/if}}
    </p>
    <div class="upgrade">
        {{if is_exists_formal_upgrade === true}}
            <span>系统有新的正式更新！</span>
            <a class="upgrade-btn" href="javascript:;" onclick="javascript:core_system_upgrade(0);">立即升级</a>
            <a class="upgrade-des" href="jsvascript:;" onclick="javascript:show_core_system_patch_list();">查看升级详情</a>
        {{else}}
            <span>您的系统已是最新版，感谢选择思途！</span>
            <a class="upgrade-des" href="jsvascript:;" onclick="javascript:show_core_system_patch_list();">查看升级详情</a>
            {{ if is_exists_beta_upgrade === true}}
                <br>
                <span>系统有新的公测更新！公测更新还在测试完善当中，可能存在未知问题，你可以选择升级体验。</span>
                <a class="upgrade-btn" href="javascript:;" onclick="javascript:core_system_upgrade(1);">升级体验</a>
                <a class="upgrade-des" href="jsvascript:;" onclick="javascript:show_core_system_patch_list();">查看升级详情</a>
            {{/if}}
        {{/if}}
    </div>
</scirpt>
<scirpt id="app_upgrade" type="text/html" style="display: none">
    <p>
        <label>
            正版授权应用数：</label>
        <em>{{app_count}}个</em> <a class="manage-a" href="javascript:;"  onclick="ST.Util.addTab('我的应用','<?php echo $cmsurl;?>mall/app/menuid/193')"><i></i>&nbsp;<span>管理应用</span></a>
        <span class="manage-memo-text">*点击进入应用管理，针对每个应用进行详细管理</span>
    </p>
    <p>
        <label>
            已安装应用数：</label>
        <em>{{app_installed_count}}个</em>
        <span class="manage-memo-text-a">*进入应用管理，选择已安装筛选项，可进行【批量卸载】操作</span>
    </p>
    <p>
        <label>
            已安装可升级应用数：</label>
        <em class="sj">{{app_exists_upgrade_count}}个</em>
        {{if app_exists_upgrade_count != 0}}
        <a class="manage-sj" href="javascript:;"  onclick="javascript:app_upgrade('{{app_exists_upgrade_upgradecode}}', 0);"><i></i>&nbsp;<span>一键升级</span></a>
        <span class="manage-memo-text">*在此处点击可进行批量升级，进入应用管理，可对单个应用进行升级管理。选择可升级筛选项，也可进行【批量升级】操作</span>
        {{/if}}
    </p>
    <p>
        <label>
            已禁止更新应用数：</label>
        <em class="sj_disabled">{{app_disabled_upgrade_count}}个</em> <a class="manage-a" href="javascript:;"  onclick="ST.Util.addTab('我的应用','<?php echo $cmsurl;?>mall/app/menuid/193')"><i></i>&nbsp;<span>管理应用</span></a>
        <span class="manage-memo-text">*禁止升级的应用，不可升级，不会再提示升级，也不会在升级列表中显示。</span>
    </p>
    <p>
        <label>
            未安装应用数：</label>
        <em>{{app_not_install_count}}个</em>
        <span class="manage-memo-text-a">*进入应用管理，选择未安装筛选项，可进行【批量安装】操作</span>
    </p>
</scirpt>
<scirpt id="templet_upgrade" type="text/html" style="display: none">
    <p>
        <label>
            正版授权模板数：</label>
        <em>{{templet_count}}个</em> <a class="manage-a" href="javascript:;"  onclick="ST.Util.addTab('我的模板','<?php echo $cmsurl;?>templetmall/my_templet/menuid/194')"><i></i>&nbsp;<span>管理模板</span></a>
        <span class="manage-memo-text">*点击进入模板管理，针对每个模板进行详细管理</span>
    </p>
    <p>
        <label>
            已安装模板数：</label>
        <em>{{templet_installed_count}}个</em>
        <span class="manage-memo-text-a">*进入模板管理，选择已安装筛选项，可进行【批量卸载】操作</span>
    </p>
    <p>
        <label>
            已安装可升级模板数：</label>
        <em class="sj">{{templet_exists_upgrade_count}}个</em>
        {{if templet_exists_upgrade_count != 0}}
        <a class="manage-sj" href="javascript:;"  onclick="javascript:templet_upgrade('{{templet_exists_upgrade_upgradecode}}', 0);"><i></i>&nbsp;<span>一键升级</span></a>
        <span class="manage-memo-text">*在此处点击可进行批量升级，进入模板管理，可对单个模板进行升级管理。选择可升级筛选项，也可进行【批量升级】操作</span>
        {{/if}}
    </p>
    <p>
        <label>
            已禁止更新模板数：</label>
        <em class="sj_disabled">{{templet_disabled_upgrade_count}}个</em> <a class="manage-a" href="javascript:;"  onclick="ST.Util.addTab('我的模板','<?php echo $cmsurl;?>templetmall/my_templet/menuid/194')"><i></i>&nbsp;<span>管理模板</span></a>
        <span class="manage-memo-text">*禁止升级的模板，不可升级，不会再提示升级，也不会在升级列表中显示。</span>
    </p>
    <p>
        <label>
            未安装模板数：</label>
        <em>{{templet_not_install_count}}个</em>
        <span class="manage-memo-text-a">*进入模板管理，选择未安装筛选项，可进行【批量安装】操作</span>
    </p>
</scirpt>
<script>
    var core_system_data_json = '<?php echo $core_system_data;?>';
    var app_data_list_json = '<?php echo $app_data_list;?>';
    var templet_data_list_json = '<?php echo $templet_data_list;?>';
    var core_system_data = null;
    var app_data_list = null;
    var templet_data_list = null;
    $(function () {
        if (core_system_data_json != "") {
            core_system_data = $.parseJSON(core_system_data_json);
        }
        if (app_data_list_json != "") {
            app_data_list = $.parseJSON(app_data_list_json);
        }
        if (templet_data_list_json != "") {
            templet_data_list = $.parseJSON(templet_data_list_json);
        }
        show_ui();
    })
    function show_ui() {
        if (core_system_data != null) {
            var is_exists_beta_upgrade = false;
            var is_exists_formal_upgrade = false;
            for (var i = 0; i < core_system_data.new_upgrade_info.length; i++) {
                if (core_system_data.new_upgrade_info[i].Status == 1)
                    is_exists_formal_upgrade = true;
                else
                    is_exists_beta_upgrade = true;
            }
            var html = template('core_system_upgrade', {
                core_system_data: core_system_data,
                is_exists_beta_upgrade: is_exists_beta_upgrade,
                is_exists_formal_upgrade: is_exists_formal_upgrade
            });
            $('#core_system_upgrade_info').html(html);
        }
        if (app_data_list != null) {
            var app_installed_count = 0;
            var app_exists_upgrade_upgradecode = "";
            var app_exists_upgrade_count = 0;
            var app_disabled_upgrade_count = 0;
            var app_not_install_count = 0;
            for (var i = 0; i < app_data_list.length; i++) {
                if (app_data_list[i].app_info.appStatus == 0)
                    app_not_install_count++;
                else {
                    app_installed_count++;
                    if (app_data_list[i].app_info.appStatus == 2)
                        app_disabled_upgrade_count++;
                    if (app_data_list[i].app_info.appStatus == 4) {
                        app_exists_upgrade_count++;
                        app_exists_upgrade_upgradecode += app_data_list[i].app_info.code + ","
                    }
                }
            }
            var html = template('app_upgrade', {
                app_count: app_data_list.length,
                app_installed_count: app_installed_count,
                app_exists_upgrade_count: app_exists_upgrade_count,
                app_exists_upgrade_upgradecode: app_exists_upgrade_upgradecode,
                app_disabled_upgrade_count: app_disabled_upgrade_count,
                app_not_install_count: app_not_install_count
            });
            $('#app_upgrade_info').html(html);
        }
        if (templet_data_list != null) {
            var templet_installed_count = 0;
            var templet_exists_upgrade_upgradecode = "";
            var templet_exists_upgrade_count = 0;
            var templet_disabled_upgrade_count = 0;
            var templet_not_install_count = 0;
            for (var i = 0; i < templet_data_list.length; i++) {
                if (templet_data_list[i].templet_info.appStatus == 0 || templet_data_list[i].templet_info.appStatus == 1)
                    templet_not_install_count++;
                else {
                    templet_installed_count++;
                    if (templet_data_list[i].templet_info.appStatus == 3)
                        templet_disabled_upgrade_count++;
                    if (templet_data_list[i].templet_info.appStatus == 5) {
                        templet_exists_upgrade_count++;
                        templet_exists_upgrade_upgradecode += templet_data_list[i].templet_info.update_ProductCode + ","
                    }
                }
            }
            var html = template('templet_upgrade', {
                templet_count: templet_data_list.length,
                templet_installed_count: templet_installed_count,
                templet_exists_upgrade_count: templet_exists_upgrade_count,
                templet_exists_upgrade_upgradecode: templet_exists_upgrade_upgradecode,
                templet_disabled_upgrade_count: templet_disabled_upgrade_count,
                templet_not_install_count: templet_not_install_count
            });
            $('#templet_upgrade_info').html(html);
        }
    }
    function app_upgrade(app_upgrade_code, is_beta) {
        var upgrade_task_list = new Array();
        var app_upgrade_code_arr = app_upgrade_code.split(",");
        for (var i = 0; i < app_upgrade_code_arr.length; i++) {
            var app_data = null;
            for (var j = 0; j < app_data_list.length; j++) {
                if (app_data_list[j].app_info.code == app_upgrade_code_arr[i]) {
                    app_data = app_data_list[j];
                    break;
                }
            }
            if (app_data != null) {
                upgrade_task_list.push(
                    {
                        system_part_code: app_data.app_info.id,
                        name: app_data.app_info.name,
                        upgrade_code: app_data.app_info.code,
                        app_number: app_data.app_info.number,
                        system_part_type: "2"
                    });
            }
        }
        do_upgrade(upgrade_task_list, is_beta, 0);
    }
    function templet_upgrade(templet_upgrade_code, is_beta) {
        var upgrade_task_list = new Array();
        var templet_upgrade_code_arr = templet_upgrade_code.split(",");
        for (var i = 0; i < templet_upgrade_code_arr.length; i++) {
            var templet_data = null;
            for (var j = 0; j < templet_data_list.length; j++) {
                if (templet_data_list[j].templet_info.update_ProductCode == templet_upgrade_code_arr[i]) {
                    templet_data = templet_data_list[j];
                    break;
                }
            }
            if (templet_data != null) {
                upgrade_task_list.push(
                    {
                        system_part_code: templet_data.templet_info.id,
                        name: templet_data.templet_info.name,
                        upgrade_code: templet_data.templet_info.update_ProductCode,
                        app_number: templet_data.templet_info.update_ID,
                        system_part_type: "3"
                    });
            }
        }
        do_upgrade(upgrade_task_list, is_beta, 0);
    }
    function core_system_upgrade(is_beta) {
        var upgrade_task_list = new Array();
        upgrade_task_list.push(
            {
                system_part_code: core_system_data.system_part.code,
                name: '基础系统',
                upgrade_code: core_system_data.system_part.pcode,
                app_number: "",
                system_part_type: "1"
            }
        );
        do_upgrade(upgrade_task_list, is_beta, 1);
    }
    //升级应用
    function do_upgrade(upgrade_task_list, is_beta, is_core_system_upgrade) {
        var upgrade_action_name = (is_beta == 1 ? "公测升级" : "正式升级");
        var upgrade_action = (is_beta == 1 ? "betaupgrade" : "upgrade");
        if (upgrade_task_list.length <= 0) {
            ST.Util.showMsg("没有可执行" + upgrade_action_name + "的任务", 5, 3000);
            return;
        }
        var confirm_message = "";
        var system_part_type_name = "";
        if (is_core_system_upgrade == 1)
        {
            confirm_message = "确定对基础系统进行" + upgrade_action_name + '?';
            system_part_type_name = "基础系统";
        }
        else
        {
            confirm_message = '确定对' + upgrade_task_list.length + "个应用执行" + upgrade_action_name + '?';
            system_part_type_name = "应用";
        }
        ST.Util.confirmBox(system_part_type_name + upgrade_action_name, confirm_message, function () {
            ST.Util.showMsg("正在准备执行" + upgrade_action_name + "...", 6, 1000000);
            $.ajax({
                type: 'post',
                url: SITEURL + "upgrade/ajax_ready_upgrade",
                data: {upgrade_task_list: upgrade_task_list},
                dataType: 'json',
                success: function (rs) {
                    ST.Util.hideMsgBox();
                    if (rs.status === 1) {
                        var url = SITEURL + "upgrade/" + upgrade_action;
                        ST.Util.showBox(system_part_type_name + upgrade_action_name, url, 450, 300, function () {
                            window.location.reload();
                        });
                    } else {
                        ST.Util.showMsg(rs.msg, 5, 3000);
                    }
                }
            });
        });
    }
    function show_core_system_patch_list()
    {
        ST.Util.addTab('升级详情','<?php echo $cmsurl;?>upgrade/patch_list/system_part_code/'+core_system_data.system_part.code+"/menuid/192");
    }
</script>
</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=6.0.201611.0306&DomainName=&ServerIP=unknown&SerialNumber=70247748" ></script>
