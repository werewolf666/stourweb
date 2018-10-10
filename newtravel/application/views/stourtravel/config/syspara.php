<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>系统参数</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,jqtransform.css,base_new.css'); }
    {php echo Common::getScript('config.js,jquery.jqtransform.js,jquery.colorpicker.js');}
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
</head>
<body right_border=zWiduj >
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
                            <div class="info-item-block">
                                <li class="rowElem">
                                    <span class="item-hd">前台出发地：</span>
                                    <div class="item-bd">
                                        <label class="radio-label"><input type="radio" name="cfg_startcity_open" value="1" {if $config['cfg_startcity_open']=='1'}checked{/if}>开启</label>
                                        <label class="radio-label ml-20"><input type="radio" name="cfg_startcity_open" value="0" {if $config['cfg_startcity_open']=='0'}checked{/if}>关闭</label>
                                    </div>
                                </li>
                                <li class="rowElem">
                                    <span class="item-hd">CSS/JS压缩{Common::get_help_icon('cfg_compress_open')}：</span>
                                    <div class="item-bd">
                                        <label class="radio-label"><input type="radio" name="cfg_compress_open" value="1" {if $config['cfg_compress_open']==1}checked{/if}>开启</label>
                                        <label class="radio-label ml-20"><input type="radio" name="cfg_compress_open" value="0" {if $config['cfg_compress_open']==0}checked{/if}>关闭</label>
                                        <span class="item-text va-t c-999 ml-20">*开启：打包合并压缩CSS/JS文件，减少加载次数；关闭：独立调取每个文件，加载一个调取一次</span>
                                    </div>
                                </li>
                                <li class="rowElem">
                                    <span class="item-hd">客户端缓存{Common::get_help_icon('cfg_cache_open')}：</span>
                                    <div class="item-bd">
                                        <label class="radio-label"><input type="radio" name="cfg_cache_open" value="1" {if $config['cfg_cache_open']==1}checked{/if}>开启</label>
                                        <label class="radio-label ml-20"><input type="radio" name="cfg_cache_open" value="0" {if $config['cfg_cache_open']==0}checked{/if}>关闭</label>
                                        <span class="item-text va-t c-999 ml-20">*开启：客户端将缓存访问数据，定期自动更新；关闭：客户端不缓存访问数据，每次从服务器获取最新数据</span>
                                    </div>
                                </li>
                                <li class="rowElem">
                                    <span class="item-hd">登录下单{Common::get_help_icon('cfg_login_order')}：</span>
                                    <div class="item-bd">
                                        <label class="radio-label"><input type="radio" name="cfg_login_order" value="1" {if $config['cfg_login_order']==1}checked{/if}>开启</label>
                                        <label class="radio-label ml-20"><input type="radio" name="cfg_login_order" value="0" {if $config['cfg_login_order']==0}checked{/if}>关闭</label>
                                        <span class="item-text va-t c-999 ml-20">*开启：只能登录后才能下订单；关闭：不登录即可下订单，后台会默认将联系人手机注册成为会员，并通知该手机号码。</span>
                                    </div>
                                </li>
                                <li class="rowElem">
                                    <span class="item-hd">后台快捷菜单{Common::get_help_icon('cfg_quick_menu')}：</span>
                                    <div class="item-bd">
                                        <label class="radio-label"><input type="radio"  name="cfg_quick_menu" value="1" {if $config['cfg_quick_menu']=='1'}checked{/if}>开启</label>
                                        <label class="radio-label ml-20"><input type="radio"  name="cfg_quick_menu" value="0" {if empty($config['cfg_quick_menu'])}checked{/if}>关闭</label>
                                    </div>
                                </li>
                                <li class="rowElem">
                                    <span class="item-hd">编辑器{Common::get_help_icon('cfg_admin_htmleditor_width')}：</span>
                                    <div class="item-bd">
                                        <span class="item-text">宽</span>
                                        <input class="input-text w50 ml-5" type="text"  name="cfg_admin_htmleditor_width" id="cfg_admin_htmleditor_width" value="{$config['cfg_admin_htmleditor_width']}" />
                                        <span class="item-text ml-5">px</span>
                                    </div>
                                </li>
                                <li class="rowElem">
                                    <span class="item-hd">管理员手机：</span>
                                    <div class="item-bd">
                                        <input type="text" class="input-text w200" name="cfg_webmaster_phone" id="cfg_webmaster_phone" value="{$config['cfg_webmaster_phone']}" />
                                        <span class="item-text c-999 ml-20">*多个手机可用“,”号分隔，所有手机都将收到对管理员的消息通知</span>
                                    </div>
                                </li>
                                <li class="rowElem">
                                    <span class="item-hd">管理员Email：</span>
                                    <div class="item-bd">
                                        <input type="text" class="input-text w200" name="cfg_webmaster_email" id="cfg_webmaster_email" value="{$config['cfg_webmaster_email']}" />
                                        <span class="item-text c-999 ml-20">*多个Email可用“,”号分隔，所有Email都将收到对管理员的消息通知</span>
                                    </div>
                                </li>
                            </div>
                            <div class="clear clearfix">
                                <a class="btn btn-primary radius size-L ml-115" href="javascript:;" id="btn_save">保存</a>
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

            //var webid= $("#webid").val();
            Config.saveConfig(0);


        });



    })












    </script>

</body>
</html>
