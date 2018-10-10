<?php defined('SYSPATH') or die();?>
<!doctype html>
<html>
<head float_border=z8I7Lj >
<meta charset="utf-8">
<title>免费通话-思途CMS{$coreVersion}</title>
 {template 'stourtravel/public/public_js'}
 {php echo Common::getCss('style.css,base.css,base2.css,plist.css,base_new.css'); }
 {php echo Common::getScript("uploadify/jquery.uploadify.min.js,jquery.buttonbox.js,choose.js"); }

</head>
<body style="overflow:hidden">

    <table class="content-tab">
        <tr>
            <td width="119px" class="content-lt-td"  valign="top">
                {template 'stourtravel/public/leftnav'}
                <!--右侧内容区-->
            </td>
            <td valign="top" class="content-rt-td" style="overflow:hidden">
                <div class="cfg-header-bar">   
                	<span class="select-box w150 mt-5 ml-10 fl">
                        <select class="select" onchange="search(this,'status')">
                            <option value="">处理状态</option>
                            <option value="1">已处理</option>
                            <option value="0">未处理</option>
                        </select>
                    </span>
                    <div class="cfg-header-search">
                    	<input type="text" id="searchkey" value="" placeholder="电话号码"  class="search-text" >
                    	<a href="javascript:;" class="search-btn" onclick="CHOOSE.searchKeyword()">搜索</a>	
                    </div>	
                    <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>               
                </div>
                
                <div id="product_grid_panel" class="content-nrt">

                </div>
            </td>
        </tr>
    </table>

