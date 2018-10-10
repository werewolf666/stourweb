<div class="header_top bar-nav">
    <a class="back-link-icon" href="#pageHome" data-rel="back"></a>
    <h1 class="page-title-bar">订单查询</h1>
</div>
<!-- 公用顶部 -->
<div class="page-content">

    <div class="user-item-list">
        <form id="queryfrm" method="post">
        <ul class="list-group">
            <li>
                <strong class="hd-name">手机号码</strong>
                <input type="text" class="num-text" name="mobile" id="mobile" placeholder="输入订单手机号码" />
            </li>
            <li>
                <strong class="hd-name">验证码</strong>
                <input type="text" class="num-text" name="code" id="code" placeholder="请输入验证码" />
                <img class="captcha yzm fr" src=""/>
            </li>
            <li>
                <strong class="hd-name">动态码</strong>
                <input type="text" class="num-text" id="msg" name="msg" placeholder="请输入短信动态码" />
                <em class="get-code" id="resend" do-send="true">获取短信验证码</em>

            </li>
        </ul>
        </form>
    </div>
    <div class="error-txt" style="display: none"><i class="ico"></i><span class="errormsg"></span></div>
    <a class="start-info-btn" id="submit_btn" href="javascript:;">开始查询</a>
    <!-- 订单查询 -->
</div>
{php echo Common::js('common.js,jquery.validate.min.js');}
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
                $('.errormsg').html('手机号码格式不正确');
                $('.error-txt').show();
                return false;
            }
            if(bool==='true'){
                //发送验证码
                $.post(SITEURL+'member/order/ajax_send_message',{'mobile':mobile,'code':code},function(rs){
                    if(!rs.status){
                        $('.errormsg').html(rs.msg);
                        $('.error-txt').show();
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
                $('#resend').html(--v+'秒后重发');
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
            $('#queryfrm').submit();
        });
        //验证
        $('#queryfrm').validate({
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
            errorPlacement: function(error, element) {
                var content=$('.errormsg').html();
                if(content==''){
                    error.appendTo($('.errormsg'));
                }
            },
            showErrors:function(errorMap,errorList){
                if(errorList.length<1){
                    $('.errormsg').html('');
                    $('.error-txt').hide();

                }else{
                    this.defaultShowErrors();
                    $('.error-txt').show();
                }
            },
            submitHandler:function(form){
                var data=$("#queryfrm").serialize();
                $.post(SITEURL+'member/order/ajax_query',data,function(rs){
                    if(rs.url){
                        window.location.href=SITEURL+rs.url;
                    }else{
                        $.layer({
                            type:1,
                            icon:2,
                            text:rs.msg,
                            time:1000
                        })
                    }


                },'json');
                return false;
            }
        });
    });
</script>