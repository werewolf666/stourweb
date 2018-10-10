<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>客服电话设置</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base_new.css'); }
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
                        <div class="w-set-tit bom-arrow">
                            <span class="kinditem on" data-url="kefu/other/parentkey/basic/itemid/8" data-name="第三方客服"><s></s>第三方客服</span>
                            <a href="javascript:;" class="fr btn btn-primary radius mr-10 mt-3" onclick="window.location.reload()">刷新</a>
                        </div>
                        <div class="w-set-nr">
                            <ul class="info-item-block">
                                <li>
                                    <span class="item-hd">客服代码{Common::get_help_icon('cfg_html_kefu')}：</span>
                                    <div class="item-bd">
                                        <textarea id="cfg_html_kefu" name="cfg_html_kefu"  cols="" rows="4" class="textarea w800"></textarea>
                                    </div>
                                </li>
                            </ul>
                            <div class="clear clearfix">
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



        //选中分类
        $(".w-set-tit").find('span').eq(2).addClass('on');
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
        var fields = 'cfg_html_kefu';
        Config.getConfig(webid,function(data){
            $("#cfg_html_kefu").val(data.cfg_html_kefu);
        },fields)

    }



</script>

</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.0.201710.1907&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
