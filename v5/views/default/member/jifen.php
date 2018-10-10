<!doctype html>
<html>
<head>
<meta charset="utf-8">
    <title>我的积分-{$webname}</title>
    {include "pub/varname"}
    {Common::css('user.css,base.css,extend.css',false)}
    {Common::js('jquery.min.js,base.js,common.js,jquery.validate.js,jquery.cookie.js')}
</head>

<body>

{request "pub/header"}
  
  <div class="big">
  	<div class="wm-1200">
    
      <div class="st-guide">
            <a href="{$cmsurl}">首页</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;我的积分
      </div><!--面包屑-->
      
      <div class="st-main-page">

          {include "member/left_menu"}
        
        <div class="user-order-box">
            <div class="user-level-block clearfix">
                <div class="pic"><img src="{$userinfo['litpic']}" width="90" height="90" /></div>
                <div class="msg">
                    <div class="name">hi, {$userinfo['nickname']}</div>
                    <div class="data">你已获得积分：<strong class="num">{$userinfo['jifen']}</strong>{if !is_null($nextGrade)}<span class="ts">还差<b>{$nextGrade['poor']}</b>升级成为{$nextGrade['name']}哦！</span>{/if}</div>
                    <div class="actual clearfix">
                        <ul>
                            {loop $grade['grade'] $k $v}
                            <li class="{if $n==$grade['total']}member-vip05{/if} {if $k<$grade['current']}item-current{/if}">
                                <em class="level-num">Lv.{$n}</em>
                                <span class="level-txt">{$v['name']}</span>
                                <span class="level-bar " {if isset($v['per'])}id="position" style="width:{$v['per']}%"{/if}></span>
                            </li>
                            {/loop}
                        </ul>
                        <i class="vip-notice" style="left:{$grade['process']}px"></i>
                    </div>
                </div>
            </div>
          <div class="tabnav">
            <span class="on">积分记录</span>
            {if !empty($userinfo['jifen'])}
                <em class="user-usable-js">当前可用积分：<b>{$userinfo['jifen']}</b></em>
            {/if}
          </div>
          <div class="tabcon">
            {if !empty($list)}
          	<table class="table-list" width="100%" border="0">
              <tr>
                <th width="20%" height="42" scope="col">日期</th>
                <th width="60%" scope="col">详细内容</th>
                <th width="20%" scope="col">分值</th>
              </tr>
              {loop $list $row}
                  <tr>
                    <td height="42">{Common::mydate('Y-m-d H:i:s',$row['addtime'])}</td>
                    <td align="left">{$row['content']}</td>
                    <td><span class="sign04">{$row['jifentype']}积分：{$row['jifen']}分</span></td>
                  </tr>
              {/loop}
            </table>
            <div class="main_mod_page clear">
              {$pageinfo}
            </div><!--翻页-->
           {else}
              <div class="jf-no-have"><span></span><p>你还没有积分，马上<a href="/" target="_blank">去下单</a>挣积分</p></div>
           {/if}
          </div>
        </div><!--积分列表-->
        
      </div>
    
    </div>
  </div>
  
{request "pub/footer"}
<script>
    $(function(){
        $("#nav_myjifen").addClass('on');
    })
</script>

</body>
</html>
