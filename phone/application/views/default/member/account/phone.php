
   <div class="header_top bar-nav">
        <a class="back-link-icon" href="#myAccount" data-rel="back"></a>
        <h1 class="page-title-bar">绑定手机号码</h1>
    </div>
    <!-- 公用顶部 -->
    <div class="page-content">
        <form id="phonefrm" method="post" right_table=Gyvz8B >
        <div class="user-item-list">
            <ul class="list-group">
                <li>
                    <strong class="hd-name">手机号码</strong>
                    <input type="text" class="data-text text-left fr" id="phone" name="phone" placeholder="输入手机号码" value="{$info['mobile']}" />
                </li>

                <li>
                    <strong class="hd-name">短信验证码</strong>
                    <input type="text" class="num-text" name="checkcode" id="checkcode" placeholder="请输入短信验证码" />
                    <em class="get-code">获取短信验证码</em>
                </li>
            </ul>
        </div>
        <div class="error-txt" style="display: none" ><i class="ico"></i><span class="errormsg"></span></div>
        <a class="save-info-btn bind-phone" href="javascript:;">保存</a>
        <input type="hidden" name="token" value="{$token}"/>
        <!-- 绑定手机 -->
        </form>
    </div>
   {Common::js('jquery.validate.min.js')}
   <script>

       var is_can_send = 1;//是否可发送验证码
       $('#phonefrm').validate({
           rules: {
               phone: {
                   required: true,
                   mobile: true,
                   remote: {
                       url: SITEURL+'member/account/ajax_check_phone',
                       type: 'post'
                   }
               },

               checkcode: 'required'

           },
           messages: {
               phone: {
                   required: '{__("手机号不能为空")}',
                   mobile: '{__("手机号错误")}',
                   remote:'{__("手机号已经存在")}'
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
               var frmdata = $("#phonefrm").serialize();
               $.ajax({
                   type:'POST',
                   url:SITEURL+'member/account/ajax_phone_save',
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
       $(function(){

           $('.bind-phone').click(function(){
                $('#phonefrm').submit();
           })


           //发送短信验证码
           $('.get-code').click(function(){

                if(!is_can_send){
                    return false;
                }

               var mobile = $("#phone").val();
               var regPartton=/^1[3-8]+\d{9}$/;
               if (!regPartton.test(mobile))
               {
                   layer.open({
                       content: '请输入正确的手机号码'
                       ,time: 2 //2秒后自动关闭
                   });
                   return false;
               }

               var token = "{$token}";
               var url = SITEURL+'member/account/ajax_send_msgcode';
               $.post(url,{mobile:mobile,token:token},function(data) {
                   if(data.status)
                   {
                       code_timeout(60);
                       is_can_send = 0;
                       return false;
                   }
                   else
                   {
                       layer.open({
                           content: data.msg
                           ,time: 2 //2秒后自动关闭
                       });
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
