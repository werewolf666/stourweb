<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>我的应用-思途CMS{$coreVersion}</title>
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
                	<span class="select-box w150 mt-5 ml-10 fl">
	                    <select id="app_type" name="app_type" class="select">
	                        <option value=''>应用类型</option>
	                        <?php
	                        foreach($app_type_list as $app_type)
	                        {
	                            echo "<option value='{$app_type->id}'>{$app_type->typename}</option>";
	                        }
	                        ?>
	                    </select>
                    </span>
                    <span class="select-box w150 mt-5 ml-10 fl">
	                    <select id="app_status" name="app_status" class="select">
	                        <option value=''>应用状态</option>
	                        <?php
	                        foreach($app_status_list as $app_status_id=> $app_status)
	                        {
	                            echo "<option value='$app_status_id'>$app_status</option>";
	                        }
	                        ?>
	                    </select>
                    </span>
                    <div class="cfg-header-search">
                    	<input type="text" id="searchkey" name="searchkey" value="应用关键字" datadef="应用关键字" class="search-text">
                    	<a href="javascript:;" id="head-search-btn" class="search-btn">搜索</a>
                   	</div>
                    

                    <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                                
                </div>
                <div id="product_grid_panel" class="content-nrt">

                </div>
            </td>
        </tr>
    </table>

