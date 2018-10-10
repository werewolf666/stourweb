<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}-后台登陆</title>
    {Common::getCss('base.css,login.css')}
    {include 'stourtravel/public/public_min_js'}
</head>
<body>
<div class="login-box clearfix">
    <div class="version fl">
        <div class="version-words"></div>
    </div>
    <div class="login-admin fr">
        <form method="post" id="login_form">
            <ul class="info-item-block">
                <li class="clearfix">
                    <span class="item-hd">登录账号</span>
                    <div class="item-bd">
                        <input type="text" class="account-input check_field" name="username" placeholder="请输入登录账号"/>
                    </div>
                </li>
                <li class="clearfix">
                    <span class="item-hd">登录密码</span>
                    <div class="item-bd">
                        <input type="password" class="password-input check_field" id="password" name="password" placeholder="请输入登录密码"/>
                    </div>
                </li>
                <li class="clearfix">
                    <span class="item-hd">验证码</span>
                    <div class="item-bd">
                        <input type="text" class="code-input check_field" id="check_code" name="checkcode" placeholder="请输入验证码"/>
                        <div class="yzm-box"><img id="yzm-box" data-src="{php} echo URL::site('captcha');{/php}?_t=" src="{php echo URL::site('captcha');}" width="120" height="40"/></div>
                    </div>
                </li>
            </ul>
            <input type="hidden" name="form_code" value="{$form_code}"/>
            <a class="login-btn" id="login_btn" href="#">立即登录</a>
        </form>
    </div>

    <div class="error-tips hide" id="error_tips"></div>
</div>
</body>
<script>
    $('#yzm-box').click(function () {
        $(this).attr('src', $(this).attr('data-src') + (new Date()).valueOf());
    });
    $('#login_btn').click(function () {
        var message;
        var passed = true;
        $('.check_field').each(function () {
            if ($(this).attr('name') == 'password' && $(this).val().length < 6) {
                message = '登录密码不少于6位';
                passed = false;
                return false;
            } else {
                if ($(this).val().length < 1) {
                    message = $(this).attr('placeholder');
                    passed = false;
                    return false;
                }
            }
        });
        if (!passed) {
            show_error(message);
            return false;
        }
        $.ajax({
            type: 'POST',
            url: SITEURL + 'login/ajax_login',
            data: $("#login_form").serialize(),
            dataType: 'json',
            success: function (data) {
                if (!data.status) {
                    $('#yzm-box').click();
                    $('#check_code').val('');
                    show_error(data.msg);
                }
                else if (data.status == 'ok') {
                    window.location.href = SITEURL;
                }
            }
        });
        return false;
    });
    //显示提示
    function show_error($message) {
        $('#error_tips').html('<p>' + $message + '</p>').removeClass('hide');
        close_error();
    }
    //关闭提示
    function close_error() {
        setTimeout(function () {
            $('#error_tips').text('').addClass('hide');
        }, 1000)
    }
    //ENTER提交
    $(document).keyup(function(event){
        if(event.keyCode ==13){
            $('#login_btn').click();
        }
    });
    //
    $('#password').blur(function(){
        var value=$(this).val();
        if(value.length>0&&value.length<6){
            //$(this).focus();
            show_error('登录密码不少于6位');
        }
    });
</script>
<script type="text/javascript">
    function noBack() {
        window.history.forward();
        setTimeout("noBack()", 500);
    }
    noBack();
    window.onload = noBack;
    window.onpageshow = function (evt) {
        if (evt.persisted) noBack();
    }
    window.onunload = function () {
        void(0);
    }
</script>

</html>

<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.0.201710.2015&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
