<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>支付设置</title>
    {template 'stourtravel/public/public_min_js'}
    {Common::getCss('style.css,base.css,base_new.css')}
    {Common::getScript('jquery.upload.js')}

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
                            <span class="item-hd">网关商户号{Common::get_help_icon('payset_bill_account')}：</span>
                            <div class="item-bd">
                                <input name="cfg_bill_account" type="text" value="{$configs['cfg_bill_account']}" id="cfg_bill_account" class="input-text w300">
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">商户证书密钥{Common::get_help_icon('payset_bill_key')}：</span>
                            <div class="item-bd">
                                <input name="cfg_bill_key" type="text" value="{$configs['cfg_bill_key']}" id="cfg_bill_key" class="input-text w300">
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">排序{Common::get_help_icon('payset_displayorder')}：</span>
                            <div class="item-bd">
                                <input name="displayorder" type="text" value="{$displayorder}" id="cfg_bill_key" onkeyup="this.value=this.value.replace(/[^0-9]/g,'')" onafterpaste="this.value=this.value.replace(/[^0-9]/g,'')" class="input-text w100">
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">证书上传{Common::get_help_icon('payset_bill_upload')}：</span>
                            <div class="item-bd">
                                <a href="javascript:;" id="upload_btn" class="btn btn-primary radius size-S">上传证书</a>
                                <span class="c-green isloaded {if !$is_uploaded}hide{/if}">*&nbsp;证书已上传！</span>

                            </div>
                        </li>
                    </ul>

                    <div class="clear clearfix mt-20">
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

        //上传文件
        $('#upload_btn').click(function(){
            uploadFile();
        })
    });

    //上传
    function uploadFile() {

        // 上传方法
        $.upload({
            // 上传地址
            url: SITEURL+'payset/upload_certs/',
            // 文件域名字
            fileName: 'Filedata',
            fileType: 'zip',
            // 其他表单数据
            params: {uploadcookie:"<?php echo Cookie::get('username')?>",'payid':'{$payid}'},
            // 上传完成后, 返回json, text
            dataType: 'json',
            // 上传之前回调,return true表示可继续上传
            onSend: function() {
                return true;
            },
            // 上传之后回调
            onComplate: function(data) {

                if(data.status){
                    ST.Util.showMsg('证书上传成功',4);
                    $('#upload_btn').siblings('.isloaded').removeClass('hide');
                }else{
                    ST.Util.showMsg(data.msg,5);
                }


            }
        });




    }
  </script>

</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.2202&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
