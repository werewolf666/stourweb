<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title background_size=zSlDTk >实名认证-{$webname}</title>
    {include "pub/varname"}
    {Common::css('user.css,base.css')}
    {Common::js('jquery.min.js,base.js,common.js,jquery.validate.js,jquery.cookie.js,user-center-operate.js,jquery.validate.addcheck.js,jquery.upload.js')}
    {Common::js('layer/layer.js')}
</head>

<body>
    {request "pub/header"}

    <div class="big">
        <div class="wm-1200">

            <div class="st-guide">
                <a href="{$cmsurl}">首页</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;会员中心
            </div>
            <!--面包屑-->

            <div class="st-main-page">

                {include "member/left_menu"}
                <!-- 会员中心导航 -->

                <div class="user-cont-box">
                    <div class="real-name-container">
                        <div class="real-name-bar">实名认证</div>
                        <div class="real-name-wrapper">
                            <div class="real-name-step">
                                <ul class="step-item clearfix">
                                    <li class="item-child item-first step-on">
                                        <span class="speed">
                                            <i class="num-icon">1</i>
                                            <em class="txt-label">填写资料</em>
                                        </span>
                                    </li>
                                    <li class="item-child item-second {if $info['verifystatus']>0&&$is_update==0} step-on {/if}">
                                        <span class="speed">
                                            <i class="num-icon">2</i>
                                            <em class="txt-label">资料审核</em>
                                        </span>
                                    </li>
                                    <li class="item-child item-third {if $info['verifystatus']==2&&$is_update==0} step-on{/if} {if $info['verifystatus']==3&&$is_update==0} step-fail{/if}">
                                        <span class="speed">
                                            <i class="num-icon">3</i>
                                            <em class="txt-label">{if $info['verifystatus']==3}审核失败 {else} 审核通过{/if}</em>
                                        </span>
                                    </li>
                                </ul>
                            </div>

                            <!-- 认证进度 -->
                            <div class="rn-info-box">
                                {if $info['verifystatus']==0||$is_update==1}
                                <form id="sub_frm"  method="post" action="{$cmsurl}member/index/do_modify_idcard">
                                <ul class="rn-info-block">
                                    <li>
                                        <strong class="item-hd"><i class="star-icon">*</i>真实姓名：</strong>
                                        <div class="item-bd">
                                            <input class="default-text" type="text" name="truename" placeholder="请填写真实姓名" value="{$info['truename']}" />

                                        </div>
                                    </li>
                                    <li>
                                        <strong class="item-hd"><i class="star-icon">*</i>身份证号码：</strong>
                                        <div class="item-bd">
                                            <input class="default-text" type="text" name="cardid" placeholder="请填写身份证号码" value="{$info['cardid']}" />
                                        </div>
                                    </li>
                                    <li>
                                        <strong class="item-hd"><i class="star-icon">*</i>身份证正面：</strong>
                                        <div class="item-bd">
                                            <p class="update-txt">仅支持JPG，PNG、GIF格式图片，大小不超过2M，要求姓名和身份证号清晰可见</p>
                                            <div class="update-box">
                                                <span class="update-before-area" data-type="1">
                                                    {if $info['idcard_pic']['front_pic']}
                                                <img src="{Common::img($info['idcard_pic']['front_pic'],198,123)}" width="198" height="123" />

                                                    {else}
                                                    <i class="icon upbtn"></i>
                                                    <em class="sm">请上传身份证正面照片</em>
                                                    {/if}
                                                </span>
                                                {if $info['idcard_pic']['front_pic']}
                                                <a href="javascript:;" class="update-delete-btn">删除</a>
                                                {/if}
                                                <input class="pic_hidden" type="hidden" value="{$info['idcard_pic']['front_pic']}" id="front_pic" name="front_pic">
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <strong class="item-hd"><i class="star-icon">*</i>身份证背面：</strong>
                                        <div class="item-bd">
                                            <p class="update-txt">仅支持JPG，PNG、GIF格式图片，大小不超过2M，要求姓名和身份证号清晰可见</p>
                                            <div class="update-box">
                                               <span class="update-before-area" data-type="2">
                                                    {if $info['idcard_pic']['verso_pic']}
                                                <img src="{Common::img($info['idcard_pic']['verso_pic'],198,123)}" width="198" height="123" />

                                                    {else}
                                                    <i class="icon upbtn"></i>
                                                    <em class="sm">请上传身份证背面照片</em>
                                                    {/if}
                                                </span>
                                                {if $info['idcard_pic']['verso_pic']}
                                                <a href="javascript:;" class="update-delete-btn">删除</a>
                                                {/if}

                                                <input class="pic_hidden"  type="hidden" value="{$info['idcard_pic']['verso_pic']}" id="verso_pic" name="verso_pic">
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <strong class="item-hd"></strong>
                                        <div class="item-bd">
                                            <a class="submit-rz-btn" href="javascript:;">提交</a>
                                        </div>
                                    </li>
                                </ul>
                                </form>
                                {/if}
                                <!-- 填写资料 -->
                                {if $is_update==0}
                                {if $info['verifystatus']==1}
                                <div class="rn-load-block ">
                                    <i class="icon"></i>
                                    <p class="txt">资料审核中...</p>
                                </div>
                                {elseif $info['verifystatus']==2}
                                <!-- 审核中 -->
                                <div class="rn-pass-block ">
                                    <dl>
                                        <dt><i class="icon"></i>实名认证成功！</dt>
                                        <dd>
                                            <p>真实姓名：{$info['truename']}</p>
                                            <p>身份证号码：{substr_replace($info['cardid'],'********',3,11)}</p>
                                        </dd>
                                    </dl>
                                </div>
                                {elseif $info['verifystatus']==3}
                                <!-- 认证成功 -->
                                <div class="rn-fail-block ">
                                    <dl>
                                        <dt><i class="icon"></i>实名认证失败！</dt>
                                        <dd>
                                            <p class="txt">失败原因：证信息不符。</p>
                                            <a class="back-step-link" href="{$cmsurl}member/index/modify_idcard?is_update=1">重新填写</a>
                                        </dd>
                                    </dl>
                                </div>
                                {/if}
                                {/if}
                                <!-- 认证失败 -->
                            </div>
                            <!-- 认证需求 -->
                        </div>
                    </div>
                </div>
                <!--实名认证-->

            </div>

        </div>
    </div>


    {request "pub/footer"}


