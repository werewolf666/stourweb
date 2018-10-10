<!doctype html>
<html>
<head size_body=oyvz8B >
<meta charset="utf-8">
<title>思途CMS{$coreVersion}</title>
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
            </td>
            <td valign="top" class="content-rt-td">
                <div class="cfg-header-bar clearfix">
                    <span class="cfg-select-box btnbox mt-5 ml-10" id="custom_website" data-url="box/index/type/custom_website?jmp_webid={$jmp_webid}" data-result="result_webid">站点切换&nbsp;&gt;&nbsp;<span id="result_webid">主站</span><i class="arrow-icon"></i></span>
                    <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                </div>
                <form id="configfrm">
                    <div class="w-set-con">
                        <!--<div class="w-set-tit bom-arrow">-->
                        <!--<span class="on"><s></s>首页设置</span>-->
                        <!--</div>-->
                        <div class="w-set-nr">
                            <ul class="info-item-block">
                                <li>
                                    <span class="item-hd">站点名称{Common::get_help_icon('cfg_webname')}：</span>
                                    <div class="item-bd">
                                        <input type="text" name="cfg_webname" id="cfg_webname" class="input-text w500" />
                                    </div>
                                </li>
                                <li>
                                    <span class="item-hd">导航文字{Common::get_help_icon('cfg_indexname')}：</span>
                                    <div class="item-bd">
                                        <input type="text" id="cfg_indexname" name="cfg_indexname" id="cfg_indexname" class="input-text w500" />
                                    </div>
                                </li>
                                <li>
                                    <span class="item-hd">标题{Common::get_help_icon('cfg_indextitle')}：</span>
                                    <div class="item-bd">
                                        <input type="text" id="cfg_indextitle" name="cfg_indextitle" id="cfg_indextitle" class="input-text w500" />
                                    </div>
                                </li>
                                <li>
                                    <span class="item-hd">关键词{Common::get_help_icon('cfg_keywords')}：</span>
                                    <div class="item-bd">
                                        <input type="text" id="cfg_keywords" name="cfg_keywords" class="input-text w500" />
                                    </div>
                                </li>
                                <li>
                                    <span class="item-hd">描述{Common::get_help_icon('cfg_description')}：</span>
                                    <div class="item-bd">
                                        <textarea id="cfg_description" name="cfg_description"  cols="" rows="" class="textarea w500"></textarea>
                                    </div>
                                </li>
                                <li>
                                    <span class="item-hd">Meta信息{Common::get_help_icon('cfg_indexcode')}：</span>
                                    <div class="item-bd">
                                        <textarea id="cfg_indexcode" name="cfg_indexcode" cols="" rows="" class="textarea w500"></textarea>
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
	//窗口改变
/*	$(window).resize(function(){
		 Config.setDivAttr();
    });*/
    var jmp_webid="{$jmp_webid}";
    var jmp_webname="{$jmp_dest_info['kindname']}";
	$(document).ready(function(){



        $(".btnbox").buttonBox();



        //配置信息保存
        $("#btn_save").click(function(){
            var webid= $("#webid").val();
            Config.saveConfig(webid);
        })

        if(jmp_webid)
        {
            custom_website(null,jmp_webid,jmp_webname,'result_webid');
        }
        else
        {
            getConfig(0); //主站配置
        }
        //默认读取配置

		});


       //获取配置
        function getConfig(webid)
        {
            var fields = ['cfg_webname','cfg_indexname','cfg_indextitle','cfg_keywords','cfg_description','cfg_indexcode'];
            Config.getConfig(webid,function(data){

                $("#cfg_webname").val(data.cfg_webname);
                $("#cfg_indexname").val(data.cfg_indexname);
                $("#cfg_indextitle").val(data.cfg_indextitle);
                $("#cfg_keywords").val(data.cfg_keywords);
                $("#cfg_description").val(data.cfg_description);
                $("#cfg_indexcode").val(data.cfg_indexcode);
            },fields)


        }


    function  custom_website(obj,webid,webname,resultid) {
        $("#" + resultid).html(webname);
        if (obj)
        {
            $(obj).addClass('cur').siblings().removeClass('cur');
        }
        $('#webid').val(webid);
        getConfig(webid);
    }




	</script>

</body>
</html>
