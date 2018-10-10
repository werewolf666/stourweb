<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,plist.css,base_new.css'); }
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js,listimageup.js,common.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }

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
                    <div class="cfg-header-search">
                        <input type="text" id="searchkey" value="{$jmp_destname}" placeholder="目的地名称" class="search-text">
                        <a href="javascript:;" title="搜索" class="search-btn" onclick="searchDest()">搜索</a>
                    </div>
                    <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                </div>

                <div id="line_grid_panel" class="content-nrt">
                    <div id="dest_tree_panel" class="content-nrt"></div>
                    {if empty($typeid)}
                    <div class="st-bottom-console-bar clear clearfix">
                        <a class="btn btn-primary radius" href="javascript:;" onClick="chooseAll()">全选</a>
                        <a class="btn btn-primary radius ml-10" href="javascript:;" onClick="chooseDiff()">反选</a>
                        <a class="btn btn-primary radius ml-10" href="javascript:;" onClick="delDest()">删除</a>
                    </div>
                    {/if}
                </div>
            </td>
        </tr>
    </table>

{php Common::getEditor('jseditor','',580,200,'Sline','','print',true);}
<script>
window.display_mode =0;

Ext.onReady(
    function () {
        $("#searchkey").focusEffect();
        //目的地store
        window.dest_store = Ext.create('Ext.data.TreeStore', {
            fields: [
                {name: 'displayorder',
                    sortType: sortTrans

                },
                {
                    name: 'iswebsite',
                    sortType: sortTrans
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
                'weburl',
                'pinyin',
                'isopen'

            ],
            proxy: {
                type: 'ajax',
                extraParams: {typeid: 0},
                api: {
                    read: SITEURL+'destination/destination/action/read',  //读取数据的URL
                    update: SITEURL+'destination/destination/action/save',
                    destroy: SITEURL+'destination/destination/action/delete'
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

                    // console.log(data_height+'---'+height);
                    if (data_height > height - 78) {
                        window.has_biged = true;
                        panel.height = height - 78;
                    }
                    else if (data_height < height - 78) {
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

                        this.up('treepanel').maxHeight = height - 78;
                        this.up('treepanel').doLayout();

                        var keyword = $("#searchkey").val();
                        if(keyword)
                        {
                            $(".search-btn").trigger('click');
                        }
                    },

                    beforedrop: function (node, data, overModel, dropPosition, dropHandlers) {
                        if (dropPosition != 'append') {
                            dropHandlers.processDrop();
                            return;
                        }

                        if (overModel.isLoaded())
                            dropHandlers.processDrop();
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
                            }
                        });

                    }
                }

            },
            columns: [
                {
                    text: '选择',
                    width: '6%',
                    dataIndex: 'issel',
                    tdCls: 'dest-al-mid',
                    xtype: 'templatecolumn',
                    align: 'center',
                    draggable: false,
                    menuDisabled:true,
                    sortable:false,
                    tpl: new Ext.XTemplate(
                        '{[this.realName(values.id,values.issel)]}',
                        {
                            realName: function (id, issel) {
                                if (id.indexOf('add') > 1)
                                    return '';
                                id = id.substr(id.indexOf('_') + 1);
                                return "<input type='checkbox' class='dest_check' value='" + id + "' style='cursor:pointer' onclick='togCheck(" + id + ")'/>";
                            }
                        }
                    )
                },
                {
                    xtype: 'treecolumn',
                    text: '<span class="grid_column_text">目的地</span>'+ST.Util.getGridHelp('destination_destination_kindname'),
                    dataIndex: 'kindname',
                    id: 'dest_name',
                    menuDisabled:true,
                    sortable:false,
                    editor:{xtype:'textfield',listeners:{
                               focus:function(ele,event)
                                {
                                    var inputId=ele.getInputId();
                                    var inputEle= $("#"+inputId)
                                    var str=ele.getValue();
                                    var width=80;
                                    if(str)
                                    {
                                        var len=str.length*20;
                                        width=len<width?width:len;
                                    }
                                    inputEle.css({'margin-left':3,'padding':'0px 10px','width':width});
                                }
                    }},
                    width: '50%',
                    renderer : function(value, metadata,record) {
                        var id=record.get('id');
                        id = id.substr(id.indexOf('_') + 1);
                        if(id.indexOf('add')==-1)
                        {
                            return "<span class='row-editable-sp'>"+value+"</span>";
                        }
                        return value;
                    }
                },
                {

                    text: '<span class="grid_column_text">ID</span>'+ST.Util.getGridHelp('destination_destination_id'),
                    dataIndex: 'id',
                    width: '9%',
                    align: 'center',
                    tdCls: 'dest-al-mid',
                    sortable: false,
                    menuDisabled:true,
                    renderer: function (value, metadata, record) {

                        var pinyin=record.get('pinyin');
                        var iswebsite=record.get('iswebsite');
                        if (value.indexOf('add') > 1){
                            return '';
                        }
                        return value.replace('dest_','');
                    }

                },
                {
                    text: '<span class="grid_column_text">拼音</span>' + ST.Util.getGridHelp('destination_destination_pinyin'),
                    dataIndex: 'pinyin',
                    width: '9%',
                    align: 'left',
                    tdCls: 'dest-al-mid',
                    sortable: false,
                    menuDisabled:true,
                    editor: {
                        xtype: 'textfield', listeners: {
                            focus: function (ele, event) {

                            }
                        }
                    },
                    renderer: function (value, metadata, record) {
                        var id = record.get('id');
                        id = id.substr(id.indexOf('_') + 1);
                        if (id.indexOf('add') == -1) {
                            return "<span class='row-editable-sp'>" + value + "</span>";
                        }
                        return value;
                    }
                },
                {
                    text: '<span class="grid_column_text">显示</span>'+ST.Util.getGridHelp('destination_destination_isopen'),
                    dataIndex: 'isopen',
                    width: '9%',
                    xtype: 'actioncolumn',
                    tdCls: 'dest-al-mid',
                    align: 'center',
                    sortable: false,
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
                                togStatus(null, record, 'isopen');
                            }
                        }
                    ]
                },
                {

                    text: '<span class="grid_column_text">子站</span>'+ST.Util.getGridHelp('destination_destination_iswebsite'),
                    dataIndex: 'iswebsite',
                    width: '9%',
                    xtype: 'actioncolumn',
                    tdCls: 'dest-al-mid',
                    align: 'center',
                    sortable: false,
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
                                togSiteStatus(null, record, 'iswebsite');
                            }
                        }
                    ]
                },
                {
                   // text: '管理',
                    text: '<span class="grid_column_text">管理</span>',
                    dataIndex: 'id',
                    width: '9%',
                    tdCls: 'dest-al-mid',
                    align: 'center',
                    sortable: false,
                    menuDisabled:true,
                    renderer : function(value, metadata,record) {
                        var id=record.get('id');
                        var pinyin=record.get('pinyin');
                        var iswebsite=record.get('iswebsite');
                        if (id.indexOf('add') > 1)
                            return '';

                        var delhtml='<a href="javascript:;" class="btn-link" title="删除" onclick="delSingle(\''+id+'\',\''+iswebsite+'\')">删除</a>';
                        return delhtml;
                    }
                }
            ],
            plugins: [
                Ext.create('Ext.grid.plugin.CellEditing', {
                    clicksToEdit: 1,
                    listeners: {
                        beforeEdit:function(editor,e){
                            if(window.display_mode!=0 && e.field!='displayorder') //排除非主目的地的编辑
                                return false;
                        },
                        edit: function (editor, e) {

                            var pinyin = e.record.get('pinyin');
                            e.record.save({params: {field: e.field}});
                            e.record.commit();

                        }
                    }
                })
            ]
        });
    }
);

