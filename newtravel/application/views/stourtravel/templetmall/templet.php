<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>模板安装包-思途CMS{$coreVersion}</title>
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
            <div class="cfg-header-bar"> 
            		<span class="select-box w100 mt-5 ml-10 fl">
	                    <select id="support_platform_type" name="support_platform_type" class="select">
	                        <option value=''>显示</option>
	                        <?php
	                        foreach($templet_support_platform_type_list as $support_platform_type)
	                        {
	                            echo "<option value='{$support_platform_type->id}'>{$support_platform_type->title}</option>";
	                        }
	                        ?>
	                    </select>
                    </span>
                    <span class="select-box w100 mt-5 ml-10 fl">
	                    <select id="support_site_type" name="support_site_type" class="select">
	                        <option value=''>所在站点</option>
	                        <?php
	                        foreach($templet_support_site_type_list as $support_site_type)
	                        {
	                            echo "<option value='{$support_site_type->id}'>{$support_site_type->title}</option>";
	                        }
	                        ?>
	                    </select>
                    </span>

					<span class="select-box w100 mt-5 ml-10 fl">
	                    <select id="upgrade_status" name="upgrade_status" class="select">
	                        <option value=''>升级状态</option>
	                        <?php
                            foreach ($templet_status_list as $templet_status_id => $templet_status)
                            {
                                echo "<option value='$templet_status_id'>$templet_status</option>";
                            }
	                        ?>
	                    </select>
					</span>
					<span class="select-box w100 mt-5 ml-10 fl">
	                    <select id="templet_from" name="templet_from" class="select">
	                        <option value=''>来源</option>
	                        <option value='mall_templet'>商城</option>
	                        <option value='custom_templet'>定制</option>
	                        <option value='system_templet'>默认</option>
	
	                    </select>
                   </span>
                    <div class="cfg-header-search">
                    	<input type="text" id="searchkey" name="searchkey" value="模板包关键字" datadef="模板包关键字" class="search-text">
                    	<a href="javascript:;" id="head-search-btn" class="search-btn">搜索</a>
                    </div>
					
					<a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
					<a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="ST.Util.addTab('我的模板','templet/index/menuid/194')" class="normal-btn">我的模板{Common::get_help_icon('templetmall_my_templet_gaoji')}</a>
                    <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="upload_templet();" class="normal-btn">上传</a>
                
               
                      
            </div>
            <div id="product_grid_panel" class="content-nrt">

            </div>
        </td>
    </tr>
