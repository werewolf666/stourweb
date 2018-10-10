<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title clear_script=zyvz8B >思途CMS{$coreVersion}短信平台</title>
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
    <div class="cfg-header-bar">
        <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>                
    </div>

    <form id="msgfrm">

     {loop $status_arr $row}
    <div class="order-info-container" style="width:100%">
        <div class="order-info-bar">
            <strong class="bt-bar">{$row['name']}{Common::get_help_icon('message_order_'.$row['status'])}</strong>
        </div>
        <div class="notice-pen">
            <div class="set-one">
                <div class="set-one-box">
                    <div class="box-tit">通知会员</div>
                    <div class="box-con">
                        <textarea name="content[{$row['status']}]">{$row['content']}</textarea>
                    </div>
                </div>
                <div class="tz-type">
                	<label class="radio-label mr-20"><input type="radio"  name="isopen[{$row['status']}]" value="1" {if $row['isopen']=='1'}checked="checked"{/if}/>开启</label>
                    <label class="radio-label mr-20"><input type="radio"  name="isopen[{$row['status']}]" value="0" {if $row['isopen']!='1'}checked="checked"{/if}/>关闭</label>
                </div>
                <div class="set-one-tool">
                    <div class="tool-bn">
                        <a href="javascript:;" class="short-cut" data="{#MEMBERNAME#}">会员名称</a>
                        <a href="javascript:;" class="short-cut" data="{#WEBNAME#}">网站名称</a>
                        <a href="javascript:;" class="short-cut" data="{#PHONE#}">联系电话</a>
                        <a href="javascript:;" class="short-cut" data="{#PRODUCTNAME#}">产品名称</a>
                        <a href="javascript:;" class="short-cut" data="{#PRICE#}">单价</a>
                        <a href="javascript:;" class="short-cut" data="{#NUMBER#}">预订数量</a>
                        <a href="javascript:;" class="short-cut" data="{#TOTALPRICE#}">总价</a>
                        <a href="javascript:;" class="short-cut" data="{#ORDERSN#}">订单号</a>
                        {if $row['status']==2}
                        <a href="javascript:;" class="short-cut" data="{#ETICKETNO#}">电子票号</a>
                        {/if}
                        <a href="javascript:;" class="short-cut" data="{#USEDATE#}">开始时间</a>
                        <a href="javascript:;" class="short-cut" data="{#DEPARTDATE#}">结束时间</a>
                    </div>
                </div>
            </div>
        </div>
       </div>
      {/loop}
        <input type="hidden" name="typeid" value="{$typeid}"/>
    </form>

    <div class="mb-40">
        <a id="normal-btn" class="btn btn-primary radius size-L ml-20" href="javascript:void(0)">保存</a>
    </div>
</td>
</tr>
</table>

<script language="javascript">
    $(function(){
        $('.set-one .short-cut').click(function(){
            var ele=$(this).parents('.set-one:first').find('.box-con textarea');
            var value=$(this).attr('data');
            ST.Util.insertContent(value,ele);
        })

        $("#normal-btn").click(function(){
            $.ajaxform({
                url:SITEURL+'message/ajax_save_order',
                method:  "POST",
                form: "#msgfrm",
                dataType: "json",
                success:  function(result, opts)
                {
                    if(result.status)
                    {
                        ST.Util.showMsg('保存成功!','4',2000);
                    }
                }
            });
        })
    })
</script>

</body>
</html>
