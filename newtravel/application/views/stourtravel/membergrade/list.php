<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>广告管理-思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,plist.css,base_new.css'); }

</head>
<body style="overflow:hidden">

    <table class="content-tab">
        <tr>
            <td width="119px" class="content-lt-td" valign="top">
                {template 'stourtravel/public/leftnav'}
                <!--右侧内容区-->
            </td>
            <td valign="top" class="content-rt-td" style="overflow:hidden">

                <div class="cfg-header-bar">
                    <div class="cfg-header-search">
                        <input type="text" id="searchkey" value="等级名称" datadef="等级名称" class="search-text"/>
                        <a href="javascript:;" class="search-btn" id="btn_search" value="搜索" onclick="searchKey()">搜索</a>
                    </div>
                    <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                    <a href="javascript:;" id="addbtn" class="fr btn btn-primary radius mt-6 mr-10">添加</a>
                </div>

                <div id="product_grid_panel" class="content-nrt">

                </div>
            </td>
        </tr>
    </table>

<script>
    $('#version').change(function(){
        var val=$(this).val();
        window.location.href=SITEURL+val;
    });

window.display_mode = 1;	//默认显示模式
window.product_kindid = 0;  //默认目的地ID
var editico = "{php echo Common::getIco('edit');}";
var delico = "{php echo Common::getIco('del');}";
var previewico = "{php echo Common::getIco('preview');}";


Ext.onReady(
    function () {
        Ext.tip.QuickTipManager.init();
        $("#searchkey").focusEffect();
        //添加
        $("#addbtn").click(function () {
            var ismobile = "{$ismobile}";
            var url = SITEURL + "membergrade/add/menuid/{$_GET['menuid']}/parentkey/{$parentkey}/itemid/{$itemid}/ismobile/"+ismobile;
            ST.Util.addTab('添加会员等级', url);

        })


        //产品store
        window.product_store = Ext.create('Ext.data.Store', {

            fields: [
                'id',
                'name',
                'section',
                'member_num',
                'begin'
            ],

            proxy: {
                type: 'ajax',
                api: {
                    read: SITEURL + 'membergrade/index/action/read',  //读取数据的URL
                    update: SITEURL + 'membergrade/index/action/save',
                    destroy: SITEURL + 'membergrade/index/action/delete'
                },
                reader: {
                    type: 'json',   //获取数据的格式
                    root: 'lists',
                    totalProperty: 'total'
                }
            },
            remoteSort: true,
            autoLoad: true,
            pageSize: 20,
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
                        //	bar.down('tbfill').hide();

                        bar.insert(0, Ext.create('Ext.panel.Panel', {border: 0, html: '<div class="panel_bar"><a class="abtn" href="javascript:void(0);" onclick="chooseAll()">全选</a><a class="abtn" href="javascript:void(0);" onclick="chooseDiff()">反选</a><a class="abtn" href="javascript:void(0);" onclick="delLine()">删除</a></div>'}));

                        bar.insert(1, Ext.create('Ext.toolbar.Fill'));
                        //items.add(Ext.create('Ext.toolbar.Fill'));
                    }
                }
            }),
            columns: [
                {
                    text: '选择',
                    width: '6%',
                    // xtype:'templatecolumn',
                    tdCls: 'product-ch',
                    align: 'center',
                    dataIndex: 'id',
                    menuDisabled: true,
                    border: 0,
                    renderer: function (value, metadata, record) {
                        id = record.get('id');
                        var begin = record.get('begin');
                        if (id.indexOf('suit') == -1)
                        {
                            if(begin>0)
                            {
                                return  "<input type='checkbox'   class='product_check' style='cursor:pointer' value='" + value + "'/>";
                            }
                            else
                            {
                                return  "<input type='checkbox'  disabled  />";
                            }
                        }



                    }

                },
                {
                    text: '等级名称',
                    width: '24%',
                    dataIndex: 'name',
                    align: 'left',
                    border: 0,
                    sortable: false,
                    menuDisabled: true,
                    renderer: function (value, metadata, record) {
                        return record.get('name');
                    }

                },
                {
                    text: '积分区间',
                    width: '24%',
                    dataIndex: 'is_pc',
                    align: 'center',
                    sortable: false,
                    menuDisabled: true,
                    border: 0,
                    renderer: function (value, metadata, record) {
                        return record.get('section');
                    }
                },
                {
                    text: '会员数量',
                    width: '24%',
                    dataIndex: 'is_pc',
                    align: 'center',
                    sortable: false,
                    menuDisabled: true,
                    border: 0,
                    renderer: function (value, metadata, record) {
                        return record.get('member_num');
                    }
                },
                {
                    text: '管理',
                    width: '22%',
                    align: 'center',
                    border: 0,
                    menuDisabled: true,
                    sortable: false,
                    renderer: function (value, metadata, record) {
                        var id = record.get('id');
                        var begin = record.get('begin');
                        if(begin>0)
                        {
                            var btn="<a href='javascript:void(0);'  class='btn-link' title='删除' onclick=\"delS(" + id + ")\">删除</a>";
                        }
                        else
                        {

                            var btn="<span  class='c-999' title='删除' >删除</span>";
                        }

                        var html = "<a href='javascript:void(0);' class='btn-link' title='修改' onclick=\"modify(" + id + ")\">修改</a>" +
                            "&nbsp;&nbsp;&nbsp;"+btn;
                        return html;
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
                            //delete window.grid.height;
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

                        },
                        beforeedit: function (editor, e) {

                        }
                    }
                })
            ],
            viewConfig: {
                //enableTextSelection:true
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
        delete window.product_grid.height;
    window.product_grid.doLayout();


})

