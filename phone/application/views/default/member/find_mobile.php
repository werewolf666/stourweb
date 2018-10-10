<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>手机找回密码-{$webname}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {php echo Common::css('style.css,amazeui.css,extend.css');}
    {php echo Common::js('jquery.min.js');}
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
            <form  data="{$mobile}" id="form-submit">
                <div class="st_login">
                    <div class="st_txt_ts">我们向您的手机<span>{php} echo preg_replace('/(\d{3})\d{4}(\d{4})/','$1***$2',$mobile){/php}</span>发送了短信验证码，请在下面输入您接收到的验证码！</div>
                    <div class="number_password">
                        <p><strong>验证码：</strong><input type="text" id="code" name="code" class="" placeholder="请输入短信验证码" /><a class="down_time cursor" id="resend" do-send="true">获取短信验证码</a></p>
                    </div>
                    <div class="error_txt" id="error_txt"></div>
                    <div class="submit_btn" id="submit_btn"><input type="button" value="下一步" /></div>
                </div>
                <input type="hidden" name="mobile" value="{$mobile}"/>
            </form>
            {request 'member/login/third'}
        </div>
    </div>
</section>


</body>
<script type="text/javascript">
    $(document).ready(function(){
     var Y=this;
     $('#resend').click(function(){
         var bool=$(this).attr('do-send');
         var node=this;
         if(bool==='true'){
          //发送验证码
             $.post(SITEURL+'member/find/ajax_send_message',{'cache': '{$md5}' },function(bool){
               if(Boolean(bool)){}
                 return false;
             },'text')
         }
         count_down(120);
      });
     function count_down(v){
        if(v>0)
        {
            $('#resend').html(--v+'秒后');
            $('#resend').attr('do-send','false').removeClass('cursor');
            setTimeout(function(){
                count_down(v);
            },1000);
        }
        else
        {
            $('#resend').attr('do-send','true').addClass('cursor').html('重新获取验证码');
        }
     }
     //检测
      $('#submit_btn').click(function(){
            if($('#code').val().length>0){
                var data={};
                $("#form-submit").find('input').each(function(){
                    if($(this).attr('type')!='button'){
                        data[$(this).attr('name')]=$(this).val();
                    }
                });
                $.post(SITEURL+'member/find/reset/{$md5}',data,function(rs){
                    if(parseInt(rs.status)<1){
                        $('#error_txt').html('<i></i>'+rs.msg);
                        if($('#captcha').length>0){
                            $('#captcha').attr('src',ST.captcha(SITEURL+'captcha'));
                        }else{
                            $('#login-content').append('<p><strong>验证码：</strong><input type="text" name="code" class="" placeholder="请填写验证码" /><img class="yzm_pic cursor captcha" id="captcha" src="'+SITEURL+'captcha" height="30" /></p>');
                        }
                    }else{
                        window.location.href=rs.url;
                    }
                },'json');
            }else{
              $('#error_txt').html('<i></i>验证码不能为空');
           }
      });

        //默认现用户发送一条短信验证码
        $("#resend").click();
    });
</script>
</html>
