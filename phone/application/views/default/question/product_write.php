<?php defined('SYSPATH') or die();?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>我要提问</title>
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('style-new.css')}
    {Common::js('lib-flexible.js')}
</head>

<body>

    <div class="header_top bar-nav">
        <a class="back-link-icon" href="javascript:;"></a>
        <h1 class="page-title-bar">我要提问</h1>
    </div>
    <!-- 公用顶部 -->
    <form id="questionfrm">
        <div class="faq-page-content">
            {if empty($member)}
                <div class="login-hint-txt">
                    温馨提示：<a class="login-link" href="{$cmsurl}member/login">登录</a>可享受预定送积分、积分抵现！
                </div>
            {/if}
            <!-- 温馨提示 -->
            <div class="faq-fb-block">
                <textarea class="faq-textarea" name="content" id="content" placeholder="请至少输入5个字"></textarea>
                <div class="faq-yzm">
                    <i class="ico"></i>
                    <input class="write-yzm" type="text" name="checkcode" id="checkcode" placeholder="请输入右侧图形验证码"  />
                    <em class="img-yzm"><img src="{$cmsurl}captcha" class="captcha" width="100%" height="100%" /></em>
                </div>
            </div>
        </div>

    <div class="faq-fix-bar">
        <label class="faq-anonymous"><i class="check-ico"></i>匿名提问</label>
        <a class="faq-submit-btn" href="javascript:;">提交</a>
    </div>
    <input type="hidden" id="is_anonymous" name="is_anonymous" value="0"/>
    <input type="hidden" id="articleid" name="articleid" value="{$articleid}"/>
    <input type="hidden" id="typeid" name="typeid" value="{$typeid}"/>
    <input type="hidden" id="token" name="token" value="{$token}"/>
    </form>
    {Common::js('jquery.min.js,jquery.layer.js,common.js')}
    <script>
        var SITEURL = '{URL::site()}';
        //设置body高度
        $("html,body").css("height","100%");

        $('.captcha').click(function(){
            $(this).attr('src',ST.captcha($(this).attr('src')));
        });

        $('.back-link-icon').click(function(){
            history.go(-1);
        })

        //匿名发表
        $(".faq-anonymous").on("click",function(){
            if( $(this).children("i").hasClass("active") ) {
                $(this).children("i").removeClass("active");
                $('#is_anonymous').val(0);
            }
            else{
                $(this).children("i").addClass("active");
                $('#is_anonymous').val(1);
            }
        })

        $('.faq-submit-btn').click(function(){
            var content = $('#content').val();
            if(ST.Tools.get_length(content)<10){
                $.layer({
                    type:2,
                    text:'提问内容至少5个汉字',
                    time:1000
                })
                return false;

            }
            var checkcode = $('#checkcode').val();
            if(checkcode == ''){
                $.layer({
                    type:2,
                    text:'请填写验证码',
                    time:1000
                })
                return false;
            }

            $.ajax({
                type:'POST',
                data:$('#questionfrm').serialize(),
                url:SITEURL+'question/ajax_product_question_save',
                dataType:'json',
                success:function(data){
                    if(data.status){
                        $.layer({
                            type:1,
                            icon:1,
                            text:'提问成功!',
                            time:10000
                        })
                        setTimeout(function(){
                            var url = '{$cmsurl}question/product_question_list?articleid={$articleid}&typeid={$typeid}';
                            window.location.href = url;
                        },1000)

                    }else{
                        $.layer({
                            type:1,
                            icon:2,
                            text:data.msg,
                            time:1000
                        })
                    }
                }
            })

        })

    </script>

</body>
</html>