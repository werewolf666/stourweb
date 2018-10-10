<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>文件浏览器-思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,plist.css,base_new.css'); }
    {php echo Common::getScript("jquery.upload.js"); }


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
                                <a href="javascript:;" class="refresh-btn" onclick="window.location.reload()">刷新</a>

                        </tr>
                    </table>
                </div>
            </div>
            <div id="line_grid_panel" class="content-nrt">


                <div id="attr_tree_panel" class="content-nrt">

                </div>
			</div>


        </td>
    </tr>
</table>
<script>

var ismobile = "{$ismobile}";

Ext.onReady(
    function () {
        //store

        var helpico = "{php echo Common::getIco('help');}";

        Ext.tip.QuickTipManager.init();
        window.attr_store = Ext.create('Ext.data.TreeStore', {
            fields: [

                'id',
                'text',
                'ext'
            ],

            proxy: {
                type: 'ajax',
                extraParams: {folder: '{$folder}', ismobile: ismobile},
                api: {
                    read: SITEURL + 'filemanager/index/action/read/'  //读取数据的URL
                },
                reader: 'json',
                listeners: {
                    metachange: function () {
                        alert('here');
                    }
                }
            },
            autoLoad: true,
            listeners: {
                sort: function (node, childNodes, eOpts) {

                }

            }

        });

        //树
        window.attr_treepanel = Ext.create('Ext.tree.Panel', {
            store: attr_store,
            rootVisible: false,
            padding: '2px',
            renderTo: 'attr_tree_panel',
            border: 0,
            style: 'margin-left:5px;border:0px;',
            width: "99%",
            bodyBorder: 0,
            bodyStyle: 'border-width:0px',
            // selModel:sel_model,
            autoScroll: true,

            listeners: {

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
                    text: '<span class="grid_column_text">文件名称</span>' + helpico,
                    dataIndex: 'text',
                    id: 'attr_name',
                    sortable: false,
                    menuDisabled:true,
                    locked: false,
                    width: '70%',
                    renderer: function (value, metadata, record) {
                        return value;
                    }
                },
                {
                    text: '<span class="grid_column_text">管理</span>' + helpico,
                    width: '30%',
                    sortable: false,
                    menuDisabled:true,
                    align: 'center',
                    tdCls: 'attr-al-mid',
                    renderer: function (value, metadata, record) {
                        var id = record.get('id');
                        var ext = record.get('ext');
                        var extarr = ['htm', 'html', 'php', 'css', 'js'];

                        //是否显示
                        if (id.indexOf('add') != -1 || $.inArray(ext, extarr) == -1) return '';

                        return '<a href="javascript:;" class="btn-link" onclick="pageedit(\'' + id + '\',\'' + ext + '\')">编辑</a>';
                    }
                }

            ]

        });

    }
);


Ext.EventManager.onWindowResize(function () {
    var height = Ext.dom.Element.getViewportHeight();
    var data_height = window.attr_treepanel.getView().getEl().down('.x-grid-table').getHeight();
    if (data_height > height - 100)
        window.attr_treepanel.height = (height - 100);
    else
        delete window.attr_treepanel.height;
    window.attr_treepanel.doLayout();
})


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

function getHelp(e) {
    if (e && e.stopPropagation)
    //因此它支持W3C的stopPropagation()方法 
        e.stopPropagation();
    else
    //否则，我们需要使用IE的方式来取消事件冒泡 
        window.event.cancelBubble = true;
}


//上传
function uploadFile(obj, path) {

    // 上传方法
    $.upload({
        // 上传地址
        url: SITEURL + 'uploader/uploadotherfile',
        // 文件域名字
        fileName: 'filedata',
        // 其他表单数据
        params: {path: path, ismobile: ismobile, version:1},
        // 上传完成后, 返回json, text
        dataType: 'json',
        // 上传之前回调,return true表示可继续上传
        onSend: function () {
            return true;
        },
        // 上传之后回调
        onComplate: function (data) {

            if (path.split('/').length > 1) {
                var precord = window.attr_store.getNodeById(path);
            }
            else {
                var precord = window.attr_store.getRootNode();
            }


            var addnode = window.attr_store.getNodeById(path + 'add');
            var newrecord = Ext.decode(data);
            precord.insertBefore(newrecord, addnode);

        }
    });


}

//页面编辑
function pageedit(id, ext) {

    var file = encodeURIComponent(id);
    var record = window.attr_store.getNodeById(id.toString());

    var title = record.get('text');
    var menuid = '{$menuid}';

    var boxurl = SITEURL + 'filemanager/pageedit';
    if (menuid != "") {
        boxurl += "/menuid/" + menuid;
    }
    boxurl += '?file=' + file + '&ext=' + ext + '&ismobile=' + ismobile;
    parent.window.addTab("编辑" + title, boxurl, 1);
    //ST.Util.showBox('页面编辑',boxurl,'900','600',function(){ });

}

</script>
</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.1402&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
