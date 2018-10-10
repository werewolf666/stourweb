<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>图片库配置-思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,base_new.css,gallery.css');}
    {php echo Common::getScript('config.js');}
</head>
<body>
<table class="content-tab">
    <tr>
        <td width="119px" class="content-lt-td" valign="top">
            {template 'stourtravel/public/leftnav'}
            <!--右侧内容区-->
        </td>
        <td valign="top" class="content-rt-td ">

            <form id="configfrm">
                <div class="manage-nr">
                    <div class="w-set-tit bom-arrow" id="nav">
                        <a href="javascript:;" class="fr btn btn-primary radius mt-3 mr-10" onclick="window.location.reload()">刷新</a>
                    </div>
                    <!--基础信息开始-->
                    <div class="product-add-div">
                        <div class="gallery-server gallery-accelerate">
                            <ul class="info-item-block">
                                <input type="hidden" name="cfg_image_quality_open" value="1" />
                                <li>
                                    <span class="item-hd">显示品质{Common::get_help_icon('image_quality')}：</span>
                                    <div class="item-bd">
                                        <input type="text" name="cfg_image_quality" value="{$config['cfg_image_quality']}" id="quality_input" class="input-text">
                                        <span>%</span>
                                        <span class="error-span c-999" id="quality_span">*允许范围50-100%</span>
                                    </div>
                                </li>
                                <li>
                                    <span class="item-hd">图片域名{Common::get_help_icon('image_url')}：</span>
                                    <div class="item-bd">
                                        <input type="text" name="cfg_m_img_url" value="{$config['cfg_m_img_url']}"  class="input-text w200">

                                    </div>
                                </li>
                            </ul>
                            <div class="gallery-server-bc mt-5">
                                <a class="btn btn-primary radius size-L ml-115" href="javascript:void(0)" id="btn_save">保存</a>
                            </div>
                        </div>
                    </div>
                    <!-- 基础信息结束 -->
                </div>
            </form>
        </td>
    </tr>
</table>
<!--图库配置-->
</body>
<script type="text/javascript" charset="utf-8">
    $('#quality_input').blur(function(){
        var value=parseInt($(this).val());
        if(isNaN(value)){
            value=0;
        }
        if(value<50 || value>100){
            $(this).addClass('error-text');
            $('#quality_span').addClass('c-red');
        }else{
            $(this).removeClass('error-text');
            $('#quality_span').removeClass('c-red');
        }
        $(this).val(value);
    });
    //保存
    $("#btn_save").click(function () {
        var value=parseInt($('input[name="cfg_image_quality"]').val());
        if(isNaN(value) || value<50 || value>100){
            $('#quality_input').addClass('error-text');
            $('#quality_span').addClass('c-red');
            return false;
        }
        var m_img_url = $('input[name="cfg_m_img_url"]').val();
        if(m_img_url.length<1){
            ST.Util.showMsg('请填写图片域名', 5);
            return;
        }

        var value = parse_url($('input[name="cfg_m_img_url"]').val());

        $('input[name="cfg_m_img_url"]').val(value);
        Config.saveConfig(0);
    });

    function parse_url(url) {
        var reg = /^https*:\/\//;
        url = reg.test(url)?url:'{parse_url(url::base(true),PHP_URL_SCHEME)}' + '://' + url;
        return url;
    }


</script>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.1503&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
