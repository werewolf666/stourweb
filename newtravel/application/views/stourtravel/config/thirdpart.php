<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>微信微博设置</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base_new.css'); }
    {php echo Common::getScript('config.js');}
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
</head>
<body left_bottom=bcIwOs >

	<table class="content-tab">
        <tr>
            <td width="119px" class="content-lt-td"  valign="top">
                {template 'stourtravel/public/leftnav'}
                <!--右侧内容区-->
            </td>
            <td valign="top" class="content-rt-td">
                <form id="configfrm">
                    <div class="cfg-header-bar">
                        <div class="cfg-header-tab">
                            <span class="item on">第三方登陆</span>
                            <span class="item">三方登录流程</span>
                        </div>
                        <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                    </div>
                    <div class="third-container">
                        <div class="clear mt-20">
                            <h5 class="ml-20">QQ号码登录{Common::get_help_icon('cfg_third_login_qq')}</h5>
                            <ul class="info-item-block">
                                <li>
                                    <span class="item-hd">App ID：</span>
                                    <div class="item-bd">
                                        <input type="text" name="cfg_qq_appid" id="cfg_qq_appid" class="input-text w300" >
                                    </div>
                                </li>
                                <li>
                                    <span class="item-hd">App Key：</span>
                                    <div class="item-bd">
                                        <input type="text" name="cfg_qq_appkey" id="cfg_qq_appkey" class="input-text w300">
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="clear mt-20">
                            <h5 class="ml-20">新浪微博登录{Common::get_help_icon('cfg_third_login_sina')}</h5>
                            <ul class="info-item-block">
                                <li>
                                    <span class="item-hd">App Key：</span>
                                    <div class="item-bd">
                                        <input type="text" name="cfg_sina_appkey" id="cfg_sina_appkey" class="input-text w300" >
                                    </div>
                                </li>
                                <li>
                                    <span class="item-hd">App secret key：</span>
                                    <div class="item-bd">
                                        <input type="text" name="cfg_sina_appsecret" id="cfg_sina_appsecret" class="input-text w300" >
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="clear mt-20">
                            <h5 class="ml-20">微信登录{Common::get_help_icon('cfg_third_login_wechat')}</h5>
                            <ul class="info-item-block">
                                <li>
                                    <span class="item-hd">App ID：</span>
                                    <div class="item-bd">
                                        <input type="text" name="cfg_weixi_appkey" id="cfg_weixi_appkey" class="input-text w300" >
                                    </div>
                                </li>
                                <li>
                                    <span class="item-hd">App secret：</span>
                                    <div class="item-bd">
                                        <input type="text" name="cfg_weixi_appsecret" id="cfg_weixi_appsecret" class="input-text w300" >
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="third-container">

                        <div class="clear mt-10 ml-20">
                            <label class="radio-label"><input class="mt-10"  id="third_bind_2" type="radio" name="cfg_third_login_bind" value="2"/> 首次使用三方登陆，绑定会员帐号{Common::get_help_icon('cfg_third_login_bind_user')}</label>

                        </div>
                        <div class="clear mt-10 ml-20">
                            <label class="radio-label"><input class="mt-10"  id="third_bind_1" type="radio" name="cfg_third_login_bind" value="1"/> 首次使用三方登陆，不绑定会员帐号{Common::get_help_icon('cfg_third_login_no_bind')}</label>

                        </div>

                    </div>
                    <div class="clear clearfix">
                        <a class="btn btn-primary radius size-L ml-115" href="javascript:;" id="btn_save">保存</a>
                        <!-- <a class="cancel" href="#">取消</a>-->
                        <input type="hidden" name="webid" id="webid" value="0">
                    </div>
                </form>
            </td>
        </tr>
    </table>

	<script>

	$(document).ready(function(){

        $(".cfg-header-tab .item").click(function(){
            $(this).addClass('on');
            $(this).siblings().removeClass('on');
            var index=$(this).index();
            $(".third-container").hide();
            $(".third-container:eq("+index+")").show();
        });
        $(".cfg-header-tab .item:first").trigger('click');


        //配置信息保存
        $("#btn_save").click(function(){

            var webid= 0
            Config.saveConfig(webid);
        })

        getConfig(0);


     });


       //获取配置
        function getConfig(webid)
        {
            var fields = ['cfg_qq_appid','cfg_qq_appkey','cfg_sina_appkey','cfg_sina_appsecret','cfg_weixi_appkey','cfg_weixi_appsecret','cfg_third_login_bind'];
            Config.getConfig(webid,function(data){
                $("#cfg_qq_appid").val(data.cfg_qq_appid);
                $("#cfg_qq_appkey").val(data.cfg_qq_appkey);
                $("#cfg_sina_appkey").val(data.cfg_sina_appkey);
                $("#cfg_sina_appsecret").val(data.cfg_sina_appsecret);
                $("#cfg_weixi_appkey").val(data.cfg_weixi_appkey);
                $("#cfg_weixi_appsecret").val(data.cfg_weixi_appsecret);

                var bind_val=data.cfg_third_login_bind;
                bind_val=!bind_val?0:bind_val;

                $("#third_bind_"+bind_val).trigger('click');


            },fields)

        }



    </script>

</body>
</html>
