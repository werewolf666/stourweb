<!doctype html>
<html>
<head head_table=Gyvz8B >
<meta charset="utf-8">
<title>{$info['servername']}-{$webname}</title>
    {include "pub/varname"}
    {Common::css('help.css,base.css,extend.css')}
    {Common::js('jquery.min.js,base.js,common.js,piccontent.min.js')}

</head>

<body>

{request "pub/header"}
  
  
  
  <div class="big">
  	<div class="wm-1200">
    
      <div class="st-guide">
          <a href="{$cmsurl}">{$GLOBALS['cfg_indexname']}</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;{$info['servername']}
      </div><!--面包屑-->
    
    	<div class="st-main-page">
				
        <div class="help-side-menu">
            {st:footnav action="pc" row="20"}
             {loop $data $s}
        	    <a {if $s['id']==$info['id']}class="on"{/if} href="{$s['url']}">{$s['title']}</a>
             {/loop}
            {/st}

        </div><!-- 导航 -->
        
        <div class="help-main-box">
        	<div class="help-showCon">
          	<h1>{$info['servername']}</h1>
            <div class="txtCon">
               {$info['content']}
            </div>
          </div>
        </div><!-- 列表内容 -->
        
      </div>
    
    </div>
  </div>
  
{request "pub/footer"}

</body>
</html>
