<div class="header_top bar-nav">
    <a class="back-link-icon"  href="javascript:;" data-rel="back" ></a>
    <h1 class="page-title-bar">{$info['action']}常用旅客</h1>
</div>
<!-- 公用顶部 -->
<div class="page-content">
    <form id="linkmanfrm" padding_strong=PlMwOs >
    <div class="block-item">

            <ul class="linkman-info">
                <li>
                    <strong class="item-hd">姓名</strong>
                    <input type="text" class="write-info" name="linkman" value="{$info['linkman']}" placeholder="必填，请填写游客姓名">
                </li>
                <li style=" border-bottom: 0">
                    <strong class="item-hd">证件<i class="down-ico"></i></strong>
                    <select name="cardtype" id="cardtype" style="line-height: 1.28rem;height:1.28rem;border: 0">
                        <option value="身份证" {if $info['cardtype']=='身份证'}selected="selected"{/if}>身份证</option>
                        <option value="护照" {if $info['cardtype']=='护照'}selected="selected"{/if}>护照</option>
                        <option value="台胞证" {if $info['cardtype']=='台胞证'}selected="selected"{/if}>台胞证</option>
                        <option value="港澳通行证" {if $info['cardtype']=='港澳通行证'}selected="selected"{/if}>港澳通行证</option>
                        <option value="军官证" {if $info['cardtype']=='军官证'}selected="selected"{/if}>军官证</option>
                    </select>
                    <input type="text" class="write-info" name="idcard" id="idcard" value="{$info['idcard']}" placeholder="证件号码">
                </li>
            </ul>
            <input type="hidden" name="id" value="{$info['id']}"/>

    </div>
    </form>
    <div class="error-txt" style="display: none"><i class="ico"></i><span class="errormsg"></div>
    <a class="save-info-btn save-linkman" href="javascript:;">保存</a>
</div>
<script>
    $(function(){
        $('.save-linkman').click(function(){
            $('#linkmanfrm').submit();

        })

        $('#cardtype').change(function(){


            $('#idcard').rules("remove", 'isIDCard');
            if ($(this).val() == '身份证') {
                $('#idcard').rules('add', { isIDCard: true, messages: {isIDCard: "身份证号码格式不正确"}});
            }
        })
    })

    $('#linkmanfrm').validate({
        rules: {
            linkman: {
                required: true,
                maxlength:5

            },
            idcard: {
                required: true,
                isIDCard: true,
                alnum:true,
                maxlength:18
                {if empty($info['id'])}
                ,remote:{
                    url:SITEURL+"member/linkman/ajax_check",
                    type:"post",
                    dataType:"html",
                    dataFilter: function(data, type) {
                        if (data == "true")
                            return true;
                        else
                            return false;
                    }
                }
                {/if}


            }

        },
        messages: {
            linkman: {
                required: '{__("请填写姓名")}',
                maxlength:'{__("姓名过长")}'

            },
            idcard:{
                required:'{__("请输入证件号码")}',
                remote:'{__("证件号重复")}',
                isIDCard:'{__("身份证号码不正确")}',
                maxlength:'{__("证件号最大长度18位")}',
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
            var frmdata = $("#linkmanfrm").serialize();
            $.ajax({
                type:'POST',
                url:SITEURL+'member/linkman/update?action=save',
                data:frmdata,
                dataType:'json',
                success:function(data){
                    if(data.status){
                        $.layer({
                            type:1,
                            icon:1,
                            text:'保存成功',
                            time:1000
                        });
                        //返回上一页面并动态刷新

                        var url = '{$cmsurl}member#&myLinkman';
                        setTimeout(function(){
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
</script>
