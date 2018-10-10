<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{$keyword}搜索结果-{$webname}</title>
    {include "pub/varname"}
    {Common::css('base.css,extend.css')}
    {Common::js('jquery.min.js,base.js,common.js')}
</head>
<body>
{request "pub/header"}
  <div class="big">
  	<div class="wm-1200">
    
    	<div class="st-guide">
            <a href="{$cmsurl}">{$GLOBALS['cfg_indexname']}</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;{$keyword}相关搜索结果
        </div><!--面包屑-->
    
    	<div class="st-main-page">
				
        <div class="cloud-search-side">
           {loop $leftnav $nav}
        	<a {if $typeid==$nav['typeid']}class="on"{/if} href="{$cmsurl}search/cloudsearch?keyword={$keyword}&typeid={$nav['typeid']}"><strong>{$nav['channelname']}</strong>{$nav['num']}</a>
           {/loop}

        </div><!-- 搜索导航 -->
        
        <div class="cloud-search-box">
        	<div class="cs-tit">共有<strong>{$total}</strong>个内容满足条件</div>
          <div class="cloud-search-conlist">
            {if !empty($list)}
          	<ul>
                {loop $list $row}
                    <li>
                     {if !empty($row['litpic'])}
                        <div class="pic"><a href="{$row['url']}" target="_blank"><img src="{Common::img($row['litpic'])}" alt="{$row['title']}" /></a></div>
                     {/if}
                    <div class="con">
                      <p class="bt"><a href="{$row['url']}" target="_blank"><span class="label">[{$row['label']}]</span>{$row['producttitle']}</a></p>
                      <p class="txt">{$row['series']}{Common::cutstr_html($row['description'],130)}</p>
                    </div>
                    </li>
                {/loop}

            </ul>
            <div class="main_mod_page clear">
              {$pageinfo}
            </div>
           {else}
              <div class="no-content">
                  <p><i></i>抱歉，没有找到符合条件的内容！</p>
              </div>
           {/if}
          </div>
        </div><!-- 搜索列表 -->
        
      </div>
    
    </div>
  </div>
  
{request "pub/footer"}

</body>
</html>
