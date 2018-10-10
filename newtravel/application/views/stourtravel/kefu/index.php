<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>客服设置</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,plist.css,kf.css,base_new.css'); }
    {php echo Common::getScript('config.js');}
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
    <style>
        .set-one .tool-bn .short-cut {
            margin-right: 20px;
            margin-bottom: 10px;
            line-height: 25px;
            height: 25px;
            color: rgb(67, 174, 228);
            float: left;
            padding: 0px 10px;
            border-width: 1px;
            border-style: solid;
            border-color: rgb(67, 174, 228);
            border-image: initial;
        }
    </style>

</head>
<body>

    <table class="content-tab">
        <tr>
            <td width="119px" class="content-lt-td"  valign="top">
                {template 'stourtravel/public/leftnav'}
                <!--右侧内容区-->
            </td>
            <td valign="top" class="content-rt-td">

                <div class="cfg-header-bar">
                    <div class="cfg-header-tab">
                        <span class="item on" data-contain="kefu-manage">在线客服</span>
                        <span class="item" data-contain="kefu-thrid">三方客服</span>
                    </div>
                    <a href="javascript:;" class="fr btn btn-primary radius mr-10 mt-6" onclick="window.location.reload()">刷新</a>
                </div>

                 <div class="kefu-manage" >
                    <form id="frm" name="frm" onsubmit="return false;">
                        <div id="kefu-manage" class="kefu_content">
                            <ul class="info-item-block">
                                <li>
                                    <span class="item-hd">在线客服开关：</span>
                                    <div class="item-bd">
                                        <div class="on-off">
                                            <label class="radio-label"><input type="radio" name="display" value="1" {if $display==1}checked="checked"{/if}>开启</label>
                                            <label class="radio-label ml-20"><input type="radio" name="display" value="0" {if $display==0}checked="checked"{/if}>关闭</label>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <div class="msg-bar pl-10">
                                <span class="on" data-rel="tpl">客服样式</span>
                                <span  data-rel="basic">客服设置</span>
                            </div>
                            <div class="w-set-nr" style="display: none;"  id="panel_basic">

                                <div class="product-add-div">

                                    <ul class="info-item-block">
                                        <li class="item_qq item_set">
                                            <span class="item-hd">QQ客服号：</span>
                                            <div class="item-bd pr-20">
                                                <div id="qq_tree_panel" class="content-nrt">

                                                </div>
                                            </div>
                                        </li>
                                        <li class="item_phone item_set">
                                            <span class="item-hd">客服电话{Common::get_help_icon('cfg_phone')}：</span>
                                            <div class="item-bd">
                                                <input type="text" name="cfg_phone" class="input-text w300" value="{$config['cfg_phone']}">
                                            </div>
                                        </li>
                                        <li class="item_icon item_set">
                                            <span class="item-hd">客服图标{Common::get_help_icon('cfg_kefu_icon')}：</span>
                                            <div class="item-bd">
                                                <a href="javascript:;" class="btn mt-3 btn-primary radius size-S event_file_upload" data="{title:'客服图标',callback:'upload_icon'}">上传图片</a>
                                                <a class="btn btn-grey-outline size-S radius mt-3 ml-5" href="javascript:;" id="kf_del_btn">恢复默认</a>
                                                <span class="item-text c-999 ml-10">*建议上传图标尺寸：45*45px</span>
                                                <div class="pt-10 {if !$config['cfg_kefu_icon']}hide{/if}">
                                                    <img id="img_cfg_kefu_icon" class="up-img-area" src="{$config['cfg_kefu_icon']}"/>
                                                    <input type="hidden" id="cfg_kefu_icon" name="cfg_kefu_icon" value="{$config['cfg_kefu_icon']}"/>
                                                </div>
                                            </div>
                                        </li>
                                        <!--<li>
                                            <span class="item-hd">400电话{Common::get_help_icon('cfg_phone_400')}：</span>
                                            <div class="item-bd">
                                                <input type="text" name="cfg_phone_400" class="input-text w300" value="{$config['cfg_phone_400']}">
                                            </div>
                                        </li>-->
                                        <li class="item_weixin item_set">
                                            <span class="item-hd">官方微信{Common::get_help_icon('cfg_weixin_logo')}：</span>
                                            <div class="item-bd">
                                                <a href="javascript:;" class="btn mt-3 btn-primary radius size-S event_file_upload" data="{title:'官方微信',callback:'upload_we_chat_logo'}">上传图片</a>
                                                <a class="btn mt-3 btn-grey-outline radius size-S ml-5" href="javascript:;" id="wx_del_btn" style="{if empty($config['cfg_weixin_logo'])}display: none;{/if}">删除</a>
                                                <span class="item-text c-999 ml-10">*建议上传图标尺寸：94*94px</span>
                                                <div class="pt-10 {if !$config['cfg_weixin_logo']}hide{/if}">
                                                    <img id="img_cfg_weixin_logo" class="up-img-area" src="{$config['cfg_weixin_logo']}"/>
                                                    <input type="hidden" id="cfg_weixin_logo" name="cfg_weixin_logo" value="{$config['cfg_weixin_logo']}"/>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="item_both item_set">
                                            <span class="item-hd">样式位置：</span>
                                            <div class="item-bd">
                                                <label class="radio-label"><input type="radio"  name="position" value="left" {if $pos=='left'}checked="checked"{/if}>居左显示</label>
                                                <label class="radio-label ml-20"><input type="radio" name="position" value="right" {if $pos=='right'}checked="checked"{/if}>居右显示</label>
                                            </div>
                                        </li>
                                        <li class="item_style item_set">
                                            <span class="item-hd">样式边距：</span>
                                            <div class="item-bd">
                                                <span class="item-text mr-10">左/右边距</span><input type="text"  name="posh" class="input-text w80" value="{$posh}">
                                                <span class="item-text mr-10 ml-20">上边距</span><input type="text" style="" name="post" value="{$post}" class="input-text w80">
                                                <span class="item-text c-999 ml-20">*可以是数字或百分比，例如20px或20%</span>
                                            </div>
                                        </li>
                                        <li class="item_weibo item_set">
                                            <span class="item-hd">微博地址{Common::get_help_icon('cfg_weibo_url')}：</span>
                                            <div class="item-bd">
                                                <input type="text" name="cfg_weibo_url" class="input-text w300" value="{$config['cfg_weibo_url']}">
                                            </div>
                                        </li>
                                       <!--
                                       <li>
                                            <span class="item-hd">免费通话{Common::get_help_icon('kefu_index_freekefu')}：</span>
                                            <div class="item-bd">
                                                <div id="freekefu_panel">

                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <span class="item-hd">免费通话提醒{Common::get_help_icon('kefu_index_free_tel_msg')}：</span>
                                            <div class="item-bd">
                                                <div class="w900">
                                                    <h5 class="lh-30 c-primary">短信通知管理员</h5>
                                                    <textarea class="textarea" name="free_tel_msg" rows="4" cols="105">{$free_tel_msg}</textarea>
                                                </div>
                                                <div class="on-off">
                                                    <label class="radio-label"><input type="radio"  name="free_tel_msg_open" value="1" {if $free_tel_msg_open=='1'}checked="checked"{/if}>开启</label>
                                                    <label class="radio-label ml-20"><input type="radio" name="free_tel_msg_open" value="0" {if $free_tel_msg_open!='1'}checked="checked"{/if}>关闭</label>
                                                </div>
                                                <div class="set-one pt-10">
                                                    <div class="tool-bn">
                                                        <a href="javascript:;" class="short-cut" data="{#MEMBERNAME#}">会员名称</a>
                                                        <a href="javascript:;" class="short-cut" data="{#WEBNAME#}">网站名称</a>
                                                        <a href="javascript:;" class="short-cut" data="{#FREEPHONE#}">客户号码</a>
                                                        <div class="clear-both"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        -->
                                    </ul>
                                </div>
                            </div>
                            <div class="w-set-nr tpl-con" id="panel_tpl" >
                                <div class="product-add-div">
                                    <div class="add-class">
                                        <table class="kf-tpl">
                                            <tr>
                                                <td>
                                                    <div><input items="qq,phone,weixin,both,style" type="radio" name="qqcl" value="1" {if $qqcl=='1'}checked="checked"{/if}/> 客户样式1 {Common::get_help_icon('national_free_call')} <span class="hint-sp">*该模板支持QQ、客服电话、微信、免费通话设置显示</span></div>
                                                    <div><img src="{$GLOBALS['cfg_public_url']}/images/kf1.jpg"/></div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="add-class">
                                        <table class="kf-tpl">
                                            <tr>
                                                <td>
                                                    <div><input type="radio" items="qq,both,style" name="qqcl" value="2" {if $qqcl=='2'}checked="checked"{/if}/> 客户样式2 <span class="hint-sp">*该模板仅支持QQ客服设置显示</span></div>
                                                    <div><img src="{$GLOBALS['cfg_public_url']}/images/kf2.jpg"/></div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="add-class">
                                        <table class="kf-tpl">
                                            <tr>
                                                <td>
                                                    <div><input type="radio" items="qq,phone,weixin,weibo,style,both" name="qqcl" value="3" {if $qqcl=='3'}checked="checked"{/if}/> 客户样式3<span class="hint-sp">*该模板支持QQ客服、客服电话、微信、微博设置显示</span></div>
                                                    <div><img src="{$GLOBALS['cfg_public_url']}/images/kf3.jpg"/></div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="add-class">
                                        <table class="kf-tpl">
                                            <tr>
                                                <td>
                                                    <div><input type="radio" items="qq,style,both" name="qqcl" value="4" {if $qqcl=='4'}checked="checked"{/if}/> 客户样式4 <span class="hint-sp">*该模板仅支持QQ客服设置显示</span></div>
                                                    <div><img src="{$GLOBALS['cfg_public_url']}/images/kf4.jpg"/></div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="add-class">
                                        <table class="kf-tpl">
                                            <tr>
                                                <td>
                                                    <div><input type="radio" items="qq,phone,weixin,weibo,both" name="qqcl" value="5" {if $qqcl=='5'}checked="checked"{/if}/> 客户样式5{Common::get_help_icon('national_free_call')}<span class="hint-sp">*该模板支持QQ、客服电话、微信、微博、免费通话设置显示，
        该样式不支持上边距和左右边距的设置</span></div>
                                                    <div><img src="{$GLOBALS['cfg_public_url']}/images/kf5.jpg"/></div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="add-class">
                                        <table class="kf-tpl">
                                            <tr>
                                                <td>
                                                    <div><input type="radio" items="qq,phone,weixin,weibo,both" name="qqcl" value="6" {if $qqcl=='6'}checked="checked"{/if}/> 客户样式6 {Common::get_help_icon('national_free_call')} <span class="hint-sp">*该模板支持QQ、客服电话、微信、微博、免费通话设置显示,
        该样式不支持上边距和左右边距的设置</span></div>
                                                    <div><img src="{$GLOBALS['cfg_public_url']}/images/kf6.jpg"/></div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="add-class">
                                        <table class="kf-tpl">
                                            <tr>
                                                <td>
                                                    <div><input type="radio" items="qq,phone,weixin,weibo" name="qqcl" value="7" {if $qqcl=='7'}checked="checked"{/if}/> 客户样式7<span class="hint-sp">*该模板支持QQ、客服电话、微信、微博设置显示，
        该样式不支持样式位置、样式边距的设置</span></div>
                                                    <div><img src="{$GLOBALS['cfg_public_url']}/images/kf7.jpg"/></div>
                                                </td>
                                                </tr>
                                            <tr>

                                            </tr>
                                        </table>
                                    </div>
                                    <div class="add-class">
                                        <table class="kf-tpl">
                                            <tr>
                                                <td>
                                                    <div><input type="radio" items="weixin,weibo,both" name="qqcl" value="8" {if $qqcl=='8'}checked="checked"{/if}/> 客户样式8 <span class="hint-sp">*该模板支持微信、微博显示，不支持上边距和左右边距的设置</span></div>
                                                    <div><img src="{$GLOBALS['cfg_public_url']}/images/kf8.jpg"/></div>
                                                </td>
                                                <td valign="bottom">

                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                 </div>
                            </div>
                        </div>
                        <div id="kefu-thrid" class="kefu_content hide">
                            <ul class="info-item-block">
                                <li>
                                    <span class="item-hd">客服代码{Common::get_help_icon('cfg_html_kefu')}：</span>
                                    <div class="item-bd">
                                        <textarea id="cfg_html_kefu" name="cfg_html_kefu"  cols="" rows="4" class="textarea w800">{$config['cfg_html_kefu']}</textarea>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="clear clearfix pt-20 pb-20">
                            <a class="btn btn-primary size-L radius w100 ml-115" href="javascript:;" id="btn_save">保存</a>
                            <!-- <a class="cancel" href="#">取消</a>-->
                            <input type="hidden" name="webid" id="webid" value="0">
                        </div>
                    </form>
                  </div>
            </td>
        </tr>
    </table>



