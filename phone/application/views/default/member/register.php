<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>用户注册--{$webname}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {php echo Common::css('style.css,amazeui.css,extend.css,../js/layer/need/layer.css');}
    <script type="text/javascript">
        var SITEURL = "{URL::site()}";
    </script>
</head>

<body>

<header>
    <div class="header_top">
        <div class="st_back"><a href="{$referer}"></a></div>
        <h1 class="tit">注册</h1>
        <a class="top_user_reg" href="{$cmsurl}member/login">登录</a>
    </div>
</header>


<section>
    <div class="mid_content">

        <div class="st_user_box">

            <div class="st_tabslide" id="swap">
                <a class="on cursor">手机注册</a>
                <a class="cursor">邮箱注册</a>
            </div>
            <div class="st_login">
                <form id="form-mobile" ul_padding=b_JwOs >
                    {if $isopen}
                    <div class="number_password">
                        <p><strong>账号：</strong><input type="text" id="phone" name="user" class="" placeholder="请输入手机号"/></p>

                        <p><strong>验证码：</strong><input type="text" name="code" class="" placeholder="请填写验证码"/><img
                                class="captcha yzm_pic cursor " src="" height="30"/></p>

                        <p><strong>动态码：</strong><input type="text" name="msg" class="" placeholder="请输入短信验证码"/>
                            <a class="yz_num cursor" id="resend" do-send="true">获取短信验证码</a>
                        </p>

                        <p><strong>密码：</strong><input type="password" name="pwd" class="" placeholder="请输入密码"/></p>
                    </div>
                    {else}
                    <div class="number_password">
                        <p><strong>账号：</strong><input type="text" name="user" class="" placeholder="请输入手机号"/></p>

                        <p><strong>密码：</strong><input type="password" name="pwd" class="" placeholder="请输入密码"/></p>


                        <p><strong>验证码：</strong><input type="text" name="code" class="" placeholder="请填写验证码"/><img
                                class="captcha yzm_pic cursor " src="" height="30"/></p>
                    </div>
                    {/if}
                    <div class="error_txt"></div>
                    <input type="hidden" name="frmcode" value="{$frmcode}"/>
                    <div class="submit_btn"><input type="button" value="注册"/></div>
                </form>
            </div>
            <div class="st_login hide">
                <form id="form-email">
                    {if $is_emailcode_open==1}
                        <div class="number_password">
                            <p><strong>邮箱：</strong><input type="text" name="user" id="email_user" class="" placeholder="请输入您的邮箱"/></p>
                            <p><strong>验证码：</strong><input type="text" id="email_code" name="code" class="" placeholder="请填写验证码"/><img
                                    class="captcha yzm_pic cursor" src="" height="30"/></p>
                            <p><strong>动态码：</strong><input type="text" name="msg" class="" placeholder="请输入邮箱验证码"/>
                                <a class="yz_num cursor" id="email_resend" do-send="true">获取邮箱验证码</a>
                            </p>
                            <p><strong>密码：</strong><input type="password" name="pwd" class="" placeholder="请填写您的密码"/></p>
                        </div>
                    {else}
                    <div class="number_password">
                        <p><strong>邮箱：</strong><input type="text" name="user" class="" placeholder="请输入您的邮箱"/></p>

                        <p><strong>密码：</strong><input type="password" name="pwd" class="" placeholder="请填写您的密码"/></p>

                        <p><strong>验证码：</strong><input type="text" name="code" class="" placeholder="请填写验证码"/><img
                                class="captcha yzm_pic cursor" src="" height="30"/></p>
                    </div>
                    {/if}

                    <div class="error_txt"></div>
                    <div class="submit_btn"><input type="button" value="注册"/></div>
                    <input type="hidden" name="is_email"/>
                    <input type="hidden" name="frmcode" value="{$frmcode}"/>
                </form>
            </div>
            {request 'member/login/third'}
        </div>

    </div>
</section>


