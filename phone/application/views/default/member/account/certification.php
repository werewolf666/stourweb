<div class="header_top bar-nav">
    <a class="back-link-icon" href="#myAccount" data-rel="back"></a>
    <h1 class="page-title-bar">实名认证</h1>
</div>
<!-- 公用顶部 -->

<div class="page-content">
    <div class="certification-on">
        {if $info['verifystatus']==3}
        <div class="msg-erro">
            <strong>认证失败：证信息不符!</strong>
            <i class="close"></i>
        </div>
        {/if}
        {if $info['verifystatus'] != 1}
        <form id="certification_frm">
            {if $info['verifystatus'] == 2}
            <div class="msg"><p>已认证</p></div>
            {else}
            <div class="msg"><p>请填写身份信息，通过后不能修改</p></div>
            {/if}
            <div class="content">
                <ul>
                    <li>
                        <div class="item">
                            <label class="lb">真实姓名</label>
                            <input class="txt input-text" type="text" id="truename" name="truename" {if $info['verifystatus']==2}readonly{/if} placeholder="请输入真实姓名" value="{if $info['verifystatus']==2 || $info['verifystatus']==3}{$info['truename']}{/if}" />
                            {if $info['verifystatus']==0 || $info['verifystatus']==3}
                            <i class="close clear hide"></i>
                            {/if}
                        </div>
                    </li>
                    <li>
                        <div class="item">
                            <label class="lb">身份证</label>
                            {if $info['cardid']}
                            {php $id_card = substr($info['cardid'],0,14) . '****';}
                            {else}
                            {php $id_card = '';}
                            {/if}
                            {if $info['verifystatus']==2}
                            <span class="txt">{$id_card}</span>
                            {else}
                            <input class="txt input-text" type="text" id="cardid" name="cardid" {if $info['verifystatus']==2}readonly{/if} placeholder="请输入身份证号码" value="{if $info['verifystatus']==2}{$id_card}{elseif $info['verifystatus']==3}{$info['cardid']}{/if}" />
                            {/if}
                            {if $info['verifystatus']==0 || $info['verifystatus']==3}
                            <i class="close clear hide"></i>
                            {/if}
                        </div>
                    </li>
                </ul>
            </div>
            <div class="card-onload">
                {if $info['verifystatus']==0 || $info['verifystatus']==3}
                <h3>请上传您的身份证正反面照片</h3>
                {else}
                <h3></h3>
                {/if}
                {if $info['verifystatus'] != 2}
                <div class="card-front">
                    {if $info['verifystatus']!=2}
                    <input type="file" class="file-upload" id="idcard_positive_file" />
                    <input type="hidden" id="idcard_positive" name="idcard_positive" />
                    <img src="{$cmsurl}/public/images/certification-front.png" width="100%" height="100%" alt="">
                    {else}
                    <img src="{$idcard_pic['front_pic']}" width="100%" height="100%" alt="">
                    {/if}
                </div>
                <div class="card-back">
                    {if $info['verifystatus']!=2}
                    <input type="file" class="file-upload" id="idcard_negative_file" />
                    <input type="hidden" id="idcard_negative" name="idcard_negative" />
                    <img src="{$cmsurl}/public/images/certification-back.png" width="100%" height="100%" alt="">
                    {else}
                    <img src="{$idcard_pic['verso_pic']}" width="100%" height="100%" alt="">
                    {/if}
                </div>
                {/if}
            </div>
            <div class="error-txt hide"><i class="ico"></i><span class="errormsg"></span></div>
            {if $info['verifystatus']!=2}
            <a class="card-btn" href="javascript:void(0)">确认</a>
            {/if}
        </form>
        {/if}
        {if $info['verifystatus']==1}
        <div class="certification-examine">
            <div class="pic"></div>
            <p>资料审核中...</p>
        </div>
        {/if}
    </div>
</div>
<script>
    var $certification_frm = $('#certification_frm');
    //表单验证
    $certification_frm.validate({
        ignore: [],
        rules: {
            truename: {
                required: true
            },
            cardid: {
                required: true,
                isIdCardNo:true
            },
            idcard_positive: {
                required: true,
            },
            idcard_negative: {
                required: true,
            }
        },
        messages: {
            truename: {
                required: '请填写真实姓名'
            },
            cardid: {
                required: '请填写身份证号',
                isIdCardNo:'身份证格式错误'
            },
            idcard_positive: {
                required: '请上传身份证正面照',
            },
            idcard_negative: {
                required: '请上传身份证反面照',
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
                $('.error-txt').addClass('hide');
            } else {
                this.defaultShowErrors();
                $('.error-txt').removeClass('hide');
            }
        },
        submitHandler: function (form) {
            var formData = $certification_frm.serialize();
            $.ajax({
                type:'POST',
                url:SITEURL+'member/account/ajax_certification_save',
                data:formData,
                dataType:'json',
                success:function(data){
                    if(data.status){
                        $.layer({
                            type:1,
                            icon:1,
                            text:'提交成功,等待审核',
                            time:2000
                        });
                        setTimeout(function(){
                            window.location.reload();
                        },1000);
                    }else{
                        $.layer({
                            type:1,
                            icon:2,
                            text:data.msg,
                            time:2000
                        });
                    }
                }
            });


        }
    });
    $(function () {
        //上传身份证照片
        $('.file-upload').change(function () {
            var $this = $(this);
            var $img_hide = $this.next();
            var $img = $this.parent().find('img');
            var fileList = $this.prop('files');
            var fileData = getFileInfo(fileList);
            $.ajax({
                url:SITEURL + 'member/account/ajax_upload_img',
                type:'POST',
                data:fileData,
                cache:false,
                processData:false,
                contentType:false,
                dataType:'json',
                beforeSend:function () {
                    $.layer({
                        type:4
                    });
                },
                success:function (data) {
                    if(data.success == 'true'){
                        $img.attr('src',data.litpic);
                        $img_hide.val(data.litpic);
                    }else{
                        $.layer({
                            type:2,
                            text:data.msg,
                            time:1000
                        });
                    }
                },
                complete:function () {
                    $.layer.close();
                }
        });
        });

        //提交实名认证信息
        $('.card-btn').click(function () {
            $certification_frm.submit();
        });

        //关闭错误提示
        $('.msg-erro').find('.close').click(function () {
            $('.msg-erro').addClass('hide');
        });

        //显示关闭按钮
        $('.input-text').keyup(function () {
            var $close = $(this).parent().find('.close');
            var text = $(this).val();
            if(text != ''){
                $close.removeClass('hide');
            }else{
                $close.addClass('hide');
            }
        });
        
        //清空当前行
        $('.clear').click(function () {
            $(this).parent().find('.input-text').val('');
            $(this).parent().find('.close').addClass('hide');
        });
    });

    //身份证图片信息获取
    function getFileInfo(fileList) {
        var data = new FormData();
        data.append('Filedata',fileList[0]);
        return data;
    }
</script>
