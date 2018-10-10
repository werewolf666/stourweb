<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title clear_left=52EwOs >思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js,listimageup.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
    {php echo Common::getCss('base.css,base_new.css,style.css'); }
    {php echo Common::getScript('jquery.validate.js');}
    {php echo Common::getScript("imageup.js"); }
    <style>
        .info-item-block{
            padding:0 !important;
        }
        .info-item-block .item-hd{
            width:60px !important;
        }
        .info-item-block .item-bd{
            padding-left:60px !important;
        }
    </style>
</head>
<body style="width:600px;height:435px;overflow: hidden" >
<div class="s-main">
    <form action="user/ajax_save" method="post" id="product_fm">
    <div class="basic-con">
        <ul class="info-item-block">
            <li>
                <span class="item-hd">用户名：</span>
                <div class="item-bd">
                    {if empty($info)}
                    <input class="input-text w200" id="username" name="username" value=""/>
                    <label class="item-text c-red ml-5">*</label>
                    {else}
                    <span class="item-text">{$info['username']}</span>
                    {/if}
                    <input type="hidden" name="id" value="{$info['id']}"/>
                </div>
            </li>
            <li>
                <span class="item-hd">密码：</span>
                <div class="item-bd">

                    <input class="input-text w200"    onfocus="this.type='password'"  id="password" type="text" name="password"/>
                    {if !$info['id']}
                    <label class="item-text c-red ml-5">*</label>
                    {/if}

                </div>
            </li>
            <li>
                <span class="item-hd">权限{Common::get_help_icon('admin_field_roleid',true)}：</span>
                <div class="item-bd">
                    <div class="select-box w200">
                        <select class="select" name="roleid" {if $info['roleid']==1} disabled  style="color:#b9b9b9" {/if}>
                            {loop $roles $role}
                              <option value="{$role['roleid']}" {if $info['roleid']==$role['roleid']}selected="selected"{/if}>{$role['rolename']}</option>
                            {/loop}
                        </select>
                    </div>
                </div>
            </li>
            <li>
                <span class="item-hd">备注：</span>
                <div class="item-bd">
                    <textarea class="textarea" name="beizu">{$info['beizu']}</textarea>
                </div>
            </li>
            <li>
                <span class="item-hd">头像：</span>
                <div class="item-bd">
                    <a id="pic_btn" class="btn btn-primary radius size-S mt-3">上传图片</a>
                    <div class="clearfix mt-10">
                            <img id="pic_upload" class=" up-img-area"  src="{$info['litpic']}"/>
                    </div>

                    <input id="hid_pic_upload" type="hidden" name="pic_upload">
                </div>
            </li>
        </ul>
    </div>
    <div class="clear clearfix text-c mt-25">
        <a href="javascript:;" class="btn btn-primary radius confirm-btn">确定</a>
    </div>
    </form>
</div>
<script>
    var id="{$info['id']}";
    var dialog = ST.Util.getDialog();
    jQuery.validator.addMethod("notblank", function(value, element) {
        var pwdblank = /^\S*$/;
        return this.optional(element) ||(pwdblank.test(value));
    }, "密码不可包含空格");
    //用户名必须需包含数字和大小写字母中至少两种
    jQuery.validator.addMethod("pwdrule", function(value, element) {
        var userblank = /^(?![0-9]+$)(?![a-z]+$)(?![A-Z]+$)[0-9A-Za-z]{6,16}$/;
        return this.optional(element) ||(userblank.test(value));
    }, "需包含数字和大小写字母中至少两种字符的6-16位字符");

    $(function() {


        ST.Util.resizeDialog('.s-main');
        $("#product_fm").validate({
            rules:{
                'username':{
                    required:true,
                    remote:{
                        type:"POST",
                        url:SITEURL+"user/ajax_checkuser", //请求地址
                        data:{
                            username:function(){ return $("#username").val(); }
                        }
                    }
                }
            },
            messages:
            {
                'username':{
                    required:'必填',
                    remote:'用户名已存在'
                },
                'password':{
                    required:'必填',

                }
            },
            submitHandler:function(form)
            {
                $.ajaxform({
                    url   :  SITEURL+"user/ajax_save",
                    method  :  "POST",
                    form  : "#product_fm",
                    dataType  :  "json",
                    success  :  function(data)
                    {
                        if(data.status)
                        {
                            ST.Util.showMsg('保存成功',4,1000);
                            setTimeout(function () {
                                ST.Util.responseDialog({},true);
                            },1000)


                        }
                        else
                        {
                            ST.Util.showMsg('保存错误',5);
                        }

                    }});

            }

        });

        $(document).on('click','.confirm-btn',function(){
                var password = $('#password').val();
                if(password)
                {
                    $("#password").rules("add",{required:true,notblank:true,pwdrule:true});
                }
              $("#product_fm").submit();
        });

        if(!id)
        {
            $("#password").rules("add",{required:true,notblank:true,pwdrule:true});
        }


        $('#pic_btn').click(function(){
            ST.Util.showBox('上传图片', SITEURL + 'image/insert_view', 0,0, null, null, parent.document, {loadWindow: window, loadCallback: Insert});
            function Insert(result,bool){
                var len=result.data.length;
                for(var i=0;i<len;i++){
                    var temp =result.data[i].split('$$');
                    $('#pic_upload').attr('src',temp[0]);
                    $('#hid_pic_upload').val(temp[0]);
                    $('#pic_upload').load(function () {
                        ST.Util.resizeDialog('.s-main')
                    })

                }
            }
        });

    });




</script>

</body>
</html>
