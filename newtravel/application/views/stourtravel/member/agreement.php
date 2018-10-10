<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>系统参数</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,jqtransform.css,base_new.css'); }
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
                <a href="javascript:;" class="fr btn btn-primary radius mr-10 mt-6" onclick="window.location.reload()">刷新</a>
            </div>
          <div class="w-set-nr">
              <ul class="info-item-block">
                  <li class="rowElem">
                      <span class="item-hd">协议状态{Common::get_help_icon('cfg_member_agreement_open')}：</span>
                      <div class="item-bd">
                          <label class="radio-label"><input type="radio" name="cfg_member_agreement_open" value="1" {if $config['cfg_member_agreement_open']==1}checked{/if}>开启</label>
                          <label class="radio-label ml-20"><input type="radio" name="cfg_member_agreement_open" value="0" {if $config['cfg_member_agreement_open']==0}checked{/if}>关闭</label>
                          <span class="c-999 ml-20">*开启网站服务协议，用户在前端注册时必须同意该协议才能进行注册，如果关闭，则在注册页面不会显示网站服务协议。</span>
                      </div>
                  </li>
                  <li class="rowElem">
                      <span class="item-hd">协议标题：</span>
                      <div class="item-bd">
                        <input type="text" name="cfg_member_agreement_title" id="cfg_member_agreement_title" class="input-text w200" value="{$config['cfg_member_agreement_title']}">
                      </div>
                  </li>
                  <li class="rowElem">
                      <span class="item-hd">协议内容：</span>
                      <div class="item-bd">
                          {php Common::getEditor('cfg_member_agreement',$config['cfg_member_agreement'],$sysconfig['cfg_admin_htmleditor_width'],300);}
                      </div>
                  </li>
              </ul>

            <div class="clear clearfix mt-5">
            	<a class="btn btn-primary radius size-L ml-115" href="javascript:;" id="btn_save">保存</a>
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

            var open = parseInt($("input[name='cfg_member_agreement_open']").val());
            if(open == 1){
                var title = $('#cfg_member_agreement_title').val();
                var agreement = cfg_member_agreementEditor.getContent();
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
