<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>汇率管理-思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,plist.css'); }
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js,jquery.buttonbox.js,choose.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }

</head>
<body style="overflow:hidden">
<table class="content-tab">
    <tr>
        <td width="119px" class="content-lt-td"  valign="top">
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
                                {loop $kindmenu $menu}
                                 <a href="javascript:;" class="menu-shortcut" onclick="ST.Util.addTab('{$menu['name']}','{$menu['url']}',1);">{$menu['name']}</a>
                                {/loop}
                            </td>
                            <td class="head-td-rt">
                                <a href="javascript:;" class="refresh-btn" onclick="window.location.reload()">刷新</a>
                               </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div id="product_grid_panel" class="content-nrt">
            </div>
        </td>
    </tr>
</table>
<script>
    window.display_mode=1;
    window.current_code='';
    Ext.onReady(
        function()
        {
            Ext.tip.QuickTipManager.init();
            $(".btnbox").buttonBox();
            $("#searchkey").focusEffect();
            //添加按钮
            $("#addbtn").click(function(){
                ST.Util.addTab('添加线路','{$cmsurl}currency/add/parentkey/product/itemid/1',0);
            });
            //线路store
            window.product_store=Ext.create('Ext.data.Store',{

                fields:[
                    'id',
                    'countryname',
                    'chinesename',
                    'englishname',
                    'code',
                    'symbol',
                    'isopen',
                    'ratio'
                ],

                proxy:{
                    type:'ajax',
                    api: {
                        read: SITEURL+'currency/list/action/read',  //读取数据的URL
                        update:SITEURL+'currency/list/action/save',
                        destroy:SITEURL+'currency/list/action/delete'
                    },
                    reader:{
                        type: 'json',   //获取数据的格式
                        root: 'lists',
                        totalProperty: 'total'
                    }
                },
                remoteSort:true,
                pageSize:30,
                autoLoad:true,
                listeners: {
                    load: function (store, records, successful, eOpts) {
                        if (!successful) {
                            ST.Util.showMsg("{__('norightmsg')}", 5, 1000);
                            return;
                        }
                    }
                }

            });

            //线路列表框
            window.product_grid=Ext.create('Ext.grid.Panel',{
                store:product_store,
                renderTo:'product_grid_panel',
                border:0,
                bodyBorder:0,
                bodyStyle:'border-width:0px',
                scroll:'vertical',
                bbar: Ext.create('Ext.toolbar.Toolbar', {
                    store: product_store,  //这个和grid用的store一样
                    displayInfo: true,
                    emptyMsg: "没有数据了",
                    items:[
                        {
                            xtype:'panel',
                            id:'listPagePanel',
                            html:'<div id="line_page"></div>'
                        }
                    ],
                    listeners: {
                        single: true,
                        render: function(bar) {
                            var items = this.items;
                            //	bar.down('tbfill').hide();

                        }
                    }
                }),
                columns:[
                    {
                        text:'国家名称',
                        width:'20%',
                        dataIndex:'countryname',
                        align:'left',
                        menuDisabled:true,
                        border:0,
                        sortable:false,
                        renderer : function(value, metadata,record) {
                           return value;
                        }
                    }
                    ,
                    {
                        text: '货币中文名',
                        width: '20%',
                        align: 'center',
                        dataIndex:'chinesename',
                        border: 0,
                        menuDisabled:true,
                        cls: 'mod-1 sort-col',
                        sortable: true,
                        renderer: function (value, metadata, record) {
                            return value;
                        }
                    },
                    {
                        text:'货币英文名',
                        width:'20%',
                        dataIndex:'englishname',
                        align:'center',
                        border:0,
                        cls:'mod-2',
                        tdCls:'suit-cell',
                        menuDisabled:true,
                        sortable:false,
                        renderer : function(value, metadata,record) {
                            return value;
                        }
                    }
                    ,
                    {
                        text:'货币代码',
                        width:'20%',
                        dataIndex:'code',
                        align:'center',
                        tdCls:'suit-cell',
                        menuDisabled:true,
                        border:0,
                        sortable:false,
                        renderer : function(value, metadata,record) {
                            return value;
                        },
                        listeners:{
                            afterrender:function(obj,eopts)
                            {
                                if(window.display_mode!=1)
                                    obj.hide();
                                else
                                    obj.show();
                            }
                        }
                    },
                    {
                        text:'货币符号',
                        width:'20%',
                        dataIndex:'symbol',
                        align:'center',
                        cls:'mod-2',
                        tdCls:'suit-cell',
                        menuDisabled:true,
                        border:0,
                        sortable:false,
                        renderer : function(value, metadata,record) {
                            return value;
                        },
                        listeners:{
                            afterrender:function(obj,eopts)
                            {
                                if(window.display_mode!=1)
                                    obj.hide();
                                else
                                    obj.show();
                            }
                        }
                    },
                    {
                        text:'汇率',
                        width:'32%',
                        dataIndex:'ratio',
                        align:'center',
                        tdCls:'suit-cell',
                        menuDisabled:true,
                        border:0,
                        sortable:false,
                        renderer : function(value, metadata,record) {
                             var id=record.get("id");
                             var code=record.get('code');
                             var ratio= value[window.current_code];
                             var ratio1='',ratio2='';
                             if(ratio)
                             {
                                 ratio1=ratio[1];
                                 ratio2=ratio[0];
                             }
                             if(code==window.current_code)
                               return '-';
                             return "<div><input class='row-edit-txt' style='width:30%' type='text' name='"+window.current_code+"' value='"+ratio1+"' onblur='updateRatio("+id+",this)'  />  ：" +
                                 " <input class='row-edit-txt' style='width:30%' type='text' name='"+code+"' value='"+ratio2+"' onblur='updateRatio("+id+",this)'/></div>";
                        },
                        listeners:{
                            afterrender:function(obj,eopts)
                            {
                                if(window.display_mode!=2)
                                   obj.hide();
                                else
                                   obj.show();
                            }
                        }
                    }
                    /*,
                    {
                        text:'管理',
                        width:'16%',
                        align:'center',
                        border:0,
                        cls:'mod-2',
                        sortable:false,
                        menuDisabled:true,
                        renderer : function(value, metadata,record) {
                            var id=record.get('id');
                            var code=record.get("code");
                            return '<a href="javascript:;" onclick="togExchange(\''+id+'\',\''+code+'\',this)">汇率</a>';
                        }
                    }*/
                ],
                listeners:{
                    boxready:function()
                    {
                        var height=Ext.dom.Element.getViewportHeight();
                        this.maxHeight=height-106;
                        this.doLayout();
                    },
                    afterlayout:function()
                    {

                    }
                },
                plugins: [
                    Ext.create('Ext.grid.plugin.CellEditing', {
                        clicksToEdit:2,
                        listeners:{
                            edit:function(editor, e)
                            {
                                var id=e.record.get('id');
                                var view_el=window.product_grid.getView().getEl();
                                view_el.scrollBy(0,this.scroll_top,false);
                                updateField(0,id,e.field,e.value,0);
                                return false;
                            },
                            beforeedit:function(editor,e)
                            {
                                if(e.field=='jifentprice'||e.field=='jifenbook'||e.field=='jifencomment')
                                {
                                    var id=e.record.get('id');
                                    if(id.indexOf('suit')==-1)
                                    {
                                        return false;
                                    }
                                }
                                var view_el=window.product_grid.getView().getEl()
                                this.scroll_top=view_el.getScrollTop();

                            }
                        }
                    })
                ]
            });
        })

    //实现动态窗口大小
    Ext.EventManager.onWindowResize(function(){

        var height=Ext.dom.Element.getViewportHeight();
        window.product_grid.maxHeight=(height-106);
        window.product_grid.doLayout();

    })



    //更新某个字段
    function updateRatio(id,ele)
    {
        var record=window.product_store.getById(id.toString());
        var sibling=$(ele).siblings("input:first");

        var params={};
        params[sibling.attr('name')]=sibling.val();
        params[$(ele).attr('name')]=$(ele).val();




        Ext.Ajax.request({
            url   : SITEURL + "currency/list/action/updateratio",
            method  :  "POST",
            datatype  :  "JSON",
            params:params,
            success  :  function(response, opts)
            {
                if(response.responseText=='ok')
                {
                    var view_el=window.product_grid.getView().getEl()
                   /* record.set(field,value);
                    record.commit();
                  */
                }
                else
                {
                    ST.Utils.showMsg("{__('norightmsg')}",5,1000);
                }
            }});
    }



    //切换显示或隐藏
    function togStatus(obj,record,field)
    {
        var val=record.get(field);
        var id=record.get('id');
        var newval=val==1?0:1;
        Ext.Ajax.request({
            url   :  "currency/list/action/update",
            method  :  "POST",
            datatype  :  "JSON",
            params:{id:id,field:field,val:newval},
            success  :  function(response, opts)
            {
                if(response.responseText=='ok')
                {
                    var view_el=window.product_grid.getView().getEl()
                    var scroll_top=view_el.getScrollTop();
                    record.set(field,newval);
                    record.commit();
                    view_el.scrollBy(0,scroll_top,false);
                }
            }});

    }
    //删除套餐
    function delSuit(id)
    {
        ST.Util.confirmBox("提示","确定删除这个套餐？",function(){

            window.product_store.getById(id).destroy();
        })
    }
    //修改
    function togExchange(id,code,ele)
    {
       window.current_code=code;
       CHOOSE.togMod(ele,2);
    }

</script>
</body>
</html>
