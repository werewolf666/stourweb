
    <style>
        .login-tips {
            padding-bottom: 50px;
        }
        .st-login-wp .login-tips .login-bind-tit {
            height: auto;
            padding-bottom: 10px;
        }

        .login-tips .tips {
            font-size: 12px;
            line-height: 13px;
        }

        .login-tips .tips em {
            color: #f00;
            padding: 0 5px;
        }
        .st-login-wp .login-bind-box{
            height:310px;
        }
    </style>
    <link type="text/css" href="/plugins/login_qq/public/css/user.css" rel="stylesheet"/>

    <div class="st-userlogin-box">
        <div class="st-login-wp">
            <div class="login-bind-box login-tips">
                <div class="login-bind-tit">绑定账号</div>
                <div class="login-account-key">
                    <ul>
                        <li class="number">
                            <span class="tb"></span>
                            <input type="text" class="np-box" id="account" placeholder="手机号/邮箱账号"/>
                        </li>
                        <li class="yzm">
                            <span class="tb"></span>
                            <input type="text" class="np-box" id="checkcode_img" style="width: 118px;" placeholder="请输入图片验证码"/>
                            <img class="send-yzm" src="/captcha" style="margin-top: 5px;width:100px;height:30px" onClick="this.src=this.src+'?math='+ Math.random()" />
                        </li>
                        <li class="yzm">
                            <span class="tb"></span>
                            <input type="text" class="np-box" id="checkcode" placeholder="请输入验证码"/>
                            <a class="send fr" href="javascript:;" id="send_checkcode" style="width:90px">发送验证码</a>
                        </li>
                        <li class="forget" style="height: 20px;">
                            <span class="user-error"></span>
                        </li>
                    </ul>
                    <div class="login-bind-xz">
                        <a class="confirm-btn" href="javascript:;" id="confirm_btn">确 定</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

  <script>
      var third = {
          openid: "<?php echo $user['openid'];?>",
          litpic: "<?php echo $user['litpic'];?>",
          nickname: "<?php echo $user['nickname'];?>",
          from: "<?php echo $user['from'];?>"
      }
      var user={};

      $(document).ready(function() {
          //账号格式检测
          $('#account').change(function () {
              check_account();
          });
          //密码检测
          $('#checkcode').change(function () {
              if (check_code()) {
                  $('.user-error').html('');
              }
          });

          //发送验证码
          $("#send_checkcode").click(function () {
              var account = $("#account").val();

              var waiting = $("#send_checkcode").attr('waiting');
              if (waiting == 1) {
                  return;
              }
              if (!check_account()) {
                  return;
              }

              $("#send_checkcode").attr('waiting',1);
              $.ajax({
                  url: '/plugins/login_qq/index/ajax_send_code',
                  type: 'POST', //GET
                  async: true,    //或false,是否异步
                  data: {account: account,checkcode_img:$("#checkcode_img").val()},
                  dataType: 'json',
                  success: function (data) {
                      if (data.Success) {
                          tick_send(60);
                      }
                      else {
                          $("#send_checkcode").removeAttr('waiting');
                          $('.user-error').html('<i></i>'+data.Message);
                      }
                  }
              })
          });

          //确定按钮
          $("#confirm_btn").click(function(){
              var status=true;
              status = check_account();
              if(!status)
              {
                  return;
              }
              status= check_code();
              if(!status)
              {
                  return;
              }
              $('.user-error').html('');

              var checkcode=$("#checkcode").val();
              var account = $("#account").val();
              $.ajax({
                  url:'/plugins/login_qq/index/ajax_both_save',
                  type:'POST', //GET
                  async:true,    //或false,是否异步
                  data:{
                      checkcode:checkcode,
                      account:account,
                      third:third
                  },
                  dataType:'json',
                  success:function(data){
                      if(data.bool)
                      {
                          window.location.href = data.url;
                      }
                      else
                      {
                          $('.user-error').html('<i></i>'+data.msg);
                      }
                  }
              })

          });
      });



      //验证账号
      function check_account()
      {
          var bool=false;
          var msg='';
          var val = $("#account").val();
          var valreg = /^\d+$/;
          if (valreg.test(val)) {
              var phone_reg=/^1[0-9]{10}$/;
              if(!phone_reg.test(val)){
                  msg = '手机号码格式不正确';
                  bool=false;
              }
              else
              {
                  bool=true;
              }
          } else {
              //邮件
              var reg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
              !reg.test(val) ? msg = '邮箱格式不正确' : bool = true;
          }
          if (!bool) {
              $('.user-error').html('<i></i>'+msg);
          }
          return bool;
      }

      //倒计时
      function tick_send(seconds)
      {
          if(seconds>0)
          {
              $("#send_checkcode").attr('seconds',seconds);
          }

          var left_seconds=$("#send_checkcode").attr('seconds');
          if(!left_seconds || left_seconds==0)
          {
              $("#send_checkcode").text("发送验证码");
              $("#send_checkcode").removeAttr('waiting');
              return;
          }
          else
          {
              $("#send_checkcode").text("请"+left_seconds+"秒后发送");
              $("#send_checkcode").attr('waiting',1);
          }
          setTimeout(function(){
              $("#send_checkcode").attr('seconds',left_seconds-1);
              tick_send();
          },1000)
      }

      //验证验证码
      function check_code()
      {
          var checkcode=$("#checkcode").val();
          var account=$("#account").val();
          var status = false;
          $.ajax({
              url:'/plugins/login_qq/index/ajax_check_code',
              type:'POST', //GET
              async:false,    //或false,是否异步
              data:{
                  checkcode:checkcode,
                  account:account
              },
              dataType:'json',
              success:function(data){
                  if(!data.status)
                  {
                      $('.user-error').html('<i></i>验证码错误!');
                  }
                  status= data.status;
              }
          })
          return status;
      }


  </script>

