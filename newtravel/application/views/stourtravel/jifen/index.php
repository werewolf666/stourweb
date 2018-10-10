<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>积分策略-思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base_new.css'); }
    {php echo Common::getScript("jquery.buttonbox.js,choose.js"); }
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
                    <div class="fl select-box w100 mt-5 ml-10">
                        <select name="section" onchange="goSearch(this)" class="select">
                            <option value="-1">送分场景</option>
                            <option value="1">产品预订</option>
                            <option value="2">产品评论</option>
                            <option value="3">文章发布</option>
                            <option value="4">文章评论</option>
                            <option value="5">积分抽奖</option>
                            <option value="6">会员签到</option>
                            <option value="7">新会员引导</option>
                            <option value="8">活跃会员</option>
                            <option value="9">分销产品</option>
                            <option value="0">其他策略</option>
                        </select>
                    </div>
                    <div class="fl select-box w100 mt-5 ml-10">
                        <select name="typeid" onchange="goSearch(this)" class="select">
                            <option value="-1">针对产品</option>
                            {loop $products $product}
                            <option value="{$product['id']}">{$product['modulename']}</option>
                            {/loop}
                        </select>
                    </div>
                    <div class="fl select-box w100 mt-5 ml-10">
                        <select name="issystem" onchange="goSearch(this)" class="select">
                            <option value="-1" >策略来源</option>
                            <option value="1" >内置</option>
                            <option value="0" >自定义</option>
                        </select>
                    </div>
                    <div class="fl select-box w100 mt-5 ml-10">
                        <select name="isopen" onchange="goSearch(this)" class="select">
                            <option value="-1" >开关状态</option>
                            <option value="1" >开启</option>
                            <option value="0" >关闭</option>
                        </select>
                    </div>
                    <div class="cfg-header-search">
                        <input type="text" id="searchkey"  placeholder="策略名称或标识"  class="search-text" />
                        <a href="javascript:;" class="search-btn" onclick="goSearch()">搜索</a>
                    </div>
                    <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10"  onclick="window.location.reload()">刷新</a>
                    <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" id="addbtn">添加</a>
                </div>
                <div id="product_grid_panel" class="content-nrt" >

                </div>
            </td>
        </tr>
    </table>

<script>

window.display_mode = 1;	//默认显示模式
window.product_kindid = 0;  //默认目的地ID


