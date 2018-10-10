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

	<table class="content-tab">
        <tr>
            <td width="119px" class="content-lt-td"  valign="top">
                {template 'stourtravel/public/leftnav'}
                <!--右侧内容区-->
            </td>
            <td valign="top" class="content-rt-td">
                <div class="cfg-header-bar">
                    <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10"  onclick="window.location.reload()">刷新</a>
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
                            <span class="item-hd">网关商户号{Common::get_help_icon('payset_huicao_account')}：</span>
                            <div class="item-bd">
                                <input name="cfg_huicao_account" type="text" value="{$configs['cfg_huicao_account']}" id="cfg_huicao_account" class="input-text w300">
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">商户密钥：</span>
                            <div class="item-bd">
                                <input name="cfg_huicao_key" type="text" value="{$configs['cfg_huicao_key']}" id="cfg_huicao_key" class="input-text w300">
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">排序{Common::get_help_icon('payset_displayorder')}：</span>
                            <div class="item-bd">
                                <input name="displayorder" type="text" value="{$displayorder}" id="displayorder" onkeyup="this.value=this.value.replace(/[^0-9]/g,'')" onafterpaste="this.value=this.value.replace(/[^0-9]/g,'')" class="input-text w100">
                            </div>
                        </li>
                    </ul>
                    <div class="clear clearfix mt-5">
                        <a class="btn btn-primary radius size-L ml-115" href="javascript:;" id="btn_save">保存</a>
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
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.2303&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
