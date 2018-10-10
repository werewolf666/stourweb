<div class="header_top bar-nav">
    <a class="back-link-icon" href="#myAccount" data-rel="back"></a>
    <h1 class="page-title-bar">绑定邮箱地址</h1>
</div>
<!-- 公用顶部 -->
<div class="page-content">
    <form id="emailfrm" method="post">
    <div class="user-item-list">

        <ul class="list-group">
            <li>
                <strong class="hd-name">邮箱</strong>
                <input type="text" name="email" id="email" class="data-text text-left fr" placeholder="输入邮箱地址" />
            </li>
            <li>
                <strong class="hd-name">验证码</strong>
                <input type="text" class="num-text" name="checkcode" id="checkcode" placeholder="请输入邮箱验证码" />
                <em class="get-code">获取邮箱验证码</em>
            </li>
        </ul>
    </div>
    <div class="error-txt" style="display: none"><i class="ico"></i><span class="errormsg"></span></div>
    <a class="save-info-btn bind-email" href="javascript:;">保存</a>
        <input type="hidden" name="token" value="{$token}"/>
    </form>
    {Common::js('jquery.validate.min.js')}
</div>
<script>

    var is_can_send = 1;//是否可发送验证码
    $('#emailfrm').validate({
        rules: {
            email: {
                required: true,
                email: true,
                remote: {
                    url: SITEURL+'member/account/ajax_check_email',
                    type: 'post'
                }
            },

            checkcode: 'required'

        },
        messages: {
            email: {
                required: '{__("邮箱不能为空")}',
                email: '{__("请输入正确的邮箱地址")}',
                remote:'{__("邮箱已被注册")}'
            },

            checkcode: '{__("请填写验证码")}'

        },
        errorPlacement: function (error, element) {

            var content = $('.errormsg').html();
            if (content == '') {
                error.appendTo($('.errormsg'));


            }
        },
        showErrors: function (errorMap, errorList) {
            if (errorList.length < 1) {
                $('.errormsg:eq(0)').html('');
                $('.error-txt').hide();
            } else {
                this.defaultShowErrors();
                $('.error-txt').show();
            }
        },
        submitHandler: function (form) {
            var frmdata = $("#emailfrm").serialize();
            $.ajax({
                type:'POST',
                url:SITEURL+'member/account/ajax_email_save',
                data:frmdata,
                dataType:'json',
                success:function(data){
                    if(data.status){
                        layer.open({
                            content: '保存成功'
                            ,time: 2 //2秒后自动关闭
                        });

                    }else{
                        layer.open({
                            content: data.msg
                            ,time: 2 //2秒后自动关闭
                        });
                    }
                }

            })


        }
    });
    $(function(){

        $('.bind-email').click(function(){
            $('#emailfrm').submit();
        })


        //发送短信验证码
        $('.get-code').click(function(){

            if(!is_can_send){
                return false;
            }

            var email = $("#email").val();
            var regPartton=/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
            if (!regPartton.test(email))
            {
                $.layer({
                    type:2,
                    text:'请输入正确的邮箱',
                    time:1000
                })

                return false;
            }
            var token = "{$token}";
            var url = SITEURL+'member/account/ajax_send_email_code';
            $.post(url,{email:email,token:token},function(data) {
                if(data.status)
                {
                    code_timeout(60);
                    is_can_send = 0;
                    return false;
                }
                else
                {
                    $.layer({
                        type:1,
                        icon:2,
                        text:data.msg,
                        time:1000
                    })
                    return false;
                }
            },'json');


        })
    })

    //短信发送倒计时
    function code_timeout(v){
        if(v>0)
        {
            $('.get-code').html((--v)+'秒后重发');
            setTimeout(function(){
                code_timeout(v)
            },1000);
        }
        else
        {
            $('.get-code').html('重发验证码');
            is_can_send = 1;

        }
    }


</script>