</body>
</html>
<script>
    $(function(){
        //上传图片
        $('.rn-info-block').on('click','.upbtn',function(){
            upload(this);
        });
        $('.rn-info-block').on('click','.update-delete-btn',function(){
            var obj = $(this).parent().parent().find('.update-before-area');
            if(obj.attr('data-type')==1)
            {
                var msg = '请上传身份证正面照片';
            }
            else if(obj.attr('data-type')==2)
            {
                var msg = '请上传身份证背面照片';
            }
            var html = ' <i class="icon upbtn"></i><em class="sm">'+msg+'</em>';
            $(this).parent().parent().find('.update-before-area').html(html);
            $(this).parent().find('.update-delete-btn').remove();
        });
        $("#nav_safecenter").addClass('on');


        $('.submit-rz-btn').click(function(){
            $('#sub_frm').submit();

        });

        $("#sub_frm").validate({
            ignore:"",
            rules: {
                'truename': {
                    required: true
                },
                'front_pic': {
                    required: true
                },
                'verso_pic': {
                    required: true
                },
                'cardid':{
                    required:true,
                    isIDCard:true
                }
            },
            messages: {
                'truename':{
                    required:'<span class="error-txt"><i class="ico"></i>请填写真实姓名</span>'
                },
                'verso_pic':{
                    required:'<span class="error-txt"><i class="ico"></i>请上传身份证背面照片</span>'
                },
                'front_pic':{
                    required:'<span class="error-txt"><i class="ico"></i>请上传身份证正面照片</span>'
                },
                'cardid':{
                    required:'<span class="error-txt"><i class="ico"></i>请填写身份证号码</span>',
                    isIDCard:'<span class="error-txt"><i class="ico"></i>请输入正确的身份证号码</span>'
                }

            },
            errorPlacement: function (error, element) {
                $(element).parent().append(error);
            },

            success: function (msg, element) {
                $(element).parent().find('.error-txt').remove();



            }
        });

    })


    //上传模板
    function upload(obj) {

        // 上传方法
        $.upload({
            // 上传地址
            url: SITEURL + 'member/index/ajax_upload_picture',
            // 文件域名字
            fileName: 'filedata',
            fileType: 'png,jpg,jpeg,gif',
            // 其他表单数据
            params: {},
            // 上传之前回调,return true表示可继续上传
            onSend: function () {
                return true;
            },
            // 上传之后回调
            onComplate: function (data) {
                var data = $.parseJSON(data);
                //如果上传成功
                if (data.status) {
                    var html = '<span class="update-after-area"><img src="'+data.litpic+'" width="198" height="123"></span>';
                    var parent_obj = $(obj).parent().parent();
                    $(obj).parent().html(html);
                    parent_obj.append('<a href="javascript:;" class="update-delete-btn">删除</a>');
                    parent_obj.find('.pic_hidden').val(data.litpic)

                }
            }
        });
    }


</script>
