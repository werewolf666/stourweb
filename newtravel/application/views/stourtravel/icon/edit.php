<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {Common::getCss('style.css,base.css,base_new.css')}
    {Common::getScript("jquery.validate.js")}
</head>

<body>

    <form id="frm" name="frm">
        <div class="out-box-con s-main">
            <ul class="info-item-block">
                <li>
                    <span class="item-hd">图标名称{Common::get_help_icon('icon_edit_kind',true)}：</span>
                    <div class="item-bd">
                         <input type="text" class="input-text w200" name="kind" id="kind" value="{$info['kind']}" >
                    </div>
                </li>
                <li>
                    <span class="item-hd">图片标识{Common::get_help_icon('icon_edit_picurl',true)}：</span>
                    <div class="item-bd">
                            <a href="javascript:;" id="file_upload" class="btn btn-primary radius size-S">上传图片</a>
                            <span class="item-text c-999 ml-5"> *建议尺寸33*15</span>
                            <div id="img" class="pt-15 pb-15">
                                {if !empty($info['picurl'])}
                                    <img class="up-img-area" id="litimg" src="{$info['picurl']}" />
                                {/if}
                            </div>
                    </div>
                </li>
            </ul>
            <div class="clearfix clear text-c">
                <a class="btn btn-primary radius w80" id="btn_save" href="javascript:;">确定</a>
                <input type="hidden" id="id" name="id" value="{$info['id']}">
                <input type="hidden" name="action" value="{$action}">
                <input type="hidden" name="litpic" id="litpic" value="{$info['picurl']}">
            </div>
        </div>
    </form>

<script language="JavaScript">

    var action='{$action}';
    //获取当前dialog
    var tdialog = ST.Util.getDialog();
    //表单验证
    $("#frm").validate({

        focusInvalid:false,
        rules: {
            kind:
            {
                required: true,
                maxlength:12

            }

        },
        messages: {

            kind:{
                required:"请输入图标名称",
                maxlength:"最长12个汉字"

            }
        },
        errUserFunc:function(element){


        },
        submitHandler:function(form){


            if($('#litpic').val() == ''){
                ST.Util.showMsg('请上传图片!','5',2000);
                return false;
            }

           $.ajax({
                url   :  SITEURL+"icon/ajax_save",
                type  :  "POST",
                dataType:'json',
                data  : $('#frm').serialize(),
                success  :  function(data)
                {
                    if(data.status)
                    {
                        $("#id").val(data.productid);
                        ST.Util.showMsg('保存成功!','4',1000);
                        setTimeout(function(){
                            tdialog.close();
                        },1500)


                    }


                }});
            return false;//阻止常规提交


       }





    });

    $(function(){

        ST.Util.resizeDialog('.s-main');
        //保存
        $("#btn_save").click(function(){


            $("#frm").submit();

            return false;

        })

        //上传图片
        $('#file_upload').click(function(){
            ST.Util.showBox('上传ico', SITEURL + 'image/insert_view', 0,0, null, null, parent.document, {loadWindow: window, loadCallback: Insert});
            function Insert(result,bool){

                var temp =result.data[0].split('$$');
                var src = temp[0];
                if(src){
                    var img = '<img id="litimg" class="up-img-area" src="'+src+'"  />';
                    $('#img').html(img);
                    $('#litpic').val(src);
                    ST.Util.resizeDialog('.s-main');

                }




            }
        })




    })

</script>

</body>
</html><script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.0713&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