</table>
</body>
<script>
    var cfg_public_url = "{$GLOBALS['cfg_public_url']}";

    $(function(){
        $("#head-search-btn").click(function(){
            searchmytemplet();
        })

        $("#upgrade_status,#support_platform_type,#support_site_type,#templet_from").change(function(){
            searchmytemplet();
        })
    });
    function searchmytemplet() {
        var keyword = $.trim($("#searchkey").val());
        keyword = $("#searchkey").attr('datadef') == keyword ? '' : keyword;
        var upgrade_status = $("#upgrade_status").val();
        var support_site_type = $("#support_site_type").val();
        var support_platform_type = $("#support_platform_type").val();
        var templet_from = $("#templet_from").val();

        window.product_store.getProxy().setExtraParam('searchkey', keyword);
        window.product_store.getProxy().setExtraParam('upgrade_status', upgrade_status);
        window.product_store.getProxy().setExtraParam('support_site_type', support_site_type);
        window.product_store.getProxy().setExtraParam('support_platform_type', support_platform_type);
        window.product_store.getProxy().setExtraParam('templet_from', templet_from);
        window.product_store.loadPage(1);
    }
    function upload_templet() {
        var url = SITEURL + "templet/upload_templet";
        ST.Util.showBox("上传", url, 450, 250, null, null, document, {loadCallback: function () {
            window.location.reload();
        }, maxHeight: 250, loadWindow: window});
    }

    Ext.onReady(
        function () {
            Ext.tip.QuickTipManager.init();

            $("#searchkey").focusEffect();

            //产品store
            window.product_store = Ext.create('Ext.data.Store', {

                fields: [
                   'id','name','status','appStatus','appStatusName','site_name','platform_name','page','from','update_ID','update_ProductCode','url'
                ],

                proxy: {
                    type: 'ajax',
                    api: {
                        read: SITEURL + 'templetmall/ajax_templet_read'
                    },
                    reader: {
                        type: 'json',   //获取数据的格式
                        root: 'templet',
                        totalProperty: 'total'
                    }
                },
                remoteSort: false,
                autoLoad: true,
                pageSize: 20,
                listeners: {
                    load: function (store, records, successful, eOpts) {
                        if (!successful) {
                            ST.Util.showMsg("查找模板包信息失败", 5, 3000);
                            return;
                        }

                        var pageHtml = ST.Util.page(store.pageSize, store.currentPage, store.getTotalCount(), 10);
                        $("#line_page").html(pageHtml);
                        window.product_grid.doLayout();

                        $(".pageContainer .pagePart a").click(function () {
                            var page = $(this).attr('page');
                            product_store.loadPage(page);
                        });
                    }
                }

            });

            //产品列表
            window.product_grid = Ext.create('Ext.grid.Panel', {
                store: product_store,
                renderTo: 'product_grid_panel',
                border: 0,
                bodyBorder: 0,
                bodyStyle: 'border-width:0px',
                scroll: 'vertical', //只要垂直滚动条
                bbar: Ext.create('Ext.toolbar.Toolbar', {
                    store: product_store,  //这个和grid用的store一样
                    displayInfo: true,
                    emptyMsg: "",
                    items: [
                        {
                            xtype: 'panel',
                            id: 'listPagePanel',
                            html: '<div id="line_page"></div>'
                        },
                        {
                            xtype: 'combo',
                            fieldLabel: '每页显示数量',
                            width: 170,
                            labelAlign: 'right',
                            forceSelection: true,
                            value: 20,
                            store: {fields: ['num'], data: [
                                {num: 20},
                                {num: 40},
                                {num: 60}
                            ]},
                            displayField: 'num',
                            valueField: 'num',
                            listeners: {
                                select: changeNum
                            }
                        }
                    ],
                    listeners: {
                        single: true,
                        render: function (bar) {
                            var items = this.items;
                            //bar.down('tbfill').hide();

                            bar.insert(0, Ext.create('Ext.panel.Panel', {border: 0, html: '<div class="panel_bar"><a class="btn btn-primary radius mr-10" href="javascript:void(0);" onclick="CHOOSE.chooseAll()">全选</a><a class="btn btn-primary radius" href="javascript:void(0);" onclick="CHOOSE.chooseDiff()">反选</a></div>'}));
                            bar.insert(1,Ext.create('Ext.panel.Panel',{border:0,items:[{
                                xtype:'button',
                                cls:'my-extjs-btn',
                                text:'批量处理',
                                menu: {
                                    cls:'menu-no-icon',
                                    width:"82px",
                                    shadow:false,
                                    items:[
                                        {
                                                text: '安装', handler: function () {
                                                    install(get_selected_record());
                                            }
                                        },
                                        {
                                                text: '卸载', handler: function () {
                                                    uninstall(get_selected_record());
                                            }
                                        },
                                        {
                                                text: '正式升级', handler: function () {
                                                    do_upgrade(get_selected_record(),0);
                                            }
                                        },
                                        {
                                                text: '公测升级', handler: function () {
                                                    do_upgrade(get_selected_record(),1);
                                            }
                                        }
                                    ]
                                }

                            }]}));
                            bar.insert(2, Ext.create('Ext.toolbar.Fill'));
                            //items.add(Ext.create('Ext.toolbar.Fill'));
                        }
                    }
                }),
                columns: [
                    {
                        text: '选择',
                        width: '5%',
                        // xtype:'templatecolumn',
                        tdCls: 'product-ch',
                        align: 'center',
                        dataIndex: 'id',
                        sortable:false,
                        border: 0,
                        menuDisabled: true,
                        renderer: function (value, metadata, record) {
                            return  "<input type='checkbox' class='product_check' id='box_" + metadata.recordIndex + "' style='cursor:pointer' value='" + metadata.recordIndex + "' data-record-index='" + metadata.recordIndex + "'/>";
                        }
                    },
                    {
                        text: '模板包名称' + ST.Util.getGridHelp('templetmall_my_templet_installername'),
                        width: '32%',
                        align: 'center',
                        border: 0,
                        dataIndex: 'name',
                        sortable:false,
                        menuDisabled: true,
                        renderer: function (value, metadata, record, rowIndex, colIndex) {
                            var infourl = record.get("url");
                            if (infourl != "") {
                                return "<a href=\"" + infourl + "\" target=\"_blank\">" + value + "</a>";
                            }
                            else {
                                return value;
                            }

                            //return value;
                        }


                    },
                    {
                        text: '显示',
                        width: '8%',
                        align: 'center',
                        border: 0,
                        dataIndex: 'platform_name',
                        sortable:false,
                        menuDisabled: true,
                        renderer: function (value, metadata, record, rowIndex, colIndex) {
                            return value;
                        }


                    },
                    {
                        text: '所在站点',
                        width: '8%',
                        align: 'center',
                        border: 0,
                        dataIndex: 'site_name',
                        sortable:false,
                        menuDisabled: true,
                        renderer: function (value, metadata, record, rowIndex, colIndex) {
                            return value;
                        }


                    },
                    {
                        text: '来源' + ST.Util.getGridHelp('templetmall_my_templet_fromtype'),
                        width: '8%',
                        align: 'center',
                        border: 0,
                        dataIndex: 'from',
                        sortable:false,
                        menuDisabled: true,
                        renderer: function (value, metadata, record, rowIndex, colIndex) {
                            return value;
                        }


                    },
                    {
                        text: '已安装' + ST.Util.getGridHelp('templetmall_my_templet_installstatus'),
                        width: '8%',
                        align: 'center',
                        border: 0,
                        dataIndex: 'appStatus',
                        sortable:false,
                        menuDisabled: true,
                        renderer: function (value, metadata, record, rowIndex, colIndex) {
                            var html = "";
                            if (record.get("update_ID") != "system_templet") {
                                if (value == 0) {
                                    var image = cfg_public_url + "images/disable-ico.png";
                                    html = "<a href=\"javascript:;\" title=\"开发中，暂不能安装\">";
                                    html += "<img src=\"" + image + "\"/>";
                                    html += "</a>";
                                } else if (value == 1) {
                                    var image = cfg_public_url + "images/close-s.png";
                                    html = "<a href=\"javascript:;\" onclick=\"javascript:install(new Array(get_record(" + metadata.recordIndex + ")));\" title=\"未安装\">";
                                    html += "<img src=\"" + image + "\"/>";
                                    html += "</a>";
                                } else {
                                    var image = cfg_public_url + "images/show-ico.png";
                                    html = "<a href=\"javascript:;\" onclick=\"javascript:uninstall(new Array(get_record(" + metadata.recordIndex + ")));\" title=\"已安装\">";
                                    html += "<img src=\"" + image + "\"/>";
                                    html += "</a>";
                                }
                            }
                            return html;
                        }

                    },
                    {
                        text: '整包启用' + ST.Util.getGridHelp('templetmall_my_templet_installerapply'),
                        width: '8%',
                        align: 'center',
                        border: 0,
                        dataIndex: 'status',
                        sortable:false,
                        menuDisabled: true,
                        renderer: function (value, metadata, record, rowIndex, colIndex) {
                            var html = "";
                            if (record.get("appStatus") >= 2) {
                                if (value == 2) {
                                    var image = cfg_public_url + "images/show-ico.png";
                                    html = "<a href=\"javascript:;\" onclick=\"javascript:is_apply_set(get_record(" + metadata.recordIndex + "),0);\" title=\"已启用\">";
                                    html += "<img src=\"" + image + "\"/>";
                                    html += "</a>";

                                } else {
                                    var image = cfg_public_url + "images/close-s.png";
                                    html = "<a href=\"javascript:;\" onclick=\"javascript:is_apply_set(get_record(" + metadata.recordIndex + "),1);\" title=\"未启用\">";
                                    html += "<img src=\"" + image + "\"/>";
                                    html += "</a>";
                                }
                            }
                            return html;
                        }
                    },
                    {
                        text: '升级状态' + ST.Util.getGridHelp('templetmall_my_templet_upgradestatus'),
                        width: '8%',
                        align: 'center',
                        border: 0,
                        dataIndex: 'appStatus',
                        sortable:false,
                        menuDisabled: true,
                        renderer: function (value, metadata, record, rowIndex, colIndex) {
                            var html = "";
                            if (record.get("update_ID") != "system_templet" && record.get("update_ID") != "custom_templet" && value > 2) {
                                var color = "";
                                if (value == 3)
                                    color = "c-warning";
                                if (value == 4)
                                    color = "c-primary";
                                if (value == 5)
                                    color = "c-success";
                                if (value == 6)
                                    color = "c-999";

                                html = "<span class=" + color + ">" + record.get("appStatusName") + "</span>";
                            }
                            return html;
                        }

                    },
                    {
                        text: '管理',
                        width: '15%',
                        align: 'center',
                        border: 0,
                        dataIndex: 'appStatus',
                        sortable:false,
                        menuDisabled: true,
                        renderer: function (value, metadata, record, rowIndex, colIndex) {
                            if (record.get("update_ID") != "system_templet" && record.get("update_ID") != "custom_templet") {
                                var enableupgradelink = "<a class='btn-link' href=\"javascript:;\" onclick=\"javascript:is_upgrade_set(get_record(" + metadata.recordIndex + "),1);\">允许升级</a>";
                                var disableupgradelink = "<a class='btn-link' href=\"javascript:;\" onclick=\"javascript:is_upgrade_set(get_record(" + metadata.recordIndex + "),0);\">禁止升级</a>";
                                var doupgradelink = "<a class='btn-link' href=\"javascript:;\" onclick=\"javascript:do_upgrade(new Array(get_record(" + metadata.recordIndex + ")),0);\">升级</a>";
                                var dobetaupgradelink = "<a class='btn-link' href=\"javascript:;\" onclick=\"javascript:do_upgrade(new Array(get_record(" + metadata.recordIndex + ")),1);\">升级</a>";

                                if (value == 2)
                                    return disableupgradelink;

                                if (value == 3)
                                    return enableupgradelink;

                                if (value == 4)
                                    return dobetaupgradelink + "&nbsp;&nbsp;" + disableupgradelink;

                                if (value == 5)
                                    return doupgradelink + "&nbsp;&nbsp;" + disableupgradelink;

                                if (value == 6)
                                    return disableupgradelink;

                            }
                        }
                    }

                ],
                listeners: {
                    boxready: function () {


                        var height = Ext.dom.Element.getViewportHeight();
                        this.maxHeight = height - 106;
                        this.doLayout();
                    },
                    afterlayout: function (grid) {

                        var data_height = 0;
                        try {
                            data_height = grid.getView().getEl().down('.x-grid-table').getHeight();
                        } catch (e) {
                        }
                        var height = Ext.dom.Element.getViewportHeight();

                        if (data_height > height - 106) {
                            window.has_biged = true;
                            grid.height = height - 106;
                        }
                        else if (data_height < height - 106) {
                            if (window.has_biged) {


                                window.has_biged = false;
                                grid.doLayout();
                            }
                        }
                    }
                },
                plugins: [

                ],
                viewConfig: {
                    //enableTextSelection:true
                }
            });

        }
    )

    //切换每页显示数量
    function changeNum(combo, records) {

        var pagesize = records[0].get('num');
        window.product_store.pageSize = pagesize;
        window.product_store.loadPage(1);

        //window.product_grid.down('pagingtoolbar').moveFirst();

    }
    //实现动态窗口大小
    Ext.EventManager.onWindowResize(function () {
        var height = Ext.dom.Element.getViewportHeight();
        var data_height = window.product_grid.getView().getEl().down('.x-grid-table').getHeight();
        if (data_height > height - 106)
            window.product_grid.height = (height - 106);
        else
            delete window.product_grid.height;
        window.product_grid.doLayout();


    })

    function get_selected_record() {
        var check_cmp = Ext.select('.product_check:checked');

        var records = new Array();
        for (var i = 0; i < check_cmp.elements.length; i++) {
            var check_cmp_obj = $(check_cmp.elements[i]);
            records.push(get_record(check_cmp_obj.attr("data-record-index")));
        }

        return records;
    }

    function get_record(record_index) {
        return window.product_store.data.items[record_index].data;
    }

    function to_upgrade_task(upgrade_records) {
        var upgrade_task_list = new Array();

        for (var i = 0; i < upgrade_records.length; i++) {
            var upgrade_task = {
                system_part_code: upgrade_records[i].id,
                name: upgrade_records[i].name,
                upgrade_code: upgrade_records[i].update_ProductCode,
                app_number: upgrade_records[i].update_ID,
                system_part_type: "3"
            };

            upgrade_task_list.push(upgrade_task);
        }

        return upgrade_task_list;
    }

    //安装模板
    function install(records) {
        var upgrade_records = new Array();
        for (var index in records) {
            if (records[index].appStatus == 1)
                upgrade_records.push(records[index]);
        }

        if (upgrade_records.length <= 0) {
            ST.Util.showMsg("没有可安装的模板", 5, 3000);
            return;
        }

        ST.Util.confirmBox('模板安装', '确定安装' + upgrade_records.length + "个模板?", function () {
            ST.Util.showMsg("正在准备安装模板...", 6, 1000000);
            $.ajax({
                type: 'post',
                url: SITEURL + "upgrade/ajax_ready_upgrade",
                data: {upgrade_task_list: to_upgrade_task(upgrade_records)},
                dataType: 'json',
                success: function (rs) {
                    ST.Util.hideMsgBox();
                    if (rs.status === 1) {
                        var url = SITEURL + "upgrade/install";
                        ST.Util.showBox("模板安装", url, 450, 300, function () {
                            searchmytemplet()
                        });
                    } else {
                        ST.Util.showMsg(rs.msg, 5, 3000);
                    }
                }
            });
        });
    }
    //卸载模板
    function uninstall(records) {
        var upgrade_records = new Array();
        for (var index in records) {
            if (records[index].appStatus != 0 && records[index].appStatus != 1 &&  records[index].update_ID != "system_templet")
                upgrade_records.push(records[index]);
        }

        if (upgrade_records.length <= 0) {
            ST.Util.showMsg("没有可卸载的模板", 5, 3000);
            return;
        }

        ST.Util.confirmBox('模板卸载', '确定卸载' + upgrade_records.length + "个模板?", function () {
            ST.Util.showMsg("正在准备卸载模板...", 6, 1000000);
            $.ajax({
                type: 'post',
                url: SITEURL + "upgrade/ajax_ready_upgrade",
                data: {upgrade_task_list: to_upgrade_task(upgrade_records)},
                dataType: 'json',
                success: function (rs) {
                    ST.Util.hideMsgBox();
                    if (rs.status === 1) {
                        var url = SITEURL + "upgrade/uninstall";
                        ST.Util.showBox("模板卸载", url, 450, 300, function () {
                            searchmytemplet()
                        });
                    } else {
                        ST.Util.showMsg(rs.msg, 5, 3000);
                    }
                }
            });
        });
    }
    //升级模板
    function do_upgrade(records, is_beta) {
        var upgrade_records = new Array();
        for (var index in records) {
            var app_status = (is_beta == 1 ? 4 : 5);
            if (records[index].appStatus == app_status)
                upgrade_records.push(records[index]);
        }

        var upgrade_action_name = (is_beta == 1 ? "公测升级" : "正式升级");
        var upgrade_action = (is_beta == 1 ? "betaupgrade" : "upgrade");
        if (upgrade_records.length <= 0) {
            ST.Util.showMsg("没有可" + upgrade_action_name + "的模板", 5, 3000);
            return;
        }

        ST.Util.confirmBox('模板' + upgrade_action_name, '确定对' + upgrade_records.length + "个模板" + upgrade_action_name + '?', function () {
            ST.Util.showMsg("正在准备模板" + upgrade_action_name + "...", 6, 1000000);
            $.ajax({
                type: 'post',
                url: SITEURL + "upgrade/ajax_ready_upgrade",
                data: {upgrade_task_list: to_upgrade_task(upgrade_records)},
                dataType: 'json',
                success: function (rs) {
                    ST.Util.hideMsgBox();
                    if (rs.status === 1) {
                        var url = SITEURL + "upgrade/" + upgrade_action;
                        ST.Util.showBox("模板" + upgrade_action_name, url, 450, 300, function () {
                            searchmytemplet()
                        });
                    } else {
                        ST.Util.showMsg(rs.msg, 5, 3000);
                    }
                }
            });
        });
    }
    //设置模板升级状态
    function is_upgrade_set(record, status) {
        ST.Util.confirmBox('模板升级状态', '确定' + (status == 0 ? '禁止' : '允许') + record.name + '升级?', function () {
            ST.Util.showMsg('正在设置模板升级状态...', 6, 1000000);
            $.ajax({
                type: 'post',
                url: SITEURL + "templetmall/is_upgrade_set",
                data: {appid: record.id, status: status},
                dataType: 'json',
                success: function (rs) {
                    ST.Util.hideMsgBox();
                    var showTime = 1000;
                    if (rs.status === 1) {
                        ST.Util.showMsg(rs.msg, 4, showTime);
                        setTimeout(function () {
                            searchmytemplet();
                        }, showTime);
                    } else {
                        ST.Util.showMsg(rs.msg, 5, 3000);
                    }
                }
            });
        });
    }
    //设置模板升级状态
    function is_apply_set(record, is_apply) {
        ST.Util.confirmBox('模板启用', '确定' + (is_apply == 0 ? '取消启用' : '启用') + record.name + '?', function () {
            ST.Util.showMsg("正在" + (is_apply == 0 ? '取消启用' : '启用') + "模板...", 6, 1000000);
            $.ajax({
                type: 'post',
                url: SITEURL + "templetmall/is_templet_apply_set",
                data: {appid: record.id, is_apply: is_apply},
                dataType: 'json',
                success: function (rs) {
                    ST.Util.hideMsgBox();
                    var showTime = 1000;
                    if (rs.status === 1) {
                        ST.Util.showMsg(rs.msg, 4, showTime);
                        setTimeout(function () {
                            searchmytemplet();
                        }, showTime);
                    } else {
                        ST.Util.showMsg(rs.msg, 5, 3000);
                    }
                }
            });
        });
    }

</script>
</html><script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.1510&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