<script>

    $(document).ready(function(){

        $('.cfg-header-tab').find('span').click(function(){
            var cdiv = $(this).attr('data-contain');
            $(this).addClass('on').siblings().removeClass('on');
            $("#"+cdiv).removeClass('hide').siblings('.kefu_content').addClass('hide');
        })



        //切换样式
        $("input[name=qqcl]").change(tog_kefu);

        function tog_kefu()
        {
            var items=$("input[name=qqcl]:checked").attr('items');
            var item_arr = items.split(',');
            $(".item_set").hide();
            $(".item_icon").show();
            $(".item_weixin").show();
            for(var i in item_arr)
            {
                var item=item_arr[i];
                $(".item_"+item).show();
            }
        }
        tog_kefu();


        //删除微信
        $("#wx_del_btn").click(function(){
            $("#cfg_weixin_logo").val('');
            $("#img_cfg_weixin_logo").attr('src','');
            $(this).hide();
        });

        //删除图标
        $("#kf_del_btn").click(function(){
            $("#cfg_kefu_icon").val('/res/images/24hours-ico.png');
            $("#img_cfg_kefu_icon").attr('src','/res/images/24hours-ico.png');
            //$(this).hide();
        });


        //菜单切换
        $(".msg-bar span").click(function(){
              $(this).addClass('on');
              $(this).siblings().removeClass('on');
              var rel=$(this).attr('data-rel');
              $(".w-set-nr").hide();
              $("#panel_"+rel).show();

              if(rel=='basic')
              {
                  window.attr_treepanel.doLayout();
              }

        });

        $('.event_file_upload').click(function(){
            var obj=eval('('+$(this).attr('data')+')');
            ST.Util.showBox(obj.title, SITEURL + 'image/insert_view', 0,0, null, null, parent.document, {loadWindow: window, loadCallback: eval('('+obj.callback+')')});
            //客服图标
            function upload_icon(result,bool) {
                if(bool){
                    var src=result['data'][0].replace(/\$\$.*?$/,'');
                    $("#cfg_kefu_icon").val(src).parent().removeClass('hide');
                    $("#img_cfg_kefu_icon").attr('src',src);
                    //$("#kf_del_btn").show();
                }
            }
            //微信Logo
            function upload_we_chat_logo(result,bool){
                if(bool){
                    var src=result['data'][0].replace(/\$\$.*?$/,'');
                    $("#cfg_weixin_logo").val(src).parent().removeClass('hide');
                    $("#img_cfg_weixin_logo").attr('src',src);
                    $("#wx_del_btn").show();
                }
            }
        });


        //配置信息保存
        $("#btn_save").click(function(){
            Ext.Ajax.request({
                url   :  SITEURL+"kefu/ajax_save",
                method  :  "POST",
                isUpload :  true,
                form  : "frm",
                success  :  function(response, opts)
                {

                    var data = $.parseJSON(response.responseText);
                    if(data.status)
                    {


                        ST.Util.showMsg('保存成功!','4',2000);


                    }


                }});

        })


        //配置短信通知
        $('.set-one .short-cut').click(function(){
            var ele=$('textarea[name=free_tel_msg]');
            var value=$(this).attr('data');
            ST.Util.insertContent(value,ele);
        })
    });


    //客服
    Ext.onReady(
        function () {
            var helpico = "{php echo Common::getIco('help');}";

            Ext.tip.QuickTipManager.init();
            window.attr_store = Ext.create('Ext.data.TreeStore', {
                fields: [

                    'id',
                    'qqname',
                    'qqnum',
                    'isopen',
                    'displayorder',
                    'pid'

                ],
                proxy: {
                    type: 'ajax',
                    extraParams: {typeid: window.display_mode},
                    api: {
                        read: SITEURL+'kefu/qqlist/action/read/',  //读取数据的URL
                        update:  SITEURL+'kefu/qqlist/action/save/',
                        destroy:  SITEURL+'kefu/qqlist/action/delete/'
                    },
                    reader: 'json'
                },
                autoLoad: true,
                listeners: {
                    sort: function (node, childNodes, eOpts) {

                    }
                }

            });
            //属性树
            window.attr_treepanel = Ext.create('Ext.tree.Panel', {
                store: attr_store,
                rootVisible: false,
                padding: '0',
                renderTo: 'qq_tree_panel',
                border: 0,
                style: 'border:0px;',
                width: "100%",
                bodyBorder: 0,
                bodyStyle: 'border-width:0px',
                // selModel:sel_model,
                autoScroll: true,
                listeners: {
                    afterlayout: function (panel) {
                        var data_height = panel.getView().getEl().down('.x-grid-table').getHeight();

                        var height = Ext.dom.Element.getViewportHeight();

                        if (data_height > height - 100) {
                            window.has_biged = true;
                            panel.height = height - 100;
                        }
                        else if (data_height < height - 100) {
                            if (window.has_biged) {

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
                        text: '排序',
                        dataIndex: 'displayorder',
                        //  tdCls:'attr-al-mid',
                        width: '10%',
                        draggable: false,
                        menuDisabled:true,
                        renderer : function(value, metadata,record) {
                            var id=record.get('id');
                            if (value == 'add')
                                return '';
                            else {
                                if(value=='9999'||value=='999999'||!value)
                                    value='';
                                return "<input type='text' class='row-edit-txt' onclick=\"ST.Util.prevPopup(event,this)\" onblur=\"updateField(this,"+id+",'displayorder',0,'input')\" value='" + value + "'/>";
                            }
                        }

                    },
                    {
                        xtype: 'treecolumn',   //有展开按钮的指定为treecolumn
                        text: '<span class="grid_column_text">组名/QQ</span>'+ST.Util.getGridHelp('kefu_index_qqname'),
                        dataIndex: 'qqname',
                        id: 'attr_name',
                        sortable:false,
                        locked: false,
                        width: '31%',
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
                        renderer : function(value, metadata,record) {
                            var id=record.get('id');
                            if(id.indexOf('add')==-1)
                            {
                                //var editHtml="<input type='text' class='row-edit-txt' value='"+value+"'  />";
                                return "<span class='row-editable-sp'>"+value+"</span>";

                            }
                            return value;

                        }
                    },
                    {
                        text: '<span class="grid_column_text">qq号码</span>',
                        dataIndex: 'qqnum',
                        //  tdCls:'attr-al-mid',
                        width: '30%',
                        sortable:false,
                        menuDisabled:true,
                        renderer : function(value, metadata,record) {
                            var id=record.get('id');
                            var pid=record.get('pid');
                            if(id.indexOf('add')==-1&&pid!=0)
                            {
                                return '<input type="text" class="row-edit-txt" style="text-align:left" onclick=\"ST.Util.prevPopup(event,this)\" onblur=\"updateField(this,'+id+',\'qqnum\',0,\'input\')\" value="'+value+'"/>';
                            }
                            return value;
                        }

                    },
                    {

                        text: '<span class="grid_column_text">开启/关闭</span>'+ST.Util.getGridHelp('kefu_index_isopen'),
                        dataIndex: 'isopen',
                        width: '20%',
                        xtype: 'actioncolumn',
                        tdCls: 'attr-al-mid',
                        sortable:false,
                        align:'center',
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
                                    var id=record.get('id');
                                    var val=record.get('isopen');
                                    var newval=val==1?0:1;
                                    updateField(null,id, 'isopen',newval);


                                }
                            }
                        ],
                        renderer : function(value, metadata,record) {
                            var id=record.get('id');
                            if(id.indexOf('add')!=-1)
                                return '';
                        }
                    },
                    {
                        text: '管理',
                        width: '10%',
                        tdCls: 'attr-al-mid',
                        align:'center',
                        menuDisabled:true,
                        renderer : function(value, metadata,record) {
                            var id=record.get('id');
                            var pid = record.get('pid');
                            if(id.indexOf('add')!=-1)
                                return '';
                            return '<a href="javascript:;" class="btn-link" onclick="delS('+id+')">删除</a>';
                        }
                    }
                ],
                plugins: [
                    Ext.create('Ext.grid.plugin.CellEditing', {
                        clicksToEdit: 1,
                        listeners: {
                            edit: function (editor, e) {

                                e.record.commit();
                                e.record.save({params: {field: e.field}});

                            },
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



    //切换每页显示数量
    function changeNum(combo, records) {

        var pagesize = records[0].get('num');
        window.product_store.pageSize = pagesize;
        window.product_grid.down('pagingtoolbar').moveFirst();
        //window.product_store.load({start:0});
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
            url: SITEURL+'kefu/qqlist/action/addsub/',
            params: {pid: pid},
            success: function (response) {

                var newrecord = Ext.decode(response.responseText);
                if(pid==0)
                {
                    newrecord.leaf=false;
                }
                else
                {
                    newrecord.leaf=true;
                }
                var view_el = window.attr_treepanel.getView().getEl()
                var scroll_top = view_el.getScrollTop();
                precord.insertBefore(newrecord, addnode);
                //view_el.scroll('t',scroll_top);
            }
        });

    }
    function updateField(ele,id,field,value,type)
    {
        var record=window.attr_store.getNodeById(id.toString());
        if(type=='select'|| type=='input')
        {
            value=Ext.get(ele).getValue();
        }


        Ext.Ajax.request({
            url   :  SITEURL+"kefu/qqlist/action/update/",
            method  :  "POST",
            datatype  :  "JSON",
            params:{id:id,field:field,val:value},
            success  :  function(response, opts)
            {
                //  alert(value);
                record.set(field,value);
                record.commit();

            }});

    }
    function delS(id) {
        ST.Util.confirmBox("提示","确定删除？",function(){
                window.attr_store.getById(id.toString()).destroy();
        })
    }

    function view(id)
    {
        var url = SITEURL+'kefu/freekefu_edit/id/'+id;
        ST.Util.showBox('回复处理',url,450,180,null,null,document,{loadCallback:function(result){
            window.product_store.reload();
        },loadWindow:window});
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
        ST.Util.confirmBox("提示","确定删除这个套餐？",function(){
            check_cmp.each(
                function (el, c, index) {
                    window.product_store.getById(el.getValue()).destroy();
                }
            );
        })
    }




</script>

</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.2102&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
