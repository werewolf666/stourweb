<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>公告设置</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base_new.css'); }
    {php echo Common::getScript('config.js');}
    {php echo Common::getScript("jquery.buttonbox.js,choose.js"); }
</head>
<body>

    <table class="content-tab">
        <tr>
            <td width="119px" class="content-lt-td"  valign="top">
             {template 'stourtravel/public/leftnav'}
             <!--右侧内容区-->
            </td>
            <td valign="top" class="content-rt-td">
                <div class="cfg-header-bar clearfix">
                    <span class="cfg-select-box btnbox mt-5 ml-10" id="custom_website" data-url="box/index/type/custom_website" data-result="result_webid">站点切换&nbsp;&gt;&nbsp;<span id="result_webid">主站</span><i class="arrow-icon"></i></span>
                    <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                </div>
                <form id="configfrm">
                    <div class="w-set-con">

                        <div class="w-set-nr">
                            <ul class="info-item-block">
                                <li>
                                    <span class="item-hd">公告{Common::get_help_icon('cfg_gonggao')}：</span>
                                    <div class="item-bd">
                                        {php Common::getEditor('cfg_gonggao',$configinfo['cfg_gonggao'],$sysconfig['cfg_admin_htmleditor_width'],300);}
                                    </div>
                                </li>
                            </ul>
                            <div class="clear clearfix mt-5">
                                <a class="btn btn-primary size-L radius ml-115" href="javascript:;" id="btn_save">保存</a>
                                <!-- <a class="cancel" href="#">取消</a>-->
                            </div>
                        </div>
                    </div>
                </form>
            </td>
        </tr>
    </table>

    <input type="hidden" id="webid" value="0">
  
  
	<script>

	$(document).ready(function(){


        $(".btnbox").buttonBox();



        //配置信息保存
        $("#btn_save").click(function(){
            var webid= $("#webid").val();
            Config.saveConfig(webid);
        });
        getConfig(0); //主站配置
		});
       //获取配置
        function getConfig(webid)
        {
            var fields='cfg_gonggao';
            Config.getConfig(webid,function(data){
                cfg_gonggaoEditor.setContent(data.cfg_gonggao);
            },fields)
            
        }

        function  custom_website(obj,webid,webname,resultid) {
            $("#"+resultid).html(webname);
            $(obj).addClass('cur').siblings().removeClass('cur');
            $('#webid').val(webid);
            getConfig(webid);
        }




	</script>

</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.0.201710.3105&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
