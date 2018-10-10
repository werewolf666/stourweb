<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>提现审核--思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css'); }
    {php echo Common::getScript("jquery.validate.js"); }
</head>
<body style="background-color: #fff">
<form id="frm" name="frm">
    <div class="out-box-con">
        <dl class="list_dl">
            <dt class="wid_90">提现申请用户：</dt>
            <dd>
                {$info['applyusername']}
            </dd>
        </dl>
        <dl class="list_dl">
            <dt class="wid_90">用户角色：</dt>
            <dd>
                {$info['applyusertype_name']}
            </dd>
        </dl>
        <dl class="list_dl">
            <dt class="wid_90">申请提现金额：</dt>
            <dd>
                &yen;{$info['withdrawamount']}
            </dd>
        </dl>


        {if $info['proceeds_type']==1}
        <dl class="list_dl">
            <dt class="wid_90">提现银行卡号：</dt>
            <dd>
                {$info['bankcardnumber']}
            </dd>
        </dl>
        <dl class="list_dl">
            <dt class="wid_90">户名：</dt>
            <dd>
                {$info['bankaccountname']}
            </dd>
        </dl>
        <dl class="list_dl">
            <dt class="wid_90">开卡银行：</dt>
            <dd>
                {$info['bankname']}
            </dd>
        </dl>
        {elseif $info['proceeds_type']==2}
        <dl class="list_dl">
            <dt class="wid_90">支付宝账号{Common::get_help_icon('finance_drawcash_detail_alipayaccount',true)}：</dt>
            <dd>
                {$info['alipayaccount']}
            </dd>
        </dl>
        <dl class="list_dl">
            <dt class="wid_90">支付宝姓名：</dt>
            <dd>
                {$info['alipayaccountname']}
            </dd>
        </dl>
        {/if}


        <dl class="list_dl">
            <dt class="wid_90">备注说明：</dt>
            <dd>
                {$info['description']}
            </dd>
        </dl>
        <dl class="list_dl">
            <dt class="wid_90">提交时间：</dt>
            <dd>
                {$info['addtime_name']}
            </dd>
        </dl>

        <dl class="list_dl">
            <dt class="wid_90">提现状态{Common::get_help_icon('finance_drawcash_detail_status',true)}：</dt>
            <dd>
                <input type="radio" name="status" value="0" {if $info['status'] ==  '0'}checked{/if} />审核中&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="status" value="1" {if $info['status'] ==  '1'}checked{/if} />已完成&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="status" value="2" {if $info['status'] ==  '2'}checked{/if} />未通过
            </dd>
        </dl>
        <dl class="list_dl">
            <dt class="wid_90">审核说明：</dt>
            <dd><textarea name="audit_description" class="set-text-xh text_300 mt-4">{$info['audit_description']}</textarea>
            </dd>
        </dl>
        <dl class="list_dl">
            <dt class="wid_90">审核时间：</dt>
            <dd>
                {$info['finishtime_name']}
            </dd>
        </dl>
        <dl class="list_dl">
            <dt class="wid_90">&nbsp;</dt>
            <dd>
                <a class="default-btn wid_60" id="btn_save" href="javascript:;">保存</a>
                <input type="hidden" name="applyid" value="{$info['applyid']}">
                <input type="hidden" name="applyusertype" value="{$info['applyusertype']}">
            </dd>
        </dl>
    </div>
</form>

<script language="JavaScript">

    //表单验证
    $("#frm").validate({

        focusInvalid: false,
        rules: {

        },
        messages: {

        },
        errUserFunc: function (element) {
            console.log(element);
        },
        submitHandler: function (form) {

            $.ajaxform({
                url: SITEURL + "finance/ajax_auditing_drawcash_apply",
                method: "POST",
                isUpload: true,
                dataType:'json',
                form: "#frm",

                success: function (data) {


                    if (data.status) {

                        $("#uid").val(data.productid);
                        ST.Util.showMsg('保存成功!', '4', 2000);

                    }


                }});
            return false;//阻止常规提交
        }
    });

    $(function () {
        //保存
        $("#btn_save").click(function () {
            $("#frm").submit();
            return false;
        })
    })

</script>

</body>
</html><script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=6.0.201707.2005&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
