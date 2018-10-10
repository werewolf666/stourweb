<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>找回密码-{$webname}</title>
    {Common::css('user.css,base.css,extend.css')}
    {include "pub/varname"}
    {Common::js('jquery.min.js,base.js,common.js,jquery.validate.js,jquery.validate.addcheck.js,jquery.cookie.js')}
</head>

<body>

{request "pub/header"}
      
  <div class="getback-pw-box">
  	<div class="getback-pw-con">
    	<div class="getback-pw-tit">找回密码</div>
      <div class="getback-pw-step">
      	<span class="on"><i>1</i><em>验证身份</em></span>
      	<span><i>2</i><em>重置密码</em></span>
      	<span><i>3</i><em>完成</em></span>
      </div>
      	<div class="reg-cont-box">

            <form id="findfrm" method="post" action="{$cmsurl}member/findpwd/step2">
        	 <ul>

                <li>
                  <span class="bt-sp">手机/邮箱：</span>
                  <input type="text" id="loginname" name="loginname" class="reg-text w230" placeholder="请输入手机号和邮箱" />
                  <span class="msg_contain"></span>

                </li>
                <li>
                    <span class="bt-sp">验证码：</span>
                  <input type="text" name="checkcode" id="checkcode" class="reg-text w105" />
                    <span class="reg-yzm"><img src="{$cmsurl}captcha" onClick="this.src=this.src+'?math='+ Math.random()" width="114" height="31" /></span>
                    <span class="reg-change"><a href="javascript:;">换一张</a></span>
                    <span class="msg_contain"></span>
                </li>
             </ul>
                <input type="hidden" name="frmcode" value="{$frmcode}"/>

                <div class="now-reg-btn"><a href="javascript:;">下一步</a></div>
            </form>
              <script>
                  $(function(){
                      $('.now-reg-btn').click(function(){
                          $('#findfrm').submit();
                      })


                      $("#findfrm").validate({
                          rules: {
                              'loginname': {
                                  required: true,
                                  isMobileEmail: true,
                                  remote: {
                                      url: SITEURL+'member/findpwd/ajax_check_loginname',
                                      type: 'post'
                                  }
                              },
                              'checkcode': {
                                  required: true,
                                  remote:{
                                      url: SITEURL+'pub/ajax_check_code',
                                      type:'post',
                                      data:{
                                          checkcode:function(){
                                              return $("#checkcode").val();
                                          }
                                      }
                                  }
                              }
                          },
                          messages: {
                              'loginname':{
                                  required:'登陆名称不能为空',
                                  remote:'该帐号不存在'
                              },

                              'checkcode':{
                                  required:'验证码不能为空',
                                  remote:'验证码错误'
                              }

                          },
                          errorPlacement: function (error, element) {

                              $(element).parents('li:first').find('.msg_contain').html(error);
                              $(element).parents('li:first').find('.msg_contain').addClass('reg-error-txt').removeClass('reg-pass-ico');

                          },

                          success: function (msg, element) {

                              $(element).parents('li:first').find('.msg_contain').html('');
                              $(element).parents('li:first').find('.msg_contain').addClass('reg-pass-ico').removeClass('reg-error-txt');

                          }




                      });
                  })
              </script>



        </div>
    </div>
  </div><!-- 找回密码 -->
  
{request "pub/footer"}

</body>
</html>
