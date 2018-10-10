<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>会员信息提示-{$webname}</title>
    {include "pub/varname"}
    {Common::css('user.css,base.css,extend.css')}
    {Common::js('jquery.min.js,base.js,common.js')}
</head>

<body>
  {request "pub/header"}
  <div class="big">
  	<div class="wm-1200">
    
    	<div class="st-guide">
      	 <a href="{$GLOBALS['cfg_basehost']}">{$GLOBALS['cfg_indexname']}</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;会员中心
        </div><!--面包屑-->
      <div class="st-main-page">
        {include "member/left_menu"}
          <div class="user-cont-box">
              <div class="success-box">
                  <h3 class="success-tit">{$title}</h3>
                  <div class="success-con">
                      <div class="success-txt">
                          <p class="t1">{$msg}</p>
                          <p class="t2"><span id="second">{$time}</span>秒后&nbsp;&nbsp;跳转到个人中心页面</p>
                      </div>
                      <div class="success-btn">
                          <a class="qd-btn" href="{$GLOBALS['cfg_basehost']}">网站首页</a>
                          <a class="back-btn" href="{$GLOBALS['cfg_basehost']}/member/index/safecenter">安全中心</a>
                      </div>
                  </div>
              </div>
          </div>
      </div>
    </div>
  </div>
  
 {request "pub/footer"}
  <script type="text/javascript">
      (function(){
          var second = document.getElementById('second');
          var href = "{$GLOBALS['cfg_basehost']}/member/";
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
