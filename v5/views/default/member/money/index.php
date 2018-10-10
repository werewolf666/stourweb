<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{__('会员中心')}-{$webname}</title>
    {include "pub/varname"}
    {Common::css('user.css,base.css,extend.css')}
    {Common::js('jquery.min.js,base.js,common.js,jquery.validate.js,jquery.cookie.js,ajaxform.js')}
    <link rel="stylesheet" href="/tools/js/datetimepicker/jquery.datetimepicker.css">
    <script src="/tools/js/datetimepicker/jquery.datetimepicker.full.js"></script>
</head>

<body>

{request "pub/header"}

  <div class="big">
  	<div class="wm-1200">

      <div class="st-guide">
      	<a href="{$cmsurl}">{__('首页')}</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;{__('我的钱包')}
      </div><!--面包屑-->

      <div class="st-main-page">
        {include "member/left_menu"}
        <div class="user-main-box">
            {if $member['verifystatus']==0}
            <div class="hint-msg-box">
                <span class="close-btn" onclick="$(this).parent().remove()">关闭</span>
                <p class="hint-txt">您的账户未实名认证！为了您资金的安全，<a href="/member/index/modify_idcard">去认证&gt;&gt;</a></p>
            </div>
            {/if}
            <!-- 认证提示 -->

            <div class="my-wallet-bar">
                <span class="txt">账号余额：<em class="price">{Currency_Tool::symbol()}
                        {php echo number_format($member['money']-$member['money_frozen'],2)}</em></span>
                <a class="go-link" href="/member/bag/withdraw">我要提现</a>
            </div>
            <!-- 账号余额 -->

            <div class="details-container">
                <div class="tab-nav-bar">
                    <a class="dh {if $type===null}on{/if}" href="/member/bag/index">交易明细</a>
                    <a class="dh {if $type===0 || $type==='0'}on{/if}" href="/member/bag/index?type=0">收入</a>
                    <a class="dh {if $type===1 || $type==='1'}on{/if}" href="/member/bag/index?type=1">支出</a>
                </div>
                <div class="tab-con-wrap clearfix">
                    <table class="tran-data-list">
                        <tr>
                            <th width="25%">时间</th>
                            <th width="35%">交易名称</th>
                            <th width="20%">交易类型</th>
                            <th width="20%">交易金额</th>
                        </tr>
                    {loop $list $row}
                        <tr>
                            <td>{date('Y-m-d H:i:s',$row['addtime'])}</td>
                            <td><span class="name">{$row['description']}</span></td>
                            <td>{if $row['type']==0}收入{elseif $row['type']==1}支出{elseif $row['type']==2}冻结{elseif $row['type']==3}解冻{/if}</td>
                            <td><span class="{if $row['type']==0 || $row['type']==3}add{else}sub{/if}">
                                    {if $row['type']==0 || $row['type']==3}+{else}-{/if}
                                    {Currency_Tool::symbol()}{$row['amount']}
                                </span></td>
                        </tr>
                    {/loop}
                    </table>
                    <div class="main_mod_page clear">
                        {$pageinfo}
                    </div>
                    {if empty($list)}
                    <div class="order-no-have"><span></span>
                        <p>暂无交易记录</p>
                    </div>
                    {/if}
                </div>
            </div>
            <!-- 交易明细 -->
        </div>
      </div>

    </div>
  </div>
{Common::js('layer/layer.js')}
{request "pub/footer"}

<script>
    $(function(){

        //提交申请
        $("#submit_btn").click(function(){
             $("#frm").submit();
        });

        //验证
        $("#frm").validate({
            rules: {
                amount:
                {
                    required:true,
                    digits:true
                },
                bankaccountname:
                {
                    required:true
                },
                bankcardnumber:
                {
                    required:true
                },
                bankname:
                {
                    required:true
                }
            },
            messages: {
                amount:
                {
                    required:'必填',
                    digits:'请填入整数金额'
                },
                bankaccountname:
                {
                    required:'必填'
                },
                bankcardnumber:
                {
                    required:'必填'
                },
                bankname:
                {
                    required:'必填'
                }
            },
            submitHandler:function(form){
                $.ajaxform({
                    method: "POST",
                    isUpload: true,
                    form: "#frm",
                    dataType: "html",
                    success: function (result) {

                    }
                });
                return false;
            },
            errorClass:'error-txt',
            errorElement:'span'
            /* highlight: function(element, errorClass, validClass) {
                $(element).attr('style','border:1px solid red');
            },
            unhighlight:function(element, errorClass){
                $(element).attr('style','');
            },
            errorPlacement:function(error,element){
                $(element).parent().append(error)
            }*/
        });
        //导航选中
        $("#nav_money").addClass('on');

    })
</script>

</body>
</html>
