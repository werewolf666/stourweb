<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>问答管理-思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,plist.css,base_new.css'); }
    {php echo Common::getScript("choose.js"); }
</head>
<style>
    /*搜索*/

</style>
<body style="overflow:hidden">
<table class="content-tab" body_color=zOo3gm >
    <tr>
        <td width="119px" class="content-lt-td" valign="top">
            {template 'stourtravel/public/leftnav'}
            <!--右侧内容区-->
        </td>
        <td valign="top" class="content-rt-td" style="overflow:hidden">

            <div class="cfg-header-bar">
                <div class="select-box w150 mt-5 ml-10 fl">
                    <select name="webid" onchange="toverifystatus(this)"  class="select">
                        <option value="all">实名认证</option>
                        <option value="0">未实名</option>
                        <option value="1">审核中</option>
                        <option value="2">已实名</option>
                        <option value="3">审核失败</option>
                    </select>
                </div>
                <div class="select-box w150 mt-5 ml-10 fl">
                    <select name="webid" onchange="tosex(this)"  class="select">
                        <option value="0">会员性别</option>
                        <option value="男">男</option>
                        <option value="女">女</option>
                        <option value="保密">保密</option>
                    </select>
                </div>
                <div class="cfg-header-search">
                    <input type="text" id="searchkey" value="会员昵称/手机号/邮箱" datadef="会员昵称/手机号/邮箱" class="search-text" />
                    <a href="javascript:;" class="search-btn" onclick="searchKeyword()">搜索</a>
                </div>
                <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
            </div>


            <div id="product_grid_panel" class="content-nrt">

            </div>
        </td>
    </tr>
</table>
<script>

window.display_mode = 1;	//默认显示模式
window.product_kindid = 0;  //默认目的地ID


