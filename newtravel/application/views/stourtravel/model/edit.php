<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css'); }
    {php echo Common::getScript("jquery.validate.js"); }
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
   <style>
        .error{
            color:red;
            padding-left:5px;
        }
    </style>
</head>
<body style="background-color: #fff">
   <form id="frm" name="frm">
    <div class="out-box-con">
        <dl class="list_dl">
            <dt class="wid_90">模型名称{Common::get_help_icon('model_field_modulename')}：</dt>
            <dd>
               <input type="text" class="set-text-xh mt-4 text_200" name="modulename" id="modulename" value="" >
            </dd>
        </dl>
        <dl class="list_dl">
            <dt class="wid_90">模型拼音{Common::get_help_icon('model_field_pinyin')}：</dt>
            <dd><input type="text" class="set-text-xh text_200 mt-4"  name="pinyin" id="pinyin" value="" ></dd>
        </dl>
        <dl class="list_dl">
            <dt class="wid_90">&nbsp;</dt>
            <dd>
                <a class="normal-btn" id="btn_save" href="javascript:;">保存</a>
            </dd>
        </dl>
    </div>
   </form>

<script language="JavaScript">

    //字段验证
    jQuery.validator.addMethod("ispinyin", function(value, element) {
        var v = /^[a-z]{1}([a-z]){0,19}$/;
        return this.optional(element) || (v.test(value));
    }, "拼音不正确");
    jQuery.validator.addMethod("chinese", function(value, element) {
        var chinese = /^[\u4e00-\u9fa5]+$/;
        return this.optional(element) || (chinese.test(value));
    }, "只能输入中文");

    //表单验证
    $("#frm").validate({

        focusInvalid:false,
        rules: {
            modulename: {
                required: true,
                chinese: true,
                remote:
                {
                    type:"POST",
                    url:SITEURL+'model/ajax_modulename_check',
                    data:
                    {
                        pinyin:function()
                        {
                            return $("#modulename").val()
                        }

                    }
                }
            },
            pinyin:
            {
                required: true,
                minlength:1,
                maxlength:20,
                ispinyin:true,
                remote:
                {
                    type:"POST",
                    url:SITEURL+'model/ajax_pinyin_check',
                    data:
                    {
                        pinyin:function()
                        {
                            return $("#pinyin").val()
                        }

                    }
                }


            }



        },
        messages: {
            modulename: {
                required: "请填写模型名称",
                chinese: "只能输入中文",
                remote:'名称重复'

            },
            pinyin:{
                required:"请输入拼音",
                minlength:'长度须为1-20位',
                maxlength:'长度须为1-20位',
                ispinyin:'须小写英文字母',
                remote:'拼音重复'

            }


        },
        errUserFunc:function(element){


        },
        submitHandler:function(form){

            $.ajaxform({
                url   :  SITEURL+"model/ajax_model_save",
                method  :  "POST",
                isUpload :  true,
                form  : "#frm",
                dataType:'json',
                success  :  function(data)
                {

                    if(data.status)
                    {
                        ST.Util.showMsg('添加模型成功!','4',2000);
                        setTimeout(function(){
                            ST.Util.responseDialog(null,true);
                        },2000);
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

        })


    })

</script>

</body>
</html><script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=6.0.201707.2103&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
