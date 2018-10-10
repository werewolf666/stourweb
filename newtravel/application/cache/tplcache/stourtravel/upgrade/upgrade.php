<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS<?php echo $coreVersion;?></title>
    <?php echo  Stourweb_View::template('stourtravel/public/public_min_js');  ?>
    <?php echo Common::getCss('style.css,base.css,base2.css,plist.css,mall.css,upgrade.css'); ?>
    <?php echo Common::getScript("template.js"); ?>
    <?php echo Common::getCss('uploadify.css','js/uploadify/'); ?>
</head>
<body style="background-color: #fff">
<div class="out-box-con">
    <?php if($error_msg!="") { ?>
        <div class="con">
            <p class="error"><?php echo $error_msg;?></p>
            <a class="sure" href="javascript: ST.Util.closeBox();">关闭</a>
        </div>
    <?php } else { ?>
        <div class="con" id="upgrade_info"></div>
    <?php } ?>
</div>
</body>
<scirpt id="task_complete" type="text/html" style="display: none;">
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <p class="tiwen">成功完成{{task_count}}个升级任务！</p>
    <a class="sure" href="javascript: ST.Util.closeBox();">关闭</a>
</scirpt>
<scirpt id="task_cancel" type="text/html" style="display: none;">
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <p class="tiwen">已取消{{task_count}}个升级任务！</p>
    <a class="sure" href="javascript: ST.Util.closeBox();">关闭</a>
</scirpt>
<scirpt id="betaupgrade_confirm" type="text/html" style="display: none;">
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <p class="tiwen">公测更新还在测试完善当中，可能存在未知问题，您确定升级？</p>
    <a class="cancel" href="javascript:show_task_cancel_ui();">放弃升级</a>
    <a class="sure" href="javascript:show_database_backup_confirm_ui();">确定升级</a>
</scirpt>
<scirpt id="database_backup_confirm" type="text/html" style="display: none;">
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <p class="tiwen">升级前，您需要备份数据库吗？</p>
    <p>备份可能花费几分钟时间，具体根据您系统配置和数据库大小而定</p>
    <a class="cancel" href="javascript:is_backup_database=false;startup();">不备份</a>
    <a class="sure" href="javascript:is_backup_database=true;startup();">确定备份</a>
