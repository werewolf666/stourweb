<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
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
                    <div class="cfg-header-bar">
                        <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                    </div>
                    <ul class="info-item-block clear">
                        <li>
                            <span class="item-hd">思途ID{Common::get_help_icon('cfg_licenseid')}：</span>
                            <div class="item-bd">
                                <input type="text" name="cfg_licenseid" id="cfg_licenseid" value="{$serailnum}" class="input-text w300" />
                                <span class="item-text c-999 ml-10">注：在www.stourweb.com会员中心的用户ID，即是系统的授权ID，请正确填写，否则会影响系统功能的使用，如升级功能；</span>
                            </div>
                        </li>
                    </ul>
                    <div class="clear clearfix mt-5">
                        <a id="save" class="btn btn-primary radius size-L ml-115" href="javascript:;">保存</a>
                    </div>
                </form>
            </td>
        </tr>
    </table>



<script>
    $('#save').click(function () {
        saveBind();
    })

    function saveBind()
    {
        var licenseid = $("#cfg_licenseid").val();
        var weburl = window.location.host;
        var frmdata ={licenseid:licenseid,weburl:weburl} ;
        ST.Util.showMsg('正在绑定授权信息...', 6, 1000000);
        $.ajax({
            type:"post",
            data: frmdata,
            dataType: 'json',
            url:SITEURL+"upgrade/ajax_bind_license",
            success:function(data){
                ST.Util.hideMsgBox();
                if(data.status==1){
                    ST.Util.showMsg(data.msg,4,1000);
                }
                else{
                    ST.Util.showMsg(data.msg,5,2000);
                }
            }
        })
    }
</script>

</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.0.201711.0102&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
