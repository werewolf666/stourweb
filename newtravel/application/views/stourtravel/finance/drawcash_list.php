<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>提现审核管理-思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,plist.css'); }
    {php echo Common::getScript("choose.js"); }
</head>
<style>
    /*搜索*/

</style>
<body style="overflow:hidden">
<table class="content-tab" margin_border=z4e-Fj >
    <tr>
        <td width="119px" class="content-lt-td" valign="top">
            {template 'stourtravel/public/leftnav'}
            <!--右侧内容区-->
        </td>
        <td valign="top" class="content-rt-td" style="overflow:hidden">

            <div class="list-top-set">
                <div class="list-web-pad"></div>
                <div class="list-web-ct">
                    <table class="list-head-tb">
                        <tr>
                            <td class="head-td-lt">
                                <div class="pro-search">

                                </div>
                            </td>
                            <td class="head-td-rt">
                                <a href="javascript:;" class="refresh-btn" onclick="window.location.reload()">刷新</a>

                        </tr>
                    </table>
                </div>
            </div>


            <div id="product_grid_panel" class="content-nrt">

            </div>
        </td>
    </tr>
</table>
<script>

Ext.onReady(
    function () {
        Ext.tip.QuickTipManager.init();
        var editico = "{php echo Common::getIco('edit');}";

        //产品store
        window.product_store = Ext.create('Ext.data.Store', {

            fields: [
                'applyusertype',
                'applyusertype_name',
                'applyid',
                'applyuserid',
                'applyusername',
                'withdrawamount',
                'bankname',
                'bankcardnumber',
                'bankaccountname',
                'description',
                'addtime',
                'addtime_name',
                'finishtime',
                'finishtime_name',
                'status',
                'status_name',
                'audit_description'
            ],

            proxy: {
                type: 'ajax',
                api: {
                    read: SITEURL + 'finance/ajax_get_drawcash_list'
                },
                reader: {
                    type: 'json',   //获取数据的格式
                    root: 'lists',
                    totalProperty: 'total'
                }
            },

            remoteSort: true,
            pageSize: 20,
            autoLoad: true,
            listeners: {
                load: function (store, records, successful, eOpts) {
                    if (!successful) {
                        ST.Util.showMsg("{__('norightmsg')}", 5, 1000);
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
            padding: '2px',
            renderTo: 'product_grid_panel',
            border: 0,
            width: "100%",
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
                        //  bar.down('tbfill').hide();
                        bar.insert(0, Ext.create('Ext.toolbar.Fill'));
                        //items.add(Ext.create('Ext.toolbar.Fill'));
                    }
                }
            }),
            columns: [
                {
                    text: '用户名',
                    width: '10%',
                    dataIndex: 'applyusername',
                    align: 'left',
                    border: 0,
                    sortable: false,
                    menuDisabled: true,
                    renderer: function (value, metadata, record) {
                        return  value;
                    }

                },

                {
                    text: '用户角色',
                    width: '5%',
                    dataIndex: 'applyusertype',
                    align: 'left',
                    border: 0,
                    cls: 'sort-col',
                    sortable: true,
                    menuDisabled: true,
                    renderer: function (value, metadata, record) {
                        return record.get("applyusertype_name");
                    }

                },
                {
                    text: '申请提现金额',
                    width: '10%',
                    dataIndex: 'withdrawamount',
                    align: 'left',
                    border: 0,
                    menuDisabled: true,
                    cls: 'sort-col',
                    sortable: true,
                    renderer: function (value, metadata, record) {
                        return "&yen;" + value;
                    }

                },
                {
                    text: '提现银行卡号/支付宝账号',
                    width: '20%',
                    dataIndex: 'bankcardnumber',
                    align: 'left',
                    border: 0,
                    sortable: false,
                    menuDisabled: true,
                    renderer: function (value, metadata, record) {
                        return value;
                    }

                },
                {
                    text: '户名/支付宝姓名',
                    width: '10%',
                    dataIndex: 'bankaccountname',
                    border: 0,
                    align: 'center',
                    sortable: false,
                    menuDisabled: true,
                    renderer: function (value, metadata, record) {
                        return value;

                    }

                },
                {
                    text: '开卡银行/支付宝',
                    width: '15%',
                    align: 'left',
                    dataIndex: 'bankname',
                    border: 0,
                    sortable: false,
                    menuDisabled: true,
                    renderer: function (value, metadata, record) {
                        return value;

                    }

                },
                {
                    text: '提交时间',
                    width: '10%',
                    dataIndex: 'addtime',
                    align: 'center',
                    border: 0,
                    cls: 'sort-col',
                    sortable: true,
                    menuDisabled: true,
                    renderer: function (value, metadata, record) {
                        return record.get("addtime_name");
                    }
                },
                {
                    text: '审核时间',
                    width: '10%',
                    dataIndex: 'finishtime',
                    align: 'center',
                    border: 0,
                    cls: 'sort-col',
                    sortable: true,
                    menuDisabled: true,
                    renderer: function (value, metadata, record) {
                        return record.get("finishtime_name");
                    }
                },
                {
                    text: '状态',
                    width: '5%',
                    dataIndex: 'status',
                    align: 'center',
                    border: 0,
                    cls: 'sort-col',
                    sortable: true,
                    menuDisabled: true,
                    renderer: function (value, metadata, record) {
                        return record.get("status_name");
                    }
                },
                {
                    text: '管理',
                    width: '5%',
                    align: 'center',
                    border: 0,
                    sortable: false,
                    menuDisabled: true,
                    cls: 'mod-1',
                    renderer: function (value, metadata, record) {

                        var applyid = record.get('applyid');
                        var usertype = record.get("applyusertype");
                        var html = "<a href='javascript:void(0);' title='查看' class='row-mod-btn' onclick='javascript:modify(\"" + applyid + "\",\"" + usertype + "\");'></a>";
                        return html;
                        // return getExpandableImage(value, metadata,record);
                    }
                }


            ],
            listeners: {
                boxready: function () {


                    var height = Ext.dom.Element.getViewportHeight();
                    this.maxHeight = height;
                    this.doLayout();
                },
                afterlayout: function (grid) {


                    var data_height = 0;
                    try {
                        data_height = grid.getView().getEl().down('.x-grid-table').getHeight();
                    } catch (e) {
                    }
                    var height = Ext.dom.Element.getViewportHeight();
                    // console.log(data_height+'---'+height);
                    if (data_height > height - 106) {
                        window.has_biged = true;
                        grid.height = height - 106;
                    }
                    else if (data_height < height - 106) {
                        if (window.has_biged) {
                            // delete window.grid.height;
                            window.has_biged = false;
                            grid.doLayout();
                        }
                    }
                }
            },
            plugins: [

            ],
            viewConfig: {

            }
        });


    })

//实现动态窗口大小
Ext.EventManager.onWindowResize(function () {
    var height = Ext.dom.Element.getViewportHeight();
    var data_height = window.product_grid.getView().getEl().down('.x-grid-table').getHeight();
    if (data_height > height - 106)
        window.product_grid.height = (height - 106);
    else
    // delete window.product_grid.height;
        window.product_grid.doLayout();


})


//切换每页显示数量
function changeNum(combo, records) {

    var pagesize = records[0].get('num');
    window.product_store.pageSize = pagesize;
    window.product_store.loadPage(1);
}

//修改
function modify(applyid, applyusertype) {
    var url = SITEURL + "finance/drawcash_detail/parentkey/{$parentkey}/itemid/{$itemid}/applyid/" + applyid + "/applyusertype/" + applyusertype;
    ST.Util.showBox('提现审核', url, 550, 550, function () {
        window.product_store.load()
    });
}

</script>

</body>
</html>
