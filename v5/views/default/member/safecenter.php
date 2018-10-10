<!doctype html>
<html>
<head float_border=z0MaAj >
    <meta charset="utf-8">
    <title>{__('会员安全中心')}-{$webname}</title>
    {include "pub/varname"}
    {Common::css('user.css,base.css,extend.css')}
    {Common::js('jquery.min.js,base.js,common.js')}
</head>

<body>
  {request "pub/header"}
  <div class="big">
  	<div class="wm-1200">
    
    	<div class="st-guide">
      	 <a href="{$GLOBALS['cfg_basehost']}">{$GLOBALS['cfg_indexname']}</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;{__('安全中心')}
        </div><!--面包屑-->
      
      <div class="st-main-page">

        {include "member/left_menu"}
        
        <div class="user-cont-box">
        	<div class="safe-center">
          	<h3 class="aq-tit">{__('安全中心')}</h3>
            <ul class="aq-con">
            	<li>
              	<div class="part1"><i class="tg"></i><!--没有通过就没有"tg"-->{__('登录密码')}</div>
                <div class="part2">
                	<strong class="fl">{__('账户安全程度')}：</strong>
                  <p class="int">
                      <span>
                          {if !empty($info['email']) && !empty($info['mobile'])}
                              <i class="on"></i>
                              <i class="on"></i>
                              <i class="on"></i>

                          {else}
                              <i class="on"></i>
                              <i class="on"></i>
                              <i></i>

                          {/if}
                      </span>&nbsp;
                      {if !empty($info['email']) && !empty($info['mobile'])}
                      {__('高')}
                      {else}
                        {__('中')}
                      {/if}
                  </p>
                </div>
                <div class="part4"><a href="{$cmsurl}member/index/modify_pwd" >{if !empty($info['pwd'])}{__('修改密码')}{else}{__('设置密码')}{/if}&gt;</a></div>
              </li>
            	<li>
              	<div class="part1">
                    {if !empty($info['mobile'])}
                        <i class="tg"></i>
                    {else}
                        <i></i>
                    {/if}
                    {__('手机验证')}
                </div>
                {if !empty($info['mobile'])}
                    <div class="part2">{$info['mobile']} </div>
                    <div class="part4"><a href="{$cmsurl}member/index/modify_phone?change=1">{__('更换手机')}&gt;</a></div>
                {else}
                    <div class="part2">{__('会员安全中心')}未绑定手机 </div>
                    <div class="part3"><a href="{$cmsurl}member/index/modify_phone?change=0">{__('绑定手机')}</a></div>
                {/if}

              </li>
            	<li>
              	<div class="part1">
                    {if !empty($info['email'])}
                    <i class="tg"></i>
                    {else}
                    <i></i>
                    {/if}{__('邮箱验证')}</div>
                {if !empty($info['email'])}
                    <div class="part2"><em>{$info['email']}</em></div>
                    <div class="part4"><a href="{$cmsurl}member/index/modify_email?change=1">{__('更换邮箱')}&gt;</a></div>
                {else}
                    <div class="part2">{__('未绑定邮箱')} </div>
                    <div class="part3"><a href="{$cmsurl}member/index/modify_email?change=0">{__('绑定邮箱')}</a></div>
                {/if}

              </li>

                <li>
                    <div class="part1"><i {if $info['verifystatus']==2}class="tg" {/if}></i>实名认证</div>
                    {if $info['verifystatus']==0}
                    <div class="part2">未认证</div>
                    <div class="part3"><a href="{$cmsurl}member/index/modify_idcard">马上认证</a></div>
                    {elseif $info['verifystatus']==1}
                    <div class="part2">认证资料审核中</div>
                    <div class="part4"><a href="{$cmsurl}member/index/modify_idcard">查看详情&gt;</a></div>
                    {elseif $info['verifystatus']==2}
                    <div class="part2">您认证的实名信息：{mb_substr($info['truename'],0,1,'utf-8')}**
                        &nbsp;&nbsp; {substr_replace($info['cardid'],'**********',3,11)}
                    </div>
                    <div class="part4"><a href="{$cmsurl}member/index/modify_idcard">查看详情&gt;</a></div>
                    {elseif $info['verifystatus']==3}
                    <div class="part2"><em class="fail">认证失败</em>，失败原因：您的身份证信息填写不全，您的身份证信息填写不全....</div>
                    <div class="part4"><a href="{$cmsurl}member/index/modify_idcard">查看详情&gt;</a></div>
                    {/if}
                </li>
            </ul>
          </div><!--安全中心-->
        </div>

        
      </div>
    
    </div>
  </div>
  
 {request "pub/footer"}
 <script>
     $(function(){
         $("#nav_safecenter").addClass('on');
     })
 </script>
</body>
</html>
