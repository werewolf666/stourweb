<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{__('会员中心')}-{$webname}</title>
    {include "pub/varname"}
    {Common::css('user.css,base.css,extend.css')}
    {Common::js('jquery.min.js,base.js,common.js,jquery.validate.js,jquery.cookie.js')}
    <link rel="stylesheet" href="/tools/js/datetimepicker/jquery.datetimepicker.css">
    <script src="/tools/js/datetimepicker/jquery.datetimepicker.full.js"></script>
</head>

<body left_top=XIHwOs >

{request "pub/header"}

  <div class="big">
  	<div class="wm-1200">

      <div class="st-guide">
      	<a href="{$cmsurl}">{__('首页')}</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;{__('会员中心')}
      </div><!--面包屑-->

      <div class="st-main-page">
        {include "member/left_menu"}
        <div class="user-cont-box">
        	<div class="personal-data">
          	<h3 class="gr-tit">{__('个人资料')}</h3>
            <dl>
            	<dt>{__('头')}&nbsp;&nbsp;{__('像')}：</dt>
              <dd>
              	<div class="head-pic"><img id="face" src="{$info['litpic']}" /><span class="upload">{__('编辑头像')}</span></div>
                <input type="hidden" id="litpic" value="{$info['litpic']}">
              </dd>
            </dl>
            <dl>
            	<dt>{__('昵')}&nbsp;&nbsp;{__('称')}：</dt>
              <dd>
              	<input type="text" class="msg-text" id="nickname" value="{$info['nickname']}" /><span class="star-ico">*</span>
              </dd>
            </dl>
            <dl>
            	<dt>{__('性')}&nbsp;&nbsp;{__('别')}：</dt>
              <dd>
              	<span class="sex"><a {if $info['sex']=='男'}class="on"{/if} href="javascript:;">男</a><a href="javascript:;" {if $info['sex']=='女'}class="on"{/if}>女</a></span>
                <input  type="hidden" name="sex" id="sex" value="{$info['sex']}">
              </dd>
            </dl>
            <dl>
                <dt>{__('出生年月')}：</dt>
                <dd>
                    <input  {if $info['verifystatus']==2} disabled="disabled" {/if} type="text" class="default-text" id="birth_date" name="birth_date" placeholder="年/月/日"  value="{$info['birth_date']}" />
                </dd>
            </dl>
                <dl>
                    <dt>籍&nbsp;&nbsp;贯：</dt>
                    <dd>
                        <input type="text" class="default-text" name="native_place" id="native_place" value="{$info['native_place']}" />
                    </dd>
                </dl>


             <dl>
                    <dt>常住地址：</dt>
                    <dd>
                        <input type="text" class="default-text" name="address" id="address" value="{$info['address']}" />
                    </dd>
             </dl>
             <dl>
            	<dt>{__('手机号')}：</dt>
              <dd>
                  {if !empty($info['mobile'])}
              	  <span class="phone-num">{$info['mobile']}</span>
                  <a class="revise" href="{$cmsurl}member/index/modify_phone?change=1">{__('会员中心')}{__('更换手机')}&gt;</a>
                  {else}
                   <span class="phone-num">{__('未绑定')}</span> <a class="revise" href="{$cmsurl}member/index/modify_phone?change=1">{__('绑定手机')}&gt;</a>
                  {/if}
              </dd>
            </dl>
            <dl>
            	<dt>E-mail：</dt>
              <dd>
                {if !empty($info['email'])}
              	    <span class="mail">{$info['email']}</span>
                    <a class="revise" href="{$cmsurl}member/index/modify_email?change=1">{__('更换邮箱')}&gt;</a>
                {else}
                     <span class="mail">{__('未绑定')}</span> <a class="revise" href="{$cmsurl}member/index/modify_email?change=1">{__('绑定邮箱')}&gt;</a>
                {/if}

              </dd>
            </dl>
                <dl>
                    <dt>微信号：</dt>
                    <dd>
                        <input type="text" class="default-text" name="wechat" id="wechat" value="{$info['wechat']}" />

                    </dd>
                </dl>
            <dl>
                <dt>{__('星')}&nbsp;&nbsp;{__('座')}：</dt>
                <dd>

                    <select  {if $info['verifystatus']==2}  disabled="disabled" {/if} name="constellation" id="constellation" class="drop-down" style="width: 65px;">
                        {loop $constellation $v}
                        <option value="{$v}" {if $info['constellation']==$v}selected="selected"{/if}>{$v}</option>
                        {/loop}
                    </select>
                </dd>
            </dl>
            <dl>
                <dt>Q&nbsp;&nbsp;Q：</dt>
                <dd>
                    <input type="text" class="default-text" name="qq" id="qq" value="{$info['qq']}" />
                </dd>
            </dl>
            <dl>
                <dt>{__('个性签名')}：</dt>
                <dd>
                    <textarea name="signature" id="signature" class="default-textarea">{$info['signature']}</textarea>
                </dd>
            </dl>
            <hr />


            <div class="save-revise"><a href="javascript:;" class="saveinfo">{__('保存修改')}</a></div>
            <div class="success-out-box" style=" display:none">
            	<div class="box-con">
              	<h3><img src="{$GLOBALS['cfg_public_url']}images/success-ico.png" />{__('个人资料修改成功')}！</h3>
                <a href="javascript:;" class="close_success">{__('确 定')}</a>
              </div>
            </div><!--修改成功弹出框-->
          </div>
        </div>
      </div>

    </div>
  </div>
