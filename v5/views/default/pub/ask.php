{Common::js('layer/layer.js')}
<div class="tabcon-list">
    <div class="list-tit"><strong>{__('我要咨询')}</strong></div>
    <div class="st-consult">
        <ul>
            {st:question action="query" productid="$info['id']" typeid="$typeid" return="quelist"}
               {loop $quelist $que}
                <li>
                    <dl class="ask">
                        <dt>{__('咨询问题')}</dt>
                        <dd>
                            <p class="bt">{$que['content']}</p>
                            <p class="name"><span>{$que['nickname']}</span><span>{Common::mydate('Y-m-d',$que['addtime'])}</span><span>{Common::mydate('H:i',$que['addtime'])}</span></p>
                        </dd>
                    </dl>
                    <dl class="answer">
                        <dt>{__('客服回复')}</dt>
                        <dd><p class="txt">{$que['replycontent']}</p></dd>
                    </dl>
                </li>
              {/loop}
            {/st}
        </ul>
        <div class="pagediv" style="text-align: center;display: none" data-quecount="{$row['plcount']}"  id="page"></div>
        <div class="st-tj-question">

            <textarea name="question" id="question" cols="" placeholder="{__('请填写你的问题')}" rows=""></textarea>
            <div class="msg">
                <a class="tj-btn" href="javascript:;" data-productid="{$info['id']}" data-typeid="{$typeid}">{__('提交')}</a>
                <span><em>{__('验证码')}：</em><input type="text" id="checkcode" style="padding-left: 5px" /><img src="{$cmsurl}captcha"  onClick="this.src=this.src+'?math='+ Math.random()" width="80" height="30" /></span>
                <span><em>{__('昵称')}：</em>
                    <span id="_c_u" style="line-height: 30px;height: 30px"></span>

                </span>
                <span><em>{__('手机号码')}：</em>
                    <span id="_c_m" style="line-height: 30px;height: 30px">

                    </span>

                </span>
            </div>
        </div>
    </div>
</div>

<script>
    //提交问答
    $(".tj-btn").click(function(){

        var question = $("#question").val();
        var checkcode = $("#checkcode").val();
        var productid = $(this).attr('data-productid');
        var typeid = $(this).attr('data-typeid');
        var nickname = $("#nickname").val();
        var mobile = $("#mobile").val();

        if(question.length<5){
            layer.alert('{__("question_empty")}', {
                icon: 5,
                title: '{__("notice")}'

            })
            return false;
        }
        var mobileReg=/^(\+?0?86\-?)?1[345789]\d{9}$/;
        if (mobile == '' || !mobileReg.test(mobile)) {
            var msg = mobile.length <1 ? '{__("手机号码不能为空")}' : '{__("请填写正确的手机号码")}';
            layer.alert(msg, {
                icon: 5,
                title: '{__("notice")}'

            });
            return false;
        }
        if(checkcode==''){
            layer.alert('{__("checkcode_empty")}', {
                icon: 5,
                title: '{__("notice")}'

            });
            return false;
        }

        $.ajax({
            type:'POST',
            url:SITEURL+'pub/ajax_add_question',
            data:{
                productid:productid,
                content:question,
                checkcode:checkcode,
                nickname:nickname,
                typeid:typeid,
                questype:0,
                mobile:mobile
            },
            success:function(data){
                if(data==1){
                    layer.alert('{__("checkcode_error")}', {
                        icon: 5,
                        title: '{__("notice")}'

                    });
                    //重新加载验证码
                    $("#imgcheckcode").attr('src',"{$cmsurl}captcha?"+Math.random());

                }else if(data==3){

                    layer.msg('{__("question_success")}',{
                        icon:6,
                        time:1500
                    });
                    location.reload();
                }else{
                    layer.alert('{__("question_failure")}', {
                        icon: 5,
                        title: '{__("notice")}'

                    });
                    //重新加载验证码
                    $("#imgcheckcode").attr('src',"{$cmsurl}captcha?"+Math.random());
                }

            }

        })


    })

    //登陆状态
    $.ajax({
        type:"POST",
        url:SITEURL+"member/login/ajax_is_login",
        dataType:'json',
        success:function(data){
            var mobile;
            if(data.status){
                mobile = '<input type="text" class="w100" value="'+data.user.mobile+'" name="mobile" id="mobile" />';
                $txt = '<span>'+data.user.nickname+'</span><input type="hidden" value="'+data.user.nickname+'" name="nickname" id="nickname" />';

            }else{
                mobile = '<input type="text" class="w100" value="" name="mobile" id="mobile" />';
                $txt = '<input type="text" name="nickname" id="nickname" /><a href="{$cmsurl}member/login/">{__("登录")}</a>';

            }
            $("#_c_u").html($txt);
            $("#_c_m").html(mobile);
        }

    })
</script>