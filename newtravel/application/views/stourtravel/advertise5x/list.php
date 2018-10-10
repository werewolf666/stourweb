<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>广告管理-思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,plist.css,lightbox.min.css,base_new.css'); }
    <style>
        .ads_body td{
            border-bottom: 1px dashed #ebebeb;
            background: #f8f9fb;
        }
    </style>
</head>
<body style="overflow:hidden">

<table class="content-tab">
    <tr>
        <td width="119px" class="content-lt-td" valign="top">
            {template 'stourtravel/public/leftnav'}
            <!--右侧内容区-->
        </td>
        <td valign="top" class="content-rt-td" style="overflow:hidden">

            <div class="cfg-header-bar clearfix">
                    <span class="select-box w150 mt-5 ml-10 fl">
                        <select name="ismobile" id="ismobile" class="select" onchange="goSearch(this.value,'ismobile')">
                            <option value="">显示端</option>
                            <option value="0">电脑版</option>
                            <option value="1">移动版</option>
                        </select>
                    </span>
                    <span class="select-box w150 mt-5 ml-10 fl">
                        <select name="weblist" id="weblist" class="select" onchange="goSearch(this.value,'webid')">
                            <option value="0">所在站点</option>
                        </select>
                    </span>
                    <span class="select-box w150 mt-5 ml-10 fl">
                        <select id="mould" class="select">
                            <option value="0">所在模块</option>
                        </select>
                    </span>
                    <span class="select-box w150 mt-5 ml-10 fl hide">
                        <select name="adtype" id="adtype" class="select" onchange="goSearch(this.value,'adtype')">

                        </select>
                    </span>
                <div class="cfg-header-search">
                    <input type="text" id="searchkey" value="" placeholder="广告标题/位置命名/调用标识" class="search-text"/>
                    <a href="javascript:;" class="search-btn" id="btn_search" value="搜索" onclick="searchKey()">搜索</a>
                </div>
                <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                <a href="javascript:;" id="developer" class="fr btn btn-primary radius mr-10 mt-6">开发者</a>
                <!--<a href="javascript:;" id="addbtn" class="fr btn btn-primary radius mt-6 mr-10">添加</a>-->
            </div>

            </div>
            <div id="product_grid_panel" class="content-nrt">

            </div>
        </td>
    </tr>
</table>