<input type="hidden" id="mid" value="{$info['mid']}"/>
<input type="hidden" id="frmcode" value="{$frmcode}">
{Common::js('layer/layer.js')}

{request "pub/footer"}

<script>
    var date = new Date();
    var now_year = date.getFullYear();
    $.datetimepicker.setLocale('ch');
    $('#birth_date').datetimepicker({
        format:'Y/m/d',
        formatDate:'Y/m/d',
        timepicker:false,
        maxDate:date,
        yearStart:'1900',
        yearEnd:now_year
    });
    $(function(){

        //导航选中
        $("#nav_userinfo").addClass('on');
        //上传头像点击
        $('.upload').click(function(){
            if($("#upiframe").length<1)
            {
                var s_left=Math.abs($(window).width()/2-350);
                var s_top=$(window).scrollTop()+200;
                ST.Util.createFade();//创建遮罩
                var url = SITEURL+"member/index/uploadface";
                var imgdiv="<div id='upiframe' style='width:700px;height:500px;position:absolute;left:"+s_left+"px;top:"+s_top+"px;z-index:9999'> <iframe src='"+url+"' style='width:100%;height:100%;border:none;'></iframe></div>";
                $("body").append(imgdiv);
                //fade点击
                $(".fade").live('click',function(){
                    ST.Util.closeFade();
                    $("#upiframe").remove();//移除
                });
            }


        });
        //性别点击选择
        $(".sex a").click(function(){
            var verifystatus = '{$info['verifystatus']}';
            if(verifystatus==2)
            {
                return false;
            }
            $("#sex").val($(this).html());
            $(this).addClass('on').siblings().removeClass('on');
        });

        //关闭保存成功信息提示框
        $('.close_success').click(function(){
            $(".success-out-box").hide();
        });
        //保存修改
       $('.saveinfo').click(function(){
           var nickname = $("#nickname").val();
           if($.trim(nickname)=='')
           {
               layer.msg('昵称不能为空!',{icon:2,time:1000});
               return false;
           }
           var mid = $("#mid").val();
           var sex = $("#sex").val();
           var truename = $("#truename").val();
           var cardid = $("#cardid").val();
           var address = $("#address").val();
           var frmcode = $("#frmcode").val();
           var litpic = $("#litpic").val();
           var native_place = $("#native_place").val();
           var wechat = $("#wechat").val();
           var constellation = $("#constellation").val();
           var qq = $("#qq").val();
           var signature = $("#signature").val();
           var birth_date=$("#birth_date").val();
           $.ajax({
               type:'post',
               url:SITEURL+'member/index/ajax_userinfo_save',
               data:{
                   mid:mid,
                   wechat:wechat,
                   native_place:native_place,
                   nickname:nickname,
                   sex:sex,
                   truename:truename,
                   cardid:cardid,
                   address:address,
                   frmcode:frmcode,
                   litpic:litpic,
                   constellation:constellation,
                   qq:qq,
                   signature:signature,
                   birth_date:birth_date
               },
               dataType:'json',
               success:function(data){
                   if(data.status){
                       //$(".success-out-box").show();
                       layer.msg("{__('个人资料修改成功')}!",{
                           icon:6,
                           time:1000
                       })
                   }else{
                       layer.msg(data.msg, {icon:5});
                       return false;
                   }
               }
           })



       })
    })
</script>

</body>
</html>
