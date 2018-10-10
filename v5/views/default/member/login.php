<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{__('用户登陆')}-{$webname}</title>
    {include "pub/varname"}
    {Common::css('base.css,user.css,extend.css')}
    {Common::js('jquery.min.js,base.js,common.js,jquery.validate.js,jquery.md5.js')}
</head>
<body>
{request "pub/header"}
      
  <div class="st-userlogin-box" {if $GLOBALS['cfg_login_bg']}style="background: url('{$GLOBALS['cfg_login_bg']}') center top no-repeat;"{/if}>
    <div class="st-login-wp">
    	<div class="st-admin-box">
        <form id="loginfrm" method="post" strong_font=zuxSjk >
      	    <div class="login-account-key">
        	<ul>
          	<li class="number">
              <span class="tb"></span>
              <input type="text" class="np-box" name="loginname" id="loginname"  placeholder="{__('请输入手机号或邮箱')}" />
            </li>
          	<li class="password">
            	<span class="tb"></span>
                <input type="password" class="np-box" name="loginpwd" id="loginpwd" placeholder="{__('请输入登录密码')}" />
            </li>
          	<li class="forget">
            	<span class="login_err"></span>
                <a href="{$cmsurl}member/findpwd">{__('忘记密码')}？</a>
            </li>
          	<li class="dl-btn"><a href="javascript:;" class="btn_login">{__('登 录')}</a></li>
          	<li class="now-zc">{__('您还没有账号')}？<a href="{$cmsurl}member/register">{__('立刻注册')}</a></li>
          </ul>
           <input type="hidden" name="logincode" id="frmcode" value="{$frmcode}"/>
                <input type="hidden" name="fromurl" id="fromurl" value="{$fromurl}">
        </div>

        </form>
        <div class="other-login">
        	<dl>
          	<dt><span>{__('使用其他方式登录')}</span><em></em></dt>
            <dd>

                {if (!empty($GLOBALS['cfg_qq_appid']) && !empty($GLOBALS['cfg_qq_appkey']))}
                 <a class="qq qqlogin" href="{$GLOBALS['cfg_basehost']}/plugins/login_qq/index/index/?refer={urlencode($fromurl)}">QQ</a>
                {/if}
                {if (!empty($GLOBALS['cfg_weixi_appkey']) && !empty($GLOBALS['cfg_weixi_appsecret']))}
                 <a class="wx wxlogin" href="{$GLOBALS['cfg_basehost']}/plugins/login_weixin/index/index/?refer={urlencode($fromurl)}">wx</a>
                {/if}

                {if (!empty($GLOBALS['cfg_sina_appkey']) && !empty($GLOBALS['cfg_sina_appsecret']))}
                 <a class="wb wblogin" href="{$GLOBALS['cfg_basehost']}/plugins/login_weibo/index/index/?refer={urlencode($fromurl)}">wb</a>
                {/if}
            </dd>
            </dl>
        </div>
      </div>
    </div>
  </div>

  
{request "pub/footer"}
{Common::js('layer/layer.js')}
<script>
    $(function(){
		 document.onkeydown = function(e){
            var ev = document.all ? window.event : e;
            if(ev.keyCode==13) {
                $(".btn_login").trigger('click');
            }
        }
		
        //登陆
        $(".btn_login").click(function(){
            $("#loginfrm").submit();
        })

        $("#loginfrm").validate({
            rules: {
                loginname: {
                    required: true
                },
                loginpwd: {
                    required: true,
                    minlength: 6
                }
            },
            messages: {
                loginname: {
                    required: '{__("error_user_not_empty")}'
                },
                loginpwd: {
                    required: '{__("error_pwd_not_empty")}',
                    minlength: '{__("error_pwd_min_length")}'
                }

            },
            errorPlacement: function (error, element) {
                var content = $('#loginfrm').find('.login_err:eq(0)').html();

                if (content == '') {

                    $('#loginfrm').find('.login_err').html(error);


                }
            },

            showErrors: function (errorMap, errorList) {

                if (errorList.length < 1) {
                    $('#loginfrm').find('.login_err:eq(0)').html('');
                } else {
                    this.defaultShowErrors();
                }
            },
            submitHandler:function(form){
                var url = SITEURL+'member/login/ajax_login';
                var loginname = $("#loginname").val();
                var loginpwd = $.md5($("#loginpwd").val());
                var frmcode = $("#frmcode").val();
                $.ajax({
                    type:"post",
                    async: false,
                    url:url,
                    data:{loginname:loginname,loginpwd:loginpwd,frmcode:frmcode},
                    dataType:'json',
                    success: function(data){

                        if(data.status == '1'){//登陆成功,跳转到来源网址
                            var url = $("#fromurl").val();
                            setTimeout(function(){window.open(url,'_self');},500);
                            $('body').append(data.js);//同步登陆js
                        }
                        else{
                            if(data.msg!=undefined){
                                $(".login_err").html(data.msg);
                            }else{
                                $(".login_err").html('{__("error_user_pwd")}');
                            }

                        }


                    },
                    error:function(a,b,c){

                    }
                });
                return false;
            }


        })



    })


</script>
</body>
</html>