</body>
{php echo Common::js('jquery.min.js,common.js,jquery.validate.min.js,layer/layer.m.js');}
<script type="text/javascript">
    $(document).ready(function () {
        //验证码切换
        $('.captcha').attr('src', ST.captcha(SITEURL+'captcha'));
        $('.captcha').click(function () {
            $(this).attr('src', ST.captcha($(this).attr('src')));
        });
        //
        $('#swap a').click(function () {
            var index = $(this).index();
            $(this).addClass('on').siblings().removeClass('on');
            $('.st_login').eq(index).removeClass('hide').siblings('.st_login').addClass('hide');
        });
        //验证
        $('#form-mobile').validate({
            rules: {
                user: {
                    required: true,
                    mobile: true
                },
                msg: 'required',
                code: 'required',
                pwd: {
                    required: true,
                    minlength: 6
                }
            },
            messages: {
                user: {
                    required: '{__("error_user_not_empty")}',
                    mobile: '{__("error_user_phone")}'
                },
                msg: '{__("error_msg_not_empty")}',
                code: '{__("error_code_not_empty")}',
                pwd: {
                    required: '{__("error_pwd_not_empty")}',
                    minlength: '{__("error_pwd_min_length")}'
                }

            },
            errorElement: "em",
            errorPlacement: function (error, element) {
                var content = $('#form-mobile').find('.error_txt:eq(0)').html();
                if (content == '') {
                    $('#form-mobile').find('.error_txt:eq(0)').html('<i></i>');
                    error.appendTo($('#form-mobile').find('.error_txt:eq(0)'));
                }
            },
            showErrors: function (errorMap, errorList) {
                if (errorList.length < 1) {
                    $('#form-mobile').find('.error_txt:eq(0)').html('');
                } else {
                    this.defaultShowErrors();
                }
            }
        });
        $('#form-email').validate({
            rules: {
                user: {
                    required: true,
                    email: true
                },
                code: 'required',
                msg: 'required',
                pwd: {
                    required: true,
                    minlength: 6
                }
            },
            messages: {
                user: {
                    required: '{__("error_user_not_empty")}',
                    email: '{__("error_user_email")}'
                },
                msg: '{__("error_msg_not_empty")}',
                code: '{__("error_code_not_empty")}',
                pwd: {
                    required: '{__("error_pwd_not_empty")}',
                    minlength: '{__("error_pwd_min_length")}'
                }

            },
            errorElement: "em",
            errorPlacement: function (error, element) {
                var content = $('#form-email').find('.error_txt:eq(0)').html();
                if (content == '') {
                    $('#form-email').find('.error_txt:eq(0)').html('<i></i>');
                    error.appendTo($('#form-email').find('.error_txt:eq(0)'));
                }
            },
            showErrors: function (errorMap, errorList) {
                if (errorList.length < 1) {
                    $('#form-email').find('.error_txt:eq(0)').html('');
                } else {
                    this.defaultShowErrors();
                }
            }
        });
        $('#resend').click(function () {
            var bool = $(this).attr('do-send');
            var frmcode = "{$frmcode}";
            var code = $("input[name='code']").val();
            var phone = $('#phone').val();
            var node = this;
            if (bool === 'true') {
                //发送验证码
				$('#resend').attr('do-send','false').removeClass('cursor');
                $.post(SITEURL+'member/register/ajax_send_message', {'phone': phone,'frmcode':frmcode,'code':code}, function (bool) {
                    $('#form-mobile').find('.error_txt:eq(0)').html('');
                    if (bool != 1) {
                        var message = bool == 0 ? '发送失败，稍后在试' : bool;
                        $('#form-mobile').find('.error_txt:eq(0)').html('<i></i>' + message);
						$('#resend').attr('do-send', 'true').addClass('cursor');
                    }else{
                        count_down(120);
                    }
                    return false;
                }, 'text')

            }
        });

        $('#email_resend').click(function () {
            var bool = $(this).attr('do-send');
            var frmcode = "{$frmcode}";
            var code = $("#email_code").val();
            var email = $('#email_user').val();
            var node = this;
            if (bool === 'true') {
                //发送验证码
				$('#email_resend').attr('do-send','false').removeClass('cursor');
                $.post(SITEURL+'member/register/ajax_send_email_message', {'email': email,'frmcode':frmcode,'code':code}, function (bool) {
                    $('#form-email').find('.error_txt:eq(0)').html('');
                    if (bool != 1) {
                        var message = bool == 0 ? '发送失败，稍后在试' : bool;
                        $('#form-email').find('.error_txt:eq(0)').html('<i></i>' + message);
						$('#email_resend').attr('do-send', 'true').addClass('cursor');
                    }else{
                        count_down_email(120);
                    }
                    return false;
                }, 'text')
            }
        });
        function count_down(v) {
            if (v > 0) {
                $('#resend').html(--v+'秒后');
                $('#resend').attr('do-send','false').removeClass('cursor');
                setTimeout(function () {
                    count_down(v);
                }, 1000);
            }
            else {
                $('#resend').attr('do-send', 'true').addClass('cursor').html('重新获取验证码');
            }
        }
        function count_down_email(v) {
            if (v > 0) {
                $('#email_resend').html(--v+'秒后');
                $('#email_resend').attr('do-send','false').removeClass('cursor');
                setTimeout(function () {
                    count_down_email(v);
                }, 1000);
            }
            else {
                $('#email_resend').attr('do-send', 'true').addClass('cursor').html('重新获取验证码');
            }
        }
        //提交
        $('.submit_btn').click(function(){
            var node=$(this).parents('form');
            if(node.valid()){
                var data={};
                node.find('input').each(function(){
                    if($(this).attr('type')!='button'){
                        data[$(this).attr('name')]=$(this).val();
                    }
                });
                $.post(SITEURL+'member/register/ajax_reg',data,function(rs){
                    if(parseInt(rs.status)<1){
                        node.find('.error_txt:eq(0)').html('<i></i>'+rs.msg);
                        $('.captcha').attr('src',ST.captcha(SITEURL+'captcha'));
                    }else{
                        layer.open({
                            content: '{__("success_member_insert")}',
                            time: 2,
                            end:function(){
                               window.location.href=rs.url;
                            }
                        });    
                    }
                },'json');
            }
        });
    });
</script>
</html>
