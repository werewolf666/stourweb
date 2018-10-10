<!doctype html>
<html>
<head>
<meta charset="utf-8">
<head>
    <meta charset="utf-8">
    <title>{$info['kindname']}-{$webname}</title>
    {include "pub/varname"}
    {Common::css('help.css,base.css,extend.css')}
    {Common::js('jquery.min.js,base.js,common.js')}

</head>


<body>

{request "pub/header"}

  <div class="big">
  	<div class="wm-1200">
    
    	<div class="st-guide">
            <a href="{$cmsurl}">{$GLOBALS['cfg_indexname']}</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;{$info['kindname']}
        </div><!--面包屑-->
    
    	<div class="st-main-page">
				
        <div class="help-side-menu">
            {st:help action="kind" row="30"}
             {loop $data $row}
        	   <a {if $info['id']==$row['id']}class="on"{/if} href="{$row['url']}">{$row['title']}</a>
             {/loop}
        	{/st}
        </div><!-- 帮助导航 -->
        
        <div class="help-main-box">
        	<div class="help-listCon">
          	<ul>
                {st:help action="article" row="40" kindid="$info['id']" return="list"}
                {loop $list $r}
                  <li><a href="{$r['url']}">{$r['title']}</a></li>
                {/loop}
                {/st}

            </ul>
          </div>
        </div><!-- 列表内容 -->
        
      </div>
    
    </div>
  </div>
  
{request "pub/footer"}

</body>
</html>
