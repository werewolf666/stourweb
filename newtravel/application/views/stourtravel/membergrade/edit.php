<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title color_clear=zqAgJl >会员等级添加/修改</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base_new.css'); }
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js,product_add.js,choose.js,imageup.js,template.js");}
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
</head>
<body>
<table class="content-tab">
    <tr>
        <td width="119px" class="content-lt-td" valign="top">
            {template 'stourtravel/public/leftnav'}
            <!--右侧内容区-->
        </td>
        <td valign="top" class="content-rt-td ">

            <form method="post" name="product_frm" id="product_frm">
                <div class="manage-nr">
                    <div class="cfg-header-bar" id="nav">
                        <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                    </div>
                    <!--基础信息开始-->
                    <div class="clear">
                        <input type="hidden" name="is_system" value="0">
                        <ul class="info-item-block">
                            <li>
                                <span class="item-hd">会员等级名称：</span>
                                <div class="item-bd">
                                    <input class="input-text w150" type="input" id="name"  name="name" value="{$info['name']}"/> <span class="red" id="name_err"></span>
                                </div>
                            </li>
                            <li>
                                <span class="item-hd">积分区间：</span>
                                <div class="item-bd">
                                    <input class="input-text w150 {if isset($info)&&$info['begin']==0}c-999{/if}" type="input" id="begin" {if isset($info)&&$info['begin']==0} disabled{/if} name="begin" value="{$info['begin']}"/> ~ <input class="input-text w150" type="input" id="end" name="end" value="{$info['end']}"/> <span class="red" id="name_err"></span>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <!--/基础信息结束-->
                    <div class="clear clearfix mt-5">
                        <input type="hidden" name="id" id="id" value="{$info['id']}"/>
                        <input type="hidden" name="action" id="action" value="{$action}"/>
                        <a class="btn btn-primary radius size-L ml-115" id="btn_save" href="javascript:;">保存</a>
                    </div>

                </div>
            </form>
        </td>
    </tr>
</table>

<script>

    var action = "{$action}";
    $(document).ready(function () {
        //保存
        $("#btn_save").click(function () {
            if($('#name').val()==''){
                ST.Util.showMsg('请填写“会员等级名称”', '3', 2000);
                return false;
            }
            if($('#begin').val()==''){
                ST.Util.showMsg('请填写“积分区间”', '3', 2000);
                return false;
            }
            if($('#end').val()=='' ){
                ST.Util.showMsg('请填写“积分区间”', '3', 2000);
                return false;
            }
            $.ajaxform({
                url: SITEURL + "membergrade/ajax_save",
                method: "POST",
                form: "#product_frm",
                dataType: "JSON",
                success: function (data) {

                    if (data.status) {
                        ST.Util.showMsg('保存成功!', '4', 2000);
                        setTimeout(function () {
                            parent.window.gbl_tabs.remove(parent.window.currentTab);
                        }, 2000);
                    }else{
                        ST.Util.showMsg(data.msg, '3', 2000);
                    }
                }});
        });
    });
</script>

</body>
</html>
