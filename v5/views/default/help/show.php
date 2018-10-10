<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{$info['title']}-{$webname}</title>
    {include "pub/varname"}
    {Common::css('help.css,base.css,extend.css')}
    {Common::js('jquery.min.js,base.js,common.js')}

</head>

<body>

{request "pub/header"}
  
  
  
  <div class="big">
  	<div class="wm-1200">
    
      <div class="st-guide">
          <a href="{$cmsurl}">{$GLOBALS['cfg_indexname']}</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;{$info['title']}
      </div><!--面包屑-->
    
    	<div class="st-main-page">
				
        <div class="help-side-menu">
            {st:help action="kind" row="5"}
            {loop $data $row}
             <a {if $info['kindid']==$row['id']}class="on"{/if} href="{$row['url']}">{$row['title']}</a>
            {/loop}
            {/st}

        </div><!-- 导航 -->
        
        <div class="help-main-box">
        	<div class="help-showCon">
          	<h1>{$info['title']}</h1>
            <div class="txtCon">
               {Common::img_content_cut($info['body'],930,0)}
            </div>
          </div>
        </div><!-- 列表内容 -->
        
      </div>
    
    </div>
  </div>
  
{request "pub/footer"}

</body>
</html>