//进行搜索
function goSearch(val, field) {

    window.product_store.getProxy().setExtraParam(field, val);
    window.product_store.loadPage(1);

}

//按进行搜索
function searchKey() {
    var keyword = $.trim($("#searchkey").val());
    var datadef = $("#searchkey").attr('datadef');
    keyword = keyword == datadef ? '' : keyword;
    goSearch(keyword, 'keyword');

}


//切换每页显示数量
function changeNum(combo, records) {

    var pagesize = records[0].get('num');
    window.product_store.pageSize = pagesize;
    window.product_store.loadPage(1);

    //window.product_grid.down('pagingtoolbar').moveFirst();

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
function delLine() {
    //window.product_grid.down('gridcolumn').hide();

    var check_cmp = Ext.select('.product_check:checked');

    if (check_cmp.getCount() == 0) {
        return;
    }
    ST.Util.confirmBox("提示", "确定删除？", function () {
        check_cmp.each(
            function (el, c, index) {
                window.product_store.getById(el.getValue()).destroy();
            }
        );
    })
}
//删除套餐
function delS(id) {
    ST.Util.confirmBox("提示", "确定删除？", function () {
        window.product_store.getById(id.toString()).destroy();
        setTimeout(function () {
            window.location.reload();
        }, 2000);
    })
}

//更新某个字段
function updateField(ele, id, field, value, type) {
    var record = window.product_store.getById(id.toString());
    if (type == 'select' || type == 'input') {
        value = Ext.get(ele).getValue();
    }
    var view_el = window.product_grid.getView().getEl();


    Ext.Ajax.request({
        url: SITEURL + "advertise/index/action/update",
        method: "POST",
        datatype: "JSON",
        params: {id: id, field: field, val: value, kindid: window.product_kindid},
        success: function (response, opts) {
            if (response.responseText == 'ok') {

                record.set(field, value);
                record.commit();

            }
            else {
                ST.Util.showMsg("{__('norightmsg')}", 5, 1000);
            }
        }
    });

}


//刷新保存后的结果
function refreshField(id, arr) {
    id = id.toString();
    var id_arr = id.split('_');
    //  var view_el=window.product_grid.getView().getEl()
    // var scroll_top=view_el.getScrollTop();
    Ext.Array.each(id_arr, function (num, index) {
        if (num) {
            var record = window.product_store.getById(num.toString());

            for (var key in arr) {
                record.set(key, arr[key]);
                record.commit();

            }
        }
    })
}

//修改
function modify(id) {

    var ismobile = "{$ismobile}";
    var url = SITEURL + 'membergrade/edit/{if isset($_GET['menuid'])}menuid/{$_GET['menuid']}/{/if}parentkey/{$parentkey}/itemid/{$itemid}/id/' + id+'/ismobile/'+ismobile;
    parent.window.addTab('修改会员等级', url, 1);
}
 $('.switch-img').each(function(){
     $(this).click(function(){
         console.log($(this).attr('src'));
     });
 });

    function switch_img(obj){
        var obj=$(obj);
        var src=obj.attr('src');
        var close=obj.attr('data-close');
        var show=obj.attr('data-show');
        var statu,bool;
        if(src==close){
            statu=1;
            bool=show;
        }else{
            statu=0;
            bool=close;
        }
        $.get(SITEURL+'membergrade/ajax_statu',{statu:statu,id:obj.attr('data-id')},function(rs){
            console.log(rs);
            if(rs==1){console.log(bool);
                obj.attr('src',bool);
            }
        },'json');
    }
</script>

</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.0312&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