function setWeb(result,bool)
{

    var id=result.id;

    var record= dest_store.getNodeById(id);


    var weburl=result.weburl;
    Ext.Ajax.request({
        url: "destination/action/update",
        method: "POST",
        datatype: "JSON",
        params: {id: id, field: 'weburl', val: weburl, typeid: window.display_mode},
        success: function (response, opts) {
            if (response.responseText == 'ok') {
                ST.Util.showMsg('开启子站成功!','4',2000);
                togStatus(null,record,'iswebsite');
                addsite(id);//添加站点默认数据.
            }else if(response.responseText =='py_repeat'){
                ST.Util.showMsg('子站拼音重复,子站开启失败',5,1000);
            }else{
                ST.Util.showMsg('开启子站失败!','1',2000);
            }
        }
    });

}
//开关子站功能
function togSiteStatus(obj, record, field) {
    var val = record.get(field);
    var id = record.get('id');
    id = id.substr(id.indexOf('_') + 1);
    var pinyin = record.get('pinyin');
    var newval = val == 1 ? 0 : 1;
    if (newval == 1) {
        ST.Util.showBox('子站开启', SITEURL + 'destination/dialog_setweb?id=' + id + '&pinyin=' + pinyin, 580, 199, null, null, document, {
            loadWindow: window,
            loadCallback: setWeb
        });
    }
    else {
        ST.Util.confirmBox("提示", "关闭子站功能,当前子站的导航等配置将会删除，确认关闭？", function () {
            togStatus(obj, record, field);
            cancelSite(id);
        })
    }
}

