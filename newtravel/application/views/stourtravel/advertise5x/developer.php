<?php defined('SYSPATH') or die();?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,base_new.css'); }
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js,product_add.js,choose.js,st_validate.js,jquery.colorpicker.js,imageup.js,jquery.upload.js,insurance.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
    {php echo Common::getCss('destination_dialog_basicinfo.css'); }
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
                    <div class="w-set-tit bom-arrow">
                        <label class="item-text pl-10"><s></s>*此处为开发者入口，用于二次开发的功能放置与管理。放置于此的功能不会被升级覆盖。{Common::get_help_icon('ads_developer',true)}</label>
                        <a href="javascript:;" class="fr btn btn-primary radius w60 mt-5 mr-10 size-MINI" onclick="window.location.reload()">刷新</a>
                    </div>
                </div>

            </div>

        </td>
    </tr>
</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.1602&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
