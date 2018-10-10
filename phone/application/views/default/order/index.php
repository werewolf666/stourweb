<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>订单查询--{$webname}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {php echo Common::css('style.css,amazeui.css,extend.css');}
    {php echo Common::js('jquery.min.js,amazeui.js,common.js,jquery.validate.min.js,layer/layer.m.js');}
    <script type="text/javascript">
        var SITEURL = "{URL::site()}";
    </script>
</head>
<body>
{request "pub/header/isparam/订单查询"}
<section>
    <div class="mid_content">
        <div class="pho_list">
            <form id="form-submit">
            <div class="pho_list_login">
                <div class="pn_code">
                    <p><strong>手机号码</strong><input type="text" id="mobile" name="mobile" class="" placeholder="输入订单手机号" /></p>
                    <p><strong>验证码</strong><input type="text" id="code" name="code" class="" placeholder="请输入验证码" /><a href="javascript:;"><img class="captcha yzm_pic cursor " src=""/></a></p>
                    <p><strong>动态码</strong><input type="text" id="msg" name="msg" class="" placeholder="请输入短信动态码" /><a  id="resend" do-send="true" href="#">获取短信动态码</a></p>
                </div>
                <div class="error_txt" id="error_txt"></div>
            </div>
            </form>
        </div>
        <div class="btn_pho">
            <input type="button" id="submit_btn" value="开始查询" />
        </div>
    </div>
</section>
</body>
<script type="text/javascript">
    $(document).ready(function(){
        //验证码切换
        $('.captcha').attr('src', ST.captcha(SITEURL+'captcha'));
        $('.captcha').click(function () {
            $(this).attr('src', ST.captcha($(this).attr('src')));
        });

        var Y=this;
        function check_mobile(value) {
            var length = value.length;
            var bool=false;
            var phone = {
                //中国移动
                cm: /^(?:0?1)((?:3[56789]|5[0124789]|8[278])\d|34[0-8]|47\d)\d{7}$/,
                //中国联通
                cu: /^(?:0?1)(?:3[012]|4[5]|5[356]|8[356]\d|349)\d{7}$/,
                //中国电信
                ce: /^(?:0?1)(?:33|53|8[079])\d{8}$/,
                //中国大陆
                cn: /^(?:0?1)[34578]\d{9}$/
            };
            for(v in phone){

                if(phone[v].test(value)){

                    bool=true;
                    break;
                }
            }
            return (length == 11 && bool);
        }
        $('#resend').click(function(){
            var mobile=$('#mobile').val();
            var code = $("input[name='code']").val();
            var bool=$(this).attr('do-send');
            var node=this;
            if(!check_mobile(mobile)){
                $('#error_txt').html('<i></i>手机号码格式不正确');
                return false;
            }
            if(bool==='true'){
                //发送验证码
                $.post(SITEURL+'order/ajax_send_message',{'mobile':mobile,'code':code},function(rs){
                    if(!rs.status){
                        $('#error_txt').html('<i></i>'+rs.msg);
                    }else{
                        count_down(120);
                    }
                    return false;
                },'json')
            }else{
                return false;
            }
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
            $('#form-submit').submit();
        });
        //验证
        $('#form-submit').validate({
            rules:{
                mobile: {
                    required:true,
                    mobile:true
                },
                code:'required',
                msg: {
                    required: true
                }
            },
            messages:{
                mobile: {
                    required: '手机号码不能为空',
                    mobile: '手机号码格式不正确'
                },
                code:'验证码不能为空',
                msg:'短信动态码不能为空'
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
            },
            submitHandler:function(form){
                var data={};
                $("#form-submit").find('input').each(function(){
                    if($(this).attr('type')!='button'){
                        data[$(this).attr('name')]=$(this).val();
                    }
                });
                $.post(SITEURL+'order/ajax_login',data,function(rs){
                    if(rs.status!=1){
                        $('#error_txt').html('<i></i>'+rs.msg);
                    }else{
                        window.location.href=SITEURL+rs.url;
                    }
                },'json');
                return false;
            }
        });
    });
</script>
</html>
