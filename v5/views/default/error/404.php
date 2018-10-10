<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{__('找不到页面')}-{$webname}</title>
    {Common::css('base.css,extend.css')}
    {include "pub/varname"}
    {Common::js('jquery.min.js,base.js,common.js')}

</head>

<body>

{request "pub/header"}
  
  
  
  <div class="big">
  	<div class="wm-1200">
    
    	<div class="st-guide">
            <a href="{$cmsurl}">{$GLOBALS['cfg_indexname']}</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;{__('找不到页面')}
      </div><!--面包屑-->
    
    	<div class="st-main-page">
      	<div class="nofound-page">
          <div class="pic-box"><img src="{$GLOBALS['cfg_public_url']}images/page-404.png" /></div>
          <div class="btn-box">
            <a class="back-home-btn" href="{$GLOBALS['cfg_basehost']}">{__('返回首页')}</a>
            <a class="back-prev-btn" href="javascript:history.go(-1)">{__('返回上一页')}</a>
          </div>
        </div><!-- 404 page -->
      </div>
    
    </div>
  </div>
  
{request "pub/footer"}

</body>
</html>
