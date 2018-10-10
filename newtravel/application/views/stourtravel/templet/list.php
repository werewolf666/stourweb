<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>我的模板-思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,base_new.css,plist.css,templet_manage.css'); }

</head>
<body style="overflow:hidden">
<table class="content-tab" html_strong=zSLwOs >
    <tr>
        <td width="119px" class="content-lt-td" valign="top">
            {template 'stourtravel/public/leftnav'}
            <!--右侧内容区-->
        </td>
        <td valign="top" class="content-rt-td" style="overflow:hidden">

            <div class="st-table-content">
                <div class="cfg-header-bar clearfix">
                     <span class="select-box fl w100 ml-10 mt-5">
                         <select id="platform"  id="platform" class="select">
                             <?php
                             foreach ($templet_support_platform_type_list as $support_platform_type)
                             {
                                 $is_selected = ($support_platform_type->id == $selected_platform ? "selected" : "");
                                 echo "<option value='" . $support_platform_type->id . "' $is_selected>" . $support_platform_type->title . "</option>";
                             }
                             ?>
                         </select>
                     </span>
                     <span class="select-box fl w100 ml-10 mt-5 {if $selected_webid<0} hide {/if}">
                         <select id="webid"  id="webid" class="select">
                             <?php
                             if ($selected_webid < 0)
                             {
                                 echo "<option value='$selected_webid' selected></option>";
                             } else
                             {
                                 foreach ($weblist as $webinfo)
                                 {
                                     $is_selected = ($webinfo["webid"] == $selected_webid ? "selected" : "");
                                     echo "<option value='" . $webinfo["webid"] . "' $is_selected>" . $webinfo["webname"] . "</option>";
                                 }
                             }

                             ?>
                         </select>
                     </span>
                    <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-20" onclick="window.location.reload();">刷新</a>
                    <a href="javascript:;" class="my-tem-btn fr btn btn-primary radius mt-6 mr-10 " onclick="ST.Util.addTab('安装包管理','templetmall/my_templet/menuid/194')">安装包管理</a>
                </div>
                <table class="st-table-block">
                    <thead>
                    <tr>
                        <th width="10%"><strong class="st-hd text-c">模板位置</strong></th>
                        <th width="60%"><strong class="st-hd text-c">模板包名称</strong></th>
                        <th width="10%"><strong class="st-hd text-c">来源</strong></th>
                        <th width="10%"><strong class="st-hd text-c">启用{Common::get_help_icon('templet_index_pageapply')}</strong></th>
                        <th width="10%"><strong class="st-hd text-c">管理{Common::get_help_icon('templet_index_pagemanage')}</strong></th>
                    </tr>
                    </thead>
                </table>
                <ul class="tem-cons">

                </ul>
            </div>

        </td>
    </tr>
