<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>网页底部</title>
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
                                    <span class="item-hd">签约流程{Common::get_help_icon('cfg_payment')}：</span>
                                    <div class="item-bd">
                                        {php Common::getEditor('cfg_payment',$configinfo['cfg_payment'],$sysconfig['cfg_admin_htmleditor_width'],300);}
                                    </div>
                                </li>
                            </ul>
                            <div class="clear clearfix">
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


          //子站切换点击
//        $(".web-set").find('a').click(function(){
//            var webid = $(this).attr('data-webid');
//            $("#webid").val($(this).attr('data-webid'));
//            $("#webname").html($(this).html());
//            $(this).addClass('on').siblings().removeClass('on');
//            getConfig(webid);//重新读取配置
//
//
//        })

        //配置信息保存
        $("#btn_save").click(function(){
            var webid= $("#webid").val();
            Config.saveConfig(webid);
        })

        //setTimeout(getConfig,500);//延迟500毫秒调用数据显示,防止编辑器没有加载完成返回错误.
    });


       //获取配置
        function getConfig(webid)
        {
            var fields = 'cfg_payment';
            Config.getConfig(webid,function(data){
                cfg_paymentEditor.setContent(data.cfg_payment);

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
