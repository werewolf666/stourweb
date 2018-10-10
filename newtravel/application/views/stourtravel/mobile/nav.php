<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,plist.css,base_new.css'); }

</head>
<body style="overflow:hidden">

    <table class="content-tab" body_script=xyvz8B >
        <tr>
            <td width="119px" class="content-lt-td" valign="top">
                {template 'stourtravel/public/leftnav'}
                <!--右侧内容区-->
            </td>
            <td valign="top" class="content-rt-td" style="overflow:hidden">
                <div></div>
                <div class="cfg-header-bar">
                    <a href="javascript:;" class="fr btn btn-primary radius mr-10 mt-6" onclick="window.location.reload()">刷新</a>
                </div>
                <div id="line_grid_panel" class="content-nrt">
                    <div id="attr_tree_panel" class="content-nrt">

                    </div>
                    <div class="st-bottom-console-bar clear clearfix">
                        <a class="btn btn-primary radius" href="javascript:;" onClick="chooseAll()">全选</a>
                        <a class="btn btn-primary radius ml-10" href="javascript:;" onClick="chooseDiff()">反选</a>
                        <a class="btn btn-primary radius ml-10" href="javascript:;" onClick="delattr()">删除</a>
                    </div>
                </div>
            </td>
        </tr>
    </table>

<script>

