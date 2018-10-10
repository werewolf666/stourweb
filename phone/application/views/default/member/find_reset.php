<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>设置新密码-{$webname}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {php echo Common::css('style.css,amazeui.css,extend.css');}
    <script type="text/javascript">
        var SITEURL = "{URL::site()}";
    </script>
</head>

<body>

<header>
    <div class="header_top">
        <div class="st_back"><a href="{$url}"></a></div>
        <h1 class="tit">找回密码</h1>
    </div>
</header>


<section>
    <div class="mid_content">

        <div class="st_user_box">
            <form action="{$cmsurl}member/find/reset/{php} echo md5($data['user']);{/php}" method="post" enctype="application/x-www-form-urlencoded" id="form-submit">
                <div class="st_login">
                    <div class="st_set_password">设置新密码</div>
                    <div class="number_password">
                        <p><strong>输入新密码：</strong><input class="twice_pass" name="pwd" id="pwd" type="password" /></p>
                        <p><strong>确认新密码：</strong><input class="twice_pass"  name="password" type="password"/></p>
                    </div>
                    <div class="error_txt" id="error_txt"></div>
                    <div class="submit_btn" id="submit_btn"><input type="button" value="下一步" /></div>
                </div>
                <input type="hidden" name="mid" value="{$data['mid']}"/>
                <input type="hidden" name="token" value="{$data['token']}">
            </form>
            {request 'member/login/third'}
        </div>
    </div>
</section>
</body>
{php echo Common::js('jquery.min.js,common.js,jquery.validate.min.js');}
<script type="text/javascript">
$(document).ready(function(){
    //验证
    $('#form-submit').validate({
        rules:{
            pwd: {
                required: true,
                minlength: 6
            },
            password: {
                required: true,
                minlength: 6,
                equalTo: "#pwd"
            }
        },

        messages:{
            pwd:'密码长度不能低于6个字符',
            password:'输入密码不一致'
        },
        errorElement: "em",
        errorPlacement: function(error, element) {
            var content=$('#error_txt').html();
            if(content==''){
                $('#error_txt').html('<i></i>');
            }
            error.appendTo($('#error_txt'));
        },
        showErrors:function(errorMap,errorList){
            if(errorList.length<1){
                $('#error_txt').html('');
            }else{
                this.defaultShowErrors();
            }
        }
    });
    $('#submit_btn').click(function(){
        if($('#form-submit').valid()){
            $('#form-submit').submit();
        }

    });
});

</script>
</html>
