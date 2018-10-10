<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}email平台</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,plist.css,sms_sms.css,base_new.css'); }
    {php echo Common::getScript('common.js,config.js,DatePicker/WdatePicker.js');}
</head>

<body>
<table class="content-tab">
    <tr>
        <td width="119px" class="content-lt-td"  valign="top">
            {template 'stourtravel/public/leftnav'}
            <!--右侧内容区-->
        </td>
        <td valign="top" class="content-rt-td">
            <!--面包屑-->
            <div class="list-top-set">
                <div class="list-web-pad"></div>
                <div class="list-web-ct">
                    <table class="list-head-tb">
                        <tbody>
                        <tr>
                            <td class="head-td-lt">

                            </td>
                            <td class="head-td-rt">
                                <a href="javascript:;" class="fr btn btn-primary radius mr-10" onclick="window.location.reload()">刷新</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <form id="msgfrm" table_bottom=5WIwOs >

                <ul class="info-item-block clearfix">
                    {loop $msg_config_data $msg_member_action $msg_config_value}
                    <li>
                        <span class="item-hd mt-10">
                            <div class="box-tit">{$msg_member_action}{Common::get_help_icon('noticemanager_member_email_'.$msg_config_value['msgtype'])}：</div>
                         </span>
                         <div class="item-bd pl-120">
                             <div class="tz-type lh-30 mt-10">
                                <label class="mr-10"><input type="radio" id="{$msg_config_value['msgtype']}_isopen" name="{$msg_config_value['msgtype']}_isopen" value="1" {if $msg_config_value['is_open']=='1'}checked="checked"{/if}/>开启</label>
                                <label><input type="radio" id="{$msg_config_value['msgtype']}_isopen" name="{$msg_config_value['msgtype']}_isopen" value="0" {if $msg_config_value['is_open']!='1'}checked="checked"{/if}/>关闭</label>
                             </div>
                            <div class="set-one w900">
                                <div class="box-con bk-gray">
                                    <textarea name="{$msg_config_value['msgtype']}" id="{$msg_config_value['msgtype']}">{$msg_config_value['templet']}</textarea>
                                </div>
                            </div>
                            <div class="set-one">
                                 <div class="set-one-tool">
                                     <div class="tool-bn">
                                         {if $n==1}
                                         <a href="javascript:;" class="short-cut" data="{#WEBNAME#}">网站名称</a>
                                         <a href="javascript:;" class="short-cut" data="{#EMAIL#}">邮箱</a>
                                         <a href="javascript:;" class="short-cut" data="{#PASSWORD#}">密码</a>
                                         {/if}
                                         {if $n==2}
                                         <a href="javascript:;" class="short-cut" data="{#WEBNAME#}">网站名称</a>
                                         <a href="javascript:;" class="short-cut" data="{#EMAIL#}">邮箱</a>
                                         <a href="javascript:;" class="short-cut" data="{#CODE#}">验证码</a>
                                         {/if}
                                         {if $n==3}
                                         <a href="javascript:;" class="short-cut" data="{#WEBNAME#}">网站名称</a>
                                         <a href="javascript:;" class="short-cut" data="{#EMAIL#}">邮箱</a>
                                         <a href="javascript:;" class="short-cut" data="{#CODE#}">验证码</a>
                                         {/if}
                                     </div>
                                 </div>
                            </div>
                         </div>
                    </li>
                    {/loop}
                </ul>
            </form>

            <a class="normal-btn btn btn-primary radius size-L ml-115" href="javascript:void(0)">保存</a>

        </td>
    </tr>
</table>

<script language="javascript">
    $(function(){
        $('.set-one .short-cut').click(function(){
            var ele=$(this).parents('.set-one').siblings('.set-one').find('.box-con textarea');
            var value=$(this).attr('data');
            ST.Util.insertContent(value,ele);
        });

        $(".normal-btn").click(function(){
            $.ajax({
                url:SITEURL+'noticemanager/ajax_save_email_msg',
                data: $('#msgfrm').serialize(),
                type: "POST",
                dataType:'json',
                success:function(data){
                    if(data.status){
                        ST.Util.showMsg('保存成功',4);
                    }
                    else {
                        ST.Util.showMsg('保存失败',5);
                    }
                }
            })
            return false;
        });

        //文本框的开启与关闭
        $(".tz-type input").click(function(){
            var thisVal=$(this).val();
            if(thisVal=="1"){
                $(this).parents('.tz-type').siblings('.set-one').find('.box-con').show();
            }else{
                $(this).parents('.tz-type').siblings('.set-one').find('.box-con').hide();
            }
        });
    })
</script>

</body>
</html>