</table>
<script>

    var cfg_public_url = "{$GLOBALS['cfg_public_url']}";
    var templet_info_list = eval('({$templet_info_list_json})');

    var bodyHeihgt = $("body").height();
    $(".tem-cons").css("height", bodyHeihgt - 74);

    $(function () {

        $("#platform,#webid").change(function () {
            window.location.href = "?platform=" + $("#platform").val() + "&webid=" + $("#webid").val();
        })

        ST.Util.showMsg("正在加载页面模块数据...", 6, 1000000);
        $.ajax({
            type: 'post',
            url: SITEURL + "templet/ajax_list",
            data: {pid: "0-0", platform: $("#platform").val(), webid: $("#webid").val()},
            dataType: 'json',
            success: function (rs) {
                ST.Util.hideMsgBox();
                if (rs.status === 1) {
                    showPageModule(rs.data);
                } else {
                    ST.Util.showMsg(rs.msg, 5, 3000);
                }
            }
        });

    });

    function showPageModule(pageModuleList) {
        var html = "";
        for (var index = 0; index < pageModuleList.length; index++) {
            html += "<li>";
            html += "<p class=\"tem-cons-tit\">";
            html += "<i class=\"menu-open-btn ml-20 mr-10 va-m\" onclick=\"showPage(this,'" + pageModuleList[index].id + "','page_module_cons_" + pageModuleList[index].id + "');\"></i>";
            html += "<span class=\"va-m\">" + pageModuleList[index].title + "</span>";
            html += "</p>";
            html += "<div class=\"tem-cons-item\" id=\"page_module_cons_" + pageModuleList[index].id + "\">";
            html += "</div>"
            html += "</li>";
        }
        $(".tem-cons").html(html);

    }

    function showPage(sender, pageModuleId, pageModuleConsId) {

        if ($(sender).hasClass("on")) {
            $(sender).removeClass("on");
            $(sender).parent().siblings(".tem-cons-item").hide();
        } else {
            if ($("#" + pageModuleConsId).html() == "") {
                ST.Util.showMsg("正在加载页面数据...", 6, 1000000);
                $.ajax({
                    type: 'post',
                    url: SITEURL + "templet/ajax_list",
                    data: {pid: pageModuleId, platform: $("#platform").val(), webid: $("#webid").val()},
                    dataType: 'json',
                    success: function (rs) {
                        ST.Util.hideMsgBox();
                        if (rs.status === 1) {
                            var html = "";
                            for (var index = 0; index < rs.data.length; index++) {
                                html += "<li>";
                                html += "<p class=\"tem-cons-tit pl-20 clearfix\">";
                                html += "<i class=\"menu-open-btn ml-20 mr-10 va-m\" onclick=\"showPageTemplet(this,'" + rs.data[index].id + "','page_templet_cons_" + rs.data[index].id + "');\"></i>";
                                html += "<span>" + rs.data[index].title + "</span>";
                                //html += "<a class=\"fr btn btn-primary-outline radius mt-6 mr-20\" href=\"#\">商城</a>";
                                html += "</p>";
                                html += "<table class=\"st-table-block table-hover tem-table-con\" id=\"page_templet_cons_" + rs.data[index].id + "\">";
                                html += "<tbody>"
                                html += "</tbody>";
                                html += "</table>";
                                html += "</li>";
                            }
                            $("#" + pageModuleConsId).html(html);
                        } else {
                            ST.Util.showMsg(rs.msg, 5, 3000);
                        }
                    }
                });

            }

            $(sender).addClass("on");
            $(sender).parent().siblings(".tem-cons-item").show();
        }

    }

    function showPageTemplet(sender, pageName, pageTempletConsId) {

        if ($(sender).hasClass("on")) {
            $(sender).removeClass("on");
            $(sender).parent().siblings(".st-table-block").hide();
        } else {
            if ($("#" + pageTempletConsId + " tbody").html() == "") {
                ST.Util.showMsg("正在加载页面模板数据...", 6, 1000000);
                $.ajax({
                    type: 'post',
                    url: SITEURL + "templet/ajax_list",
                    data: {pid: pageName, platform: $("#platform").val(), webid: $("#webid").val()},
                    dataType: 'json',
                    success: function (rs) {
                        ST.Util.hideMsgBox();
                        if (rs.status === 1) {
                            var html = "";
                            for (var index = 0; index < rs.data.length; index++) {
                                html += getPageTempletItemHtml(rs.data[index]);
                            }

                            $("#" + pageTempletConsId + " tbody").html(html);
                        } else {
                            ST.Util.showMsg(rs.msg, 5, 3000);
                        }
                    }
                });

            }

            $(sender).addClass("on");
            $(sender).parent().siblings(".st-table-block").show();
        }

    }

    function getPageTempletItemHtml(pageTempletItem) {
        var pageTempletItemData = getPageTempletItemData(pageTempletItem);

        var html = "";
        html += "<tr>";
        html += "<td width=\"10%\"></td>";
        html += "<td width=\"60%\"><a class=\"btn-link text-overflow pl-30\" href=\"" + (pageTempletItemData.url == "" ? "javascript:;" : pageTempletItemData.url) + "\" target='_blank'>" + pageTempletItemData.templetName + "</a></td>";
        html += "<td width=\"10%\" class=\"text-c\">" + pageTempletItemData.from + "</td>";
        html += "<td width=\"10%\" class=\"text-c\">";
        html += "<i class=\"use-btn state-btn " + (pageTempletItemData.isuse == 1 ? "on" : "") + "\" onclick=\"setPageTempletUse(this,'" + pageTempletItem.pid + "','" + pageTempletItem.pagepath + "');\"></i>";
        html += "</td>";
        html += "<td width=\"10%\">";
        if (pageTempletItemData.fromType == "" || pageTempletItemData.fromType == "custom_templet") {
            //不能关联到安装包的模板页面或是定制安装包可以删除，修改
            html += "<div class=\"ml-50\">";
            if (pageTempletItemData.fromType == "custom_templet") {
                var editTempletPageUrl = "templet/edit_templet_page";
                editTempletPageUrl += "/folder/" + pageTempletItem.pagepath;
                editTempletPageUrl += "/ismobile/" + ($("#webid").val() >= 0 ? "0" : "1");
                editTempletPageUrl += "/system_part_code/" + pageTempletItemData.templetId;
                editTempletPageUrl += "/menuid/194";
                html += "<a href=\"javascript:;\" class=\"btn-link va-m\" onclick=\"ST.Util.addTab('模板编辑','" + editTempletPageUrl + "')\">编辑</a>";
            }
            if (pageTempletItemData.fromType == "") {
                html += "<a href=\"javascript:;\" class=\"btn-link va-m\" onclick=\"deletePageTemplet(this,'" + pageTempletItem.pid + "','" + pageTempletItem.pagepath + "')\">删除</a>";
            }
            html += "</div>";
        }
        html += "</td>";
        html += "</tr>";
        return html;
    }

    function getPageTempletItemData(pageTempletItem) {
        var result = {
            templetId: 0,
            templetName: pageTempletItem.title,
            from: (pageTempletItem.pagepath == "" ? "默认" : ""),
            fromType: (pageTempletItem.pagepath == "" ? "system_templet" : ""),
            url:"",
            isuse: pageTempletItem.isuse,
            templetStatus: 2,
            templetStatusName: "已安装"
        };

        if ($.isArray(templet_info_list) && templet_info_list.length > 0 && pageTempletItem.pagepath != "") {
            for (var i = 0; i < templet_info_list.length; i++) {
                for (var j = 0; j < templet_info_list[i].handle_pagepath.length; j++) {
                    if (templet_info_list[i].handle_pagepath[j] == pageTempletItem.pagepath) {
                        result.templetId = templet_info_list[i].id;
                        result.templetName = templet_info_list[i].name;
                        result.from = templet_info_list[i].from;
                        result.fromType = templet_info_list[i].from_type;
                        result.url = templet_info_list[i].url;
                        result.templetStatus = templet_info_list[i].appStatus;
                        result.templetStatusName = templet_info_list[i].appStatusName;
                        return result;
                    }
                }
            }
        }

        return result;
    }

    function setPageTempletUse(sender, pageName, pagePath) {
        if ($(sender).hasClass("on")) {
            return;
        }

        ST.Util.showMsg("正在开启页面模板...", 6, 1000000);
        $.ajax({
            type: 'post',
            url: SITEURL + "templet/ajax_set_page_templet_use",
            data: {pagename: pageName, pagepath: pagePath, platform: $("#platform").val(), webid: $("#webid").val()},
            dataType: 'json',
            success: function (rs) {
                ST.Util.hideMsgBox();
                if (rs.status === 1) {
                    $(sender).parent().parent().parent().find(".use-btn").removeClass("on");
                    $(sender).addClass("on");
                } else {
                    ST.Util.showMsg(rs.msg, 5, 3000);
                }
            }
        });
    }

    function deletePageTemplet(sender, pageName, pagePath) {
        ST.Util.confirmBox('删除页面模板', '确定删除' + pagePath + "页面模板?", function () {
            ST.Util.showMsg("正在删除页面模板...", 6, 1000000);
            $.ajax({
                type: 'post',
                url: SITEURL + "templet/ajax_delete_page_templet",
                data: {pagename: pageName, pagepath: pagePath, platform: $("#platform").val(), webid: $("#webid").val()},
                dataType: 'json',
                success: function (rs) {
                    ST.Util.hideMsgBox();
                    if (rs.status === 1) {
                        $(sender).parent().parent().parent().remove();
                    } else {
                        ST.Util.showMsg(rs.msg, 5, 3000);
                    }
                }
            });
        });
    }

</script>
</body>
</html>
