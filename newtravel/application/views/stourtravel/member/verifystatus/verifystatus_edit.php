<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>会员资料-{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,member-info.css,base_new.css'); }
    {php echo Common::getScript("choose.js"); }
    {php echo Common::getScript("jquery.upload.js"); }
    <style>
        .item-bd .sm-txt
        {
            color: #999;
            display: inline-block;
            padding-left: 20px;
            font-size: 14px;
            margin-top:6%;
        }

        .up-file-div .btn-file
        {
            color: rgb(255, 255, 255);
            width: 75px;
            height: 22px;
            line-height: 22px;
            float: left;
            margin-top:6%;
            text-align: right;
            padding-right: 5px;
            z-index: 9;
            cursor: pointer;
            background: url(/newtravel/public/images/base-ico.png) 0px -249px no-repeat;
        }
    </style>
</head>
<body>

<table class="content-tab">
    <tr>
        <td width="119px" class="content-lt-td" valign="top">
            {template 'stourtravel/public/leftnav'}
            <!--右侧内容区-->
        </td>
        <td valign="top" class="content-rt-td">

            <div class="cfg-header-bar">
                <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
            </div>
            {if $action=='show'}
            <div class="member-info-container">

                <div class="clear">
                    <ul class="info-item-block">
                        <li>
                            <span class="item-hd">昵称：</span>
                            <div class="item-bd">
                                <span class="member-name">{$info['nickname']}</span>
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">实名状态：</span>
                            <div class="item-bd">
                                <span class="member-name">
                                    {if $info['verifystatus']==0}未认证
                                    {elseif $info['verifystatus']==1}审核中
                                    {elseif $info['verifystatus']==2}已实名
                                    {elseif $info['verifystatus']==3}认证失败
                                    {/if}
                                </span>
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">真实姓名：</span>
                            <div class="item-bd">
                                <span class="member-name">{$info['truename']}</span>
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">身份证号：</span>
                            <div class="item-bd">
                                <span class="member-name">{$info['cardid']}</span>
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">身份证正面：</span>

                            <div class="item-bd">
                                {if $info['idcard_pic']['front_pic']}
                                <img style="cursor:pointer" src="{$info['idcard_pic']['front_pic']}"  onclick="show_big_pic('{$info['idcard_pic']['front_pic']}')" width="198" height="123" />
                                <span class="c-999 va-b ml-20">*点击查看大图，并可支持右键保存到本地</span>
                                {/if}
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">身份证背面：</span>
                            <div class="item-bd">
                                {if $info['idcard_pic']['verso_pic']}
                                <img style="cursor:pointer" src="{$info['idcard_pic']['verso_pic']}" onclick="show_big_pic('{$info['idcard_pic']['verso_pic']}')" width="198" height="123" />
                                <span class="c-999 va-b ml-20">*点击查看大图，并可支持右键保存到本地</span>
                                {/if}
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            {elseif $action=='modify'}
            <form id="frm">
            <div class="member-info-container">
                <div class="clear">
                    <ul class="info-item-block">
                        <li>
                            <span class="item-hd">会员ID：</span>
                            <div class="item-bd">
                                <span class="member-name">{$info['mid']}</span>
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">昵称：</span>
                            <div class="item-bd">
                                <span class="member-name">{$info['nickname']}</span>
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">当前实名状态：</span>
                            <div class="item-bd">
                                <select name="verifystatus" class="drop-down wid_100">
                                    <option value="0">未实名</option>
                                    <option value="1">审核中</option>
                                    <option value="2">已实名</option>
                                    <option value="3">审核失败</option>
                                </select>
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">真实姓名：</span>
                            <div class="item-bd">
                                <input type="text" class="default-text" name="truename" value="{$info['truename']}">
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">身份证号：</span>
                            <div class="item-bd">
                                <input type="text" class="default-text" name="cardid" value="{$info['cardid']}">
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">身份证正面：</span>
                            <div class="item-bd">
                                <div class="up-file-div">
                                    <div class="up-img">
                                        {if $info['idcard_pic']['front_pic']}
                                        <img style="cursor:pointer;float: left" src="{$info['idcard_pic']['front_pic']}"  onclick="show_big_pic('{$info['idcard_pic']['front_pic']}')" width="198" height="123" />
                                        <input type="hidden" value="{$info['idcard_pic']['front_pic']}" name="front_pic">
                                        {/if}
                                    </div>
                                    <div class="btn btn-primary radius size-S mt-10 ml-10" onclick="upload(this,'front_pic')">上传图片</div>
                                    <span class="item-text c-999 mt-10 ml-20">*点击查看大图，并可支持右键保存到本地</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">身份证背面：</span>
                            <div class="item-bd">
                                <div class="up-file-div">
                                    <div class="up-img">
                                        {if $info['idcard_pic']['verso_pic']}
                                        <img style="cursor:pointer;float: left" src="{$info['idcard_pic']['verso_pic']}" onclick="show_big_pic('{$info['idcard_pic']['verso_pic']}')" width="198" height="123" />
                                        <input type="hidden" value="{$info['idcard_pic']['verso_pic']}" name="verso_pic">
                                        {/if}
                                    </div>
                                    <div  class="btn btn-primary radius size-S mt-10 ml-10" onclick="upload(this,'verso_pic')">上传图片</div>
                                    <span class="item-text c-999 mt-10 ml-20">*点击查看大图，并可支持右键保存到本地</span>
                                </div>
                            </div>
                        </li>


                    </ul>
                </div>
            </div>
                <input name="mid" value="{$info['mid']}" type="hidden">
            </form>
            <div class="clear clearfix mt-20">
                <a class="btn btn-primary radius size-L ml-115" id="save_btn" href="javascript:;">保存</a>
            </div>
            {/if}

        </td>
    </tr>
</table>




</body>
</html>

<script>

    $(function(){
        $('#save_btn').click(function(){
             var url=SITEURL+"member/verifystatus_list/action/do_modify/parentkey/member/menuid/{$meunid}/itemid/1/mid/{$info['mid']}";
            $.ajax({
                type:'post',
                dataType:'json',
                data:$('#frm').serialize(),
                url:url,
                success:function(data)
                {
                    if(data.status)
                    {
                        ST.Util.showMsg('保存成功!',4,2000)
                    }
                    else
                    {
                        ST.Util.showMsg('保存失败!',5,2000)
                    }
                }

            })
        })

        var actoin = '{$action}';
        if(actoin=='modify')
        {
            var verifystatus = '{$info['verifystatus']}';
            $('select[name=verifystatus]').val(verifystatus);
        }


    });

    function show_big_pic(pic)
    {

        console.debug(pic);
        var url=pic;
        ST.Util.showBox('图片查看',url,1200,800,function(){window.product_store.load()});
    }

    //上传模板
    function upload(obj,input_name) {

        // 上传方法
        $.upload({
            // 上传地址
            url: SITEURL + 'member/ajax_upload_picture',
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
                    var html = '<img style="cursor:pointer;float: left" src="'+data.litpic+'" onclick="show_big_pic(\''+data.litpic+'\')" width="198" height="123" /> ' +
                        '<input type="hidden" value="'+data.litpic+'" name="'+input_name+'">';
                    $(obj).parent().find('.up-img').html(html);

                }
            }
        });
    }






</script><script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.0.201710.3105&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
