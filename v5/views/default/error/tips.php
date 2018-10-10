<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{$info['title']}-{$webname}</title>
    {Common::css('base.css,extend.css')}
    {include "pub/varname"}
    {Common::js('jquery.min.js,base.js,common.js')}

</head>

<body>

{request "pub/header"}
  
  
  
  <div class="big">
  	<div class="wm-1200">
        <div class="st-guide">
            <a href="{$cmsurl}">{$GLOBALS['cfg_indexname']}</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;{$info['title']}
      </div><!--面包屑-->
        <div class="st-payment-ts">
            <div class="payment-lose-box">
                <div class="lose-con">
                    <h3>{$info['title']}</h3>
                    <div class="txt">{$info['msg']}</div>
                    <div class="btn-box">
                        <a class="back-home-btn again-pay-btn" href="{$GLOBALS['cfg_basehost']}">返回首页</a>
                        {if $referurl}
                        <a class="back-prev-btn" href="{$referurl}">返回上一页</a>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
  
{request "pub/footer"}

</body>
</html>
