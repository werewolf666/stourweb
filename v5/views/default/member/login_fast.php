<div class="content-fix-box hide" id="is_login_order">
    <div class="login-small-box">
        <form id="loginfrm" method="post" margin_html=kyvz8B >
            <div class="login-small-con">
                <h3>{__('登陆')}<i class="close-btn"></i></h3>
                <div class="user-name"><input type="text" name="loginname" id="loginname" class="user-zh" placeholder="{__('请输入手机号')}/{__('邮箱')}"></div>
                <div class="user-password"><input type="password" name="loginpwd" id="loginpwd" class="user-zh" placeholder="{__('请输入登录密码')}"></div>
                <div class="user-num">
                    <input type="text" name="logincode" id="frmcode" class="user-zh" placeholder="{__('请输入验证码')}">
                    <span class="yzm"><img id="login_order_yzm" src="{$cmsurl}captcha"></span>
                    <span class="change-next" id="chang_yzm">{__('换一张')}</span>
                </div>
                <div id="login_order_error"></div>
                <div class="login-btn"><a class="login" id="login_order" href="javascript:">{__('登 录')}</a></div>
                <div class="reg-find-acc">{__('没有账号')}，<a href="{$cmsurl}member/register">{__('免费注册')}</a>
                    <a class="fr" href="{$cmsurl}member/findpwd">{__('找回密码')}</a></div>
            </div>
            <div class="other-login">
                <dl>
                    <dt><span>{__('使用其他方式登录')}</span><em></em></dt>
                    <dd>
                        {if (!empty($GLOBALS['cfg_qq_appid']) && !empty($GLOBALS['cfg_qq_appkey']))}
                        <a class="qq third-login" href="javascript:" data="{$GLOBALS['cfg_basehost']}/plugins/login_qq/index/index/?refer">QQ</a>
                        {/if}
                        {if (!empty($GLOBALS['cfg_weixi_appkey']) && !empty($GLOBALS['cfg_weixi_appsecret']))}
                        <a class="wx third-login" href="javascript:" data="{$GLOBALS['cfg_basehost']}/plugins/login_weixin/index/index/?refer">wx</a>
                        {/if}
                        {if (!empty($GLOBALS['cfg_sina_appkey']) && !empty($GLOBALS['cfg_sina_appsecret']))}
                        <a class="wb third-login" href="javascript:" data="{$GLOBALS['cfg_basehost']}/plugins/login_weibo/index/index/?refer">wb</a>
                        {/if}
                    </dd>
                </dl>
            </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        var node;
        //关闭弹出框
        $('.close-btn').click(function () {
            $('#is_login_order').addClass('hide');
        });
        //切换验证码
        $('#chang_yzm').click(function(){
            refresh_code()
        });
        $('#login_order_yzm').click(function(){
            refresh_code()
        });
        function refresh_code(){
            $('#login_order_yzm').attr('src',SITEURL+'captcha?'+Math.random());
        }
        //第三方登陆
        $('.other-login dd a').click(function(e){
            e.preventDefault();
            var href = $(this).attr('data') + '=' + encodeURIComponent(window.location.href);
            window.location.href=href;
        });
        //登陆
        $('#login_order').click(function(){
            node=$(this);
            $("#loginfrm").submit();
        });
        $("#loginfrm").validate({
            rules: {
                loginname: {
                    required: true
                },
                loginpwd: {
                    required: true,
                    minlength: 6
                },
                logincode:{
                    required: true
                }
            },
            messages: {
                loginname: {
                    required: '{__("error_user_not_empty")}'
                },
                loginpwd: {
                    required: '{__("error_pwd_not_empty")}',
                    minlength: '{__("error_pwd_min_length")}'
                },
                logincode:{
                    required: '{__("error_code_not_empty")}'
                }
            },
            errorPlacement: function (error, element) {
                var content = $('#loginfrm').find('#login_order_error').html();
                if (content == '') {
                    $('#loginfrm').find('#login_order_error').addClass('login-error').html(error);
                }
            },
            showErrors: function (errorMap, errorList) {
                if (errorList.length < 1) {
                    $('#loginfrm').find('#login_order_error').removeClass('login-error').html('');
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
                            var type=node.attr('data');
                            var url = $("#fromurl").val();
                            $('body').append(data.js);//同步登陆js
                            window.location.reload();
                        }
                        else{
                            refresh_code()
                            if(data.msg!=undefined){
                                $("#login_order_error").addClass('login-error').html(data.msg);
                            }else{
                                $("#login_order_error").addClass('login-error').html('{__("error_user_pwd")}');
                            }
                        }
                    },
                    error:function(a,b,c){}
                });
                return false;
            }
        });
    });
</script>
