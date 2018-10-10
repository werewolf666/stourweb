<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS<?php echo $coreVersion;?>-网站登陆</title>
    <meta http-equiv="Expires" CONTENT="0">
    <meta http-equiv="Cache-Control" CONTENT="no-cache">
    <meta http-equiv="Pragma" CONTENT="no-cache">
<meta name="renderer" content="webkit">
    <?php echo Common::getCss('login.css,base.css,style.css'); ?>
    <?php echo Common::getScript('jquery-1.8.3.min.js,common.js,msgbox/msgbox.js,jquery.validate.js'); ?>
    <?php echo Common::getCss('msgbox.css','js/msgbox/'); ?>
    <?php echo Common::getCss('common.css')?>
    <script>
        window.SITEURL =  "<?php echo URL::site();?>";
      
    </script>
</head>
<body>
<div class="login-box">
    <div class="head-box">
        <span class="head-version">V<?php echo $majorVersion;?></span>
        <a href="http://www.stourweb.com" target="_blank"><img class="fl" src="<?php echo $GLOBALS['cfg_public_url'];?>images/gw-blank-ico.png" /></a>
    </div>
    <form method="post" id="loginfrm">
    <div class="login-admin">
        <div class="admin-head">
            <p>管理员登录</p>
            <p>当前IP:<?php echo $ip;?></p>
        </div>
        <dl class="zh-password">
            <dt>账号</dt>
            <dd><input type="text" name="username" class="text_250" /></dd>
        </dl>
        <dl class="zh-password">
            <dt>密码</dt>
            <dd><input type="password" name="password" class="text_250" /></dd>
        </dl>
        <dl class="zh-password">
            <dt>验证</dt>
            <dd>
                <input type="text" name="checkcode" class="text_100" />
                <div class="yzm-box"><img id="yzm-box" src="<?php echo URL::site('captcha');?>" style="cursor:pointer" onClick="this.src=this.src+'?'" title="点击我更换图片" alt="点击我更换图片"></div>
            </dd>
        </dl>
        <a class="login-btn" href="javascript:;">登 录</a>
        <a class="reset-btn" href="javascript:;">重 置</a>
    </div>
        <input type="hidden" name="form_code" value="<?php echo $form_code;?>"/>
    </form>
</div>
</body>
<script language="JavaScript">
    $(function(){
        document.onkeydown=function(event){
            if(event.keyCode==13){
                $("#loginfrm").submit();
            }
        }
    })
    var SITEURL='<?php echo URL::site()?>';
    $('.login-btn').click(function(){
        $("#loginfrm").submit();
    })
    $('.reset-btn').click(function(){
        $("#loginfrm")[0].reset();
        $(".yzm-box img").trigger('click');
        //$('input').val('');
    })
    //表单验证
    $("#loginfrm").validate({
        focusInvalid:false,
        rules: {
            username:
            {
                required: true
            },
            password: {
                required: true,
                rangelength: [6, 16]
            },
            checkcode:{
                required:true
            }
        },
        messages: {
            username:{
                required:"请输入帐号"
            },
            password: {
                required:"请输入密码",
                rangelength:"密码长度为6-16位"
            },
            checkcode:{
                required:'请输入验证码'
            }
        },
        errUserFunc:function(element){
        },
        submitHandler:function(form){
            $.ajax({
                type : 'POST',
                url : SITEURL+'login/ajax_login',
                data : $("#loginfrm").serialize(),
                dataType : 'json',
                success:function(data){
                    if(!data.status){
                        $('#yzm-box').click();
                        ST.Util.showMsg(data.msg,5,1000);
                    }
                    else if(data.status=='ok'){
                        window.location.href=SITEURL;
                    }
                }
            });
            return false;//阻止常规提交
        }
    });
</script>
<script type="text/javascript">
       function noBack() {
           window.history.forward();
           setTimeout("noBack()", 500);
       }
       noBack();
       window.onload=noBack;
       window.onpageshow=function(evt) {
           if (evt.persisted) noBack();
       }
       window.onunload=function() {
           void(0);
       }
   </script>
</html><script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=6.0.201703.3001&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