function togStatus(obj, record, field) {
    var val = record.get(field);
    var id = record.get('id');
    id = id.substr(id.indexOf('_') + 1);
    var newval = val == 1 ? 0 : 1;
    Ext.Ajax.request({
        url: "destination/action/update",
        method: "POST",
        datatype: "JSON",
        params: {id: id, field: field, val: newval, typeid: window.display_mode},
        success: function (response, opts) {
            if (response.responseText == 'ok') {
                record.set(field, newval);
                record.commit();
            }
        }
    });
}

function setProductInfo(result,bool)
{
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
    record.save({extraParams:{typeid:typeid},callback: function (records, operation, success) {
        if (success) {
            ST.Util.showMsg("修改成功",4);
        }
        else {
            ST.Util.showMsg("修改失败",5);
        }
    }});

}
function destProductSet(dest_id) {
    var id = dest_id.substr(dest_id.indexOf('_') + 1);
    ST.Util.showBox('设置', SITEURL + 'destination/dialog_productinfo?id=' + id + "&typeid=" + display_mode, 600, 500, null, null, document, {
        loadCallback: setProductInfo,
        loadWindow: window
    });

}
function setInfo(result, bool) {
    if (!bool) {
        return;
    }
    var id = result.id;
    var record = dest_store.getNodeById(id);
    for (var i in result.data) {
        var val = result.data[i];
        record.set(i, val);
        record.commit();
    }
    record.save({
        callback: function (records, operation, success) {
            var hint = null;
            if (success) {
                for (var i in result.data) {

                    var val = result.data[i];

                    record.set(i, val);
                    record.commit();
                }
                ST.Util.showMsg("修改成功", 4);
            }
            else {
                ST.Util.showMsg("修改失败，可能是数据保存不成功或目的地拼音重复", 5, 5000);
            }

        }
    });
}

function addSub(pid) {
    var precord = pid == 0 ? window.dest_store.getRootNode() : window.dest_store.getNodeById(pid);
    var addnode = window.dest_store.getNodeById(pid + 'add');

    Ext.Ajax.request({
        method: 'post',
        url: 'destination/action/addsub',
        params: {pid: pid},
        success: function (response) {
            var newrecord = Ext.decode(response.responseText);
            var view_el = window.dest_treepanel.getView().getEl()
            var scroll_top = view_el.getScrollTop();
            precord.insertBefore(newrecord, addnode);
        }
    });
}


