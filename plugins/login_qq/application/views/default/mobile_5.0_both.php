<style>
.user-now-login{padding: 0.5rem 0;font-size: 1.4rem;}
.user-now-login .login-link{color: #ff5a00}
.fr{ float: right;color: #ff5a00}
footer{display: none}
.img_checkcode{
position: absolute;
    right: 1rem;
    top: 50%;
    margin-top: -15px;
}
</style>
<section>
    <div class="mid_content">

        <div class="st_user_box login-bind-box">

            <div class="st_login">
                <div class="number_password">
                    <p><strong>账号：</strong><input type="text" id="account" name="account" class="" placeholder="手机/邮箱" /></p>
                    <p>
                        <strong>验证码：</strong><input type="text" id="checkcode_img" class="" name="checkcode_img" placeholder="图片验证码" />
                        <img class="send-yzm img_checkcode" src="/captcha" width="100" height="30" onClick="this.src=this.src+'?math='+ Math.random()" />
                    </p>
                    <p><strong>验证码：</strong><input type="password" id="checkcode" class="" name="checkcode" placeholder="验证码" /><a href="javascript:;" id="send_checkcode">发送验证码</a></p>
                </div>
                <div class="error_txt user-error"></div>
                <div class="submit_btn"><input type="submit" id="confirm_btn" value="确定" /></div>
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
        $('#account').change(function () {
            check_account();
        });
        //密码检测
        $('#checkcode').change(function () {
            if(check_code())
            {
                $('.user-error').html('');
            }
        });

        //发送验证码
        $("#send_checkcode").click(function(){
            var account=$("#account").val();

            var waiting=$("#send_checkcode").attr('waiting');
            if(waiting==1)
            {
                return;
            }
            if(!check_account())
            {
                return;
            }

            $("#send_checkcode").attr('waiting',1);
            $.ajax({
                url:'/plugins/login_qq/index/ajax_send_code',
                type:'POST', //GET
                async:true,    //或false,是否异步
                data:{account:account,checkcode_img:$("#checkcode_img").val()},
                dataType:'json',
                success:function(data){
                    if(data.Success)
                    {
                        tick_send(60);
                    }
                    else
                    {
                        $("#send_checkcode").removeAttr('waiting');
                        $('.user-error').html('<i></i>'+data.Message);
                    }
                }
            })
        });

        //倒计时
        function tick_send(seconds)
        {
            if(seconds>0)
            {
                $("#send_checkcode").attr('seconds',seconds);
            }

            var left_seconds=$("#send_checkcode").attr('seconds');
            if(!left_seconds || left_seconds==0)
            {
                $("#send_checkcode").text("发送验证码");
                $("#send_checkcode").removeAttr('waiting');
                return;
            }
            else
            {
                $("#send_checkcode").text("请"+left_seconds+"秒后发送");
                $("#send_checkcode").attr('waiting',1);
            }
            setTimeout(function(){
                $("#send_checkcode").attr('seconds',left_seconds-1);
                tick_send();
            },1000)
        }

        $("#confirm_btn").click(function(){
            var status=true;
            status = check_account();
            if(!status)
            {
                return;
            }
            status= check_code();
            if(!status)
            {
                return;
            }
            $('.user-error').html('');

            var checkcode=$("#checkcode").val();
            var account = $("#account").val();
            $.ajax({
                url:'/plugins/login_qq/index/ajax_both_save',
                type:'POST', //GET
                async:true,    //或false,是否异步
                data:{
                    checkcode:checkcode,
                    account:account,
                    third:third
                },
                dataType:'json',
                success:function(data){
                    if(data.bool)
                    {
                        window.location.href = data.url;
                    }
                    else
                    {
                        $('.user-error').html('<i></i>'+data.msg);
                    }
                }
            })

        });
        //直接登陆


        //验证账号
        function check_account()
        {
            var bool=false;
            var msg='';
            var val = $("#account").val();
            var valreg = /^\d+$/;
            if (valreg.test(val)) {
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
                $('.user-error').html('<i></i>'+msg);
            }
            return bool;
        }

        //验证验证码
        function check_code()
        {
            var checkcode=$("#checkcode").val();
            var account=$("#account").val();
            var status = false;
            $.ajax({
                url:'/plugins/login_qq/index/ajax_check_code',
                type:'POST', //GET
                async:false,    //或false,是否异步
                data:{
                    checkcode:checkcode,
                    account:account
                },
                dataType:'json',
                success:function(data){
                    if(!data.status)
                    {
                        $('.user-error').html('<i></i>验证码错误!');
                    }
                    status= data.status;
                }
            })
            return status;
        }


    });
</script>