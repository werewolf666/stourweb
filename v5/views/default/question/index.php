<!doctype html>
<html>
<head>
<meta charset="utf-8">

    <title>{$seoinfo['seotitle']}-{$GLOBALS['cfg_webname']}</title>
    {if $seoinfo['keyword']}
    <meta name="keywords" content="{$seoinfo['keyword']}" />
    {/if}
    {if $seoinfo['description']}
    <meta name="description" content="{$seoinfo['description']}" />
    {/if}
    {include "pub/varname"}
    {Common::css('wenda.css,base.css,extend.css')}
    {Common::js('jquery.min.js,base.js,common.js')}
</head>

<body>
{request "pub/header"}
  
  
  
  <div class="big">
  	<div class="wm-1200">
    
    	<div class="st-guide">
            <a href="{$cmsurl}">{$GLOBALS['cfg_indexname']}</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;{$channelname}
        </div><!--面包屑-->
      
      <div class="st-main-page">
      	
        <div class="st-wenda-box">
        
        	<div class="online-wenda">
          	<div class="online-tit"><h3>{$channelname}</h3><a href="#ask">{__('我要提问')}</a></div>
            <div class="online-conlist">
            	<ul>
                {loop $list $row}
              	  <li>
                	<div class="bt">

                  	<span class="tag">{__('标题')}</span>
                  	<span class="con">{$row['title']}</span>
					<span class="date"><span class="name">{$row['nickname']}</span>{__('发表')}：{Common::mydate('Y-m-d',$row['addtime'])}</span>
                  </div>
                	<div class="nr">
                  	<span class="tag">{__('内容')}</span>
                  	<span class="con">{$row['content']}</span>
                  </div>
                  <div class="hf">
                  	<span class="con">{strip_tags($row['replycontent'])}{if strlen($row['replycontent'])>40}<a class="more" href="javascript:;">【{__('详情')}】</a>{/if}</span>
                    <span class="date">{__('回复')}：{Common::mydate('Y-m-d',$row['replytime'])}</span>
                  </div>
                </li>
                {/loop}
              </ul>
              <div class="main_mod_page clear">
                   {$pageinfo}

              </div><!-- 翻页 -->
            </div>
          </div><!-- 在线问答 -->
          
          <div class="online-quiz">
          	<div class="online-tit" id="ask"><h3>{__('在线提问')}</h3></div>
             <form id="askfrm" method="post" action="{$cmsurl}question/add">
                <div class="online-data">
            
            	<div class="data-conlist">
              	<span class="tit">{__('称呼')}：</span>
                <div class="box">
                    {if !empty($userInfo['mid'])}
                     <span style="height: 30px;line-height: 30px;">{$userInfo['nickname']}</span>
                    {else}
                	<input type="text" class="ol-text w250" name="nickname" id="nickname" />
                	<label class="nm"><input type="checkbox" name="anonymous" value="1" id="CheckboxGroup1_0">{__('匿名')}</label>
                    {/if}
                </div>
              </div>
            
            	<div class="data-conlist">
              	<span class="tit">{__('联系方式')}：</span>
                <div class="box">
                	<input type="text" class="ol-text w250" name="mobile" id="mobile" placeholder="{__('电话')}" value="{$userinfo['mobile']}" />
                	<input type="text" class="ol-text w250 ml15" name="email" id="email" placeholder="{__('邮箱')}" value="{$userinfo['email']}" />
                	<input type="text" class="ol-text w250 mt15" name="weixin" id="weixin" placeholder="{__('微信')}" />
                	<input type="text" class="ol-text w250 mt15 ml15" name="qq" id="qq" placeholder="QQ" value="{$userinfo['qq']}" />
                </div>
              </div>
            
            	<div class="data-conlist">
              	<span class="tit">{__('问题标题')}：</span>
                <div class="box">
                	<input type="text" class="ol-text w250" name="questitle" id="questitle"  />
                </div>
              </div>
            
            	<div class="data-conlist">
              	<span class="tit">{__('内容')}：</span>
                <div class="box">
                	<textarea name="quescontent" id="quescontent" cols="" rows=""></textarea>
                </div>
              </div>
            
            	<div class="data-conlist">
              	<span class="tit">{__('验证码')}：</span>
                <div class="box">
                	<input type="text" name="checkcode" id="checkcode" class="ol-text w250" />
                  <span class="yzm"><img src="{$cmsurl}captcha"  id="imgcheckcode" onClick="this.src=this.src+'?math='+ Math.random()" width="80" height="30" /></span>
                </div>
              </div>
              
              <div class="online-tjbtn"><a href="javascript:;" class="btn-submit">{__('提 交')}</a></div>
              
            </div>
                <input type="hidden" name="frmcode" id="frmcode" value="{$frmcode}"/>
             </form>
          </div><!-- 在线提问 -->
          
        </div>
        
		<div class="st-sidebox">
            {st:right action="get" typeid="$typeid" data="$templetdata" pagename="index"}
        </div><!--边栏模块-->
      
      </div>
    
    </div>
  </div>
  
{request "pub/footer"}
{Common::js('layer/layer.js')}
<script>
    $(function(){
        $(".more").click(function(){
            if($(this).parent().attr('style')==undefined){
                $(this).parent().attr('style','height:auto');
                $(this).text('【{__("收起")}】');
            }else{
                $(this).parent().removeAttr('style');
                $(this).text('【{__("详情")}】');
            }

        })

        //提问
        $(".btn-submit").click(function(){
            var fm=$("#askfrm");
            var titleEle=fm.find("input[name=questitle]");
            var contentEle=fm.find('textarea[name=quescontent]');
            var checkcodeEle=fm.find('input[name=checkcode]');
            var mobileEle=fm.find('input[name=mobile]');
            var emailEle=fm.find('input[name=email]');

            var mobile = mobileEle.val();
            var email=emailEle.val();
            var qq=fm.find('input[name=qq]').val();
            var weixin=fm.find('input[name=weixin]').val();
            var title= titleEle.val();
            var content= contentEle.val();
            var checkcode=checkcodeEle.val();
            var frmcode = $("#frmcode").val();

            if(!title){
                layer.alert('{__("question_title_empty")}', {
                    icon: 5,
                    title: '{__("notice")}'

                });
                return false;

            }

            if(!content){
                layer.alert('{__("question_empty")}', {
                    icon: 5,
                    title: '{__("notice")}'

                });
                return false;
            }

            if(!checkcode){
                layer.alert('{__("checkcode_empty")}', {
                    icon: 5,
                    title: '{__("notice")}'

                });
                return false;
            }
            if(mobile=="" && email=="" && qq=="" && weixin==""){

                layer.alert('{__("question_linktype_empty")}', {
                    icon: 5,
                    title: '{__("notice")}'

                })
                return false;
            }

            var frmdata = $("#askfrm").serialize();
            $.ajax({
                type:'POST',
                url:SITEURL+'question/ajax_add',
                data:frmdata,
                dataType:'json',
                success:function(data){
                    if(data.status){

                        layer.msg(data.msg, {
                            icon: 6,
                            time: 1000

                        })
                        window.location.reload();
                    }else{
                        layer.alert(data.msg, {
                            icon: 5,
                            title: '{__("notice")}'

                        })
                    }
                }

            })


        })


    })
</script>

</body>
</html>
