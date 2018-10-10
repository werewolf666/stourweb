<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title clear_left=jwFwOs >思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css'); }
    {php echo Common::getScript("jquery.validate.js"); }
</head>
<body style="background-color: #fff">
   <form id="frm" name="frm">
    <div class="out-box-con">

        <dl class="list_dl">
            <dt class="wid_90">数量：</dt>
            <dd><input type="text" class="set-text-xh text_200 mt-4" errormsg="请输入虚拟会员数量" name="num" value="" ></dd>
        </dl>
        <dl class="list_dl">
            <dt class="wid_90">&nbsp;</dt>
            <dd>
                <a class="default-btn wid_60" id="btn_save" href="javascript:;">保存</a>
            </dd>
        </dl>
    </div>
   </form>

<script language="JavaScript">

    var action='{$action}';
    //表单验证
    $("#frm").validate({

        focusInvalid:false,
        rules: {
            num:
            {
                required: true,
                minlength: 1,
                digits: true,
            },
        },
        messages: {

            num:{
                required:"请输入添加虚拟会员的数量",
                minlength:"请输入添加虚拟会员的数量",
                digits:"必须是数字",
                remote:"请输入添加虚拟会员的数量"
            },


        },
        errUserFunc:function(element){

           console.log(element);
        },
        submitHandler:function(form){

            $.ajaxform({
                url   :  SITEURL+"member/ajax_save_virtual",
                method  :  "POST",
                form  : "#frm",
                dataType:'json',
                success  :  function(data)
                {
                    if(data.status)
                    {
                        ST.Util.showMsg('添加成功!','4',2000);
                        setTimeout(function(){
                            ST.Util.closeBox();
                        },2000)
                    }
                }});
            return false;//阻止常规提交


       }




    });

    $(function(){
        //保存
        $("#btn_save").click(function(){


            $("#frm").submit();

            return false;

          /*  var mobile = $.trim($("#mobile").val());
            var email = $.trim($("#email").val());
            var pwd = $.trim($("#password").val());
            if(action == 'add'){

               if(mobile==''||pwd==''||email==''){

                    ST.Util.showMsg('请将信息填写完整',5);
                    return false;
               }


            }*/





        })
    })

</script>

</body>
</html>