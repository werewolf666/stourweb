<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>找回密码-{$webname}</title>
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
            <form action="{$cmsurl}member/find" method="post" enctype="application/x-www-form-urlencoded" id="form-submit">
                <div class="st_login">
                    <div class="number_password">
                        <p><strong>账号：</strong><input id="type" type="text" class="mail" name="user" placeholder="{if $isopen}手机/{/if}邮箱" /></p>
                        <p><strong>验证码：</strong><input type="text" id="code" name="code" class="" placeholder="请填写验证码" /><img class="yzm_pic cursor captcha" src="" height="30" /></p>
                    </div>
                    <div class="error_txt" id="error_txt"></div>
                    <div class="submit_btn" id="submit_btn"><input type="button" value="下一步" /></div>
                </div>
            </form>
            {request 'member/login/third'}
        </div>

    </div>
</section>
</body>
{php echo Common::js('jquery.min.js,common.js,jquery.validate.min.js');}
<script type="text/javascript">
    $(document).ready(function(){
        //手机短信
        var isopen={$isopen};
        //切换验证码
       $('.captcha').attr('src',ST.captcha(SITEURL+'captcha'));
       $('.captcha').click(function(){
           $(this).attr('src',ST.captcha($(this).attr('src')));
       });
        //验证
        $('#form-submit').validate({
            rules:{
                email:{
                   required:true,
                   email: true
                },
                mobile: {
                    required:true,
                    mobile:true
                },
                code:'required',   
            },
            messages:{
                mobile: {
                    required: '{__("error_user_not_empty")}',
                    mobile: '{__("error_user_phone")}'
                },
                email: {
                    required: '{__("error_user_not_empty")}',
                    email: '{__("error_user_email")}'
                },
                code:'{__("error_code_not_empty")}'
            },
            errorElement: "em",
            errorPlacement: function(error, element) {
                var content=$('#error_txt').html();
                if(content==''){
                    $('#error_txt').html('<i></i>');
                    error.appendTo($('#error_txt'));
                }
            },
            showErrors:function(errorMap,errorList){
                 if(errorList.length<1){
                     $('#error_txt').html('');
                 }else{
                     this.defaultShowErrors();
                 }
            }
        });
        $('#type').keyup(function(){
            var reg=/^[0-9]+$/;
            if(isopen==0 || !reg.test($(this).val())){
                $(this).attr({class:'email',name:'email'});
            }else{
                $(this).attr({class:'mobile',name:'mobile'});
            }
        });
        $('#submit_btn').click(function(){
        var node=$(this).parents('form');
            if(node.valid()){
                var data={};
                node.find('input').each(function(){
                    if($(this).attr('type')!='button'){
                        var name=$(this).attr('name');
                        if(name=='email' || name=='mobile'){
                            data['user']=$(this).val();
                        }else{
                           data[name]=$(this).val(); 
                        }  
                    }
                });
                $.post(SITEURL+'member/find/index',data,function(rs){console.log(rs);
                    if(parseInt(rs.status)<1){
                        node.find('.error_txt:eq(0)').html('<i></i>'+rs.msg);
                        $('.captcha').attr('src',ST.captcha(SITEURL+'captcha'));
                        $('#code').val('');
                    }else{
                        window.location.href=rs.url;
                    }
                },'json');
            }
        });
    });

</script>
</html>
