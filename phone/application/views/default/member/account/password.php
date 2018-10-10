<div class="header_top bar-nav">
    <a class="back-link-icon" href="#myAccount" data-rel="back"></a>
    <h1 class="page-title-bar">修改登录密码</h1>
</div>
<!-- 公用顶部 -->
<div class="page-content">
    <form id="pwdfrm" method="post" clear_size=zSlDTk >
    <div class="user-item-list">
        <ul class="list-group">
            <li>
                <strong class="hd-name">原密码</strong>
                <input type="password" class="data-text" name="oldpwd" id="oldpwd" placeholder="输入原密码" />
            </li>
            <li>
                <strong class="hd-name">新密码</strong>
                <input type="password" class="data-text" name="newpwd1" id="newpwd1" placeholder="请输入新密码" />
            </li>
            <li>
                <strong class="hd-name">确认密码</strong>
                <input type="password" class="data-text" name="newpwd2" id="newpwd2" placeholder="再次输入新密码" />
            </li>
        </ul>
    </div>
    <div class="error-txt" style="display: none"><i class="ico"></i><span class="errormsg"></div>
    <a class="save-info-btn pwd-save" href="javascript:;">保存</a>
    <input type="hidden" name="token" value="{$token}"/>
    <!-- 绑定手机 -->
    </form>
</div>
{Common::js('jquery.validate.min.js')}
<script>
    $('#pwdfrm').validate({
        rules: {
            oldpwd: {
                required: true

            },
            newpwd1: {
                required: true,
                minlength: 6

            },
            newpwd2:{
                required: true,
                equalTo:'#newpwd1'
            }

        },
        messages: {
            oldpwd: {
                required: '{__("请输入旧密码")}'

            },
            newpwd1:{
                required:'{__("请输入新密码")}',
                minlength:'{__("新密码长度至少6位")}'
            },
            newpwd2:{
                required:'{__("请再次确认密码")}',
                equalTo:'新密码两次输入不一致'
            }


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
            var frmdata = $("#pwdfrm").serialize();
            $.ajax({
                type:'POST',
                url:SITEURL+'member/account/ajax_password_save',
                data:frmdata,
                dataType:'json',
                success:function(data){
                    if(data.status){
                        $.layer({
                            type:1,
                            icon:1,
                            text:'保存成功',
                            time:1000
                        })
                        setTimeout(function(){
                            var url = "{$cmsurl}member"+"#&myAccount";
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


        }
    });

    //保存密码
    $('.pwd-save').click(function(){
        $('#pwdfrm').submit();
    })

</script>