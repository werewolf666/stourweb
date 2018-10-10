<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>思途CMS{$coreVersion}</title>
    {php echo Common::getScript('jquery-1.8.3.min.js,common.js,msgbox/msgbox.js,extjs/ext-all.js'); }
    {php echo Common::getCss('msgbox.css','js/msgbox/'); }
    {php echo Common::getCss('style.css,base.css,base_new.css'); }

</head>

<body right_color=zWkYsm >
<table class="content-tab">
    <tr>
        <td width="119px" class="content-lt-td" valign="top">
            {template 'stourtravel/public/leftnav'}
            <!--右侧内容区-->
        </td>
        <td valign="top" class="content-rt-td" style="overflow:auto;">
            <div class="cfg-header-bar clearfix">
                <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
            </div>
            <form name="seofrm" id="seofrm">
            <div class="w-set-con">
                <div class="w-set-nr">
                    <ul class="info-item-block">
                        <li>
                            <span class="item-hd">优化标题{Common::get_help_icon('config_seoinfo_seotitle',true)}：</span>
                            <div class="item-bd">
                                <input type="text" name="seotitle" id="seotitle" class="input-text w500" value="{$seoinfo['seotitle']}" />
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">关键词{Common::get_help_icon('config_seoinfo_keyword',true)}：</span>
                            <div class="item-bd">
                                <input type="text" id="keyword" name="keyword" class="input-text w500" value="{$seoinfo['keyword']}" />
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">Tag{Common::get_help_icon('content_tagword',true)}：</span>
                            <div class="item-bd">
                                <input type="text" id="tagword" name="tagword" class="input-text w500" value="{$seoinfo['tagword']}" />
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">描述{Common::get_help_icon('config_seoinfo_description',true)}：</span>
                            <div class="item-bd">
                                <textarea name="description" cols="3" class="textarea w500">{$seoinfo['description']}</textarea>
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">介绍{Common::get_help_icon('config_seoinfo_jieshao',true)}：</span>
                            <div class="item-bd">
                                {php Common::getEditor('jieshao',$seoinfo['jieshao'],800,400,'Line');}
                            </div>
                        </li>
                    </ul>
                    <div class="clear clearfix mt-5">
                        <a class="btn btn-primary size-L radius ml-115" href="javascript:;"  id="btn_save">保存</a>
                        <!-- <a class="cancel" href="#">取消</a>-->
                    </div>
                </div>
            </div>
            <input type="hidden" id="navid" name="navid" value="{$seoinfo['id']}">
            </form>
        </td>
    </tr>
</table>



    <




    <script>
       var id="{$seoinfo['id']}";
       var SITEURL = "{URL::site()}";
       $('#btn_save').click(function(){

           var ajaxurl = '{php echo URL::site('config/ajax_saveseo');}';
          //ST.Util.showMsg('保存中,请稍后...',6,5000);
           Ext.Ajax.request({
               url: ajaxurl,
               method: 'POST',
               form : 'seofrm',
               success: function (response, options) {

                   var data = $.parseJSON(response.responseText);
                   if(data.status)
                   {

                       ST.Util.showMsg('保存成功',4);
                       //ST.Util.responseDialog({id:id,isfinish:data.isfinish},true);
                       //ST.Util.closeBox();//关闭当前窗口
                   }

               }

           });

       })
     </script>

</body>
</html>
