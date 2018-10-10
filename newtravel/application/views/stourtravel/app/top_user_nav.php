<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>顶部自定义导航管理-思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,plist.css,base_new.css'); }
    {php echo Common::getScript('config.js,jquery.colorpicker.js');}

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
                    <div class="cfg-header-tab">
                        <span class="item on" data-contain="navigation">自定义导航</span>
                        <span class="item" data-contain="open">功能开关</span>
                    </div>
                    <a href="javascript:;" class="fr btn btn-primary radius w60 size-MINI mr-10 mt-6" onclick="window.location.reload()">刷新</a>
                </div>
                
                
                <div id="navigation" class="div_container">
                    <div id="line_grid_panel" class="content-nrt">
                        <div id="attr_tree_panel" class="content-nrt">

                        </div>
                        <div class="st-bottom-console-bar clear clearfix">
                            <a class="btn btn-primary radius" href="javascript:;" onClick="chooseAll()">全选</a>
                            <a class="btn btn-primary radius ml-10" href="javascript:;" onClick="chooseDiff()">反选</a>
                            <a class="btn btn-primary radius ml-10" href="javascript:;" onClick="delattr()">删除</a>
                        </div>
                    </div>
                </div>
                <div id="open" class="div_container hide">
                    <form id="configfrm">
                        <div class="w-set-nr">
                            <ul class="info-item-block">
                                <li class="rowElem">
                                    <span class="item-hd">自定义导航{Common::get_help_icon('cfg_usernav_open')}：</span>

                                    <div class="item-bd">
                                        <label class="radio-label"><input type="radio" name="cfg_usernav_open" value="1" {if $config['value']=='1'}checked{/if}>开启</label>
                                        <label class="radio-label ml-20"><input type="radio" name="cfg_usernav_open" value="0" {if $config['value']=='0'}checked{/if}>关闭</label>
                                    </div>
                                </li>
                            </ul>
                            <div class="clear clearfix mt-5">
                                <input type="hidden" name="webid" id="webid" value="0">
                                <a class="btn btn-primary radius size-L ml-115" href="javascript:;" id="btn_save">保存</a>
                            </div>
                        </div>
                    </form>
                </div>
            </td>
        </tr>
    </table>

<script>
    $('.cfg-header-tab').find('span').click(function(){
        var cdiv = $(this).attr('data-contain');
        $(this).addClass('on').siblings().removeClass('on');

        $("#"+cdiv).removeClass('hide').siblings('.div_container').addClass('hide');

    });
    //配置信息保存
    $("#btn_save").click(function () {
        Config.saveConfig(0);
    });