<script>
    $('#developer').click(function () {
        var url = SITEURL + "advertise5x/developer/menuid/{$_GET['menuid']}/";
        ST.Util.addTab('开发者', url);
    });
    var pageConfig=eval('({Common::format_page_name()})');
    var mouldHtml='';
    for (var i in pageConfig['mould']){
        mouldHtml+='<option value="'+pageConfig['mould'][i]['id']+'">'+pageConfig['mould'][i]['name']+'</option>';
    }
    $('#mould').append(mouldHtml).change(function(){
        var val = $('#mould').val();
        if (val != 0) {
            $('#adtype').parent().removeClass('hide');
            var pageHtml = '';
            for (var i in pageConfig['page']) {
                var item = pageConfig['page'][i];
                if (val == item['pid']) {
                    pageHtml += '<option value="' + item['page_name'] + '">' + item['name'] + '</option>';
                }
            }
            $('#adtype').html(pageHtml).parent().removeClass('hide');
        } else {
            $('#adtype').html('<option value="" selected></option>').parent().addClass('hide');
        }
        $('#adtype').change();
    });

    window.display_mode = 2;
    window.product_kindid = 0;
    Ext.onReady(
            function () {
                Ext.tip.QuickTipManager.init();
                $("#searchkey").focusEffect();
                //添加广告
                $("#addbtn").click(function () {
                    var ismobile = "{$ismobile}";
                    var url = SITEURL + "advertise5x/add/menuid/{$_GET['menuid']}/parentkey/{$parentkey}/itemid/{$itemid}/ismobile/"+ismobile;
                    ST.Util.addTab('添加广告', url);

                })


                var web_menu_items = [];
                Ext.Array.each(window.WEBLIST, function (row, index, itself) {
                    var option = "<option value=" + row.webid + ">" + row.webname + "</option>";
                    $("#weblist").append(option);
                });

                //产品store
                window.product_store = Ext.create('Ext.data.Store', {

                    fields: [
                        'id',
                        'webid',
                        'kindname',
                        'flag',
                        'is_show',
                        'prefix',
                        'number',
                        'custom_label',
                        'is_system',
                        'is_pc',
                        'remark',
                        'size',
                        'position',
                        'ad_id',
                        'ad_src',
                        'tr_class',
                        'ad_link',
                        'ad_name',
                        'ad_order',
                        'ad_index',
                        'child'
                    ],

                    proxy: {
                        type: 'ajax',
                        api: {
                            read: SITEURL + 'advertise5x/index/action/read/',  //读取数据的URL
                            update: SITEURL + 'advertise5x/index/action/save',
                            destroy: SITEURL + 'advertise5x/index/action/delete'
                        },
                        reader: {
                            type: 'json',   //获取数据的格式
                            root: 'lists',
                            totalProperty: 'total'
                        },
                        extraParams  : {
                            ismobile : $('#ismobile').val(),
                            webid : $('#weblist').val(),
                            adtype : $('#adtype').val()
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
                                cls: 'turn-page-bar',
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

                                bar.insert(0, Ext.create('Ext.panel.Panel', {
                                    border: 0,
                                    cls: 'turn-page-bar',
                                   /* html: '<div class="panel_bar"><a class="btn btn-primary radius" href="javascript:void(0);" onclick="chooseAll()">全选</a><a class="btn btn-primary radius ml-10" href="javascript:void(0);" onclick="chooseDiff()">反选</a><a class="btn btn-primary radius ml-10" href="javascript:void(0);" onclick="delLine()">删除</a></div>'*/
                                   html:''
                                }));

                                bar.insert(1, Ext.create('Ext.toolbar.Fill'));
                                //items.add(Ext.create('Ext.toolbar.Fill'));
                            }

                        }
                    }),
                    columns: [
                       /* {
                            text: '选择',
                            width: '6%',
                            // xtype:'templatecolumn',
                            tdCls: 'product-ch',
                            align: 'center',
                            dataIndex: 'id',
                            menuDisabled: true,
                            border: 0,
                            renderer: function (value, metadata, record) {
                                var id = record.get('id');
                                var is_system = record.get('is_system');
                                return  "<input type='checkbox' class='product_check' style='cursor:pointer' value='" + value + "'/>";
                            }
                        },*/
                        {

                            text: '<span class="grid_column_text">所在页面/排序</span>',
                            width: '17%',
                            dataIndex: 'kindname',
                            align: 'center',
                            border: 0,
                            sortable: false,
                            menuDisabled: true,
                            renderer: function (value, metadata, record) {
                                var id = record.get('id');
                                var ad_id=record.get('ad_id');
                                if (id.indexOf('ad') == -1){
                                    return value;
                                }
                                else
                                {
                                    var order=record.get('ad_order');
                                    var index=record.get('ad_index');
                                    return "<input type='text' style=\"width:108px;\" value='"+order+"' class='row-edit-txt' onblur=\"updateField(this,'"+ad_id+"','ad_order',0,'input',"+index+")\"/>";
                                }
                            }

                        },
                        {

                            text: '<span class="grid_column_text">广告位置/内容</span>',
                            width: '27%',
                            dataIndex: 'position',
                            align: 'center',
                            border: 0,
                            sortable: false,
                            menuDisabled: true,
                            renderer: function (value, metadata, record) {
                                var id = record.get('id');
                                if (id.indexOf('ad') == -1){
                                    return value;
                                }
                                else
                                {
                                    var src=record.get('ad_src');
                                    return src.length==0?'':'<div class="example-image-block"><a class="example-image-link" rel="lightbox" href="'+src+'"><img class="example-image" src="'+src+'"/></a></div>';
                                }
                            }
                        },
                        {
                            text: '显示端/广告标题',
                            width: '21%',
                            dataIndex: 'is_pc',
                            align: 'center',
                            sortable: false,
                            menuDisabled: true,
                            border: 0,
                            renderer: function (value, metadata, record) {
                                var id = record.get('id');
                                if (id.indexOf('ad') == -1){
                                    return value==1?'电脑版':'移动版';
                                }
                                else
                                {
                                    var name=record.get('ad_name');
                                    return name.length==0?'':name;
                                }
                            }
                        },
                        {
                            text: '所在站点/广告链接',
                            width: '21%',
                            dataIndex: 'webid',
                            align: 'center',
                            cls: 'mod-1',
                            sortable: false,
                            menuDisabled: true,
                            renderer: function (value, metadata, record, rowIndex, colIndex) {
                                var id = record.get('id');
                                if (id.indexOf('ad') == -1){
                                    return value;
                                }
                                else
                                {
                                    var link=record.get('ad_link');
                                    return link.length==0?'':link;
                                }
                            }
                        },
                        {

                            text: '<span class="grid_column_text">显示</span>',
                            width: '6%',
                            align: 'center',
                            dataIndex: 'is_show',
                            border: 0,
                            menuDisabled: true,
                            cls: 'mod-1',
                            sortable: false,
                            renderer: function (value, metadata, record, rowIndex, colIndex) {
                                var id = record.get('id');
                                var html='';
                                if (id.indexOf('ad') == -1){
                                    var src = "{$GLOBALS['cfg_public_url']}";
                                    var close=src+'images/close-s.png';
                                    var show =src+'images/show-ico.png';
                                    src=value == 0?close:show;
                                    html='<img src='+src+' onclick="switch_img(this)" style="cursor:pointer" data-id="'+record.get('id')+'" data-close="'+close+'" data-show="'+show+'"/>';
                                }
                                return html;
                            }
                        },
                        {
                            text: '管理'+ST.Util.getGridHelp('advertise_manager'),
                            width: '8%',
                            align: 'center',
                            border: 0,
                            menuDisabled: true,
                            sortable: false,
                            renderer: function (value, metadata, record) {
                                var id = record.get('id');

                                var btn;
                                if (id.indexOf('ad') == -1) {
                                    btn= "<a href='javascript:void(0);' class='btn-link' title='修改' onclick=\"modify(" + id + ",null)\">编辑</a>" +"&nbsp;&nbsp;&nbsp;"+"<a href='javascript:void(0);'  title='删除'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>";
                                }else{
                                    var ad_id=record.get('ad_id');
                                    var index=record.get('ad_index');
                                    btn= "<a href='javascript:void(0);' class='btn-link' title='修改' onclick=\"modify(" + ad_id + ","+index+")\">编辑</a>&nbsp;&nbsp;&nbsp;"+"<a href='javascript:void(0);' class='btn-link' title='删除' onclick=\"delad('"+id+"')\">删除</a>";
                                }
                                return btn;
                            }
                        }
                    ],
                    listeners: {
                        boxready: function () {
                            var height = Ext.dom.Element.getViewportHeight();
                            this.maxHeight = height - 40;
                            this.doLayout();
                        }
                    },
                    viewConfig: {
                        stripeRows: false,
                        getRowClass: function(record) {
                            return record.get('id').indexOf('ad') == -1  ? 'ads_header' : 'ads_body';
                        }
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

    //进行搜索
    function goSearch(val, field) {
        window.product_store.getProxy().setExtraParam(field, val);
        window.product_store.loadPage(1);

    }

    //按进行搜索
    function searchKey() {
        var keyword = $.trim($("#searchkey").val());
        goSearch(keyword, 'keyword');
    }


    //切换每页显示数量
    function changeNum(combo, records) {

        var pagesize = records[0].get('num');
        window.product_store.pageSize = pagesize;
        window.product_store.loadPage(1);
    }
    //选择全部
    function chooseAll() {

        //  window.sel_model.selectAll();
        $('.product_check').not(':disabled').attr('checked','checked');
    }
    //反选
    function chooseDiff() {
        $('.product_check').not(':disabled').each(function () {
            $(this).attr("checked", !$(this).attr("checked"));
        });
    }
    function delLine() {
        //window.product_grid.down('gridcolumn').hide();

        var check_cmp = Ext.select('.product_check:checked');

        if (check_cmp.getCount() == 0) {
            ST.Util.showMsg("请选择至少一条数据",5);
            return;
        }
        ST.Util.confirmBox("提示", "确定删除？", function () {
            check_cmp.each(
                    function (el, c, index) {
                        var obj=window.product_store.getById(el.getValue()).data;
                        if(obj.child){
                            for(var _index in obj.child){
                                window.product_store.getById(obj.child[_index]).destroy();
                            }
                        }
                        window.product_store.getById(el.getValue()).destroy();
                    }
            );
        })
    }

    //更新某个字段
    function updateField(ele, id, field, value, type,index) {
        var record = window.product_store.getById(id.toString());
        if (type == 'select' || type == 'input') {
            value = Ext.get(ele).getValue();
        }
        var view_el = window.product_grid.getView().getEl();


        Ext.Ajax.request({
            url: SITEURL + "advertise5x/update",
            method: "POST",
            datatype: "JSON",
            params: {id: id, field: field, val: value, index:index},
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
    function delad(id){
        ST.Util.confirmBox("提示", "确定删除？", function () {
            var obj=window.product_store.getById(id.toString()).data;
            if(obj.child){
                for(var i in obj.child){
                    window.product_store.getById(obj.child[i]).destroy();
                }
            }
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

                }
            }
        })
    }

    //修改
    function modify(id) {

        var ismobile = "{$ismobile}";
        var url = SITEURL + 'advertise5x/edit/{if isset($_GET['menuid'])}menuid/{$_GET['menuid']}/{/if}parentkey/{$parentkey}/itemid/{$itemid}/id/' + id+'/ismobile/'+ismobile;
        parent.window.addTab('修改广告', url, 1);
    }
    $('.switch-img').each(function(){
        $(this).click(function(){
            console.log($(this).attr('src'));
        });
    });

    function switch_img(obj){
        var obj=$(obj);
        var record = window.product_store.getById(obj.attr('data-id'));
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
        $.get(SITEURL+'advertise5x/ajax_statu',{statu:statu,id:obj.attr('data-id')},function(rs){
            if(rs==1){
                obj.attr('src',bool);
            }
        },'json');
    }
</script>
{php echo Common::getScript("lightbox.min.js"); }
</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.2001&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
