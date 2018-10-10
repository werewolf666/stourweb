<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,jqtransform.css,base_new.css'); }

    {php echo Common::getScript("uploadify/jquery.uploadify.min.js,product_add.js,choose.js,st_validate.js,jquery.colorpicker.js,jquery.jqtransform.js,imageup.js,jquery.upload.js,insurance.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
</head>



<body>
<!--顶部-->
{php Common::getEditor('jseditor','',$sysconfig['cfg_admin_htmleditor_width'],300,'Sline','','print',true);}
<table class="content-tab">
<tr>
    <td width="119px" class="content-lt-td" valign="top">
        {template 'stourtravel/public/leftnav'}
        <!--右侧内容区-->
    </td>
    <td valign="top" class="content-rt-td" style="overflow:auto;">
            <div class="manage-nr">
                <div class="w-set-con">
                    <div class="cfg-header-bar">
                        <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                    </div>
                </div>
                <form id="frm" name="frm">
                    <div class="out-box-con">
                        <ul class="info-item-block">
                            <li>
                                <span class="item-hd">文件名称：</span>
                                <div class="item-bd">
                                    <span class="item-text">{$filename}</span>
                                </div>
                            </li>
                            <li>
                                <span class="item-hd">文件内容：</span>
                                <div class="item-bd">
                                    <textarea class="textarea w900" style="min-height: 500px"  name="content">{$content}</textarea>
                                </div>
                            </li>
                        </ul>

                        <div class="clear">
                            <a class="btn btn-primary radius size-L ml-115" id="btn_save" href="javascript:;">保存</a>
                            <input type="hidden" name="filename" value="{$filename}"/>
                            <input type="hidden" name="ismobile" value="{$ismobile}"/>
                        </div>

                    </div>
                </form>
            </div>

    </td>
</tr>


<!--左侧导航区-->

<!--右侧内容区-->

<script>
    $(function(){
        //保存
        $("#btn_save").click(function(){

            Ext.Ajax.request({
                url   :  SITEURL+"filemanager/ajax_page_save",
                method  :  "POST",
                form  : "frm",
                headers: {'Content-Type':'application/x-www-data-urlencoded'},
                success  :  function(response, opts)
                {
                    var data = $.parseJSON(response.responseText);
                    if(data.status)
                    {
                        ST.Util.showMsg('保存成功!','4',2000);
                    }
                }
            });
        })
    })
</script>
</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.1004&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