Ext.onReady(
    function () {
        var editico = "{php echo Common::getIco('edit');}";
        var helpico = "{php echo Common::getIco('help');}";


        Ext.tip.QuickTipManager.init();
        window.attr_store = Ext.create('Ext.data.TreeStore', {
            fields: [
                {name: 'displayorder',
                    sortType: sortTrans

                },

                {name: 'isopen',
                    sortType: sortTrans

                },
                'id',
                'pid',
                'kindname',
                'remark',
                'litpic',
                'linkurl',
                'color'

            ],
            proxy: {
                type: 'ajax',
                api: {
                    read: SITEURL + 'app/topusernav/action/read/',  //读取数据的URL
                    update: SITEURL + 'app/topusernav/action/save/',
                    destroy: SITEURL + 'app/topusernav/action/delete/'
                },
                reader: 'json'
            },
            autoLoad: false,
            listeners:{
                load:function( store, records, successful, eOpts )
                {
                    if(!successful){
                        ST.Util.showMsg("{__('norightmsg')}",5,1000);
                    }

                }
            }




        });

        //树
        window.attr_treepanel = Ext.create('Ext.tree.Panel', {
            store: attr_store,
            rootVisible: false,
            renderTo: 'attr_tree_panel',
            border: 0,
            style: 'margin-left:0px;border:0px;',
            width: "100%",
            bodyBorder: 0,
            bodyStyle: 'border-width:0px',
            scroll:'vertical',

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
                    if (data_height > height - 78) {
                        window.has_biged = true;
                        panel.height = height - 78;
                    }
                    else if (data_height < height - 78) {
                        if (window.has_biged) {
                            delete panel.height;
                            window.has_biged = false;
                            window.attr_treepanel.doLayout();
                        }
                    }
                    //颜色选择器
                    $(".title_color").colorpicker({
                        ishex:true,
                        success:function(o,color){
                            $(o).val(color);
                            var id = $(o).attr('data-id');
                            var val = color;
                            updateField(null, id, 'color', val);
                        },
                        reset:function(o){
                            $(o).val('');
                            var id = $(o).attr('data-id');
                            var val = '';
                            updateField(null, id, 'color', '');
                        }
                    });

                }
            },
            viewConfig: {
                forceFit: true,
                border: 0,
                plugins: {
                    ptype: 'treeviewdragdrop',
                    enableDrag: true,
                    enableDrop: true,
                    displayField: 'cityname'
                },

                listeners: {
                    boxready: function () {

                        var height = Ext.dom.Element.getViewportHeight();

                        this.up('treepanel').maxHeight = height - 78;
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
                            url: SITEURL + 'startplace/index/action/drag/typeid/{$typeid}',
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
                    text: '<span class="grid_column_text">选择</span>',
                    width: '6%',
                    dataIndex: 'issel',
                    tdCls: 'attr-al-mid',
                    align: 'center',
                    draggable: false,
                    sortable: false,
                    menuDisabled:true,
                    renderer: function (value, metadata, record) {
                        var id = record.get('id');

                        if (id.indexOf('add') == -1)
                            return "<input type='checkbox' class='attr_check' value='" + id + "' style='cursor:pointer'/>";
                    }


                },
                {

                    text: '<span class="grid_column_text">排序</span>'+ST.Util.getGridHelp('app_topusernav_displayorder'),
                    dataIndex: 'displayorder',
                    //  tdCls:'attr-al-mid',
                    width: '6%',
                    align:'center',
                    cls:'sort-col',
                    draggable: false,
                    menuDisabled:true,
                    renderer: function (value, metadata, record) {
                        var id = record.get('id');
                        if (id.indexOf("add") > 0)
                            return '';
                        else {
                            if (value == '9999' || value == '999999' || !value)
                                value = '';
                            return "<input type='text' class='row-edit-txt' onclick=\"ST.Util.prevPopup(event,this)\" onblur=\"updateField(this," + id + ",'displayorder',0,'input')\" value='" + value + "'/>";
                        }
                    }

                },
                {
                    xtype: 'treecolumn',   //有展开按钮的指定为treecolumn
                    text: '<span class="grid_column_text">自定义导航名</span>'+ST.Util.getGridHelp('app_topusernav_kindname'),
                    dataIndex: 'kindname',
                    id: 'attr_name',
                    sortable: false,
                    locked: false,
                    width: '22%',
                    menuDisabled:true,
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
                    renderer: function (value, metadata, record) {

                        var id=record.get('id');
                       // id = id.substr(id.indexOf('_') + 1);
                        if(id.indexOf('add')==-1)
                        {
                            //var editHtml="<input type='text' class='row-edit-txt' value='"+value+"'  />";
                            return "<span class='row-editable-sp'>"+value+"</span>";

                        }
                        return value;
                    }
                },
                /*{
                    text: '<span class="grid_column_text">说明</span>'+ST.Util.getGridHelp('app_topusernav_remark'),
                    dataIndex: 'remark',
                    width: '17%',
                    draggable: false,
                    menuDisabled:true,
                    sortable:false,
                    renderer: function (value, metadata, record) {
                        var id=record.get('id');
                        if (id&&id.indexOf('add')!=-1)
                            return '';

                        value=!value?'':value;
                        return "<input type='text' class='row-edit-txt' style='text-align:left' onclick=\"ST.Util.prevPopup(event,this)\" onblur=\"updateField(this," + id + ",'remark',0,'input')\" value='" + value + "'/>";
                    }

                },*/
                {

                    text: '<span class="grid_column_text">图标</span>'+ST.Util.getGridHelp('app_topusernav_litpic'),
                    dataIndex: 'litpic',
                    width: '17%',
                    align: 'left',
                    border: 0,
                    sortable: false,
                    draggable: false,
                    menuDisabled:true,
                    renderer: function (value, metadata, record) {
                        var id = record.get('id');
                        if(value=='add'){
                            return '';
                        }else{
                            var imgHtml=value?"<img style='vertical-align: bottom;' src="+value+" width='20' height='20' border='0'/>":'';
                            var del_hide= imgHtml?'':'display:none';
                            var str = value ? '替换' : '<a class="btn btn-primary radius size-S event_file_upload" data="{title:\'上传图片\',callback:\'upload_img\',id:'+id+'}">上传图片</a>';
                            var html="<div>";
                            html+="<span style='float:left; width:30px;' id='record_src_"+id+"'>"+imgHtml;
                            html+="</span><div style='float:left;'><div id='file_upload_"+id+"' class='btn-file mt-4'><a href='javascript:void(0);' data=\"{title:\'上传图片\',callback:\'upload_img\',id:"+id+"}\" class='event_file_upload'>"+str+"</a><a href='javascript:void(0);' style='padding: 10px;"+del_hide+"'  onclick=\"del(" + id + ")\">删除</a></div></div></div>";
                            return html;
                        }
                        
                    }

                },
                {
                    text: '<span class="grid_column_text">链接地址</span>'+ST.Util.getGridHelp('app_topusernav_linkurl'),
                    dataIndex: 'linkurl',
                    width: '15%',
                    draggable: false,
                    menuDisabled:true,
                    sortable:false,
                    renderer: function (value, metadata, record) {
                        var id = record.get('id');
                        if (id && id.indexOf('add') != -1) {
                            return '';
                        } else {
                            value = value ? value : '';
                            return "<input type='text' class='row-edit-txt' style='text-align:left' onclick=\"ST.Util.prevPopup(event,this)\" onblur=\"updateField(this," + id + ",'linkurl',0,'input')\" value='" + value + "'/>";
                        }

                    }

                },
                {
                    text: '<span class="grid_column_text">颜色</span>'+ST.Util.getGridHelp('app_topusernav_color'),
                    dataIndex: 'color',
                    width: '13%',
                    draggable: false,
                    align:'center',
                    sortable:false,
                    menuDisabled:true,
                    renderer: function (value, metadata, record) {
                        var id = record.get('id');
                        if (id.indexOf('add') != -1){
                            return '';
                        }
                        var style = value!=''? "color:"+value : '';
                        value=value?value:'';
                        return "<input class='row-edit-txt title_color' type='text' data-id="+record.get('id')+" value='"+value+"' style='width:70px;"+style+"'>";
                    }


                },

                {
                    text: '<span class="grid_column_text">显示</span>'+ST.Util.getGridHelp('app_topusernav_isopen'),
                    dataIndex: 'isopen',
                    width: '8%',
                    xtype: 'actioncolumn',
                    tdCls: 'attr-al-mid',
                    sortable: false,
                    align: 'center',
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
                                // alert(itm);
                                var id = record.get('id');
                                var val = record.get('isopen');
                                var newval = val == 1 ? 0 : 1;
                                updateField(null, id, 'isopen', newval);


                            }
                        }
                    ],
                    renderer: function (value, metadata, record) {
                        var id = record.get('id');
                        if (id.indexOf('add') != -1)
                            return '';
                    }
                },
                {
                    text: '<span class="grid_column_text">删除</span>',
                    width: '13%',
                    tdCls: 'attr-al-mid',
                    align:'center',
                    menuDisabled:true,
                    sortable:false,
                    renderer: function (value, metadata, record) {
                        var id = record.get('id');
                        var pid = record.get('pid');
                        if (id.indexOf('add') != -1)
                            return '';
                        return '<a href="javascript:;" class="btn-link" onclick="delS(' + id + ')">删除</a>';
                    }
                }
            ],
            plugins: [
                Ext.create('Ext.grid.plugin.CellEditing', {
                    clicksToEdit:1,
                    listeners: {
                        edit: function (editor, e) {

                            e.record.commit();
                            e.record.save({params: {field: e.field}});

                        },
                        beforeEdit:function(editor,e){
                            var id=e.record.get('id');
                            if(id.indexOf('add')!=-1)
                               return false;
                           if(e.field=='color'){

                           }
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
    if (data_height > height - 78)
        window.attr_treepanel.height = (height - 78);
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

    function beforeDestroy(idArr) {
        var bool = true;
        $.ajax({
            async: false,
            type: 'POST',
            url: SITEURL + "app/ajax_before_delete",
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



function delattr() {

    var check_cmp = Ext.select('.attr_check:checked');
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
                    Y.attr_store.getNodeById(el.getValue()).destroy();
                }
            );
        },
        cancel: function () {

        }
    });
    d.showModal();
}
function delS(id) {
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
            var node = window.attr_store.getNodeById(id.toString());
            node.destroy();
        },
        cancel: function () {

        }
    });
    d.showModal();
}