var webid=0;
Ext.onReady(
    function () {
        //store
        $(".isattr").addClass('on');


        Ext.tip.QuickTipManager.init();
        window.attr_store = Ext.create('Ext.data.TreeStore', {
            fields: [
                {name: 'm_displayorder',
                    sortType: sortTrans

                },
                {name: 'm_issystem',
                    sortType: sortTrans

                },
                {name: 'm_isopen',
                    sortType: sortTrans

                },
                'id',
                'm_title',
                'm_ico',
                'm_url',
                'default_ico'

            ],
            proxy: {
                type: 'ajax',
                extraParams: {typeid: window.display_mode},
                api: {
                    read: SITEURL+'mobile/nav/action/read',  //读取数据的URL
                    update:  SITEURL+'mobile/nav/action/save',
                    destroy:  SITEURL+'mobile/nav/action/delete'
                },
                reader: 'json'
            },
            remoteSort:false,
            autoLoad: true,
            listeners: {
                load:function( store, records, successful, eOpts )
                {

                    if(!successful){
                        ST.Util.showMsg("{__('norightmsg')}",5,1000);
                    }


                }
            }

        });

        //属性树
        window.attr_treepanel = Ext.create('Ext.tree.Panel', {
            store: attr_store,
            rootVisible: false,
            renderTo: 'attr_tree_panel',
            border: 0,
            style: 'margin-left:0px;border:0px;',
            width: "100%",
            bodyBorder: 0,
            bodyStyle: 'border-width:0px',
            // selModel:sel_model,
            scroll:'vertical', //只要垂直滚动条

            listeners: {
                itemmousedown: function (node, record, item, index, e, eOpts) {
                    var x = e.xy[0];
                    var column_x = Ext.getCmp('attr_name').getX();
                    var column_width = Ext.getCmp('attr_name').getWidth();

                    if (x < column_x || x > column_x + column_width)
                        return false;

                    window.node_moving = true;

                },
                sortchange: function (ct, column, direction, eOpts) {

                    window.sort_direction = direction;

                    var field = column.dataIndex;
                    if (field == 'kindname')
                        field = 'pinyin';
                    window.attr_store.sort(field, direction);

                },
                celldblclick: function (view, td, cellIndex, record, tr, rowIndex, e, eOpts) {

                    if (record.get('displayorder') == 'add')
                        return false;
                },
                afterlayout: function (panel) {
                    var data_height = panel.getView().getEl().down('.x-grid-table').getHeight();

                    var height = Ext.dom.Element.getViewportHeight();

                    // console.log(data_height+'---'+height);
                    if (data_height > height - 75) {
                        window.has_biged = true;
                        panel.height = height - 75;
                    }
                    else if (data_height < height - 75) {
                        if (window.has_biged) {
                            delete panel.height;
                            window.has_biged = false;
                            window.attr_treepanel.doLayout();
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
                    displayField: 'attrname'
                },

                listeners: {
                    boxready: function () {

                        var height = Ext.dom.Element.getViewportHeight();

                        this.up('treepanel').maxHeight = height - 75;
                        this.up('treepanel').doLayout();
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


                        if (dropPosition == 'append') {

                            var btn_node = window.attr_store.getNodeById(params['overid'] + 'add');
                            overModel.insertBefore(data.records[0], btn_node);

                        }

                        //alert(overModel.children);
                        Ext.Ajax.request({
                            url: SITEURL+'mobile/nav/action/drag/typeid/{$typeid}',
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
                    text: '选择',
                    width: '8%',
                    dataIndex: 'issel',
                    tdCls: 'attr-al-mid',
                    align: 'center',
                    draggable: false,
                    sortable:false,
                    menuDisabled:true,
                    renderer : function(value, metadata,record) {
                        var id=record.get('id');
                        var issystem=record.get('m_issystem');
                        if(issystem==1){
                            return '';
                        }
                        if(id.indexOf('add')==-1){
                            return "<input type='checkbox' class='attr_check' value='" + id + "' style='cursor:pointer'/>";
                        }



                    }

                },
                {
                    text: '<span class="grid_column_text">排序</span>',
                    dataIndex: 'm_displayorder',
                    width: '10%',
                    align:'center',
                    draggable: false,
                    cls:'sort-col',
                    menuDisabled:true,
                    renderer : function(value, metadata,record) {
                        var id=record.get('id');
                        if(value=='add')
                            return '';
                        else {
                            var newvalue=value;
                            if(value==9999||value==999999||!value)
                                newvalue='';
                            return "<input type='text' value='"+newvalue+"' class='row-edit-txt' onblur=\"updateField(this,'"+id+"','m_displayorder',0,'input')\" onclick='ST.Util.prevPopup(event,this)'/>";
                        }
                    }

                },
                {
                    xtype: 'treecolumn',   //有展开按钮的指定为treecolumn
                    text: '<span class="grid_column_text">导航名称</span>'+ST.Util.getGridHelp('mobile_nav_m_title'),
                    dataIndex: 'm_title',
                    id: 'attr_name',
                    sortable:false,
                    locked: false,
                    menuDisabled:true,
                    width: '27%',
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
                    renderer : function(value, metadata,record) {
                        var id=record.get('id');
                        if(id.indexOf('add')==-1)
                        {
                            return "<span class='row-editable-sp'>"+value+"</span>";
                               // "&nbsp;&nbsp;<font color='orange'>[id:"+id+"]</font>";

                        }
                        return value;
                    }
                },
                {

                    text: '<span class="grid_column_text">链接</span>'+ST.Util.getGridHelp('mobile_nav_m_url'),
                    width: '37%',
                    dataIndex:'m_url',
                    draggable: false,
                    sortable:false,
                    align:'left',
                    menuDisabled:true,
                    renderer : function(value, metadata,record) {
                        var id=record.get('id');
                        if(id.indexOf('add')!=-1)
                            return '';
                        var issystem=record.get('m_issystem');
                        if(issystem==1)
                            return value;

                        value=!value?'':value;
                        var html="<input type='text' value='"+value+"' class='row-edit-txt' onblur=\"updateField(this,'"+id+"','m_url',0,'input')\" style='text-align:left' onclick='ST.Util.prevPopup(event,this)'/>";
                        return html;
                    }
                },
                {

                    text: '<span class="grid_column_text">图标</span>'+ST.Util.getGridHelp('mobile_nav_m_ico'),
                    width: '10%',
                    dataIndex:'m_ico',
                    draggable: false,
                    sortable:false,
                    align:'center',
                    menuDisabled:true,
                    renderer : function(value, metadata,record) {
                        var id = record.get('id');
                        var default_ico = record.get('default_ico');
                        if(id.indexOf('add') > -1)
                        {
                            return '';
                        }
                        var str = value ? '<img src="'+value+'" width="25" height="25">': ' <img src="'+default_ico+'" width="25" height="25">';
                        var html = '<a id="icon_'+id+'" href="javascript:;" data-id="'+id+'" class="set-icon">'+str+'</a>';
                        return html;
                    }

                },
                {

                    text: '<span class="grid_column_text">显示</span>'+ST.Util.getGridHelp('mobile_nav_m_isopen'),
                    width: '10%',
                    dataIndex:'m_isopen',
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

                                togStatus(null, record, 'm_isopen');


                            }
                        }
                    ]


                }
            ],
            plugins: [
                Ext.create('Ext.grid.plugin.CellEditing', {
                    clicksToEdit: 1,
                    listeners: {
                        edit: function (editor, e) {

                            e.record.commit();
                            e.record.save({params: {field: e.field}});
                        }
                        ,
                        beforeEdit:function(editor,e){
                            var id=e.record.get('id');
                            if(id.indexOf('add')!=-1)
                                return false;
                        }
                    }
                })
            ]
        });


    }
);




Ext.getBody().on('mouseup', function () {
    window.node_moving = false;
});
Ext.getBody().on('mousemove', function (e, t, eOpts) {

    if (window.node_moving == true) {
        // console.log('mov_'+window.node_moving);

        var tree_view = window.attr_treepanel.down('treeview');
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
    var data_height = window.attr_treepanel.getView().getEl().down('.x-grid-table').getHeight();
    if (data_height > height - 75)
        window.attr_treepanel.height = (height - 75);
    else
        delete window.attr_treepanel.height;
    window.attr_treepanel.doLayout();
})
function togWeb(ele)
{
    webid=$(ele).val();
    attr_store.load({params:{webid:webid}});

}

function togStatus(obj, record, field) {
    var val = record.get(field);
    var id = record.get('id');
    id = id.substr(id.indexOf('_') + 1);
    var newval = val == 1 ? 0 : 1;
    Ext.Ajax.request({
        url: SITEURL+'mobile/nav/action/update',
        method: "POST",
        datatype: "JSON",
        params: {id: id, field: field, val: newval, typeid: window.display_mode},
        success: function (response, opts) {
            console.info(response,11);
            if (response.responseText == 'ok') {
                record.set(field, newval);
                record.commit();
            }
        }});

}

function cascadeattr(attr, index) {
    if (attr.length == 1) {
        var node = window.attr_store.getNodeById(attr[0]);
        var ele = window.attr_treepanel.getView().getNode(node);
        if (ele) {

            var edom = Ext.get(ele);
            edom.addCls('search-attr-tr');
            if (index == 0)
                viewScroll(edom);
        }
    }
    else {
        var node = window.attr_store.getNodeById(attr[0]);
        attr.shift();
        node.expand(false, function () {
            cascadeattr(attr, index);
        });

    }
}
function viewScroll(extdom)   //在treeview里滚动
{
    var tree_view = window.attr_treepanel.getView();
    var view_y = tree_view.getY();
    var dom_y = extdom.getY();


    window.setTimeout(function () {
        window.first_scroll = true;
        extdom.scrollIntoView(tree_view.getEl());
    }, 450);
    //else
    // extdom.scrollIntoView(tree_view.getEl());


}

function chooseAll() {
    var check_cmp = Ext.query('.attr_check');
    for (var i in check_cmp) {
        if (!Ext.get(check_cmp[i]).getAttribute('checked'))
            check_cmp[i].click();
    }

    //  window.sel_model.selectAll();
}
function chooseDiff() {
    var check_cmp = Ext.query('.attr_check');
    for (var i in check_cmp)
        check_cmp[i].click();
    //var records=window.sel_model.getSelection();
    //window.sel_model.selectAll(true);

    //	window.sel_model.deselect(records,true);

    //var
}
function delattr() {
    var check_cmp = Ext.select('.attr_check:checked');
    if(check_cmp.getCount()==0)
    {
        ST.Util.showMsg("请选择至少一条数据",5);
        return;
    }
    ST.Util.confirmBox("提示","确定删除？",function(){

        check_cmp.each(
            function (el, c, index) {
                // alert(el.getValue());
                //  window.attr_store.getNodeById(el.getValue().toString()).destroy();
                // window.attr_store.

                var id=el.getValue();
                var node=window.attr_store.getNodeById(id);
                node.destroy();


            }
        );
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
        if (val == 'add'){
            if (window.sort_direction == 'ASC')
                return 10000000000000;
            else
                return -10;
        }
        else
            return window.parseInt(val);
    }
    // alert(val);
}

function addSub(pid) {
    var precord = pid == 0 ? window.attr_store.getRootNode() : window.attr_store.getNodeById(pid);
    var addnode = window.attr_store.getNodeById(pid + 'add');

    Ext.Ajax.request({
        method: 'post',
        url: SITEURL+'mobile/nav/action/addsub/typeid/{$typeid}',
        params: {pid: pid,webid:webid},
        success: function (response) {

            var newrecord = Ext.decode(response.responseText);
            newrecord.leaf=true;

            var view_el = window.attr_treepanel.getView().getEl()
            var scroll_top = view_el.getScrollTop();
            precord.insertBefore(newrecord, addnode);
            //view_el.scroll('t',scroll_top);
        }
    });

}
function updateField(ele,id,field,value,type,callback)
{
    var record=window.attr_store.getNodeById(id.toString());
    if(type=='select' || type=='input')
    {
        value=Ext.get(ele).getValue();
    }


    Ext.Ajax.request({
        url   :  SITEURL+"mobile/nav/action/update/typeid/{$typeid}",
        method  :  "POST",
        datatype  :  "JSON",
        params:{id:id,field:field,val:value},
        success  :  function(response, opts)
        {
            if(field=='m_displayorder' && (!value||value==''))
            {
                value='9999';
            }
            record.set(field,value);
            record.commit();
            if(typeof(callback)=='function')
            {
                callback(record);
            }
        }});

}

function stopDef(e)
{
    if (e && e.stopPropagation)
    //因此它支持W3C的stopPropagation()方法
        e.stopPropagation();
    else
    //否则，我们需要使用IE的方式来取消事件冒泡
        window.event.cancelBubble = true;
}
function seoShow(id,navname,issystem)
{
    if(issystem)
    {
        var url = SITEURL+'config/seoinfo/id/'+id;
        ST.Util.showBox(navname+'-优化设置',url,650,450,null,null,document,{loadWindow:window,loadCallback: seoFinish});
    }

}
function seoFinish(result,bool)
{
    var id=result.id;
    var node=attr_store.getNodeById(id);
    node.set('isfinishseo',result.isfinish);
}

$(function(){
    // 设置图标
    $(document).on('click',".set-icon", function(){
        var id = $(this).data('id');
        var dom_id = $(this).attr('id');
        var boxurl = SITEURL + 'mobile/dialog_ico/id/' + id;

        var params = {
            loadWindow:     window,
            loadCallback:   set_icon
        };
        ST.Util.showBox('导航ICO配置', boxurl,260,225,null,null,document,params);
    });


});
function set_icon(data, flag)
{

    if( data.src )
    {
        str =  '<img src="'+data.src+'" width="25" height="25" >';
    }
    else
    {
        var str = '<img src="'+data.default_ico+'" width="25" height="25" >';
    }
    $("#icon_"+data.id).html(str);
    updateField(null, data.id, 'm_ico', data.src);
}
</script>
</body>
</html>