Ext.getBody().on('mouseup', function () {
    window.node_moving = false;

});
Ext.getBody().on('mousemove', function (e, t, eOpts) {

    if (window.node_moving == true) {
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
    if (data_height > height - 78)
        window.dest_treepanel.height = (height - 78);
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


    /* var check_arr=Ext.query('.dest_check[checked]');

     var del_btn=Ext.ComponentQuery.query("#dest_del_btn")[0];

     if(check_arr.length>0)
     {
     del_btn.enable();
     }
     else
     del_btn.disable();
     */

}
function chooseAll() {
    var check_cmp = Ext.query('.dest_check');
    for (var i in check_cmp) {
        if (!Ext.get(check_cmp[i]).getAttribute('checked'))
            check_cmp[i].click();
    }

    //  window.sel_model.selectAll();
}
function chooseDiff() {
    var check_cmp = Ext.query('.dest_check');
    for (var i in check_cmp)
        check_cmp[i].click();
    //var records=window.sel_model.getSelection();
    //window.sel_model.selectAll(true);

    //	window.sel_model.deselect(records,true);

    //var
}
function delDest() {

    var check_cmp = Ext.select('.dest_check:checked');
    if(check_cmp.getCount()==0)
    {
        ST.Util.showMsg("请选择至少一条数据",5);
        return;
    }
    var idArr=new Array();
    check_cmp.each(
        function (el, c, index) {
            idArr.push(el.getValue())
        }
    );
    if(!beforeDestroy(idArr)) {
        return false;
    }
    var Y=window;
    var d = parent.window.dialog({
        title: '提示',
        content: '<div class="confirm-box center">确定删除？</div>',
        cancelValue: '取消',
        okValue: '确定',
        width: 250,
        ok: function () {
            check_cmp.each(
                function (el, c, index) {
                    Y.dest_store.getNodeById(el.getValue()).destroy();
                }
            );
        },
        cancel: function () {

        }
    });
    d.showModal();
}

function beforeDestroy(idArr) {
    var bool = true;
    $.ajax({
        async: false,
        type: 'POST',
        url: SITEURL + "destination/ajax_before_destroy",
        data: {id: idArr},
        dataType: 'json',
        success: function (data) {
            if (!data.status) {
                var d = parent.window.dialog({
                    title: '提示',
                    content: '<div class="confirm-box center">存在下级,请先删除</div>',
                    okValue: '确定',
                    width: 250,
                    ok: function () {
                    }
                });
                d.showModal();
                bool = false;
            }
        }
    });
    return bool;
}

function searchDest() {

    var s_str = Ext.get('searchkey').getValue();
    s_str = s_str.replace(/\s/g,"");
    s_str = s_str.replace(/[\r\n]/g, "");
    Ext.select('.search-dest-tr').removeCls('search-dest-tr');

    if (!s_str){
        ST.Util.showMsg('未找到相关目的地',5,1000);
        return;
    }

    Ext.Ajax.request({
        url: 'destination/action/search',
        params: {keyword: s_str},
        method: 'POST',
        success: function (response) {


            var text = response.responseText;
            if (text == 'no') {
                ST.Util.showMsg('未找到与'+s_str+'相关的目的地',5,1000);
                //Ext.Msg.alert('查询结果', "未找到与'" + s_str + "'相关的目的地");
            } else {
                var list = Ext.decode(text);
                var index = 0;
                for (var i in list) {

                    var dest = list[i];
                    cascadeDest(dest, index);
                    index++;
                }
            }
            // process server response here
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

//删除目的地
function delSingle(id, iswebsite) {

    if (iswebsite == 1) {
        ST.Util.showMsg('当前目的地已经设置成子站,不能删除!', 5);
        return;
    }
    id = id.substr(id.indexOf('_') + 1);
    var idArr = new Array(id.toString());
    if (!beforeDestroy(idArr)) {
        return false;
    }
    var Y=window;
    var d = parent.window.dialog({
        title: '提示',
        content: '<div class="confirm-box center">确定删除？</div>',
        cancelValue: '取消',
        okValue: '确定',
        width: 250,
        ok: function () {
            window.dest_store.getById(id.toString()).destroy();
        },
        cancel: function () {

        }
    });
    d.showModal();

}
//取消子站
function cancelSite(id)
{
    var siteid = id.substr(id.indexOf('_') + 1);
    $.post(SITEURL+'destination/cancelsite/siteid/'+siteid);
}
//设置站点成功,添加默认导航数据
function addsite(siteid)
{
    $.post(SITEURL+'destination/addsite/siteid/'+siteid);
}

</script>
</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.0706&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