Ext.onReady(
    function () {
        Ext.tip.QuickTipManager.init();


        $("#searchkey").focusEffect();

        $("#addbtn").click(function(){
            ST.Util.addTab('添加积分策略','{$cmsurl}jifen/add/{if isset($_GET['menuid'])}menuid/{$_GET['menuid']}/{/if}',0);
        });

        //产品store
        window.product_store = Ext.create('Ext.data.Store', {

            fields: [
                'id',
                'typeid',
                'typeid_names',
                'title',
                'label',
                'issystem',
                'section',
                'section_name',
                'rewardway',
                'value',
                'frequency_type',
                'frequency',
                'addtime',
                'isopen'
            ],

            proxy: {
                type: 'ajax',
                api: {
                    read: SITEURL+'jifen/index/action/read/typeid/{$typeid}',  //读取数据的URL
                    update: SITEURL+'jifen/index/action/save/typeid/{$typeid}',
                    destroy: SITEURL+'jifen/index/action/delete/typeid/{$typeid}'
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
                    var pageHtml=ST.Util.page(store.pageSize,store.currentPage,store.getTotalCount(),10);
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
                    text: '选择'+ST.Util.getGridHelp('jifen_index_choose'),
                    width: '6%',
                    // xtype:'templatecolumn',
                    tdCls: 'product-ch',
                    align: 'center',
                    dataIndex: 'id',
                    menuDisabled:true,
                    border: 0,
                    renderer: function (value, metadata, record) {

                        var issystem = record.get('issystem');
                        if(issystem==1)
                           return '';
                        return  "<input type='checkbox' class='product_check' style='cursor:pointer' value='" + value + "'/>";

                    }

                },
                {
                    text: '策略名称'+ST.Util.getGridHelp('jifen_index_title'),
                    width: '16%',
                    dataIndex: 'title',
                    align: 'left',
                    border: 0,
                    sortable: false,
                    menuDisabled:true,
                    renderer: function (value, metadata, record) {
                        return value;
                    }

                },
               /* {
                    text: '调用标识',
                    width: '14%',
                    dataIndex: 'label',
                    align: 'center',
                    border: 0,
                    sortable: false,
                    menuDisabled:true,
                    renderer: function (value, metadata, record) {
                        return value;
                    }

                },*/

                {
                    text: '送分场景'+ST.Util.getGridHelp('jifen_index_section'),
                    width: '10%',
                    dataIndex: 'section_name',
                    align: 'center',
                    border: 0,
                    sortable: false,
                    menuDisabled:true,
                    renderer: function (value, metadata, record) {
                        return value;
                    }
                },
                {
                    text: '送分周期'+ST.Util.getGridHelp('jifen_index_frequency_type'),
                    width: '12%',
                    dataIndex: 'frequency_type',
                    align: 'center',
                    border: 0,
                    sortable: false,
                    menuDisabled:true,
                    renderer: function (value, metadata, record) {
                        var str='';
                        value = parseInt(value);
                        var frequency = parseInt(record.get('frequency'));
                        frequency = !frequency?0:frequency;
                        switch(value)
                        {
                            case 0:
                                str='每次';
                                break;
                            case 1:
                                str='仅一次';
                                break;
                            case 2:
                                str='每天仅'+frequency+'次';
                                break;
                            case 3:
                                str='总共'+frequency+'次';
                                break;
                        }
                        return str;
                    }

                },
               /* {
                    text: '奖励方式',
                    width: '9%',
                    dataIndex: 'rewardway',
                    align: 'center',
                    border: 0,
                    sortable: false,
                    menuDisabled:true,
                    renderer: function (value, metadata, record) {
                        if(value=='0'||!value)
                        {
                            return '按分值';
                        }
                        else
                        {
                            return '按百分比';
                        }
                    }
                },*/
                {
                    text: '送分分值/比例'+ST.Util.getGridHelp('jifen_index_value'),
                    width: '12%',
                    dataIndex: 'value',
                    align: 'center',
                    border: 0,
                    sortable: false,
                    menuDisabled:true,
                    renderer: function (value, metadata, record) {
                        var rewardway=record.get('rewardway');
                        var percent_label = rewardway==1?'%':'';
                        return value+percent_label;
                    }

                },
                {
                    text: '策略来源'+ST.Util.getGridHelp('jifen_index_issystem'),
                    width: '12%',
                    dataIndex: 'issystem',
                    align: 'center',
                    border: 0,
                    sortable: false,
                    menuDisabled:true,
                    renderer: function (value, metadata, record) {
                       return value==0?'自定义':'内置';
                    }
                },
                {
                    text: '针对产品'+ST.Util.getGridHelp('jifen_index_typeid'),
                    width: '12%',
                    dataIndex: 'typeid_names',
                    align: 'center',
                    border: 0,
                    sortable: false,
                    menuDisabled:true,
                    renderer: function (value, metadata, record) {
                        var section=record.get('section');
                        var issystem=record.get('issystem');
                        if($.inArray(section, ['1','2','3','4'])==-1)
                        {
                            return '/';
                        }
                        if(issystem==1 || section!=1)
                        {
                            return value;
                        }

                        if(value)
                        {
                            metadata.tdAttr = "data-qtip='" + value + "'data-qclass='dest-tip'";
                        }
                        var id=record.get('id');
                        var d_text=value?'<span style="color:green">已设</span>':'<span style="color:red">未设</span>';
                        return "<a href='javascript:;' onclick=\"setTypids("+id+")\">"+d_text+"</a>";
                       // return value==0?'自定义':'内置';
                    }
                },
                {
                    text:'开关状态'+ST.Util.getGridHelp('jifen_index_isopen'),
                    width:'10%',
                    // xtype:'templatecolumn',
                    align:'center',
                    border:0,
                    dataIndex:'isopen',
                    xtype:'actioncolumn',
                    menuDisabled:true,
                    cls:'mod-1 sort-col',
                    items:[
                        {
                            getClass: function(v, meta, rec) {          // Or return a class from a function
                                if(v==1)
                                    return 'dest-status-ok';
                                else
                                    return 'dest-status-none';
                            },
                            handler:function(view,index,colindex,itm,e,record)
                            {
                                // togStatus(null,record,'ishidden');
                                var val=record.get('isopen');
                                var id=record.get('id');
                                var newval=val==1?0:1;
                                updateField(null,record.get('id'),'isopen',newval)
                            }
                        }]
                },
                {
                    text: '操作',
                    width: '10%',
                    align: 'center',
                    border: 0,
                    sortable: false,
                    menuDisabled:true,
                    cls: 'mod-1',
                    renderer: function (value, metadata, record) {

                        var id = record.get('id');
                        return '<a href="javascript:;" title="编辑" class="btn-link" onclick="goModify('+id+')">编辑</a>'
                    }
                }
            ],
            listeners: {
                boxready: function () {


                    var height = Ext.dom.Element.getViewportHeight();
                    //console.log('viewportHeight:'+height);
                    this.maxHeight = height-40 ;
                    this.doLayout();
                },
                afterlayout: function (grid) {






               /*    var data_height = 0;
                    try {
                        data_height = grid.getView().getEl().down('.x-grid-table').getHeight();
                    } catch (e) {
                    }
                    var height = Ext.dom.Element.getViewportHeight();
                    console.log(data_height+'---'+height);
                    if (data_height > height - 40) {
                        window.has_biged = true;
                        grid.height = height - 40;
                    }
                    else if (data_height < height - 40) {
                        if (window.has_biged) {
                            delete window.grid.height;
                            window.has_biged = false;
                            grid.doLayout();
                        }
                    }*/
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
                enableTextSelection:true
            }
        });


    })

//实现动态窗口大小
Ext.EventManager.onWindowResize(function () {

    var height = Ext.dom.Element.getViewportHeight();
    var data_height = window.product_grid.getView().getEl().down('.x-grid-table').getHeight();
    if (data_height > height - 40)
        window.product_grid.height = (height - 40);
    else
       delete window.product_grid.height;
    window.product_grid.doLayout();


})

function togStatus(ele)
{
    var status=$(ele).val();
    window.product_store.getProxy().setExtraParam('status',status);
    window.product_store.loadPage(1);

}
function togPaysource(ele)
{
    var paysource=$(ele).val();
    window.product_store.getProxy().setExtraParam('paysource',paysource);
    window.product_store.loadPage(1);
}

$(function(){


    var typeid = "{$typeid}";
    var channelname = "{$channelname}";
    //查看数据报表
    $(".btn_report").click(function(){
        var url=SITEURL+"order/dataview/typeid/"+typeid;
        ST.Util.showBox(channelname+'订单数据报表查看',url,860,510,function(){});

    })
    //导出excel
    $(".btn_excel").click(function(){
        var url=SITEURL+"order/excel/typeid/"+typeid;
        ST.Util.showBox(channelname+'订单生成excel',url,560,380,function(){});
    })

    //获取当前产品订单常规信息
    $.getJSON(SITEURL+'order/ajax_sell_info/typeid/'+typeid,function(data){
        $("#today_price").html(data.today);
        $("#last_price").html(data.last);
        $("#thisweek_price").html(data.thisweek);
        $("#thismonth_price").html(data.thismonth);
        $("#total_price").html(data.total);

    })





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

    //var pagesize = records[0].get('num');
    //window.product_store.pageSize = pagesize;
    //window.product_grid.down('pagingtoolbar').moveFirst();
    //window.product_store.load({start:0});
    var pagesize=records[0].get('num');
    window.product_store.pageSize=pagesize;
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
function updateField(ele, id, field, value, type,callback) {
    var record = window.product_store.getById(id.toString());

    Ext.Ajax.request({
        url: SITEURL+"jifen/index/action/update/",
        method: "POST",
        datatype: "JSON",
        params: {id: id, field: field, val: value, kindid: 0},
        success: function (response, opts) {
            if (response.responseText == 'ok') {
                record.set(field, value);
                record.commit();
                if(typeof(callback)=='function')
                {
                    callback(record);
                }

                // view_el.scrollBy(0,scroll_top,false);
            }
        }});

}

//删除套餐
function delS(id) {
    ST.Util.confirmBox("提示","确定删除？",function(){
            window.product_store.getById(id.toString()).destroy();
    })
}

//刷新保存后的结果
function refreshField(id, arr) {
    id = id.toString();
    var id_arr = id.split('_');

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


//查看订单
function goModify(id)
{
    var record = window.product_store.getById(id.toString());
    var url=SITEURL+"jifen/edit/{if isset($_GET['menuid'])}menuid/{$_GET['menuid']}/{/if}id/"+id;
    ST.Util.addTab('积分策略：'+record.get('title'),url,1);

}
function goSearch(ele)
{
    var keyword=$("#searchkey").val();
    window.product_store.getProxy().setExtraParam('keyword',keyword);
    if(ele)
    {
        var name = $(ele).attr('name');
        var val = $(ele).val();
        window.product_store.getProxy().setExtraParam(name,val);
    }
    window.product_store.loadPage(1);
}

function setTypids(id)
{
  CHOOSE.setSome("设置产品",{loadCallback:setRealTypeids},SITEURL+'jifen/dialog_settypeids?id='+id,true);
}
function  setRealTypeids(result)
{
    var ids=[];
    var names=[];
    for(var i in result.data)
    {
        var row=result.data[i];
        ids.push(row['id']);
        names.push(row['attrname']);
    }
    var idsStr=ids.join(',');
    var nameStr=names.join(',');
    if(result.id)
    {
        updateField(null,result.id,'typeid',idsStr,0,function(record){
            record.set('typeid_names',nameStr);
            record.commit();
            // var id=record.get('id');
            // $("#box_"+id).attr("checked",true);
        });
        return;
    }
}


</script>

</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.2103&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
