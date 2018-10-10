<!doctype html>
<html>
<head border_table=Iyvz8B >
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base_new.css'); }
    {php echo Common::getScript('config.js');}
</head>

<body>

    <table class="content-tab">
        <tr>
            <td width="119px" class="content-lt-td"  valign="top">
                {template 'stourtravel/public/leftnav'}
                <!--右侧内容区-->
            </td>
            <td valign="top" class="content-rt-td">
                <form id="configfrm">
                    <div class="cfg-header-bar">
                        <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                    </div>
                    <div class="currency_con clear">
                        <form id="configfrm">
                            <ul class="info-item-block">
                            <li>
                                <span class="item-hd">前台货币：</span>
                                <div class="item-bd">
                                    <span class="select-box w100">
                                        <select class="select" name="cfg_front_currencycode" id="front_code">
                                            {loop $list $val}
                                            <option value="{$val['code']}" {if $val['code']==$frontcode}selected="selected"{/if}>{$val['chinesename']}</option>
                                            {/loop}
                                        </select>
                                    </span>
                                    <span class="item-text c-999 ml-20">注：包括前台产品展示及结算的货币单位，后台积分抵现货币单位，保险货币单位</span>
                                </div>
                            </li>
                            <li>
                                <span class="item-hd">后台货币：</span>
                                <div class="item-bd">
                                    <span class="select-box w100">
                                        <select class="select" name="cfg_back_currencycode" id="back_code">
                                            {loop $list $val}
                                            <option value="{$val['code']}" {if $val['code']==$backcode}selected="selected"{/if}>{$val['chinesename']}</option>
                                            {/loop}
                                        </select>
                                    </span>
                                    <span class="item-text c-999 ml-20">注：仅限后台产品报价、单房间、定金支付所使用的货币单位</span>
                                </div>
                            </li>
                            <li>
                                <span class="item-hd">汇率比：</span>
                                <div class="item-bd" class="ratio">
                                    <input class="input-text w100 mr-5" type="text" name="front_ratio"  id="front_ratio"/>
                                    ：
                                    <input class="input-text w100 ml-5" type="text" name="back_ratio" id="back_ratio"/>
                                    <span class="item-text c-999 ml-20">注：即前台货币与后台货币的比值，例如1：6.2</span>
                                </div>
                            </li>
                            <li>
                                <span class="item-hd">前台货币小数位：</span>
                                <div class="item-bd" class="precise">
                                    <input class="input-text w100" type="text" name="cfg_front_currency_precise" id="precise" value="{$precise}"/>
                                    <span class="item-text c-999 ml-20">注：前台价格可保留小数位位数，请不要随意修改</span>
                                </div>
                            </li>
                            </ul>
                        </form>
                        <div class="clearfix">
                            <a class="btn btn-primary radius size-L ml-115" id="save-btn" href="javascript:;">保存</a>
                        </div>
                    </div>
                </form>
            </td>
        </tr>
    </table>



<script>


    $(document).ready(function(){
         getRate();

         $("#front_code,#back_code").change(function(){
             getRate();
         });

    });
    $('#save-btn').click(function(){
        var url = SITEURL+"currency/ajax_saveconfig";
        var frmdata = $("#configfrm").serialize();
        if(!checkForm())
           return;
        $.ajax({
            type:'POST',
            url:url,
            dataType:'json',
            data:frmdata,
            success:function(data){
                if(data.status==true)
                {
                    ST.Util.showMsg(data.msg,4);
                }
                else
                {
                    ST.Util.showMsg(data.msg,5);
                }
            }
        })
    })

    function checkForm()
    {
        var frontCode=$("#front_code").val();
        var backCode=$("#back_code").val();
        var frontRatio= $("#front_ratio").val();
        var backRatio=$("#back_ratio").val();
        var precise=$("#precise").val();

        if(precise&&isNaN(precise))
        {
            ST.Util.showMsg('前台货币小数位必须为整数',5);
            return false;
        }
        if(precise && Math.floor(precise) != precise && precise!=0)
        {
            ST.Util.showMsg('前台货币小数位必须为整数',5);
            return false;
        }

        if(frontCode==backCode)
            return true;
        if(isNaN(frontRatio)||isNaN(backRatio)||!frontRatio||!backRatio)
        {
            ST.Util.showMsg('汇率不能为空且只能是数字',5);
            return false;
        }
        return true;
    }
    function getRate()
    {
        var frontCode=$("#front_code").val();
        var backCode=$("#back_code").val();

        if(frontCode==backCode)
        {
            $("#front_ratio").val('');
            $("#back_ratio").val('');
            $("#front_ratio").attr("disabled",true);
            $("#back_ratio").attr("disabled",true);
            return;
        }
        else {
            $("#front_ratio").val('');
            $("#back_ratio").val('');
            $("#front_ratio").removeAttr("disabled")
            $("#back_ratio").removeAttr("disabled");
        }

        var url = SITEURL+"currency/ajax_getrate";
        var params={frontcode:frontCode,backcode:backCode};
        $.ajax({
            type:'POST',
            url:url,
            dataType:'json',
            data:params,
            success:function(result){
                if(result.data)
                {
                    $("#front_ratio").val(result.data[0]);
                    $("#back_ratio").val(result.data[1]);
                }
            }
        })

    }
</script>

</body>
</html>