function sortTrans(val) {
    if (!window.sort_direction)
        return window.parseInt(val);
    else {
        if (val == 'add') {
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
        url: SITEURL + 'app/topusernav/action/addsub/',
        params: {pid: pid},
        success: function (response) {
            var newrecord = Ext.decode(response.responseText);
           
            var view_el = window.attr_treepanel.getView().getEl()
            var scroll_top = view_el.getScrollTop();
            precord.insertBefore(newrecord, addnode);
        }
    });

}
function updateField(ele, id, field, value, type) {
    var record = window.attr_store.getNodeById(id.toString());
    if (type == 'select'||type=='input') {
        value = Ext.get(ele).getValue();
    }
    if(field == 'linkurl'){

        if(value.indexOf('http')=== -1){
            value = 'http://'+value;
        }
    }



    Ext.Ajax.request({
        url: SITEURL + "app/topusernav/action/update/",
        method: "POST",
        datatype: "JSON",
        params: {id: id, field: field, val: value},
        success: function (response, opts) {
            //  alert(value);
            record.set(field, value);
            record.commit();

        }});

}
//图标上传
$('.event_file_upload').live('click', function () {
    var obj = eval('(' + $(this).attr('data') + ')');
    ST.Util.showBox(obj.title, SITEURL + 'image/insert_view', 0, 0, null, null, parent.document, {
        loadWindow: window,
        loadCallback: eval('(' + obj.callback + ')')
    });
    function upload_img(result, bool) {
        if (bool) {
            var src = result['data'][0].replace(/\$\$.*?$/, '');
            var val = "<img style='vertical-align: bottom;' src=" + obj.bigpic + " width='20'  height='20' border='0'/>"
            $('#record_src_' + obj.id).find('img').remove();
            $('#record_src_' + obj.id).append("<img style='vertical-align: bottom;' src=" + src + " width='20'  height='20' border='0'/>");
            //修改数据库
            updateField(null, obj.id, 'litpic', src);
        }
    }
});
function del(id) {
    updateField(null, id, 'litpic', '');
}

</script>
</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.1306&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