<script>
    //免费客服
    Ext.onReady(
        function () {
            Ext.tip.QuickTipManager.init();
            //产品store
            window.product_store = Ext.create('Ext.data.Store', {

                fields: [
                    'id',
                    'phone',
                    'description',
                    'status',
                    'addtime',
                    'finishtime'
                ],
                proxy: {
                    type: 'ajax',
                    api: {
                        read: SITEURL+'kefu/freekefu/action/read',  //读取数据的URL
                        update: SITEURL+'kefu/freekefu/action/save',
                        destroy: SITEURL+'kefu/freekefu/action/delete'
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
                        if(!successful){
                            ST.Util.showMsg("{__('norightmsg')}",5,1000);
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
                scroll:'vertical', //只要垂直滚动条
                bbar: Ext.create('Ext.toolbar.Toolbar', {
                    store: product_store,  //这个和grid用的store一样
                    displayInfo: true,
                    emptyMsg: "",
                    items: [
                        {
                            xtype:'panel',
                            id:'listPagePanel',
                            cls: 'turn-page-bar',
                            html:'<div id="line_page"></div>'
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
                            // bar.down('tbfill').hide();

                            bar.insert(0, Ext.create('Ext.panel.Panel', {border: 0, cls: 'turn-page-bar', html: '<div class="panel_bar"><a class="btn btn-primary radius" href="javascript:void(0);" onclick="chooseAll()">全选</a><a class="btn btn-primary radius ml-10" href="javascript:void(0);" onclick="chooseDiff()">反选</a><a class="btn btn-primary radius ml-10" href="javascript:void(0);" onclick="del()">删除</a></div>'}));

                            bar.insert(1, Ext.create('Ext.toolbar.Fill'));
                            //items.add(Ext.create('Ext.toolbar.Fill'));
                        }
                    }
                }),
                columns: [
                    {
                        text: '选择',
                        width: '7%',
                        // xtype:'templatecolumn',
                        tdCls: 'product-ch',
                        align: 'center',
                        dataIndex: 'id',
                        border: 0,
                        menuDisabled:true,
                        renderer: function (value, metadata, record) {
                            return  "<input type='checkbox' class='product_check' style='cursor:pointer' value='" + value + "'/>";
                        }

                    },
                    {

                        text: '客户电话',
                        width: '16%',
                        dataIndex: 'phone',
                        align: 'left',
                        border: 0,
                        sortable: false,
                        menuDisabled:true,
                        renderer: function (value, metadata, record) {
                            return value;
                        }

                    },

                    {
                        text: '提交时间',
                        width: '14%',
                        cls:'sort-col',
                        dataIndex: 'addtime',
                        align: 'left',
                        border: 0,
                        sortable: true,
                        menuDisabled:true,
                        renderer: function (value, metadata, record) {
                            return value;
                        }

                    },
                    {
                        text: '状态',
                        width: '10%',
                        cls:'sort-col',
                        dataIndex: 'status',
                        align: 'left',
                        border: 0,
                        sortable: true,
                        menuDisabled:true,
                        renderer: function (value, metadata, record) {
                            var name=value==0?'<font color="red">未处理</font>':'已处理';
                            return name;
                        }

                    },
                    {
                        text: '回复说明',
                        width: '31%',
                        dataIndex: 'description',
                        align: 'left',
                        border: 0,
                        sortable: false,
                        menuDisabled:true,
                        renderer: function (value, metadata, record) {
                            return value;
                        }

                    },
                    {
                        text: '处理时间',
                        width: '14%',
                        dataIndex: 'finishtime',
                        align: 'left',
                        border: 0,
                        sortable: true,
                        cls:'sort-col',
                        menuDisabled:true,
                        renderer: function (value, metadata, record) {
                            return value;
                        }

                    },
                    {
                        text: '管理',
                        width: '8%',
                        align: 'center',
                        border: 0,
                        sortable: false,
                        cls: 'mod-1',
                        menuDisabled:true,
                        renderer: function (value, metadata, record) {

                            var id = record.get('id');
                            var html = "<a href='javascript:void(0);' class='btn-link' onclick=\"view(" + id + ")\">编辑</a>";
                            return html;
                            // return getExpandableImage(value, metadata,record);
                        }


                    }

                ],
                listeners: {
                    boxready: function () {


                        var height=Ext.dom.Element.getViewportHeight();
                        this.maxHeight=height-40;
                        this.doLayout();
                    },
                    afterlayout: function (grid) {
                        var data_height = 0;
                        try {
                            data_height = grid.getView().getEl().down('.x-grid-table').getHeight();
                        } catch (e) {
                        }
                        console.log(data_height);

                        var height = Ext.dom.Element.getViewportHeight();
                        console.log('view-height:'+height)
                        // console.log(data_height+'---'+height);
                        if (data_height > height - 40) {
                            window.has_biged = true;
                            grid.height = height - 40;
                        }
                        else if (data_height < height - 40) {
                            if (window.has_biged) {
                                // delete window.grid.height;
                                window.has_biged = false;
                                grid.doLayout();
                            }
                        }
                    }
                },
                plugins: [
                    Ext.create('Ext.grid.plugin.CellEditing', {
                        clicksToEdit: 2,
                        listeners: {
                            edit: function (editor, e) {
                                var id = e.record.get('id');
                                updateField(0, id, e.field, e.value, 0);
                                return false;
                            }
                        }
                    })
                ],
                viewConfig: {

                }
            });


        })


    //实现动态窗口大小
    Ext.EventManager.onWindowResize(function(){
        var height=Ext.dom.Element.getViewportHeight();
        var data_height=window.product_grid.getView().getEl().down('.x-grid-table').getHeight();
        if(data_height>height-40)
            window.product_grid.height=(height-40);
        else
            delete window.product_grid.height;
        window.product_grid.doLayout();
    })



    //切换每页显示数量
    function changeNum(combo, records) {

        var pagesize=records[0].get('num');
        window.product_store.pageSize=pagesize;
        window.product_store.loadPage(1);
    }

    function view(id)
    {
        var url = SITEURL+'kefu/freekefu_edit/id/'+id;
        ST.Util.showBox('回复处理',url,450,183,null,null,document,{loadCallback:function(result){
            window.product_store.reload();
        },loadWindow:window});
    }

    //选择全部
    function chooseAll() {
        var check_cmp = Ext.query('.product_check');
        for (var i in check_cmp) {
            if (!Ext.get(check_cmp[i]).getAttribute('checked'))
                check_cmp[i].checked = 'checked';
        }
    }
    //反选
    function chooseDiff() {
        var check_cmp = Ext.query('.product_check');
        for (var i in check_cmp)
            check_cmp[i].click();

    }
    function del() {
        //window.product_grid.down('gridcolumn').hide();
        var check_cmp = Ext.select('.product_check:checked');
        if (check_cmp.getCount() == 0) {
            return;
        }
        ST.Util.confirmBox("提示","确定删除所选的通话记录？",function(){
            check_cmp.each(
                function (el, c, index) {
                    window.product_store.getById(el.getValue()).destroy();
                }
            );
        })
    }
    function search(ele,field)
    {
        window.product_store.getProxy().setExtraParam(field,$(ele).val());
        window.product_store.loadPage(1);
    }

</script>

</body>
</html>
