<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,plist.css,base_new.css'); }
    {php echo Common::getCss('ext-theme-neptune-all-debug.css','js/extjs/resources/ext-theme-neptune/'); }
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js"); }
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
            <div class="list-top-set">
                <div class="list-web-pad"></div>
                <div class="list-web-ct">
                    <table class="list-head-tb">
                        <tr>
                            <td class="head-td-lt">
                            </td>
                            <td class="head-td-rt">
                                <a href="javascript:;" class="btn btn-primary radius" onclick="window.location.reload()">刷新</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div id="line_grid_panel" class="content-nrt">
                <div id="attr_tree_panel" class="content-nrt">
                </div>
                <div class="panel_bar">
                </div>
            </div>
        </td>
    </tr>
</table>
<script>

var menu_list=<?php echo json_encode($list);  ?>;
var roleid={$roleid};
Ext.onReady(
    function () {
        //目的地store

        $(".w-set-tit").find('#tb_carattr').addClass('on');

        Ext.tip.QuickTipManager.init();

        Ext.define('MyModel', {
            extend: 'Ext.data.TreeModel',
            requires: ['Ext.data.SequentialIdGenerator'],
            fields: [
                'id',
                'key',
                'text',
                'isparent',
                'right'

            ]
        });
        window.attr_store = Ext.create('Ext.data.TreeStore', {
            model:'MyModel',
            proxy: {
                type: 'ajax',
                extraParams: {typeid: window.display_mode},
                api: {
                    read: SITEURL+'user/setright/action/read/roleid/'+roleid,  //读取数据的URL
                    update:  '',
                    destroy:  ''
                },
                reader: 'json'
            },
            autoLoad: true

        });

        //panel
        window.attr_treepanel = Ext.create('Ext.tree.Panel', {
            store: attr_store,
            rootVisible: false,
            renderTo: 'attr_tree_panel',
            border: 0,
            style: 'border:0px;',
            width: "100%",
            bodyBorder: 0,
            bodyStyle: 'border-width:0px',
            scroll:'vertical',


            listeners: {


                celldblclick: function (view, td, cellIndex, record, tr, rowIndex, e, eOpts) {

                    if (record.get('displayorder') == 'add')
                        return false;
                },
                afterlayout: function (panel) {
                    var data_height = panel.getView().getEl().down('.x-grid-table').getHeight();

                    var height = Ext.dom.Element.getViewportHeight();

                    // console.log(data_height+'---'+height);
                    if (data_height > height - 100) {
                        window.has_biged = true;
                        panel.height = height - 100;
                    }
                    else if (data_height < height - 100) {
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
                listeners: {
                    boxready: function () {

                        var height = Ext.dom.Element.getViewportHeight();
                        this.up('treepanel').maxHeight = height - 100;
                        this.up('treepanel').doLayout();
                    }


                }

            },
            columns: [
                {
                    xtype: 'treecolumn',   //有展开按钮的指定为treecolumn
                    text: '<span class="grid_column_text">模块名称</span>',
                    dataIndex: 'text',
                    id: 'attr_name',
                    sortable:false,
                    locked: false,
                    width: '25%',
					  menuDisabled:true,
                   renderer : function(value, metadata,record) {
                          return value;
                    }
                },
                {
                    text: '<span class="grid_column_text">管理权限</span>',
                    width:'75%',
                    // xtype:'templatecolumn',
                    align:'center',
                    border:0,
                    dataIndex:'right',
					  menuDisabled:true,
                    renderer : function(value, metadata,record) {
                           var id=record.get('id');
                           var is_checked=value==1?"checked='checked'":'';
                           var isparent=record.get('isparent');
                           var cls=isparent==1?'pck':'';

                           var mstr=isparent==1?'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;':'';
                           return "<input type='checkbox' class='"+cls+"' "+is_checked+" onclick=\"updateField(this,'"+id+"','right',0,'checkbox','"+isparent+"')\" />"+mstr;
                    }


                },

            ],
            plugins: [
                Ext.create('Ext.grid.plugin.CellEditing', {
                    clicksToEdit: 2,
                    listeners: {
                        edit: function (editor, e) {

                            e.record.commit();
                            e.record.save({params: {field: e.field}});

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
    if (data_height > height - 100)
        window.attr_treepanel.height = (height - 100);
    else
        delete window.attr_treepanel.height;
    window.attr_treepanel.doLayout();
})



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

function updateField(ele,id,field,value,type,isparent)
{
    var record=window.attr_store.getNodeById(id);

    if(type=='checkbox')
    {
        var key=record.get('id');
        var extdom=Ext.get(ele);
        if(extdom.is(':checked'))
        {
            record.set(field,1);
            record.commit();
            setField(field,key,1);
        }
        else
        {
            record.set(field,0);
            setField(field,key,0);
        }


    }
    return true;
}
function setField(field,menuid,value)
{
    Ext.Ajax.request({
        url   :  SITEURL+"user/setright/action/update",
        method  :  "POST",
        datatype  :  "JSON",
        params:{field:field,menuid:menuid,value:value,roleid:roleid},
        success  :  function(response, opts)
        {

        }});
    return true;
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

function rowChoose(ele,id)
{
    var field_arr=['slook','smodify','sadd','sdelete'];
    var record=window.attr_store.getNodeById(id);
    var key=record.get('key');

    for(var i in field_arr)
    {
        var field=field_arr[i];
        record.set(field,1);
        setField(field,key,1);
    }
}
function rowChooseDiff(ele,id)
{
    var field_arr=['slook','smodify','sadd','sdelete'];
    var record=window.attr_store.getNodeById(id);
    var key=record.get('key');

    for(var i in field_arr)
    {
        var field=field_arr[i];
        var value=record.get(field);
        value=value==1?0:1;
        record.set(field,value);
        setField(field,key,value);
    }

}



</script>
</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.0.201710.2607&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