function toverifystatus(ele)
{
    var verifystatus=$(ele).val();
    window.product_store.getProxy().setExtraParam('verifystatus',verifystatus);
    window.product_store.loadPage(1);

}
function tosex(ele)
{
    var sex=$(ele).val();
    window.product_store.getProxy().setExtraParam('sex',sex);
    window.product_store.loadPage(1);

}
Ext.onReady(
    function () {
        Ext.tip.QuickTipManager.init();
        var editico = "{php echo Common::getIco('edit');}";
        var delico = "{php echo Common::getIco('del');}";

        $("#searchkey").focusEffect();


        //产品store
        window.product_store = Ext.create('Ext.data.Store', {

            fields: [
                'mid',
                'nickname',
                'truename',
                'mobile',
                'native_place',
                'verifystatus',
                'cardid',
                'sex'
            ],
            proxy: {
                type: 'ajax',
                api: {
                    read: SITEURL+'member/verifystatus_list/action/read',  //读取数据的URL
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
            width: "100%",
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
                        bar.insert(0, Ext.create('Ext.panel.Panel', {border: 0, html: '<div class="panel_bar"></div>'}));
                        bar.insert(1, Ext.create('Ext.toolbar.Fill'));

                    }
                }
            }),
            columns: [
                {
                    text: '会员昵称',
                    width: '15%',
                    dataIndex: 'nickname',
                    align: 'center',
                    border: 0,
                    sortable: false,
					  menuDisabled:true,
                    renderer: function (value, metadata, record) {
                        return value;
                    }
                },
                {
                    text: '真实姓名',
                    width: '15%',
                    dataIndex: 'truename',
                    align: 'center',
                    border: 0,
                    sortable: false,
					  menuDisabled:true,
                    renderer: function (value, metadata, record) {
                        return value;
                    }

                },
                {
                    text: '手机号码',
                    width: '10%',
                    dataIndex: 'mobile',
                    align: 'center',
                    border: 0,
					  menuDisabled:true,
                    sortable: false,
                    renderer: function (value, metadata, record) {

                        return value;
                    }

                },
                {
                    text: '身份证号',
                    width: '20%',
                    dataIndex: 'cardid',
                    align: 'center',
                    border: 0,
                    sortable: false,
					  menuDisabled:true,
                    renderer: function (value, metadata, record) {
                       return value;
                    }

                },
                {
                    text: '性别',
                    width: '10%',
                    dataIndex: 'sex',
                    border: 0,
                    align:'center',
                    sortable: false,
					  menuDisabled:true,
                    renderer: function (value, metadata, record) {
                       return value;

                    }

                },
                {
                    text: '认证状态',
                    width: '10%',
                    dataIndex: 'verifystatus',
                    border: 0,
                    align:'center',
                    sortable: false,
                    menuDisabled:true,
                    renderer: function (value, metadata, record) {
                        switch (parseInt(value))
                        {
                            case 1:
                                return '审核中';
                            break;
                            case 2:
                                return '已实名';
                            break;
                            case 3:
                                return '审核失败';
                            break;
                            default :
                                return '未实名';
                        }
                    }
                },
                {
                    text: '籍贯',
                    width: '10%',
                    dataIndex: 'native_place',
                    border: 0,
                    align:'center',
                    sortable: false,
                    menuDisabled:true,
                    renderer: function (value, metadata, record) {
                        return value;
                    }
                },
                {
                    text: '管理',
                    width: '10%',
                    align: 'center',
                    border: 0,
                    sortable: false,
					  menuDisabled:true,
                    cls: 'mod-1',
                    renderer: function (value, metadata, record) {

                        var id = record.get('mid');
                        var verifystatus = record.get('verifystatus');
                        if(verifystatus==1)
                        {
                            var html = "<a href='javascript:void(0);' title='审核'  style='color:#43AEE4' onclick=\"toCheck(" + id + ")\">审核</a>"

                        }
                        else
                        {
                            var html = "<a href='javascript:void(0);' title='详情'  style='color:#43AEE4' onclick=\"toShow(" + id + ")\">详情</a>"+
                                "&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' style='color:#43AEE4' title='修改' onclick=\"toModify(" + id + ")\">编辑</a>"

                        }
                         return html;
                        // return getExpandableImage(value, metadata,record);
                    }
                }




            ],
            listeners: {
                boxready: function () {


                    var height = Ext.dom.Element.getViewportHeight();
                    this.maxHeight = height ;
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
                    if (data_height > height - 38) {
                        window.has_biged = true;
                        grid.height = height - 38;
                    }
                    else if (data_height < height - 38) {
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
                            var id = e.record.get('mid');
                            //  var view_el=window.product_grid.getView().getEl();
                            //  view_el.scrollBy(0,this.scroll_top,false);
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
Ext.EventManager.onWindowResize(function () {
    var height = Ext.dom.Element.getViewportHeight();
    var data_height = window.product_grid.getView().getEl().down('.x-grid-table').getHeight();
    if (data_height > height - 38)
        window.product_grid.height = (height - 38);
    else
       // delete window.product_grid.height;
    window.product_grid.doLayout();


})







//按进行搜索
function searchKeyword() {
    var keyword = $.trim($("#searchkey").val());
    var datadef = $("#searchkey").attr('datadef');
    keyword = keyword==datadef ? '' : keyword;
    window.product_store.getProxy().setExtraParam('keyword',keyword);
    window.product_store.loadPage(1);

}


//切换每页显示数量
function changeNum(combo, records) {

    var pagesize = records[0].get('num');
    window.product_store.pageSize = pagesize;
    window.product_store.loadPage(1);
}





//主题设置回调函数
function themeSetBack(id, arr, bl) {
    if (bl) {
        ST.Util.showMsg('设置主题成功', 4);
        var themelist = '';
        for (var i in arr) {
            themelist += arr[i].id + ',';
        }
        themelist = themelist.slice(0, -1);
        refreshField(id, {themelist: themelist});
    }
    else {
        ST.Util.showMsg('保存失败', 5);
    }
}

//图标设置回调函数
function iconSetBack(id, arr, bl) {
    if (bl) {
        ST.Util.showMsg('设置图标成功', 4);
        var iconlist = '';
        for (var i in arr) {
            iconlist += arr[i].id + ',';
        }
        iconlist = iconlist.slice(0, -1);
        refreshField(id, {iconlist: iconlist});

    }
    else {
        ST.Util.showMsg('保存失败', 5);
    }
}


//刷新保存后的结果
function refreshField(id, arr) {
    id = id.toString();
    var id_arr = id.split('_');
    // var view_el=window.product_grid.getView().getEl()
    //var scroll_top=view_el.getScrollTop();
    Ext.Array.each(id_arr, function (num, index) {
        if (num) {
            var record = window.product_store.getById(num.toString());

            for (var key in arr) {
                record.set(key, arr[key]);
                record.commit();
                // view_el.scrollBy(0,scroll_top,false);
                // window.line_grid.getView().refresh();
            }
        }
    })
}


//修改
function toModify(id)
{
    var url=SITEURL+"member/verifystatus_list/action/modify/parentkey/member/menuid/{$meunid}/itemid/1/mid/"+id;
    var record=window.product_store.getById(id.toString());
    parent.window.addTab('修改认证信息',url,1);
}
//审核
function toCheck(id)
{

    var url=SITEURL+"member/verifystatus_list/action/check/parentkey/member/menuid/{$meunid}/itemid/1/mid/"+id;
   // ST.Util.showBox('审核实名认证',url,450,300,function(){window.product_store.load()});



    var record=window.product_store.getById(id.toString());
    parent.window.addTab('审核实名认证',url,1);
}
//查看
function toShow(id)
{
    var url=SITEURL+"member/verifystatus_list/action/show/parentkey/member/menuid/{$meunid}/itemid/1/mid/"+id;
    var record=window.product_store.getById(id.toString());
    parent.window.addTab('实名认证详情',url,1);
}


</script>

</body>
</html>
