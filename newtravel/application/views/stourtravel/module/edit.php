<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('msgbox.css','js/msgbox/'); }
    {php echo Common::getCss('base_new.css'); }
    <style>
        .info-item-block{
            padding: 0;
        }
        .info-item-block>li{
            padding: 0 0 10px;
        }
        .info-item-block>li .item-hd{
            width: 80px;
        }
        .info-item-block>li .item-bd{
            padding-left: 85px;
        }
    </style>
</head>

<body style=" width: 600px; height: 367px; overflow: hidden">

    <div class="middle-con" >
        <form name="frm" id="frm" action="{$action}">
            <ul class="info-item-block">
                <li class="nr-list">
                    <span class="item-hd">模块名称：</span>
                    <div class="item-bd">
                        <input type="text" id="modulename" name="modulename" class="input-text" value="{$info['modulename']}" />
                        <div class="help-ico">{$helpico}</div>
                    </div>
                </li>
                <li class="nr-list">
                    <span class="item-hd">模块类型{Common::get_help_icon('module_edit_moduletype',true)}：</span>
                    <div class="item-bd">
                        <span class="select-box">
                            <select class="select" name="moduletype">
                                <option value="0" {if $info['type']==0}selected="selected"{/if}>信息模块</option>
                                <option value="1" {if $info['type']==1}selected="selected"{/if}>广告模块</option>
                            </select>
                        </span>
                    </div>
                </li>
                <li class="nr-list">
                    <span class="item-hd">模块内容{Common::get_help_icon('module_edit_body',true)}：</span>
                    <div class="item-bd">
                        <textarea class="textarea" name="body" id="body" rows="10">{$info['body']}</textarea>
                    </div>
                </li>
            </ul>
            <div class="clear clearfix text-c">
                <a class="btn btn-primary radius size-L" href="javascript:;" id="btn_save">保存</a>
            </div>
            <input type="hidden" name="webid" id="webid" value="{$webid}"/>
            <input type="hidden" name="articleid" id="articleid" value="{$info['id']}"/>
        </form>
    </div>

	<script>
        $(function(){

            $("#btn_save").click(function(){
                var url = "{$GLOBALS['cfg_cmspath']}";
                var ajaxurl = SITEURL + 'module/'+"{$action}";
                var modulename = $("#modulename").val();
                var body = $("#body").val();
                if(modulename==''){
                    ST.Util.showMsg('模块名称不能为空',5);
                    return false;
                }
                if(body==''){
                    ST.Util.showMsg('模块内容不能为空',5);
                    return false;
                }


                $.ajaxform({
                    url: ajaxurl,
                    method: 'POST',
                    form : '#frm',
                    dataType:'json',
                    success: function (data) {
                        if(data.status)
                        {
                            ST.Util.showMsg('保存成功',4);
                           // ST.Util.closeBox();//关闭当前窗口
                        }
                        else
                        {
                            ST.Util.showMsg('保存失败',5);
                        }

                    }

                });


            })
        })

	</script>
</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.0609&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
