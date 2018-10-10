<?php defined('SYSPATH') or die();?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {Common::getCss('style.css,base.css,base2.css,base_new.css')}
    {Common::getScript("product_add.js,choose.js,st_validate.js")}
    <style type="text/css">
        .spec_hide{
            height: 0px !important; overflow:hidden;
        }
    </style>
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
            <div class="cfg-header-bar">
                <div class="cfg-header-tab">
                    <span class="item on" id="column_databackup" onclick="Product.switchTabs(this,'databackup')">数据备份</span>
                    <span class="item" id="column_databackuplist" onclick="Product.switchTabs(this,'databackuplist',switchBack)">备份记录</span>
                </div>
                <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
            </div>
            <div class="clear clearfix">
                <div id="content_databackup" class="product-add-div content-show">
                    <ul class="info-item-block">
                        <li>
                            <span class="item-hd">备份数据范围：</span>
                            <div class="item-bd">
                                <label class="radio-label fl mr-10"><input type="radio" name="databackuptype" checked="checked" value="0">全站数据备份</label>
                                <a href="javascript:;" class="fl btn btn-primary radius" onclick="backupData(this);" title="开始备份">开始备份</a>

                            </div>
                        </li>
                    </ul>
                </div>
                <div id="content_databackuplist" class="product-add-div spec_hide">
                    <div id="product_grid_panel" class="content-nrt">

                    </div>
                </div>
            </div>
        </td>
    </tr>
</table>

<script>
    //切换时的回调函数
    function switchBack(columnname) {
        if (columnname == "databackuplist") {
            $("#content_databackuplist").removeClass("spec_hide");
            window.product_store.loadPage(1);
        }
    }

    Ext.onReady(
        function () {
            Ext.tip.QuickTipManager.init();

            //产品store
            window.product_store = Ext.create('Ext.data.Store', {

                fields: [
                    'id','name','time','size'
                ],

                proxy: {
                    type: 'ajax',
                    api: {
                        read: SITEURL + 'databackup/ajax_databackup_read'
                    },
                    reader: {
                        type: 'json',   //获取数据的格式
                        root: 'list',
                        totalProperty: 'total'
                    }
                },
                remoteSort: false,
                autoLoad: false,
                pageSize: 20,
                listeners: {
                    load: function (store, records, successful, eOpts) {
                        if (!successful) {
                            ST.Util.showMsg("查找信息失败", 5, 3000);
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

                            bar.insert(0, Ext.create('Ext.panel.Panel', {border: 0, html: '<div class="panel_bar">' +
                                '<a class="btn btn-primary radius mr-10" href="javascript:void(0);" onclick="CHOOSE.chooseAll()">全选</a>' +
                                '<a class="btn btn-primary radius mr-10" href="javascript:void(0);" onclick="CHOOSE.chooseDiff()">反选</a>' +
                                '<a class="btn btn-primary radius mr-10" href="javascript:void(0);" onclick="deleteDataBackup(get_selected_record())">删除</a>' +
                                '</div>'}));

                            bar.insert(1, Ext.create('Ext.toolbar.Fill'));
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
                        border: 0,
                        sortable: false,
                        menuDisabled: true,
                        renderer: function (value, metadata, record) {
                            return  "<input type='checkbox' class='product_check' id='box_" + metadata.recordIndex + "' style='cursor:pointer' value='" + metadata.recordIndex + "' data-record-index='" + metadata.recordIndex + "'/>";
                        }
                    },
                    {
                        text: '数据包名',
                        width: '40%',
                        align: 'center',
                        border: 0,
                        dataIndex: 'name',
                        sortable: false,
                        menuDisabled: true,
                        renderer: function (value, metadata, record, rowIndex, colIndex) {

                            return value;

                        }


                    },
                    {
                        text: '备份时间',
                        width: '20%',
                        align: 'center',
                        border: 0,
                        dataIndex: 'time',
                        sortable: false,
                        menuDisabled: true,
                        renderer: function (value, metadata, record, rowIndex, colIndex) {

                            return value;

                        }


                    },
                    {
                        text: '数据大小',
                        width: '20%',
                        align: 'center',
                        border: 0,
                        dataIndex: 'size',
                        sortable: false,
                        menuDisabled: true,
                        renderer: function (value, metadata, record, rowIndex, colIndex) {
                            return value;
                        }


                    },
                    {
                        text: '管理',
                        width: '20%',
                        align: 'center',
                        border: 0,
                        dataIndex: 'id',
                        sortable: false,
                        menuDisabled: true,
                        renderer: function (value, metadata, record, rowIndex, colIndex) {
                            var downloadlink = "<a href=\"/data/backup/" + record.get("id") + ".zip\" class=\"btn-link\" target=\"_blank\">下载</a>";
                            var deletelink = "<a href=\"javascript:;\" class=\"btn-link\" onclick=\"javascript:deleteDataBackup(new Array(get_record(" + metadata.recordIndex + ")));\">删除</a>";

                            return  downloadlink + "&nbsp;&nbsp;" + deletelink;
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

    //删除
    function deleteDataBackup(records) {

        if (records.length <= 0) {
            ST.Util.showMsg("没有可删除的数据备份", 5, 3000);
            return;
        }

        ST.Util.confirmBox('删除数据备份', '确定删除' + records.length + "个数据备份?", function () {
            ST.Util.showMsg("正在删除数据备份...", 6, 1000000);
            $.ajax({
                type: 'post',
                url: SITEURL + "databackup/ajax_delete_databackup",
                data: {databackup_list: records},
                dataType: 'json',
                success: function (rs) {
                    ST.Util.hideMsgBox();
                    if (rs.status === 1) {
                        window.product_store.loadPage(1);
                    } else {
                        ST.Util.showMsg(rs.msg, 5, 3000);
                    }
                }
            });
        });
    }

    //备份数据
    function backupData(sender) {
        if ($(sender).hasClass("disabled")) {
            return;
        }

        ST.Util.confirmBox('数据备份', '确定进行全站数据备份?', function () {
            ST.Util.showMsg("正在执行数据备份...", 6, 1000000);
            $(sender).removeClass("btn-primary").addClass("disabled");
            $.ajax({
                type: 'post',
                url: SITEURL + "databackup/ajax_do_databackup",
                data: {},
                dataType: 'json',
                success: function (rs) {
                    ST.Util.hideMsgBox();
                    $(sender).removeClass("disabled").addClass("btn-primary");
                    if (rs.status === 1) {
                        ST.Util.showMsg("数据备份完成", 4, 3000);
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
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.2104&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