</body>
<script>
    $(function(){
        $("#head-search-btn").click(function(){
            searchmyapp();
        })

        $("#app_type,#app_status").change(function(){
            searchmyapp();
        })
    });
    function searchmyapp() {
        var keyword = $.trim($("#searchkey").val());
        keyword = $("#searchkey").attr('datadef') == keyword ? '' : keyword;
        var app_type = $("#app_type").val();
        var app_status = $("#app_status").val();

        window.product_store.getProxy().setExtraParam('searchkey', keyword);
        window.product_store.getProxy().setExtraParam('app_type', app_type);
        window.product_store.getProxy().setExtraParam('app_status', app_status);
        window.product_store.loadPage(1);
    }

    Ext.onReady(
        function () {
            Ext.tip.QuickTipManager.init();

            $("#searchkey").focusEffect();

            //产品store
            window.product_store = Ext.create('Ext.data.Store', {

                fields: [
                    'id','name','appType','appStatus','appStatusName','number','summary','tag','version','author','modtime','backstageVersion','code','url','app_config_title','app_config_url'
                ],

                proxy: {
                    type: 'ajax',
                    api: {
                        read: SITEURL + 'mall/ajax_app_read'
                    },
                    reader: {
                        type: 'json',   //获取数据的格式
                        root: 'app',
                        totalProperty: 'total'
                    }
                },
                remoteSort: false,
                autoLoad: true,
                pageSize: 20,
                listeners: {
                    load: function (store, records, successful, eOpts) {
                        if (!successful) {
                            ST.Util.showMsg("查找应用信息失败", 5, 3000);
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

                            bar.insert(0, Ext.create('Ext.panel.Panel', {border: 0, html: '<div class="panel_bar"><a class="btn btn-primary radius mr-10" href="javascript:void(0);" onclick="CHOOSE.chooseAll()">全选</a><a class="btn btn-primary radius mr-10" href="javascript:void(0);" onclick="CHOOSE.chooseDiff()">反选</a></div>'}));
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
                        dataIndex: 'number',
                        border: 0,
                        menuDisabled: true,
                        renderer: function (value, metadata, record) {
                            return  "<input type='checkbox' class='product_check' id='box_" + metadata.recordIndex + "' style='cursor:pointer' value='" + metadata.recordIndex + "' data-record-index='" + metadata.recordIndex + "'/>";
                        }
                    },
                    {
                        text: '应用名称',
                        width: '30%',
                        align: 'center',
                        border: 0,
                        dataIndex: 'name',
                        menuDisabled: true,
                        renderer: function (value, metadata, record, rowIndex, colIndex) {

                            var infourl = record.get("url");
                            if (infourl != "") {
                                return "<a href=\"" + infourl + "\" target=\"_blank\">" + value + "</a>";
                            }
                            else {
                                return value;
                            }
                        }


                    },
                    {
                        text: '应用类型',
                        width: '20%',
                        align: 'center',
                        border: 0,
                        dataIndex: 'appType',
                        sortable: false,
                        menuDisabled: true,
                        renderer: function (value, metadata, record, rowIndex, colIndex) {
                            return value;
                        }


                    },
                    {
                        text: '最新发布',
                        width: '15%',
                        align: 'center',
                        border: 0,
                        dataIndex: 'modtime',
                        menuDisabled: true,
                        renderer: function (value, metadata, record, rowIndex, colIndex) {
                            return value;
                        }


                    },
                    {
                        text: '应用状态',
                        width: '15%',
                        align: 'center',
                        border: 0,
                        dataIndex: 'appStatus',
                        menuDisabled: true,
                        cls:'sort-col',
                        sortable: true,
                        renderer: function (value, metadata, record, rowIndex, colIndex) {
                            var color="#009933";
                            if(value == 0)
                                color="#FF9966";
                            if(value==1)
                                color="#0099CC";
                            if(value==2)
                                color="#FF99CC";
                            if(value==3||value==4)
                                color="red";

                            return "<font color="+color+">"+record.get("appStatusName")+"</font>";
                        }

                    },

                    {
                        text: '管理',
                        width: '15%',
                        align: 'center',
                        border: 0,
                        dataIndex: 'appStatus',
                        menuDisabled: true,
                        renderer: function (value, metadata, record, rowIndex, colIndex) {
                            var configurl = "";
                            if (record.get('app_config_url') != "") {
                                configurl = "<a href=\"javascript:;\" class=\"btn-link\" onclick=\"ST.Util.addTab('" + record.get('app_config_title') + "','" + record.get('app_config_url') + "');\">配置</a>";
                            }
                            var installlink = "<a href=\"javascript:;\" class=\"btn-link\" onclick=\"javascript:install(new Array(get_record(" + metadata.recordIndex + ")));\">安装</a>";
                            var uninstalllink = "<a href=\"javascript:;\" class=\"btn-link\" onclick=\"javascript:uninstall(new Array(get_record(" + metadata.recordIndex + ")));\">卸载</a>";
                            var enableupgradelink = "<a href=\"javascript:;\" class=\"btn-link\" onclick=\"javascript:is_upgrade_set(get_record(" + metadata.recordIndex + "),1);\">允许升级</a>";
                            var disableupgradelink = "<a href=\"javascript:;\" class=\"btn-link\" onclick=\"javascript:is_upgrade_set(get_record(" + metadata.recordIndex + "),0);\">禁止升级</a>";
                            var doupgradelink = "<a href=\"javascript:;\" class=\"btn-link\" onclick=\"javascript:do_upgrade(new Array(get_record(" + metadata.recordIndex + ")),0);\">升级</a>";
                            var dobetaupgradelink = "<a href=\"javascript:;\" class=\"btn-link\" onclick=\"javascript:do_upgrade(new Array(get_record(" + metadata.recordIndex + ")),1);\">升级</a>";

                            if (value == 0)
                                return installlink;

                            if (value == 1)
                                return (configurl == "" ? "" : configurl + "&nbsp;&nbsp;") + uninstalllink + "&nbsp;&nbsp;" + disableupgradelink

                            if (value == 2)
                                return (configurl == "" ? "" : configurl + "&nbsp;&nbsp;") + uninstalllink + "&nbsp;&nbsp;" + enableupgradelink;

                            if (value == 3)
                                return (configurl == "" ? "" : configurl + "&nbsp;&nbsp;") + uninstalllink + "&nbsp;&nbsp;" + disableupgradelink + "&nbsp;&nbsp;" + dobetaupgradelink;

                            if (value == 4)
                                return (configurl == "" ? "" : configurl + "&nbsp;&nbsp;") + uninstalllink + "&nbsp;&nbsp;" + disableupgradelink + "&nbsp;&nbsp;" + doupgradelink;

                            if (value == 5)
                                return (configurl == "" ? "" : configurl + "&nbsp;&nbsp;") + uninstalllink + "&nbsp;&nbsp;" + disableupgradelink;

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
                upgrade_code: upgrade_records[i].code,
                app_number: upgrade_records[i].number,
                system_part_type: "2"
            };

            upgrade_task_list.push(upgrade_task);
        }

        return upgrade_task_list;
    }

    //安装应用
    function install(records) {
        var upgrade_records = new Array();
        for (var index in records) {
            if (records[index].appStatus == 0)
                upgrade_records.push(records[index]);
        }

        if (upgrade_records.length <= 0) {
            ST.Util.showMsg("没有可执行安装的应用", 5, 3000);
            return;
        }

        ST.Util.confirmBox('应用安装', '确定对' + upgrade_records.length + "个应用执行安装?", function () {
            ST.Util.showMsg("正在准备执行应用安装...", 6, 1000000);
            $.ajax({
                type: 'post',
                url: SITEURL + "upgrade/ajax_ready_upgrade",
                data: {upgrade_task_list: to_upgrade_task(upgrade_records)},
                dataType: 'json',
                success: function (rs) {
                    ST.Util.hideMsgBox();
                    if (rs.status === 1) {
                        var url = SITEURL + "upgrade/install";
                        ST.Util.showBox("应用安装", url, 450, 300, function () {
                            searchmyapp()
                        });
                    } else {
                        ST.Util.showMsg(rs.msg, 5, 3000);
                    }
                }
            });
        });
    }
    //卸载应用
    function uninstall(records) {
        var upgrade_records = new Array();
        for (var index in records) {
            if (records[index].appStatus != 0)
                upgrade_records.push(records[index]);
        }

        if (upgrade_records.length <= 0) {
            ST.Util.showMsg("没有可执行卸载的应用", 5, 3000);
            return;
        }

        ST.Util.confirmBox('应用卸载', '确定对' + upgrade_records.length + "个应用执行卸载?", function () {
            ST.Util.showMsg("正在准备执行应用卸载...", 6, 1000000);
            $.ajax({
                type: 'post',
                url: SITEURL + "upgrade/ajax_ready_upgrade",
                data: {upgrade_task_list: to_upgrade_task(upgrade_records)},
                dataType: 'json',
                success: function (rs) {
                    ST.Util.hideMsgBox();
                    if (rs.status === 1) {
                        var url = SITEURL + "upgrade/uninstall";
                        ST.Util.showBox("应用卸载", url, 450, 300, function () {
                            searchmyapp()
                        });
                    } else {
                        ST.Util.showMsg(rs.msg, 5, 3000);
                    }
                }
            });
        });
    }
    //升级应用
    function do_upgrade(records, is_beta) {
        var upgrade_records = new Array();
        for (var index in records) {
            var app_status = (is_beta == 1 ? 3 : 4);
            if (records[index].appStatus == app_status)
                upgrade_records.push(records[index]);
        }

        var upgrade_action_name = (is_beta == 1 ? "公测升级" : "正式升级");
        var upgrade_action = (is_beta == 1 ? "betaupgrade" : "upgrade");
        if (upgrade_records.length <= 0) {
            ST.Util.showMsg("没有可执行" + upgrade_action_name + "的应用", 5, 3000);
            return;
        }

        ST.Util.confirmBox('应用' + upgrade_action_name, '确定对' + upgrade_records.length + "个应用执行" + upgrade_action_name + '?', function () {
            ST.Util.showMsg("正在准备执行应用" + upgrade_action_name + "...", 6, 1000000);
            $.ajax({
                type: 'post',
                url: SITEURL + "upgrade/ajax_ready_upgrade",
                data: {upgrade_task_list: to_upgrade_task(upgrade_records)},
                dataType: 'json',
                success: function (rs) {
                    ST.Util.hideMsgBox();
                    if (rs.status === 1) {
                        var url = SITEURL + "upgrade/" + upgrade_action;
                        ST.Util.showBox("应用" + upgrade_action_name, url, 450, 300, function () {
                            searchmyapp()
                        });
                    } else {
                        ST.Util.showMsg(rs.msg, 5, 3000);
                    }
                }
            });
        });
    }
    //设置应用升级状态
    function is_upgrade_set(record, status) {
        ST.Util.confirmBox('应用升级状态', '确定' + (status == 0 ? '禁止' : '允许') + record.name + '升级?', function () {
            ST.Util.showMsg('正在设置应用升级状态...', 6, 1000000);
            $.ajax({
                type: 'post',
                url: SITEURL + "mall/is_upgrade_set",
                data: {appid: record.id, status: status},
                dataType: 'json',
                success: function (rs) {
                    ST.Util.hideMsgBox();
                    var showTime = 1000;
                    if (rs.status === 1) {
                        ST.Util.showMsg(rs.msg, 4, showTime);
                        setTimeout(function () {
                            searchmyapp();
                        }, showTime);
                    } else {
                        ST.Util.showMsg(rs.msg, 5, 3000);
                    }
                }
            });
        });
    }

</script>
</html><script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.2303&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
