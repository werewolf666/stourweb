<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{__('修改邮箱')}-{$webname}</title>
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
                  <h3 class="yz-tit">{if $change==1}{__('修改邮箱')}{else}{__('绑定邮箱')}{/if}</h3>
                  <form id="changefrm" method="post" action="{$cmsurl}member/index/do_modify_email">
                      <div class="verify-con">
                          <dl>
                              <dt>{__('邮箱地址')}：</dt>
                              <dd>
                                  <input type="text" id="email" name="email" class="user-name">
                                  <span class="msg_contain emailmsg">
                                    <span class="yz-ts tsmsg" style="display: none"></span>
                                  </span>
                              </dd>
                          </dl>
                          <dl>
                              <dt>{__('邮箱验证码')}：</dt>
                              <dd>
                                  <input type="text" class="yzm-text" name="emailcode" id="emailcode">
                                  <input type="button" class="get-number sendemail" value="{__('获取邮箱验证码')}">
                                   <span class="msg_contain">
                                    <span class="yz-ts"></span>
                                  </span>
                              </dd>
                          </dl>
                          <div class="verify-btn">
                              <a class="qd-btn" href="javascript:;">{__('确 定')}</a>
                              <a class="back-btn" href="javascript:;" onclick="history.go(-1)" >{__('返 回')}</a>
                          </div>
                          <div class="pc-txt">
                              <p>{__('没有收到邮件')}？</p>
                              <p>1. {__('请查看邮件是否发送至邮箱中的垃圾邮箱中')}。</p>
                              <p>2. {__('由于系统或者网络原因存在延迟')}，{__('请耐心等待')}。</p>
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


        $("#changefrm").validate({
            rules: {
                'email': {
                    required: true,
                    email: true,
                    remote: {
                        url: SITEURL+'member/register/ajax_check_email',
                        type: 'post'
                    }
                },
                'emailcode':{
                    required:true,
                    remote:{
                        url: SITEURL+'member/index/ajax_check_email_code',
                        type: 'post',
                        data: {
                            email: function() {
                                return $("#email").val();
                            }
                        }
                    }
                }
            },
            messages: {
                'email':{
                    required:'{__("新邮箱不能为空")}',
                    email:'{__("请输入正确的邮箱地址")}',
                    remote:'{__("该邮箱已被注册")}'
                },

                'emailcode':{
                    required:'{__("邮箱验证码不能为空")}',
                    remote:'{__("邮箱验证码错误")}'
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

        //发送邮箱验证码
        $('.sendemail').click(function(){
            var email = $("#email").val();
            var regPartton=/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
            if (!regPartton.test(email))
            {
                layer.alert('{__("请输入正确的邮箱")}', {icon:5});
                return false;
            }
            $(".tsmsg").hide();

            if($(".emailmsg").hasClass('input-error')){
                return false;
            }

            var t=this;
            t.disabled=true;
            var token = "{$frmcode}";
            var url = SITEURL+"member/index/ajax_send_emailcode";
            var sendnum = $.cookie('email_sendnum') ? $.cookie('email_sendnum') : 0;

            if(sendnum>3){

                layer.alert('{__("验证码发送请求过于频繁")},{__("请过15分钟后再试")}', {icon:5});
                t.disabled=false;
                return false;
            }

            if(sendnum!=0){
                $.cookie('email_sendnum', sendnum++);
            }else{
                $.cookie('email_sendnum', 1,{ expires: 1/96 });
            }

            $.post(url,{email:email,token:token},function(data) {
                if(data.status)
                {
                    //layer.alert('邮箱验证码已发送到你的新邮箱,请登陆邮箱查看验证', {icon:5});
                    $(".tsmsg").html('{__("邮箱验证码已发送到你的新邮箱,请登陆邮箱查看验证")}!');
                    $(".tsmsg").show();
                    code_timeout(60);
                    return false;
                }
                else
                {
                    t.disabled=false;
                    layer.alert(data.msg, {icon:5});
                    return false;
                }
            },'json');



        });
    })
    //短信发送倒计时
    function code_timeout(v){
        if(v>0)
        {
            $('.sendemail').val((--v)+'{__("秒")}{__("后")}{__("重发")}');
            setTimeout(function(){
                code_timeout(v)
            },1000);
        }
        else
        {
            $('.sendemail').val('{__("重发验证码")}');
            $('.sendemail').disabled = false;
        }
    }
</script>

</body>
</html>
