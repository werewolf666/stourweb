<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>系统模块版本管理-思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,plist.css,sms_sms.css'); }
    {php echo Common::getScript('common.js,config.js,DatePicker/WdatePicker.js');}
</head>
<body>
<table class="content-tab">
<tr>
<td width="119px" class="content-lt-td"  valign="top">
    {template 'stourtravel/public/leftnav'}
    <!--右侧内容区-->
</td>
<td valign="top" class="content-rt-td">
<!--面包屑-->
    <div class="list-top-set">
        <div class="list-web-pad"></div>
        <div class="list-web-ct">
            <table class="list-head-tb">
                <tbody><tr>
                    <td class="head-td-lt">

                    </td>
                    <td class="head-td-rt">
                        <a href="javascript:;" class="refresh-btn" onclick="window.location.reload()">刷新</a>

                </tr>
                </tbody></table>
        </div>
    </div>
    <form id="configfrm">
    <div class="manage-nr">
        <div class="sms-base sms-single">
            <div class="b-tit">电脑版</div>
            <div class="b-con">
                <div class="tool-cs">
                    {loop $pcVersionList $pcVersion}
                    <span><input type="radio" name="cfg_pc_version" {if $pcVersion['status'] == '0'} disabled="disabled" {/if} value="{$pcVersion['id']}" {if $config['cfg_pc_version']==$pcVersion['id']} checked="checked" {/if}/><label>{$pcVersion['name']}</label></span>&nbsp;&nbsp;
                    {/loop}
                </div>
            </div>
        </div>
        <div class="sms-base sms-single">
            <div class="b-tit">移动版</div>
            <div class="b-con">
                <div class="tool-cs">
                    {loop $mobileVersionList $mobileVersion}
                    <span><input type="radio" name="cfg_mobile_version" {if $mobileVersion['status'] == '0'} disabled="disabled" {/if} value="{$mobileVersion['id']}" {if $config['cfg_mobile_version']==$mobileVersion['id']} checked="checked" {/if}/><label>{$mobileVersion['name']}</label></span>&nbsp;&nbsp;
                    {/loop}
                </div>
            </div>
        </div>
        <div class="sms-base sms-single">
            <div class="b-tit">自动更新控制</div>
            <div class="b-con">
                <div class="tool-cs">
                    <input id="crl_cfg_pc_upgrade" type="checkbox"  {if $config['cfg_pc_upgrade']=='1'} checked="checked" {/if} /><label>启用电脑版自动更新</label>
                    <input id="cfg_pc_upgrade" name="cfg_pc_upgrade" type="hidden"  value="" />
                    &nbsp;&nbsp;
                    <input id="crl_cfg_mobile_upgrade" type="checkbox"  {if $config['cfg_mobile_upgrade']=='1'} checked="checked" {/if} /><label>启用移动版自动更新</label>
                    <input id="cfg_mobile_upgrade" name="cfg_mobile_upgrade" type="hidden"  value="" />
                </div>
            </div>
        </div>
        <div class="opn-btn">
            <a class="normal-btn" href="javascript:;" id="btn_save">保存</a>
            <!-- <a class="cancel" href="#">取消</a>-->
            <input type="hidden" name="webid" id="webid" value="0">

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

            if($("#crl_cfg_pc_upgrade").attr('checked') == undefined)
                $("#cfg_pc_upgrade").val("0");
            else
                $("#cfg_pc_upgrade").val("1");

            if($("#crl_cfg_mobile_upgrade").attr('checked') == undefined)
                $("#cfg_mobile_upgrade").val("0");
            else
                $("#cfg_mobile_upgrade").val("1");

            var webid= $("#webid").val();
            Config.saveConfig(webid, function(){
                $.ajax(
                    {
                        type: "post",
                        url: SITEURL + 'systemparts/ajax_further_processing',
                        dataType: 'json',
                        beforeSend: function () {
                            ST.Util.showMsg('正在完成后续处理,请稍后...', 6, 60000);
                        },
                        success: function (data) {
                            if (data.status) {
                                ST.Util.showMsg('处理成功', 4, 1000);
                            }
                        }

                    }
                );
            });
        })

    });
</script>


</body>
</html>
