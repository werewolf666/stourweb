<?php defined('SYSPATH') or die();?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,plist.css,base_new.css'); }
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js,common.js,choose.js"); }
    <style>
        .tab-nav-label{
            color: #fff;
            display: inline-block;
            height: 33px;
            line-height: 33px;
            padding: 0 20px;
            vertical-align: middle;
            font-size: 12px;
            background: #43aee4;
        }
        .tab-nav-label.on{
            background: #008ed8;
        }
    </style>
</head>
<body style="overflow:hidden" right_left=52EwOs >

    <table class="content-tab">
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

                                </td>
                                <td class="head-td-rt">
                                    <a href="javascript:;" class="fr btn btn-primary radius mr-10" onclick="window.location.reload()">刷新</a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>


                <div  class="search-bar filter" id="search_bar">
                    <div class="pro-search ml-10" style=" float:left; margin-top:4px">
                        <input type="text" id="searchkey" value="目的地名称" datadef="目的地名称" class="sty-txt1 set-text-xh wid_200">
                        <a href="javascript:;" title="搜索" class="head-search-btn" onclick="searchDest()"></a>
                    </div>

                    <span class="display-mod" style="font-size: 0">
                       <a class="tab-nav-label on" href="javascript:void(0);" title="列表" class="on" onClick="togMod(this,1)">列表</a>
                       <a class="tab-nav-label" href="javascript:void(0);" title="高级" onClick="togMod(this,2)">高级</a>
                     </span>
                </div>
                <div id="line_grid_panel" class="content-nrt">

                    <div id="dest_tree_panel" class="content-nrt">

                    </div>
                    <div class="panel_bar">

                    </div>
                </div>
            </td>
        </tr>
    </table>
{php Common::getEditor('jseditor','',580,200,'Sline','','print',true);}
<script>

    var rootUrl = "{php echo URL::site();}";
    window.display_mode =1;
    var module_info =' {php echo json_encode($model_info);}';

    Ext.onReady(
        function () {
            $("#searchkey").focusEffect();
            //目的地store
            window.dest_store = Ext.create('Ext.data.TreeStore', {
                fields: [
                    {name: 'displayorder',
                        sortType: sortTrans

                    },
                    {name: 'is_open',
                        sortType: sortTrans

                    },
                    {name: 'isnav',
                        sortType: sortTrans

                    },
                    {name: 'ishot',
                        sortType: sortTrans
                    },
                    {
                        name: 'istopnav',
                        sortType: sortTrans
                    },
                    {
                        name: 'iswebsite',
                        sortType: sortTrans
                    },
                    {
                        name: 'pinyin',
                        sortType: sortPinyin
                    },
                    {name: 'id', convert: function (v, record) {
                        return 'dest_' + v;
                    }},
                    'kindname',
                    'pid',
                    'seotitle',
                    'keyword',
                    'description',
                    'tagword',
                    'jieshao',
                    'kindtype',
                    'isfinishseo',
                    'templetpath',
                    'litpic',
                    'piclist',
                    'issel',
                    'shownum',
                    'templet',
                    'templet_name',
                    'weburl'

                ],
                proxy: {
                    type: 'ajax',
                    api: {
                        read: SITEURL+'destination/admin/destination/read',  //读取数据的URL
                        update: SITEURL+'destination/admin/destination/save',
                        destroy: SITEURL+'destination/admin/destination/delete'
                    },
                    reader: 'json'
                },
                autoLoad: true,
                listeners: {
                    sort: function (node, childNodes, eOpts) {

                    },
                    load:function( store, records, successful, eOpts )
                    {
                        if(!successful){
                            ST.Util.showMsg("{__('norightmsg')}",5,1000);
                        }
                    },
                    write:function(store, operation, eOpts){
                        var obj=operation.request.operation;
                        var id=obj.request.jsonData.id;
                        id = id.substr(id.indexOf('_') + 1);
                        var obj=Ext.decode(obj.response.responseText);
                        if(obj.success){
                            var record=dest_store.getNodeById(id);
                            record.set('pinyin',obj.pinyin);
                            record.commit();
                        }
                    }
                }

            });
            window.sel_model = Ext.create('Ext.selection.CheckboxModel');
            //目的地panel
            window.dest_treepanel = Ext.create('Ext.tree.Panel', {
                store: dest_store,
                rootVisible: false,
                renderTo: 'dest_tree_panel',
                border: 0,
                style: 'border:0px;',
                width: "100%",
                bodyBorder: 0,
                bodyStyle: 'border-width:0px',
                scroll:'vertical',
                listeners: {
                    itemmousedown: function (node, record, item, index, e, eOpts) {

                        var x = e.xy[0];
                        var column_x = Ext.getCmp('dest_name').getX();
                        var column_width = Ext.getCmp('dest_name').getWidth();

                        if (x < column_x || x > column_x + column_width)
                            return false;

                        window.node_moving = true;

                    },
                    sortchange: function (ct, column, direction, eOpts) {

                        window.sort_direction = direction;

                        var field = column.dataIndex;
                        if (field == 'kindname')
                            field = 'pinyin';
                        window.dest_store.sort(field, direction);

                    },
                    cellclick: function (view, td, cellIndex, record, tr, rowIndex, e, eOpts) {
                        if (record.get('displayorder') == 'add')
                            return false;
                    },
                    afterlayout: function (panel) {
                        var data_height = panel.getView().getEl().down('.x-grid-table').getHeight();
                        var height = Ext.dom.Element.getViewportHeight();
                        if (data_height > height - 66) {
                            window.has_biged = true;
                            panel.height = height - 66;
                        }
                        else if (data_height < height - 66) {
                            if (window.has_biged) {
                                delete panel.height;
                                window.has_biged = false;
                                window.dest_treepanel.doLayout();
                            }
                        }
                    }
                },
                viewConfig: {
                    forceFit: true,
                    border: 0,
                    plugins: {
                        ptype: 'treeviewdragdrop',
                        enableDrag: true,
                        enableDrop: true,
                        displayField: 'kindname'
                    },
                    listeners: {
                        boxready: function () {
                            var height = Ext.dom.Element.getViewportHeight();
                            this.up('treepanel').maxHeight = height - 66;
                            this.up('treepanel').doLayout();
                        },

                        beforedrop: function (node, data, overModel, dropPosition, dropHandlers) {
                            if (dropPosition != 'append') {
                                dropHandlers.processDrop();
                                return;
                            }
                            if (overModel.isLoaded()){
                                dropHandlers.processDrop();
                            }
                            else {
                                overModel.expand(false, function () {
                                    dropHandlers.processDrop();
                                });
                            }

                            dropHandlers.cancelDrop();
                        },
                        drop: function (node, data, overModel, dropPosition, eOpts) {

                            var params = {};
                            params['moveid'] = data.records[0].get('id');
                            params['overid'] = overModel.get('id');
                            params['position'] = dropPosition;
                            params['moveid'] = params['moveid'].substr(params['moveid'].indexOf('_') + 1);
                            params['overid'] = params['overid'].substr(params['overid'].indexOf('_') + 1);


                            if (dropPosition == 'append') {

                                var btn_node = window.dest_store.getNodeById(params['overid'] + 'add');
                                overModel.insertBefore(data.records[0], btn_node);

                            }

                            //alert(overModel.children);
                            Ext.Ajax.request({
                                url: 'destination/action/drag',
                                params: params,
                                method: 'POST',
                                success: function (response) {
                                    var text = response.responseText;
                                    if (text == 'ok') {

                                    } else {

                                    }
                                    // process server response here
                                }
                            });

                        }
                    }

                },
                columns: [

                    {
                        text: '<span class="grid_column_text">排序</span>'+ST.Util.getGridHelp('destination_index_displayorder'),
                        dataIndex: 'displayorder',
                        align:'center',
                        width: '9%',
                        cls:'sort-col',
                        draggable: false,
                        menuDisabled:true,
                        renderer : function(value, metadata,record) {
                            var id=record.get('id');
                            if (value == 'add')
                                return '';
                            else {
                                if(value=='9999'||value=='999999'||!value)
                                    value='';
                                id = id.substr(id.indexOf('_') + 1);
                                return "<input type='text' class='row-edit-txt' onclick=\"prevPopup(event,this)\" onblur=\"updateField(this,"+id+",'displayorder',0,'input')\" value='" + value + "'/>";
                            }
                        }

                    },
                    {
                        xtype: 'treecolumn',   //有展开按钮的指定为treecolumn
                        text: '<span class="grid_column_text">目的地</span>'+ST.Util.getGridHelp('destination_appname'),
                        dataIndex: 'kindname',
                        id: 'dest_name',
                        menuDisabled:true,
                        sortable:false,
                        width: '56%',
                        listeners: {
                            afterrender: function (obj, eopts) {



                            }
                        }
                    },
                    {
                        text: '<span class="grid_column_text">显示</span>'+ST.Util.getGridHelp('destination_index_is_open'),
                        dataIndex: 'is_open',
                        width: '9%',
                        xtype: 'actioncolumn',
                        tdCls: 'dest-al-mid',
                        align: 'center',
                        cls:'sort-col',
                        menuDisabled:true,
                        items: [
                            {
                                getClass: function (v, meta, rec) {          // Or return a class from a function

                                    var id = rec.get('id');
                                    if (id.indexOf('add') > 0)
                                        return '';
                                    if (v == 1)
                                        return 'dest-status-ok';
                                    else
                                        return 'dest-status-none';
                                },
                                handler: function (view, index, colindex, itm, e, record) {
                                    togStatus(null, record, 'is_open');
                                }
                            }
                        ],
                        listeners:{
                            afterrender:function(obj,eopts)
                            {
                                window.display_mode==1?obj.show():obj.hide();
                            }
                        }
                    },
                    {
                        text: '<span class="grid_column_text">栏目首页</span>'+ST.Util.getGridHelp('destination_index_isnav'),
                        dataIndex: 'isnav',
                        width: '9%',
                        xtype: 'actioncolumn',
                        tdCls: 'dest-al-mid',
                        align: 'center',
                        border: 0,
                        menuDisabled:true,
                        cls:'sort-col',
                        items: [
                            {
                                getClass: function (v, meta, rec) {          // Or return a class from a function
                                    var id = rec.get('id');
                                    if (id.indexOf('add') > 0)
                                        return '';
                                    if (v == 1)
                                        return 'dest-status-ok';
                                    else
                                        return 'dest-status-none';
                                },
                                handler: function (view, index, colindex, itm, e, record) {
                                    togStatus(null, record, 'isnav');
                                }
                            }
                        ],
                        listeners:{
                            afterrender:function(obj,eopts)
                            {
                                window.display_mode==1?obj.show():obj.hide();
                            }
                        }
                    },
                    {
                        text: '<span class="grid_column_text">是否热门</span>'+ST.Util.getGridHelp('destination_index_ishot'),
                        dataIndex: 'ishot',
                        width: '9%',
                        xtype: 'actioncolumn',
                        tdCls: 'dest-al-mid',
                        align: 'center',
                        cls:'sort-col',
                        menuDisabled:true,
                        items: [
                            {
                                getClass: function (v, meta, rec) {          // Or return a class from a function
                                    var id = rec.get('id');
                                    if (id.indexOf('add') > 0)
                                        return '';
                                    if (v == 1)
                                        return 'dest-status-ok';
                                    else
                                        return 'dest-status-none';
                                },
                                handler: function (view, index, colindex, itm, e, record) {
                                    togStatus(null, record, 'ishot');
                                }
                            }
                        ],
                        listeners:{
                            afterrender:function(obj,eopts)
                            {
                                window.display_mode==1?obj.show():obj.hide();
                            }
                        }
                    },
                    {
                        text: '<span class="grid_column_text">ID</span>'+ST.Util.getGridHelp('destination_destination_id'),
                        dataIndex: 'id',
                        width: '9%',
                        align: 'center',
                        menuDisabled:true,
                        sortable:false,
                        renderer : function(value, metadata,record) {
                            return value.replace('dest_','');
                        },
                        listeners:{
                            afterrender:function(obj,eopts)
                            {
                                window.display_mode==2?obj.show():obj.hide();
                            }
                        }
                    },
                    {
                        text: '<span class="grid_column_text">模板设置</span>'+ST.Util.getGridHelp('templates_name'),
                        dataIndex: 'templet_name',
                        width: '18%',
                        align: 'center',
                        sortable:false,
                        menuDisabled:true,
                        renderer : function(value, metadata,record) {
                            var id=record.get('id');
                            return '<a href="javascript:;" title="模板设置" onclick="setTemplate(\'' + id + '\')">'+value+'</a>';
                        },
                        listeners:{
                            afterrender:function(obj,eopts)
                            {
                                window.display_mode==2?obj.show():obj.hide();
                            }
                        }
                    },

                    {
                        text: '<span class="grid_column_text">管理</span>'+ST.Util.getGridHelp('destination_index_manager'),
                        dataIndex: 'id',
                        width: '9%',
                        tdCls: 'dest-al-mid',
                        xtype: 'templatecolumn',
                        sortable: false,
                        align: 'center',
                        border: 0,
                        menuDisabled: true,
                        tpl: new Ext.XTemplate(
                            '{[this.realName(values.id)]}',
                            {
                                realName: function (id) {
                                    var viewHtml = (window.display_mode ==1?'<a href="javascript:;" class="row-mod-btn" title="优化设置" onclick="destProductSet(\'' + id + '\')"></a>':'')+'&nbsp;&nbsp;&nbsp;<a href="javascript:;" class="row-view-btn" title="预览" onclick="viewDestType(\'' + id + '\')"></a>';
                                    return viewHtml;
                                }
                            }
                        )
                    }
                ],
                plugins: [
                    Ext.create('Ext.grid.plugin.CellEditing', {
                        clicksToEdit: 1,
                        listeners: {
                            beforeEdit:function(editor,e){
                                if(window.display_mode!=0 && e.field!='displayorder')
                                    return false;
                            },
                            edit: function (editor, e) {
                                var pinyin = e.record.get('pinyin');
                                e.record.save({params: {field: e.field,pinyin:pinyin}});
                                e.record.commit();

                            }
                        }
                    })
                ]
            });
        }
    );

    function togStatus(obj, record, field) {

        var val = record.get(field);
        var id = record.get('id');

        id = id.substr(id.indexOf('_') + 1);
        var newval = val == 1 ? 0 : 1;
        Ext.Ajax.request({
            url: SITEURL+"destination/admin/destination/update",
            method: "POST",
            datatype: "JSON",
            params: {id: id, field: field, val: newval},
            success: function (response, opts) {

                if (response.responseText == 'ok') {
                    record.set(field, newval);
                    record.commit();
                }
            }});
    }

    function updateField(ele,id,field,val,type)
    {
        if(type=='input')
            val=$(ele).val();
        var record = window.dest_store.getById(id.toString());
        Ext.Ajax.request({
            url: SITEURL+"destination/admin/destination/update",
            method: "POST",
            datatype: "JSON",
            params: {id: id, field: field, val: val, typeid: window.display_mode},
            success: function (response, opts) {
                if (response.responseText == 'ok') {
                    record.set(field, val);
                    record.commit();
                }
            }});
    }


    function destProductSet(dest_id) {
        var id = dest_id.substr(dest_id.indexOf('_') + 1);
        var record = dest_store.getNodeById(id);
        ST.Util.addTab(record.data.kindname+'-目的地设置', 'destination/admin/destination/set/menuid/{$_GET["menuid"]}/id/'+id);
    }




    Ext.getBody().on('mouseup', function () {
        window.node_moving = false;

        //console.log('up_'+window.node_moving);
    });
    Ext.getBody().on('mousemove', function (e, t, eOpts) {

        if (window.node_moving == true) {
            // console.log('mov_'+window.node_moving);

            var tree_view = window.dest_treepanel.down('treeview');
            var view_y = tree_view.getY();
            var view_bottom = view_y + tree_view.getHeight();
            var mouse_y = e.getY();
            if (mouse_y < view_y)
                tree_view.scrollBy(0, -5, false);
            if (mouse_y > view_bottom)
                tree_view.scrollBy(0, 5, false);

        }
    });


    Ext.EventManager.onWindowResize(function () {
        var height = Ext.dom.Element.getViewportHeight();
        var data_height = window.dest_treepanel.getView().getEl().down('.x-grid-table').getHeight();
        if (data_height > height - 66)
            window.dest_treepanel.height = (height - 66);
        else
            delete window.dest_treepanel.height;
        window.dest_treepanel.doLayout();
    })

    function cascadeDest(dest, index) {
        if (dest.length == 1) {
            var node = window.dest_store.getNodeById(dest[0]);
            var ele = window.dest_treepanel.getView().getNode(node);
            if (ele) {

                var edom = Ext.get(ele);
                edom.addCls('search-dest-tr');
                if (index == 0)
                    viewScroll(edom);
            }
        }
        else {
            var node = window.dest_store.getNodeById(dest[0]);
            dest.shift();
            node.expand(false, function () {
                cascadeDest(dest, index);
            });

        }
    }
    function viewScroll(extdom)   //在treeview里滚动
    {
        var tree_view = window.dest_treepanel.getView();
        var view_y = tree_view.getY();
        var dom_y = extdom.getY();


        window.setTimeout(function () {
            window.first_scroll = true;
            extdom.scrollIntoView(tree_view.getEl());
        }, 450);
        //else
        // extdom.scrollIntoView(tree_view.getEl());


    }
    function togCheck(id) {

    }
    function chooseAll() {
        var check_cmp = Ext.query('.dest_check');
        for (var i in check_cmp) {
            if (!Ext.get(check_cmp[i]).getAttribute('checked'))
                check_cmp[i].click();
        }
    }
    function chooseDiff() {
        var check_cmp = Ext.query('.dest_check');
        for (var i in check_cmp){
            check_cmp[i].click();
        }
    }
    function delDest() {

        var check_cmp = Ext.select('.dest_check:checked');
        if(check_cmp.getCount()==0)
        {
            ST.Util.showMsg("请选择至少一条数据",5);
            return;
        }

        ST.Util.confirmBox("提示","确定删除？",function(){

            check_cmp.each(
                function (el, c, index) {

                    window.dest_store.getNodeById(el.getValue()).destroy();

                }
            );
        });

    }
    function searchDest() {

        var s_str = Ext.get('searchkey').getValue();
        //s_str=s_str.trim();
        Ext.select('.search-dest-tr').removeCls('search-dest-tr');

        if (!s_str)
            return;
        Ext.Ajax.request({
            url: SITEURL+'line/admin/destination/destination/action/search',
            params: {keyword: s_str},
            method: 'POST',
            success: function (response) {
                var text = response.responseText;
                if (text == 'no') {
                    ST.Util.showMsg('未找到与'+s_str+'相关的目的地',5,1000);
                } else {
                    var list = Ext.decode(text);
                    var index = 0;
                    for (var i in list) {

                        var dest = list[i];
                        cascadeDest(dest, index);
                        index++;
                    }
                }
            }
        });

    }

    function getHelp(e) {
        if (e && e.stopPropagation)
        //因此它支持W3C的stopPropagation()方法
            e.stopPropagation();
        else
        //否则，我们需要使用IE的方式来取消事件冒泡
            window.event.cancelBubble = true;
    }
    function sortTrans(val) {
        if(!val)
            val=0;

        if (!window.sort_direction)
            return window.parseInt(val);
        else {

            if (val == 'add') {
                if (window.sort_direction == 'ASC')
                    return 100000000000000000;
                else
                    return -10;
            }
            else
                return window.parseInt(val);
        }
        // alert(val);
    }
    function sortPinyin(val) {

        if (!window.sort_direction)
            return val;
        else {
            if (val == 'add') {
                if (window.sort_direction == 'ASC')
                    return 1000000000000;
                else
                    return 1;
            }
            else {
                if (!val)
                    return 555555555555;
                else {
                    val.toLowerCase();
                    var num1 = val.charCodeAt(0);
                    var num2 = val.charCodeAt(1);
                    if (isNaN(num2))
                        num2 = '000';
                    if (num2 < 100)
                        num2 = '0' + num2;

                    var num3 = val.charCodeAt(2);
                    if (isNaN(num3))
                        num3 = '000';
                    if (num3 < 100)
                        num3 = '0' + num3;

                    var num4 = val.charCodeAt(3);
                    if (isNaN(num4))
                        num4 = '000';
                    if (num4 < 100)
                        num4 = '0' + num4;

                    var result = window.parseInt(num1 + '' + num2 + '' + num3 + '' + num4);

                    // console.log(val + '_' + result);
                    return result;
                }
            }
        }
    }


    //切换模块
    function togMod(obj, num,title) {
        window.display_mode = num;
        Ext.get(obj).parent().parent().select("a.on").removeCls('on');
        Ext.get(obj).addCls('on');
        for (var i in window.dest_treepanel.columns) {
            window.dest_treepanel.columns[i].fireEvent('afterrender', window.dest_treepanel.columns[i]);
        }
        window.dest_store.getProxy().setExtraParam('typeid', num);
        window.dest_store.load();
        $("#position").html(title);
        if(num!=0)
        {
            $(".panel_bar").hide();
            $(".dest_check").hide();

        }
        else
        {
            $(".panel_bar").show();
            $(".dest_check").show();
        }


    }


    //设置模板
    function setTemplate(dest_id) {
        var id = dest_id.substr(dest_id.indexOf('_') + 1);
        ST.Util.showBox('选择模板',SITEURL+'destination/admin/destination/set_template/id/'+id,400,200,null,null,document,{loadCallback:setTemplateCallback,loadWindow:window});
    }

    //设置模板回调
    function setTemplateCallback(result,bool)
    {console.log(bool);
        if(!bool)
            return;
        var id=result.id;
        var typeid=result.typeid;
        var record = dest_store.getNodeById(id);
        for(var i in result.data)
        {
            var val=result.data[i];
            record.set(i,val);
        }
        record.save({extraParams:{},callback: function (records, operation, success) {
            if (success) {
                ST.Util.showMsg("修改成功",4);
            }
            else {
                ST.Util.showMsg("修改失败",5);
            }
        }});



    }

    function viewDest(id)
    {
        id = id.substr(id.indexOf('_') + 1);
        var record= dest_store.getNodeById(id);
        var name=record.get('kindname');
        var iswebsite=record.get('iswebsite');
        var weburl=record.get('weburl');
        var pinyin=record.get('pinyin');
        if(iswebsite==1)
        {
            window.open(weburl);
        }
        else
        {
            if(!pinyin)
            {
                ST.Util.showMsg('请选填写目的地拼音',5);
                return;
            }
            window.open(BASEHOST+'/'+pinyin);
        }

    }
    function viewDestType(id){
        id = id.substr(id.indexOf('_') + 1);
        var record= dest_store.getNodeById(id);
        var pinyin=record.get('pinyin');
        var url=BASEHOST+'/'+pinyin;
        window.open(url);
    }

    function prevPopup(e,ele) {
        var evt = e ? e : window.event;
        if (evt.stopPropagation) {
            evt.stopPropagation();
        }
        else {

            evt.cancelBubble = true;
        }
    }
</script>
</body>
</html>
