<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>支付设置</title>
    {template 'stourtravel/public/public_min_js'}
    {Common::getCss('style.css,base.css,base_new.css')}
    {Common::getScript('jquery.upload.js')}
</head>
<body left_bottom=bcIwOs >

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
                <div class="clear">
                    <form id="frm">
                        <div class="pay-container" >
                            <ul class="info-item-block">
                                <li>
                                    <span class="item-hd">接口类型{Common::get_help_icon('payset_alipay_alipaytype')}：</span>
                                    <div class="item-bd">
                                        <label class="check-label" for="alipaytype0"><input id="alipaytype0" type="checkbox" name="payids[]" value="11" {if in_array('11',$pay_types)} checked="checked"{/if} >即时到帐交易接口</label>
                                        <label class="check-label ml-20" for="alipaytype1"><input id="alipaytype1" type="checkbox" name="payids[]" value="12" {if in_array('12',$pay_types)} checked="checked"{/if}>双功能</label>
                                        <label class="check-label ml-20" for="alipaytype2"><input id="alipaytype2" type="checkbox" name="payids[]" value="13" {if in_array('13',$pay_types)} checked="checked"{/if}>纯担保交易</label>
                                        <label class="check-label ml-20" for="alipaytype3"><input id="alipaytype3" type="checkbox" name="payids[]" value="14" {if in_array('14',$pay_types)} checked="checked"{/if}>网银支付</label>
                                    </div>
                                </li>
                                <li>
                                    <span class="item-hd">收款帐号{Common::get_help_icon('pay_alipay_account')}：</span>
                                    <div class="item-bd">
                                        <input name="cfg_alipay_account" type="text" value="{$configs['cfg_alipay_account']}" id="cfg_alipay_account" class="input-text w300">
                                    </div>
                                </li>
                                <li>
                                    <span class="item-hd">合作者身份ID{Common::get_help_icon('payset_alipay_pid')}：</span>
                                    <div class="item-bd">
                                        <input name="cfg_alipay_pid" type="text" value="{$configs['cfg_alipay_pid']}" id="cfg_alipay_pid" class="input-text w300">
                                    </div>
                                </li>
                                <li>
                                    <span class="item-hd">应用ID{Common::get_help_icon('payset_alipay_appid')}：</span>
                                    <div class="item-bd">
                                        <input name="cfg_alipay_appid" type="text" value="{$configs['cfg_alipay_appid']}" id="cfg_alipay_appid" class="input-text w300">
                                    </div>
                                </li>
                                <li>
                                    <span class="item-hd">安全校验码{Common::get_help_icon('payset_alipay_key')}：</span>
                                    <div class="item-bd">
                                        <input name="cfg_alipay_key" type="text" value="{$configs['cfg_alipay_key']}" id="cfg_alipay_key" class="input-text w300">
                                    </div>
                                </li>
                                <li>
                                    <span class="item-hd">排序{Common::get_help_icon('payset_displayorder')}：</span>
                                    <div class="item-bd">
                                        <input name="displayorder" type="text" value="{$displayorder}" id="displayorder" onkeyup="this.value=this.value.replace(/[^0-9]/g,'')" onafterpaste="this.value=this.value.replace(/[^0-9]/g,'')" class="input-text w100">
                                    </div>
                                </li>
                                <li>
                                    <span class="item-hd">证书上传{Common::get_help_icon('payset_alipay_upload')}：</span>
                                    <div class="item-bd">
                                        <!--<a href="javascript:;" class="hide" id="upload_file_btn">上传文件</a>-->
                                        <a href="javascript:;" id="upload_btn" class="btn btn-primary radius size-S">上传证书</a>
                                        <span class="c-green isloaded {if !$is_uploaded}hide{/if}">*&nbsp;证书已上传！</span>

                                        <div class="c-999 mt-10">*请以*.zip格式上传</div>
                                    </div>
                                </li>
                            </ul>
                            <div class="ml-115 c-999">*提示：由于"双功能"“担保交易”“网银支付”三项服务已被支付宝下线，不再接收签约，新用户请只选择“即时到账”服务。</div>
                        </div>
                        <div class="clear clearfix mt-20">
                            <a class="btn btn-primary radius size-L ml-115" href="javascript:;" id="btn_save">保存</a>
                        </div>
                    </form>
                </div>
            </td>
        </tr>
    </table>
  
  <script>
	$(document).ready(function(){

        $("#btn_save").click(function(){
            $.ajaxform({
                url   :  SITEURL+"payset/ajax_alipay_save",
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
