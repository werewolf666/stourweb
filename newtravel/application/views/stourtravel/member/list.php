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
<body style="overflow:hidden" top_left=jwFwOs >

    <table class="content-tab">
        <tr>
            <td width="119px" class="content-lt-td" valign="top">
                {template 'stourtravel/public/leftnav'}
                <!--右侧内容区-->
            </td>
            <td valign="top" class="content-rt-td" style="overflow:hidden">
                <div class="cfg-header-bar">
                    <div class="select-box w150 mt-5 ml-10 fl">
                        <select name="webid" onchange="toverifystatus(this)" class="select">
                            <option value="0">实名认证</option>
                            <option value="1">未认证</option>
                            <option value="2">已认证</option>
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
                    <div class="select-box w150 mt-5 ml-10 fl">
                        <select name="webid" onchange="virtual(this)" id="virtual" class="select">
                            <option value="0">全部会员</option>
                            <option value="1">普通会员</option>
                            <option value="2">虚拟会员</option>
                        </select>
                    </div>
                    <div class="cfg-header-search">
                        <input type="text" id="searchkey" value="会员昵称/手机号/邮箱" datadef="会员昵称/手机号/邮箱" class="search-text" />
                        <a href="javascript:;" class="search-btn" onclick="searchKeyword()">搜索</a>
                    </div>
                    <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                    <a href="javascript:;" id="addbtn" class="fr btn btn-primary radius mt-6 mr-10" >添加</a>
                    </div>
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
function virtual(ele) {
    var virtual=$(ele).val();
    window.product_store.getProxy().setExtraParam('virtual',virtual);
    window.product_store.loadPage(1);
}
Ext.onReady(
    function () {
        Ext.tip.QuickTipManager.init();
        var editico = "{php echo Common::getIco('edit');}";
        var delico = "{php echo Common::getIco('del');}";

        $("#searchkey").focusEffect();

        //添加按钮
        $("#addbtn").click(function(){
//            var url=SITEURL+"member/add/parentkey/member/itemid/1/menuid/165/";
//            parent.window.addTab('添加会员',url,1);
            if($('#virtual option:selected').val() == 2)
            {
                var url=SITEURL+"member/add/parentkey/member/itemid/1/virtual/1";
                ST.Util.showBox('添加虚拟会员',url,450,300,function(){window.product_store.load()});
            }
            else
            {
                var url=SITEURL+"member/add/parentkey/member/itemid/1/virtual/0";
                ST.Util.showBox('添加会员',url,320,300,function(){window.product_store.load()});
            }

        });

        //产品store
        window.product_store = Ext.create('Ext.data.Store', {

            fields: [
                'id',
                'mid',
                'nickname',
                'truename',
                'mobile',
                'email',
                'jifen',
                'logintime',
                'verifystatus',
				'virtual'
            ],

            proxy: {
                type: 'ajax',
                api: {
                    read: SITEURL+'member/index/action/read',  //读取数据的URL
                    update: SITEURL+'member/index/action/save',
                    destroy: SITEURL+'member/index/action/delete'
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
                      //  bar.down('tbfill').hide();

                        bar.insert(0, Ext.create('Ext.panel.Panel', {border: 0, html: '<div class="panel_bar"><a class="btn btn-primary radius" href="javascript:void(0);" onclick="CHOOSE.chooseAll()">全选</a><a class="btn btn-primary radius ml-10" href="javascript:void(0);" onclick="CHOOSE.chooseDiff()">反选</a><a class="btn btn-primary radius ml-10" href="javascript:void(0);" onclick="CHOOSE.delMore()">删除</a></div>'}));

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
					  menuDisabled:true,
                    renderer: function (value, metadata, record) {

                        return  "<input type='checkbox' class='product_check' style='cursor:pointer' value='" + value + "'/>";

                    }

                },
                {
                    text: '<span class="grid_column_text">会员昵称</span>',
                    width: '15%',
                    dataIndex: 'nickname',
                    align: 'left',
                    border: 0,
                    sortable: false,
					  menuDisabled:true,
                    renderer: function (value, metadata, record) {
                        var id=record.get('id');
                        return  "<input type='text' value='"+value+"' style='text-align:left' class='row-edit-txt' onblur=\"updateField(this,'"+id+"','nickname',0,'input')\"/>";
                    }

                },

                {

                    text: '<span class="grid_column_text">真实姓名</span>',
                    width: '12%',
                    dataIndex: 'truename',
                    align: 'left',
                    border: 0,
                    sortable: false,
					  menuDisabled:true,
                    renderer: function (value, metadata, record) {
                        var id=record.get('id');
                        return  "<input type='text' style='text-align:left;width:65%' value='"+value+"' class='row-edit-txt' onblur=\"updateField(this,'"+id+"','truename',0,'input')\"/>";
                    }

                },
                {

                    text: '<span class="grid_column_text">手机号码</span>',
                    width: '10%',
                    dataIndex: 'mobile',
                    align: 'left',
                    border: 0,
					  menuDisabled:true,
                    sortable: false,
                    renderer: function (value, metadata, record) {

                        return value;
                    }

                },
                {

                    text: '<span class="grid_column_text">Email</span>',
                    width: '10%',
                    dataIndex: 'email',
                    align: 'left',
                    border: 0,
                    sortable: false,
					  menuDisabled:true,
                    renderer: function (value, metadata, record) {
                       return value;
                    }

                },
                {
                    text: '最后登陆',
                    width: '8%',
                    dataIndex: 'logintime',
                    border: 0,
                    align:'center',
                    sortable: false,
					  menuDisabled:true,
                    renderer: function (value, metadata, record) {
                       return value;

                    }

                },
                {
                    text: '订单查询',
                    width: '8%',
                    align: 'center',
                    border: 0,
                    sortable: false,
					menuDisabled:true,
                    renderer: function (value, metadata, record) {
                        var id = record.get('id');
                        var nickname = record.get('nickname');
                        var html = "<a href='javascript:void(0);' onclick=\"viewOrder(" + id + ",'"+nickname+"')\">查看订单</a>";

                        return html;

                    }

                },
                {
                    text: '实名认证',
                    width: '8%',
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
                                return '已认证';
                            break;
                            case 3:
                                return '审核失败';
                            break;
                            default :
                                return '未认证';
                        }
                    }
                },
                {
                    text: '积分',
                    width: '8%',
                    dataIndex: 'jifen',
                    align: 'center',
                    border: 0,
                    cls:'sort-col',
                    menuDisabled:true,
                    renderer: function (value, metadata, record) {
                        return value;
                    }
                },
                {
                    text: '会员类别',
                    width: '8%',
                    dataIndex: 'virtual',
                    align: 'center',
                    border: 0,
                    cls:'sort-col',
                    menuDisabled:true,
                    renderer: function (value, metadata, record) {
                        if(value == 2)
                        {
                            return "虚拟会员";
                        }
                        else
                        {
                            return "普通会员";
                        }
                    }
                },
                {
                    text: '管理',
                    width: '9%',
                    align: 'center',
                    border: 0,
                    sortable: false,
					  menuDisabled:true,
                    cls: 'mod-1',
                    renderer: function (value, metadata, record) {

                        var id = record.get('id');
                        var html = "<a href='javascript:void(0);' title='修改' class='btn-link' onclick=\"modify(" + id + ")\">修改</a>"+
                            "&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' title='删除' class='btn-link' onclick=\"delS(" + id + ")\">删除</a>"
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
  //  if (data_height > height - 34)
       // window.product_grid.height = (height - 34);
 //   else
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
//选择全部
function chooseAll() {
    var check_cmp = Ext.query('.product_check');
    for (var i in check_cmp) {
        if (!Ext.get(check_cmp[i]).getAttribute('checked'))
            check_cmp[i].checked = 'checked';
    }

    //  window.sel_model.selectAll();
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
    ST.Util.confirmBox("提示","确定删除？",function(){
        check_cmp.each(
            function (el, c, index) {
                window.product_store.getById(el.getValue()).destroy();
            }
        );
    })
}


//更新某个字段
function updateField(ele, id, field, value, type) {
    var record = window.product_store.getById(id.toString());
    if (type == 'select' || type =='input') {
        value = Ext.get(ele).getValue();
    }
    var view_el = window.product_grid.getView().getEl();


    Ext.Ajax.request({
        url: SITEURL+"member/index/action/update",
        method: "POST",
        datatype: "JSON",
        params: {id: id, field: field, val: value, kindid: 0},
        success: function (response, opts) {
            if (response.responseText == 'ok') {


                record.set(field, value);
                record.commit();
                // view_el.scrollBy(0,scroll_top,false);
            }
            else
            {
                ST.Utils.showMsg("{__('norightmsg')}",5,1000);
            }
        }});

}


//删除套餐
function delS(id) {
    ST.Util.confirmBox("提示","确定删除？",function(){
            window.product_store.getById(id.toString()).destroy();
    })
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
function modify(id)
{
    var url=SITEURL+"member/edit/parentkey/member/menuid/165/itemid/1/mid/"+id;
    var record=window.product_store.getById(id.toString());
    parent.window.addTab('修改会员信息',url,1);
}
//订单查看
function viewOrder(id,nickname)
{
    var url = SITEURL+"member/vieworder/mid/"+id;
    ST.Util.showBox(nickname+'会员订单查看',url,1024,768,function(){});

}

</script>

</body>
</html>
