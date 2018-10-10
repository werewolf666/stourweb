<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>预定协议</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base_new.css,jqtransform.css'); }
    {php echo Common::getScript('config.js');}
</head>
<body>

	<table class="content-tab">
        <tr>
            <td width="119px" class="content-lt-td"  valign="top">
             {template 'stourtravel/public/leftnav'}
            <!--右侧内容区-->
            </td>
            <td valign="top" class="content-rt-td">

                <form id="configfrm">
                    <div class="w-set-con">
                        <div class="cfg-header-bar">
                            <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                        </div>
                        <div class="w-set-nr">

                            <ul class="info-item-block">
                                <li>
                                    <span class="item-hd">协议状态：</span>
                                    <div class="item-bd">
                                        <label class="radio-label">
                                            <input type="radio" name="cfg_order_agreement_open" value="1" {if $config['cfg_order_agreement_open']==1}checked{/if}>开启
                                        </label>
                                        <label class="radio-label ml-20">
                                            <input type="radio" name="cfg_order_agreement_open" value="0" {if $config['cfg_order_agreement_open']==0}checked{/if}>关闭
                                        </label>
                                        <span class="item-text pl-20 c-999">*开启预订协议，用户在预定产品时必须同意预订协议才能进行预定，关闭预订协议，则在预订产品时不显示预订协议。</span>
                                    </div>
                                </li>
                                <li>
                                    <span class="item-hd">协议标题：</span>
                                    <div class="item-bd">
                                        <input type="text" name="cfg_order_agreement_title" id="cfg_order_agreement_title" class="input-text w200" value="{$config['cfg_order_agreement_title']}">
                                    </div>
                                </li>
                                <li>
                                    <span class="item-hd">协议内容{Common::get_help_icon('cfg_order_agreement')}：</span>
                                    <div class="item-bd">
                                        {php Common::getEditor('cfg_order_agreement',$config['cfg_order_agreement'],$sysconfig['cfg_admin_htmleditor_width'],300);}
                                    </div>
                                </li>
                            </ul>

                            <div class="clear clearfix mt-5">
                                <a class="btn btn-primary size-L radius ml-115" href="javascript:;" id="btn_save">保存</a>
                                <!-- <a class="cancel" href="#">取消</a>-->
                                <input type="hidden" name="webid" id="webid" value="0">
                            </div>

                        </div>
                    </div>
                </form>
            </td>
        </tr>
    </table>

  
  
	<script>

        $(document).ready(function(){

            //配置信息保存
            $("#btn_save").click(function(){

                var open = parseInt($("input[name='cfg_order_agreement_open']").val());
                if(open == 1){
                    var title = $('#cfg_order_agreement_title').val();
                    var agreement = cfg_order_agreementEditor.getContent();
                    if(title==''){
                        ST.Util.showMsg('协议标题必须填写',5,1500);
                        return false;
                    }
                    if(agreement == ''){
                        ST.Util.showMsg('协议内容必须填写',5,1500);
                        return false;
                    }
                }

                Config.saveConfig(0);

            });

        })

    </script>

</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.0.201710.3105&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
