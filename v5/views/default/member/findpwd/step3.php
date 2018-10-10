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
      	<span class="on"><i>2</i><em>重置密码</em></span>
      	<span class="on"><i>3</i><em>完成</em></span>
      </div>
      	<div class="reg-cont-box">


          <div class="success-get-back">
          	<div class="get-txt">
              <span><i></i>恭喜您，密码找回成功！</span>
              <em><span id="second" style="display: inline-block">5</span>秒后自动跳转到登陆页面</em>
            </div>
            <div class="get-link">
            	<a href="{$cmsurl}">网站首页</a>
            	<a href="{$cmsurl}member/login">立即登录</a>
            </div>
          </div>

        </div>
    </div>
  </div><!-- 找回密码 -->
  
{request "pub/footer"}
<script type="text/javascript">
    (function(){
        var second = document.getElementById('second');
        var href = "{$GLOBALS['cfg_basehost']}/member/login";
        var interval = setInterval(function(){
            var time = --second.innerHTML;
            if(time <= 1) {
                location.href = href;
                clearInterval(interval);
            };
        }, 1000);
    })();
</script>

</body>
</html>
