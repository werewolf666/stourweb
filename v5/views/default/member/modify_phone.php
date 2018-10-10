<!doctype html>
<html>
<head html_margin=jnLwOs >
<meta charset="utf-8">
<title>{__('修改手机')}-{$webname}</title>
    {include "pub/varname"}
    {Common::css('user.css,base.css,extend.css')}
    {Common::js('jquery.min.js,base.js,common.js,jquery.validate.js,jquery.cookie.js')}

</head>

<body>

{request "pub/header"}
  
  <div class="big">
  	<div class="wm-1200">
    
      <div class="st-guide">
      	<a href="{$cmsurl}">{__('首页')}</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;{__('会员中心')}
      </div><!--面包屑-->
      
      <div class="st-main-page">
        {include "member/left_menu"}
          <div class="user-cont-box">
              <div class="verify-number">
                  <h3 class="yz-tit">{if $change==1}{__('修改手机')}{else}{__('绑定手机')}{/if}</h3>
                  <form id="changefrm" method="post" action="{$cmsurl}member/index/do_modify_phone">
                      <div class="verify-con">
                          <dl>
                              <dt>{__('手机号码')}：</dt>
                              <dd>
                                  <input type="text" id="mobile" name="mobile" class="user-name">
                                  <span class="msg_contain mobilemsg">
                                    <span class="yz-ts tsmsg" style="display: none">{__('验证码已经发送到您的手机，请注意查收')}</span>
                                  </span>
                              </dd>
                          </dl>
                          <dl>
                              <dt>{__('短信验证码')}：</dt>
                              <dd>
                                  <input type="text" class="yzm-text" name="msgcode" id="msgcode">
                                  <input type="button" class="get-number sendmsg" value="{__('获取短信验证码')}">
                                   <span class="msg_contain">
                                    <span class="yz-ts"></span>
                                  </span>
                              </dd>
                          </dl>
                          <div class="verify-btn">
                              <a class="qd-btn" href="javascript:;">{__('确 定')}</a>
                              <a class="back-btn"  onclick="history.go(-1)" href="javascript:;">{__('返 回')}</a>
                          </div>
                          <div class="pc-txt">
                              <p>{__('没有收到验证码')}？</p>
                              <p>1.{__('网络通讯异常可能会造成短信丢失，请重新获取或稍后再试')}。</p>
                              <p>2.{__('请核实手机是否已欠费停机，或者屏蔽了系统短信')}。</p>
                              <p>3.{__('如果您的手机已丢失或停用，请联系人工客服更换手机号码')}。</p>
                          </div>
                      </div>
                      <input type="hidden" name="frmcode" value="{$frmcode}">
                  </form>
              </div><!--绑定手机-->
          </div>
      </div>
    
    </div>
  </div>
<input type="hidden" id="mid" value="{$info['mid']}"/>


{Common::js('layer/layer.js')}
{request "pub/footer"}
<script>
    $(function(){
        $("#nav_safecenter").addClass('on');

        $('.qd-btn').click(function(){
            $("#changefrm").submit();
        })

        jQuery.validator.addMethod("mobile",function(value, element){
            var pattern=/^1+\d{10}$/;
            return pattern.test(value);
        },"{__('请输入正确的手机号')}");
        $("#changefrm").validate({
            rules: {
                'mobile': {
                    required: true,
                    mobile: true,
                    remote: {
                        url: SITEURL+'member/register/ajax_reg_checkmobile',
                        type: 'post'
                    }
                },
                'msgcode':{
                    required:true,
                    remote:{
                        url: SITEURL+'member/register/ajax_check_msgcode',
                        type: 'post',
                        data: {
                            mobile: function() {
                                return $( "#mobile" ).val();
                            }
                        }
                    }
                }
            },
            messages: {
                'mobile':{
                    required:'{__("手机号不能为空")}',
                    remote:'{__("该手机号已被注册")}'
                },

                'msgcode':{
                    required:'{__("短信验证码不能为空")}',
                    remote:'{__("验证码错误")}'
                }

            },
            errorPlacement: function (error, element) {

                $(element).parents('dd:first').find('.msg_contain').html(error);
                $(element).parents('dd:first').find('.msg_contain').addClass('input-error').removeClass('input-ok');

            },

            success: function (msg, element) {

                $(element).parents('dd:first').find('.msg_contain').html('');
                $(element).parents('dd:first').find('.msg_contain').addClass('input-ok').removeClass('input-error');


            }


            /*  ,submitHandler: function (form) {
             //form.submit();


             }*/
        });

        //发送短信验证码
        $('.sendmsg').click(function(){
            var mobile = $("#mobile").val();
            var checkcode = $("#checkcode").val();
            var regPartton=/^1[3-8]+\d{9}$/;
            if (!regPartton.test(mobile))
            {
                layer.alert('{__("请输入正确的手机号码")}', {icon:5});
                return false;
            }

            if($("#mobile").hasClass('error')){
                layer.alert('{__("该手机号已注册")}', {icon:5});
                return false;
            }
            $(".tsmsg").hide();

            var t=this;
            t.disabled=true;


            var token = "{$frmcode}";
            var url = SITEURL+'member/index/ajax_send_msgcode';
            $.post(url,{mobile:mobile,token:token},function(data) {
                if(data.status)
                {
                    var t=this;
                    t.disabled=true;
                    code_timeout(60);
                    $(".tsmsg").show();
                    return false;
                }
                else
                {

                    $('.sendmsg').attr("disabled",false);
                    layer.alert(data.msg,{icon:5});
                    return false;
                }
            },'json');


        })
    })
    //短信发送倒计时
    function code_timeout(v){
        if(v>0)
        {
            $('.sendmsg').val((--v)+'{__("秒")}{__("后")}{__("重发")}');
            setTimeout(function(){
                code_timeout(v)
            },1000);
        }
        else
        {
            $('.sendmsg').val('{__("重发验证码")}');
            $('.sendmsg').attr("disabled",false);
        }
    }
</script>

</body>
</html>
