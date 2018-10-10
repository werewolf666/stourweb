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
                    <div class="cfg-header-bar">
<!--                        <span  data-rel="jieshao" class="on"><s></s>底部导航</span>-->
                        <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                    </div>
                </div>
                <form id="product_fm">
                    <div class="product-add-div item-one" id="item_jieshao">
                        <ul class="info-item-block">
                            <li>
                                <span class="item-hd">导航名称{Common::get_help_icon('footernav_index_servername',true)}：</span>
                                <div class="item-bd">
                                    <input type="text" id="servername" name="servername" class="set-text w500" value="{$serverinfo['servername']}" />
                                </div>
                            </li>
                            <li>
                                <span class="item-hd">介绍内容：</span>
                                <div class="item-bd">
                                    {php Common::getEditor('content',$serverinfo['content'],1000,380);}
                                </div>
                            </li>
                        </ul>
                    </div>
                    <input type="hidden" name="webid" id="webid" value="{$webid}"/>
                    <input type="hidden" name="articleid" id="articleid" value="{$serverinfo['id']}"/>
                    <div class="clear clearfix">
                        <a class="btn btn-primary radius size-L w100 ml-115 mt-10" id="save_btn" href="javascript:;">保存</a>
                    </div>
                </form>
            </div>

        </td>
    </tr>
</body>
<script>
    $(function () {
        $("#save_btn").click(function () {
            var url = "{$GLOBALS['cfg_cmspath']}";
            var ajaxurl = url + 'footernav/' + "{$action}";
            $.ajaxform({
                url: ajaxurl,
                method: 'POST',
                form: '#product_fm',
                dataType: 'json',
                success: function (data) {
                    if (data.status) {

                        ST.Util.showMsg('保存成功', 4);
                        ST.Util.closeBox();
                    }
                    else {
                        ST.Util.showMsg('保存失败', 5);
                    }
                }
            });
        })
    })
</script>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.0.201711.0103&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