</scirpt>
<scirpt id="get_patch_list_error" type="text/html" style="display: none;">
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    {{if current_task.system_part_type === "1"}}
        <p class="num">获取{{current_task.name}}的更新包</p>
        <p class="error">获取{{current_task.name}}的更新包失败，原因：{{#error_msg}}</p>
    {{else}}
        <p class="num">共需升级<em>{{task_count}}</em>个，正在升级{{current_task.name}}，第<span>{{current_task_position}}/{{task_count}}</span>个</p>
        <p class="error">获取{{current_task.name}}的更新包失败，原因：{{#error_msg}}</p>
    {{/if}}
</scirpt>
<scirpt id="patch_exec_error" type="text/html" style="display: none;">
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    {{if current_task.system_part_type === "1"}}
        <p class="num">{{current_task.name}}共<em>{{patch_count}}</em>个更新，正在执行第<span>{{current_patch_position}}/{{patch_count}}</span>个</p>
    {{else}}
        <p class="num">共需升级<em>{{task_count}}</em>个，正在升级{{current_task.name}}，第<span>{{current_task_position}}/{{task_count}}</span>个</p>
        <p class="tiwen">{{current_task.name}}共{{patch_count}}个更新，正在执行第{{current_patch_position}}个</p>
    {{/if}}
    <p class="error">执行更新包：{{current_patch.version}}失败，原因：{{#error_msg}}</p>
</scirpt>
<scirpt id="get_patch_list" type="text/html" style="display: none;">
    {{if current_task.system_part_type === "1"}}
        <p class="num">获取{{current_task.name}}的更新包，请稍候...</p>
    {{else}}
        <p class="num">共需升级<em>{{task_count}}</em>个，正在升级{{current_task.name}}，第<span>{{current_task_position}}/{{task_count}}</span>个</p>
        <p class="tiwen">获取{{current_task.name}}的更新包，请稍候...</p>
    {{/if}}
    <p class="progress-bar"></p>
    <div class="notice">
        <label>
            注意：</label>
        <p>
            升级过程中，请勿强制取消、刷新、关闭系统页面，否则会导致升级失败！</p>
    </div>
</scirpt>
<scirpt id="patch_exec" type="text/html" style="display: none;">
    {{if current_task.system_part_type === "1"}}
        <p class="num">{{current_task.name}}共<em>{{patch_count}}</em>个更新，正在执行第<span>{{current_patch_position}}/{{patch_count}}</span>个</p>
    {{else}}
        <p class="num">共需升级<em>{{task_count}}</em>个，正在升级{{current_task.name}}，第<span>{{current_task_position}}/{{task_count}}</span>个</p>
        <p class="tiwen">{{current_task.name}}共{{patch_count}}个更新，正在执行第{{current_patch_position}}个</p>
    {{/if}}
    <p class="progress-bar"></p>
    <div class="info">
        <div>
            <label>
                更新包版本：</label><div class="descript">{{current_patch.version}}</div></div>
        <div>
            <label>
                发布时间：</label><div class="descript">{{current_patch.pubdate}}</div></div>
        <div>
            <label>
                更新包大小：</label><div class="descript">{{current_patch.filesize}}</div></div>
        <div>
            <label>
                更新包说明：</label><div class="descript">{{#current_patch.desc}}</div></div>
    </div>
    <div class="notice">
        <label>
            注意：</label>
        <p>
            升级过程中，请勿强制取消、刷新、关闭系统页面，否则会导致升级失败！</p>
    </div>
</scirpt>
<script language="JavaScript">
    var startup_error = '<?php echo $error_msg;?>';
    var is_betaupgrade = <?php echo $is_betaupgrade;?>;
    var is_backup_database = false;
    var task_list_json = '<?php echo $task_list;?>';
    var task_list = new Array();
    var current_task = null;
    var current_task_index = 0;
    var patch_list = new Array();
    var current_patch = null;
    var current_patch_index = 0;
    $(function () {
        if (startup_error != "")
            return;
        if (task_list_json != "") {
            task_list = $.parseJSON(task_list_json);
        }
        if (is_betaupgrade == 1) {
            show_betaupgrade_confirm_ui();
            return;
        }
        show_database_backup_confirm_ui();
    })
    function startup() {
        if (is_backup_database == true) {
            $.ajax({
                type: 'POST',
                url: SITEURL + 'upgrade/ajax_backup_database',
                dataType: 'json',
                beforeSend: function () {
                    ST.Util.showMsg('正在备份数据库...', 6, 1000000);
                },
                success: function (data) {
                    ST.Util.hideMsgBox();
                    if (data.status) { //备份数据库成功
                        exec_upgrade_task();
                    }
                    else {
                        ST.Util.showMsg('备份数据库失败', 5, 2000);
                    }
                }
            })
        }
        else {
            exec_upgrade_task();
        }
    }
    function exec_upgrade_task() {
        if (current_task_index > (task_list.length - 1)) {
            show_task_complete_ui();
            return;
        }
        current_task = task_list[current_task_index];
        show_get_patch_list_ui();
        $.ajax({
            type: 'post',
            url: SITEURL + "upgrade/ajax_exec_upgrade_task",
            data: {task: current_task, is_betaupgrade: is_betaupgrade},
            dataType: 'json',
            success: function (rs) {
                if (rs.status == 1) {
                    patch_list = rs.data;
                    current_patch_index = 0;
                    exec_upgrade_patch();
                } else {
                    show_get_patch_list_error_ui(rs.msg);
                }
            }
        });
    }
    function exec_upgrade_patch() {
        if (current_patch_index > (patch_list.length - 1)) {
            current_task_index++;
            exec_upgrade_task();
            return;
        }
        current_patch = patch_list[current_patch_index];
        show_patch_exec_ui();
        $.ajax({
            type: 'post',
            url: SITEURL + "upgrade/ajax_exec_upgrade_patch",
            data: {task: current_task, patch: current_patch},
            dataType: 'json',
            success: function (rs) {
                if (rs.status == 1) {
                    current_patch_index++;
                    exec_upgrade_patch();
                } else {
                    show_patch_exec_error_ui(rs.msg);
                }
            }
        });
    }
    function show_task_complete_ui() {
        var html = template('task_complete', {task_count: task_list.length});
        $('#upgrade_info').html(html);
        clear_cache();
    }
    function show_task_cancel_ui() {
        var html = template('task_cancel', {task_count: task_list.length});
        $('#upgrade_info').html(html);
    }
    function show_betaupgrade_confirm_ui() {
        var html = template('betaupgrade_confirm', null);
        $('#upgrade_info').html(html);
    }
    function show_database_backup_confirm_ui() {
        var html = template('database_backup_confirm', null);
        $('#upgrade_info').html(html);
    }
    function show_get_patch_list_error_ui(error_msg) {
        var html = template('get_patch_list_error', {
            task_count: task_list.length,
            current_task_position: current_task_index + 1,
            error_msg: error_msg,
            current_task: current_task
        });
        $('#upgrade_info').html(html);
    }
    function show_patch_exec_error_ui(error_msg) {
        var html = template('patch_exec_error', {
            task_count: task_list.length,
            current_task_position: current_task_index + 1,
            error_msg: error_msg,
            current_task: current_task,
            patch_count: patch_list.length,
            current_patch_position: current_patch_index + 1,
            current_patch: current_patch
        });
        $('#upgrade_info').html(html);
    }
    function show_get_patch_list_ui() {
        var html = template('get_patch_list', {
            task_count: task_list.length,
            current_task_position: current_task_index + 1,
            current_task: current_task
        });
        $('#upgrade_info').html(html);
    }
    function show_patch_exec_ui() {
        var html = template('patch_exec', {
            task_count: task_list.length,
            current_task_position: current_task_index + 1,
            current_task: current_task,
            patch_count: patch_list.length,
            current_patch_position: current_patch_index + 1,
            current_patch: current_patch
        });
        $('#upgrade_info').html(html);
    }
    function clear_cache() {
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
                    }
                }
            }
        );
    }
</script>
</html><script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=6.0.201611.0306&DomainName=&ServerIP=unknown&SerialNumber=70247748" ></script>
