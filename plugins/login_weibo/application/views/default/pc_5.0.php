<!--thrid_login-->
<link type="text/css" href="/plugins/login_weibo/public/css/user.css" rel="stylesheet"/>
<script>

    var third = {
        openid: "<?php echo $user['openid'];?>",
        litpic: "<?php echo $user['litpic'];?>",
        nickname: "<?php echo $user['nickname'];?>",
        from: "<?php echo $user['from'];?>"
    }
    var user={};
    $(document).ready(function () {
        //账号格式检测
        $('#user-check').blur(function () {
            user.status = false;
            var bool = false, msg;
            var val = $(this).val();
            var valreg = /^\d+$/;
            if (valreg.test(val)) {
                //手机
                var phone_reg=/^1[0-9]{10}$/;
                if(!phone_reg.test(val)){
                    msg = '手机号码格式不正确';
                    bool=false;
                }
                else
                {
                    bool=true;
                }
            } else {
                //邮件
                var reg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
                !reg.test(val) ? msg = '邮箱格式不正确' : bool = true;
            }
            if (!bool) {
                $('.user-error').html(msg);
                return false;
            }
            user.status = true;
            $('.user-error').html('');
        });
        //密码检测
        $('#pwd-check').blur(function () {
            var val = $(this).val();
            user.status = false;
            if (val.length < 6) {
                $('.user-error').html('密码不能小于6位');
                return false;
            }
            user.status = true;
            $('.user-error').html('');
        });
        //直接登陆
        $('.user-bind').click(function () {
            user_bind({'third':third});
        });
        //绑定登陆
        $('#user-bind-success').click(function () {
            var post={};
            var name = $('#user-check').val();
            var pwd = $('#pwd-check').val();
            if (name.length < 1) {
                $('#user-check').blur();
                return false;
            }
            if (pwd.length < 1) {
                $('#pwd-check').blur();
                return false;
            }
            user.user = name;
            user.pwd = pwd;
            user_bind({'third':third,'member':user});
        });
        //绑定函数
        function user_bind(data) {
            $.post('/plugins/login_weibo/index/bind', data, function (rs) {
                if (rs.bool == true) {
                    $('.login-bind-box').not('.hide').find('.user-error').html('');
                    window.location.href = rs.url;
                }else{
                    $('.login-bind-box').not('.hide').find('.user-error').html(rs.msg);
                }
            }, 'json')
        }
    });
</script>
<div class="st-userlogin-box">
    <div class="st-login-wp">
        <div class="login-bind-box">
            <div class="login-bind-tit">绑定账号</div>
            <div class="login-account-key">
                <ul>
                    <li class="number">
                        <span class="tb"></span>
                        <input type="text" class="np-box" id="user-check" placeholder="请输入手机号和邮箱">
                    </li>
                    <li class="password">
                        <span class="tb"></span>
                        <input type="password" class="np-box" id="pwd-check" placeholder="请输入登录密码">
                    </li>
                    <li class="forget">
                        <span class="user-error"></span>
                        <a href="/member/findpwd">忘记密码？</a>
                    </li>
                </ul>
                <div class="login-bind-xz"><span class="bind-txt-btn">没有账号，<a class="user-bind"
                                                                             href="javascript:">直接登录</a></span><a
                        class="direct-login-btn fr" id="user-bind-success" href="javascript:">绑定登录</a></div>
            </div>
        </div>

    </div>
</div>
<!--thrid_login end-->