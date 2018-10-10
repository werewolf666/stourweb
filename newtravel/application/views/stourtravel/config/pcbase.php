<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>基本设置</title>
    {template 'stourtravel/public/public_min_js'}
    {Common::getCss('style.css,base.css,base_new.css')}
    {Common::getScript('config.js')}

</head>
<body>

    <table class="content-tab">
        <tr>
            <td width="119px" class="content-lt-td" valign="top">
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
                                    <span class="item-hd">站点开关{Common::get_help_icon('cfg_web_open')}：</span>
                                    <div class="item-bd">
                                        <label class="radio-label"><input type="radio" name="cfg_web_open" value="1" {if $config['cfg_web_open']==1}checked{/if}>开启</label>
                                        <label class="radio-label ml-20"><input type="radio" name="cfg_web_open" value="0" {if $config['cfg_web_open']==0}checked{/if}>关闭</label>
                                    </div>
                                </li>
                                <li class="rowElem">
                                    <span class="item-hd">电脑端域名{Common::get_help_icon('pc_url')}：</span>
                                    <div class="item-bd">
                                        <input type="text" name="cfg_pc_url" id="cfg_pc_url" class="input-text w300" value="{$web_url}">
                                    </div>
                                </li>


                            </ul>
                            <div class="clear clearfix pt-20">
                                <input type="hidden" name="webid" id="webid" value="0">
                                <a class="btn btn-primary size-L radius ml-115" href="javascript:;" id="btn_save">保存</a>
                                <input type="hidden" name="webid" id="webid" value="0">
                            </div>
                        </div>
                    </div>
                </form>
            </td>
        </tr>
    </table>

<script>
    $(document).ready(function () {
        //配置信息保存
        $("#btn_save").click(function () {

            var cfg_pc_url = $('#cfg_pc_url').val();
            if(cfg_pc_url == ''){
                ST.Util.showMsg('电脑端域名不能为空',5,1000);
                return false;
            }
            var cfg_pc_url = parse_url(cfg_pc_url);
            $('#cfg_pc_url').val(cfg_pc_url)
            Config.saveConfig(0);
            $.post(SITEURL+'config/ajax_save_pc_url',{'cfg_pc_url':cfg_pc_url},function(){})
        });
    });
    //补全域名
    function parse_url(url) {
        var reg = /^https*:\/\//;
        url = reg.test(url)?url:'{parse_url(url::base(true),PHP_URL_SCHEME)}' + '://' + url;
        return url;
    }
</script>
</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.1503&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
