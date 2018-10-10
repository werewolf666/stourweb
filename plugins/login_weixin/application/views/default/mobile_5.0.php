<style>
    .user-now-login{padding: 0.5rem 0;font-size: 1.4rem;}
    .user-now-login .login-link{color: #ff5a00}
    .fr{ float: right;color: #ff5a00}
    footer{display: none}
</style>
<section>
    <div class="mid_content">

        <div class="st_user_box login-bind-box">

            <div class="st_login">
                <div class="number_password">
                    <p><strong>账号：</strong><input type="text" id="user-check" class="" placeholder="手机/邮箱" /></p>
                    <p><strong>密码：</strong><input type="password" id="pwd-check" class="" placeholder="请输入密码" /><a href="/phone/member/find">忘记密码</a></p>
                </div>
                <div class="error_txt user-error"></div>
                <div class="submit_btn"><input type="submit" id="user-bind-success" value="完成绑定" /></div>
                <div class="user-now-login"><input type="submit" class="user-bind" value="不绑了，直接登录" /></div>
            </div>
        </div>
    </div>
</section>

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
                var phone = {
                    //中国移动
                    cm: /^(?:0?1)((?:3[56789]|5[0124789]|8[278])\d|34[0-8]|47\d)\d{7}$/,
                    //中国联通
                    cu: /^(?:0?1)(?:3[012]|4[5]|5[356]|8[356]\d|349)\d{7}$/,
                    //中国电信
                    ce: /^(?:0?1)(?:33|53|8[079])\d{8}$/,
                    //中国大陆
                    cn: /^(?:0?1)[3458]\d{9}$/
                };
                for(v in phone){
                    if(phone[v].test(val)){
                        bool=true;
                        break;
                    }
                }
                if(!bool){
                    msg = '手机号错误';
                }
            } else {
                //邮件
                var reg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
                !reg.test(val) ? msg = '邮箱格式不正确' : bool = true;
            }
            if (!bool) {
                $('.user-error').html('<i></i>'+msg);
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
                $('.user-error').html('<i></i>密码不能小于6位');
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
            $.post('/plugins/login_weixin/index/bind', data, function (rs) {
                if (rs.bool == true) {
                    $('.login-bind-box').not('.hide').find('.user-error').html('');
                    window.location.href = rs.url;
                }else{
                    $('.login-bind-box').not('.hide').find('.user-error').html('<i></i>'+rs.msg);
                }
            }, 'json')
        }
    });
</script>