<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base_new.css'); }
    {php echo Common::getScript("jquery.validate.js,jquery.validate.addcheck.js"); }
    <style>
        .info-item-block{
            padding: 0;
        }
        .info-item-block>li .item-hd{
            width: 60px;
        }
        .info-item-block>li .item-bd{
            padding-left: 65px;
        }
    </style>
</head>
<body style="width: 320px; height: 243px; overflow: hidden">

    <form id="frm" name="frm">
        <ul class="info-item-block">
            <li class="list_dl">
                <span class="item-hd">手机号：</span>
                <div class="item-bd">
                    {if $action=='add'}
                    <input type="text" class="input-text" name="mobile" id="mobile" value="{$info['mobile']}" >
                    {else}
                    <input type="text" class="input-text" readonly disabled name="mobile" value="{$info['mobile']}" >
                    {/if}
                </div>
            </li>
            <li class="list_dl">
                <span class="item-hd">昵称：</span>
                <div class="item-bd">
                    <input type="text" class="input-text" datatype="require" errormsg="请输入昵称" name="nickname" value="{$info['nickname']}" >
                </div>
            </li>
            <li class="list_dl">
                <span class="item-hd">真实姓名：</span>
                <div class="item-bd">
                    <input type="text" class="input-text" name="truename" value="{$info['truename']}" >
                </div>
            </li>
            {if $action=='add'}
            <li class="list_dl">
                <span class="item-hd">密码：</span>
                <div class="item-bd">
                    <input type="text" class="input-text"   onfocus="this.type='password'" name="password" id="password" value="{$info['password']}" >
                </div>
            </li>
            {/if}
            <li class="list_dl">
                <span class="item-hd">邮箱：</span>
                <div class="item-bd">
                    <input type="text" class="input-text" name="email" id="email" value="{$info['email']}" >
                </div>
            </li>
        </ul>
        <div class="clearfix mt-15 text-c">
            <a class="btn btn-primary radius" id="btn_save" href="javascript:;">保存</a>
            <input type="hidden" id="mid" name="mid" value="{$info['mid']}">
            <input type="hidden" name="action" value="{$action}">
        </div>
    </form>

<script language="JavaScript">

    var action='{$action}';

    var is_allow =1 ;
    //表单验证
    $("#frm").validate({

        focusInvalid:false,
        rules: {
            mobile:
            {
                required: true,
                isMobile:true,
                minlength: 11,
                maxlength: 11,
                digits: true,
                remote:
                {
                    type:"POST",
                    url: SITEURL+'member/ajax_check/type/mobile/',
                    data:
                    {
                        val:function()
                        {return $("#mobile").val()
                        },
                        mid:function(){return $("#mid").val()}
                    }
                }
            },
            password: {
                required: true,
                rangelength: [6, 16]
            },
            email:{
                required:true,
                email:true,
                remote:
                {
                    type:"POST",
                    url: SITEURL+'member/ajax_check/type/email/',
                    data:
                    {
                        val:function()
                        {return $("#email").val()
                        }
                        ,
                        mid:function(){return $("#mid").val()}
                    }
                }
            }




        },
        messages: {

            mobile:{
                required:"请输入你的手机号码",
                minlength:"手机号码位数不正确",
                maxlength:"手机号码位数不正确",
                digits:"手机号码必须是数字",
                remote:"手机号码已经被注册"
            },

            password: {
                required:"请输入密码",
                rangelength:"密码长度为6-16位"
            },

            email:{
                required:"请输入email",
                email:"email输入错误",
                remote:"email地址重复"


            }

        },
        errUserFunc:function(element){

           console.log(element);
        },
        submitHandler:function(form){
            is_allow = 0 ;

            $.ajaxform({
                url   :  SITEURL+"member/ajax_save",
                method  :  "POST",
                form  : "#frm",
                dataType:'json',
                success  :  function(data)
                {
                    if(data.status)
                    {
                        var uid=$("#mid").val();
                        if(uid){
                            ST.Util.showMsg('修改成功!','4',2000);
                        }else{
                            ST.Util.showMsg('添加成功!','4',2000);
                            setTimeout(function(){
                                ST.Util.closeBox();
                            },2000)
                        }
                    }


                }});
            return false;//阻止常规提交


       }




    });

    $(function(){
        //保存
        $("#btn_save").click(function(){

            if(is_allow==1)
            {
                $("#frm").submit();

                return false;
            }
            else
            {
                ST.Util.showMsg('请勿重复提交!','5',2000);
            }



        })
    })

</script>

</body>
</html><script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.0.201710.2612&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
