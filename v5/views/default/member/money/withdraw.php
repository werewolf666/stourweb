<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{__('会员中心')}-{$webname}</title>
    {include "pub/varname"}
    {Common::css('user.css,base.css,header.css,footer.css,extend.css')}
    {Common::js('jquery.min.js,base.js,common.js,jquery.validate.js,jquery.cookie.js,ajaxform.js')}
    <link rel="stylesheet" href="/tools/js/datetimepicker/jquery.datetimepicker.css">
    <script src="/tools/js/datetimepicker/jquery.datetimepicker.full.js"></script>
</head>

<body>

{request "pub/header"}

  <div class="big">
  	<div class="wm-1200">

      <div class="st-guide">
      	<a href="{$cmsurl}">{__('首页')}</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;{__('提现')}
      </div><!--面包屑-->

      <div class="st-main-page">
        {include "member/left_menu"}
        <div class="user-cont-box">
            <form action="/member/bag/ajax_withdraw_save" method="post" id="frm">
            <div class="take-container">
                <h3 class="take-tit-bar">我要提现</h3>
                <div class="take-tit-wrap">
                    <span class="hd-txt">提现金额</span>
                    <input class="default-text" type="text" name="amount" placeholder="提现金额" />
                    <span class="price">可提现金额：<em class="num">{Currency_Tool::symbol()}{php echo $member['money']-$member['money_frozen'];}</em></span>
                </div>
            </div>
            <div class="take-way-container">
                    <h3 class="take-way-tit">提现方式</h3>
                    <div class="take-way-wrap clearfix">
                        {if in_array('bank',$way)}
                        <label class="label"><input type="radio" name="way" class="radio"  value="bank">银行卡</label>
                        {/if}
                        {if in_array('alipay',$way)}
                        <label class="label"><input type="radio" name="way" class="radio" value="alipay">支付宝</label>
                        {/if}
                        {if in_array('weixin',$way)}
                        <label class="label"><input type="radio" name="way" class="radio"  value="weixin">微信</label>
                        {/if}
                    </div>
           </div>
            <div class="bank-card-container"  id="way_bank">
                <h3 class="bank-card-bar">银行卡信息</h3>
                <div class="bank-card-wrap">
                    <dl>
                        <dt>账户名：</dt>
                        <dd>
                            <input class="default-text w300" name="bankaccountname" type="text" />
                        </dd>
                    </dl>
                    <dl>
                        <dt>银行卡号：</dt>
                        <dd>
                            <input class="default-text w300" name="bankcardnumber" type="text" />
                        </dd>
                    </dl>
                    <dl>
                        <dt>开户银行：</dt>
                        <dd>
                            <input class="default-text w300" name="bankname" type="text" />
                        </dd>
                    </dl>
                    <dl>
                        <dt>备注说明：</dt>
                        <dd>
                            <textarea class="default-textarea w700" name="description" placeholder="备注"></textarea>
                        </dd>
                    </dl>
                </div>
            </div>
                <div class="bank-card-container" id="way_alipay">
                    <h3 class="bank-card-bar">支付宝信息</h3>
                    <div class="bank-card-wrap">
                        <dl>
                            <dt>支付宝账号：</dt>
                            <dd>
                                <input class="default-text w300" name="alipay_bankcardnumber" type="text"/>
                            </dd>
                        </dl>
                        <dl>
                            <dt>真实姓名：</dt>
                            <dd>
                                <input class="default-text w300" name="alipay_bankaccountname" type="text">
                            </dd>
                        </dl>
                        <dl>
                            <dt>备注说明：</dt>
                            <dd>
                                <textarea class="default-textarea w700" name="alipay_description" placeholder="备注"></textarea>
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="bank-card-container"  id="way_weixin">
                    <h3 class="bank-card-bar">微信信息</h3>
                    <div class="bank-card-wrap">
                        <dl>
                            <dt>微信账号：</dt>
                            <dd>
                                <input class="default-text w300" name="weixin_bankcardnumber" type="text"/>
                            </dd>
                        </dl>
                        <dl>
                            <dt>真实姓名：</dt>
                            <dd>
                                <input class="default-text w300" name="weixin_bankaccountname" type="text">
                            </dd>
                        </dl>
                        <dl>
                            <dt>备注说明：</dt>
                            <dd>
                                <textarea class="default-textarea w700" name="weixin_description" placeholder="备注"></textarea>
                            </dd>
                        </dl>
                    </div>
                </div>
            <div class="take-btn-bar">
                <a class="submit btn" href="javascript:;" id="submit_btn">提交</a>
                <a class="cancel btn" href="/member">取消</a>
            </div>
            </form>
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
                    number:true
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
                ,
                alipay_bankaccountname:
                {
                    required:true
                },
                alipay_bankcardnumber:
                {
                    required:true
                }
                ,
                weixin_bankaccountname:
                {
                    required:true
                },
                weixin_bankcardnumber:
                {
                    required:true
                }


            },
            messages: {
                amount:
                {
                    required:'必填',
                    number:'请填入数字'
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
                },
                alipay_bankaccountname:
                {
                    required:'必填'
                },
                alipay_bankcardnumber:
                {
                    required:'必填'
                }
                ,
                weixin_bankaccountname:
                {
                    required:'必填'
                },
                weixin_bankcardnumber:
                {
                    required:'必填'
                }
            },
            submitHandler:function(form){
                $.ajaxform({
                    method: "POST",
                    isUpload: true,
                    form: "#frm",
                    dataType: "json",
                    success: function (result) {
                        if(result.status){
                            layer.msg(result.msg, {
                                icon: 6,
                                time: 1000
                            },function(){
                                window.location.reload();
                            })
                        }else{
                            layer.msg(result.msg, {
                                icon: 5,
                                time: 1000
                            })
                        }
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


        $(".take-way-wrap input:radio").click(function(){
            var val = $(this).val();
            $(".bank-card-container").hide();
            $("#way_"+val).show();
        });
        $(".take-way-wrap input:radio:first").trigger("click");


        //导航选中
        $("#nav_money").addClass('on');

    })
</script>

</body>
</html>
