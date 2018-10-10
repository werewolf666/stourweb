<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>支付设置</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base_new.css'); }
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
</head>
<body>

	<table class="content-tab" div_body=myvz8B >
        <tr>
            <td width="119px" class="content-lt-td"  valign="top">
                {template 'stourtravel/public/leftnav'}
                <!--右侧内容区-->
            </td>
            <td valign="top" class="content-rt-td">
                <div class="cfg-header-bar">
                    <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                </div>
                <form id="frm">
                    <ul class="info-item-block">
                        <li>
                            <span class="item-hd">是否开启：</span>
                            <div class="item-bd">
                                <label class="radio-label"><input type="radio" name="isopen" value="1" {if in_array($payid,$pay_types)} checked="checked"{/if}/>开启</label>
                                <label class="radio-label ml-20"><input type="radio" name="isopen" value="0" {if !in_array($payid,$pay_types)} checked="checked"{/if}/>关闭</label>
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">说明{Common::get_help_icon('payset_offline_description')}：</span>
                            <div class="item-bd">
                                {php Common::getEditor('cfg_pay_xianxia',$configs['cfg_pay_xianxia'],$sysconfig['cfg_admin_htmleditor_width'],300);}
                            </div>
                        </li>
                    </ul>
                    <div class="clear clearfix mt-5">
                        <a class="btn btn-primary radius size-L ml-115" data-content-id="btn_save" href="javascript:;" id="btn_save">保存</a>
                    </div>
                    <input type="hidden" name="payid" value="{$payid}"/>
                </form>
            </td>
        </tr>
    </table>

  <script>
	$(document).ready(function(){
        $("#btn_save").click(function(){
            $.ajaxform({
                url   :  SITEURL+"payset/ajax_save",
                method  :  "POST",
                form  : "#frm",
                dataType:'json',
                success  :  function(data)
                {
                    if(data.status)
                    {
                        ST.Util.showMsg('保存成功!','4',2000);
                    }
                    else
                    {
                        ST.Util.showMsg(data.msg,'5',2000);
                    }
                }
            });
        });
     });

  </script>

</body>
</html>
