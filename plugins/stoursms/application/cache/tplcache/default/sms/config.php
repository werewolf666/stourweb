<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途短信接口配置-思途CMS</title>
    <script type="text/javascript" src="/<?php echo $admindir;?>/public/js/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="/<?php echo $admindir;?>/public/js/common.js"></script>
    <script type="text/javascript" src="/<?php echo $admindir;?>/public/js/msgbox/msgbox.js"></script>
    <link type="text/css" href="/<?php echo $admindir;?>/public/js/msgbox/msgbox.css" rel="stylesheet" />
    <script>
        window.SITEURL = "<?php echo URL::site();?>";
        window.PUBLICURL = "<?php echo $GLOBALS['cfg_res_url'];?>";
        window.BASEHOST = "<?php echo $GLOBALS['cfg_basehost'];?>";
    </script>
    <link type="text/css" href="/<?php echo $admindir;?>/public/css/style.css" rel="stylesheet" />
    <link type="text/css" href="/<?php echo $admindir;?>/public/css/base.css" rel="stylesheet" />
    <link type="text/css" href="/<?php echo $admindir;?>/public/css/plist.css" rel="stylesheet" />
    <link type="text/css" href="/<?php echo $admindir;?>/public/css/sms_sms.css" rel="stylesheet" />
    <link type="text/css" href="/<?php echo $admindir;?>/public/css/sms_dialog.css" rel="stylesheet" />
    <script type="text/javascript" src="/<?php echo $admindir;?>/public/js/common.js"></script>
    <script type="text/javascript" src="/<?php echo $admindir;?>/public/js/config.js"></script>
    <script type="text/javascript" src="/<?php echo $admindir;?>/public/js/DatePicker/WdatePicker.js"></script>
    <style>
        .s-main
        {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<table class="content-tab">
<tr>
<td valign="top" class="content-rt-td">
<div class="manage-nr">
<div class="sms-base sms-single">
    <div class="b-tit">
        短信接口配置
    </div>
    <div class="e-con">
        <form id="configfrm">
            <table>
                <tr>
                    <td>
                        思途接口开关：
                    </td>
                    <td>
                        <table style="border: 0;">
                            <tr>
                                <td><input type="radio" name="isopen" <?php if($provider['isopen']=='0') { ?> checked <?php } ?>
  value="0" />
                                </td>
                                <td>关</td>
                                <td style="width: 20px"></td>
                                <td><input type="radio" name="isopen" <?php if($provider['isopen']=='1') { ?> checked <?php } ?>
  value="1" />
                                </td>
                                <td>开</td>
                            </tr>
                        </table>
                    </td>
                    <td class="tb-hint">
                    </td>
                </tr>
                <tr>
                    <td>
                        思途官网账号<img class="ml-5 mt-3" title="查看帮助cfg_sms_username" style="cursor:pointer" src="/newtravel/public/images/help-ico.png" onclick="helpBox(this,'cfg_sms_username',event)">：
                    </td>
                    <td>
                        <input type="text" name="cfg_sms_username" id="cfg_sms_username" value="<?php echo $cfg_sms_username;?>" />
                    </td>
                    <td class="tb-hint">
                        购买思途短信时所使用的官网账号、密码
                    </td>
                </tr>
                <tr>
                    <td>
                        思途官网密码<img class="ml-5 mt-3" title="查看帮助cfg_sms_password" style="cursor:pointer" src="/newtravel/public/images/help-ico.png" onclick="helpBox(this,'cfg_sms_password',event)"/>：
                    </td>
                    <td>
                        <input type="password" name="cfg_sms_password" id="cfg_sms_password" value="<?php echo $cfg_sms_password;?>" />
                    </td>
                    <td class="tb-hint">
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <div class="e-ope">
        <a href="javascript:;" id="sms_save_btn" class="normal-btn">保存</a>
    </div>
</div>
<div class="sms-num">剩余短信：<strong><?php echo $balance;?></strong>条</div>
<div class="sms-set">
    <div class="msg-bar">
        <span class="">购买短信</span>
        <span class="">购买记录</span>
        <span class="">使用记录<img class="ml-5 mt-3" title="查看帮助plugins_stoursms_sms_use_log" style="cursor:pointer" src="/newtravel/public/images/help-ico.png" onclick="helpBox(this,'plugins_stoursms_sms_use_log',event)"/></span>
        <span class="">失败记录<img class="ml-5 mt-3" title="查看帮助plugins_stoursms_sms_fail_log" style="cursor:pointer" src="/newtravel/public/images/help-ico.png" onclick="helpBox(this,'plugins_stoursms_sms_fail_log',event)"/></span>
    </div>
    <div class="msg-switcher">
        <div class="info-one">
            <div class="s-main">
                <div class="set-con">
                    <div class="msg-tc">
                        <div class="tit" style="height: auto">
                            <ul>
                                <li class="bt">购买说明</li>
                                <li>-请使用思途官方帐号登陆,购买</li>
                                <li>-请按需购买,一旦购买成功费用不退</li>
                                <li>-如果在购买过程中有任何问题,请联系我们的客服.联系电话400-609-9927</li>
<li>-购买短信不予提供发票</li>
                                <li><a href="javascript:;"></a></li>
                            </ul>
                        </div>
                        <div class="con-list">
                            <dl>
                                <dt>A套餐</dt>
                                <dd>
                                    100条</dd>
                                <dd>
                                    10元</dd>
                                <dd class="bor-0">
                                    <a href="javascript:;" class="buybtn" data-suit="E">购买</a></dd>
                            </dl>
                            <dl>
                                <dt>B套餐</dt>
                                <dd>
                                    500条</dd>
                                <dd>
                                    45元</dd>
                                <dd class="bor-0 ">
                                    <a href="javascript:;" class="buybtn" data-suit="F">购买</a>
                                </dd>
                            </dl>
                            <dl>
                                <dt>C套餐</dt>
                                <dd>
                                    1000条</dd>
                                <dd>
                                    80元</dd>
                                <dd class="bor-0">
                                    <a href="javascript:;" class="buybtn" data-suit="G">购买</a></dd>
                            </dl>
                            <dl>
                                <dt>D套餐</dt>
                                <dd>
                                    5000条</dd>
                                <dd>
                                    350元</dd>
                                <dd class="bor-0">
                                    <a href="javascript:;" class="buybtn" data-suit="H">购买</a></dd>
                            </dl>
                            <dl>
                                <dt>E套餐</dt>
                                <dd>
                                    10000条</dd>
                                <dd>
                                    650元</dd>
                                <dd class="bor-0">
                                    <a href="javascript:;" class="buybtn" data-suit="I">购买</a></dd>
                            </dl>
                            <dl>
                                <dt>F套餐</dt>
                                <dd>
                                    50000条</dd>
                                <dd>
                                    3000元</dd>
                                <dd class="bor-0">
                                    <a href="javascript:;" class="buybtn" data-suit="J">购买</a></dd>
                            </dl>
                            <dl>
                                <dt>G套餐</dt>
                                <dd>
                                    100000条</dd>
                                <dd>
                                    5500元</dd>
                                <dd class="bor-0">
                                    <a href="javascript:;" class="buybtn" data-suit="K">购买</a></dd>
                            </dl>
                            <dl>
                                <dt>H套餐</dt>
                                <dd>
                                    500000条</dd>
                                <dd>
                                    25000元</dd>
                                <dd class="bor-0">
                                    <a href="javascript:;" class="buybtn" data-suit="L">购买</a></dd>
                            </dl>
                            <dl>
                                <dt>I套餐</dt>
                                <dd>
                                    1000000条</dd>
                                <dd>
                                    45000元</dd>
                                <dd class="bor-0">
                                    <a href="javascript:;" class="buybtn" data-suit="M">购买</a></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="info-one">
            <div class="s-main">
                <div class="search-con">
                    <input type="text" class="time-txt" name="querydate" onclick="WdatePicker({maxDate:'%y-%M-%d'})" /><span>至今天</span>
                    <a href="javascript:;" data_type="buylog" class="normal-btn">查询</a>
                </div>
                <div class="s-list">
                    <table class="tb-list" border="1px" bordercolor="#dcdcdc" cellspacing="0px" style="border-collapse: collapse">
                        <tr>
                            <th width="20%">
                                时间
                            </th>
                            <th width="60%" align="left">
                                订单号
                            </th>
                            <th width="10%">
                                短信条数
                            </th>
                            <th width="10%">
                                价格
                            </th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="info-one">
            <div class="s-main">
                <div class="search-con">
                    <input type="text" class="time-txt" name="querydate" onclick="WdatePicker({maxDate:'%y-%M-%d'})" /><span>至今天</span>
                    <a href="javascript:;" data_type="uselog" class="normal-btn">查询</a>
                </div>
                <div class="s-list">
                    <table class="tb-list" border="1px" bordercolor="#dcdcdc" cellspacing="0px" style="border-collapse: collapse">
                        <tr>
                            <th width="20%" height="40" scope="col">
                                时间
                            </th>
                            <th width="60%" height="40" scope="col" align="left">
                                内容
                            </th>
                            <th width="10%" height="40" scope="col">
                                手机号码
                            </th>
                            <th width="10%" height="40" scope="col">
                                操作状态
                            </th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="info-one">
            <div class="s-main">
                <div class="search-con">
                    <input type="text" class="time-txt" name="querydate" onclick="WdatePicker({maxDate:'%y-%M-%d'})" /><span>至今天</span>
                    <a href="javascript:;" data_type="faillog" class="normal-btn">查询</a>
                </div>
                <div class="s-list">
                    <table class="tb-list" border="1px" bordercolor="#dcdcdc" cellspacing="0px" style="border-collapse: collapse">
                        <tr>
                            <th width="20%" height="40" scope="col">
                                时间
                            </th>
                            <th width="38%" height="40" scope="col" align="left">
                                内容
                            </th>
                            <th width="22" scope="col">
                                错误原因
                            </th>
                            <th width="10%" height="40" scope="col">
                                手机号码
                            </th>
                            <th width="10%" height="40" scope="col">
                                操作状态
                            </th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</td>
</tr>
</table>
<script>
    var provider_id = <?php echo $provider['id'];?>;
    $(document).ready(function () {
        $(".msg-bar span").click(function () {
            var index = $(".msg-bar span").index(this);
            $(".msg-bar span.on").removeClass('on');
            $(this).addClass('on');
            $(".msg-switcher .info-one").hide();
            $(".msg-switcher .info-one:eq(" + index + ")").show();
        });
        $(".msg-bar span:first").trigger('click');
        $("#sms_save_btn").click(function () {
            var url = SITEURL + "sms/ajax_saveconfig?provider_id=" + provider_id;
            var frmdata = $("#configfrm").serialize();
            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                data: frmdata,
                success: function (data) {
                    if (data.status == true) {
                        ST.Util.showMsg('保存成功', 4);
                    }
                    else {
                        ST.Util.showMsg('保存失败', 5, 3000);
                    }
                }
            })
        });
        $(".buybtn").click(function () {
            var suit = $(this).attr('data-suit');
            var payurl = "";
            $.ajax({
                type: "post",
                data: {suittype: suit},
                url: SITEURL + "sms/buysms?provider_id=" + provider_id,
                async: false,
                dataType: 'json',
                success: function (data) {
                    if (data.status == 0) {
                        ST.Util.showMsg(data.msg, 5, 3000);
                    }
                    else if (data.status == 1) {
                        payurl = data.payurl
                    }
                }
            })
            if (payurl != '') {
                window.open(payurl);//支付页面
            }
        })
        $("a[data_type$='log']").click(function () {
            var data_type = $(this).attr("data_type");
            var querydate = $(this).siblings("input[name='querydate']").val();
            var table_result = $(this).parent().siblings(".s-list").find("table");
            table_result.find(".item").remove();
            var url = SITEURL + 'sms/ajax_query/querytype/' + data_type + '/querydate/' + querydate + "?provider_id=" + provider_id;
            if (querydate == "") {
                ST.Util.showMsg("请选择查询起始日期", 5, 3000);
                return;
            }
            ST.Util.showMsg('加载中...',6,60000);
            $.ajax({
                type: 'GET',
                url: url,
                dataType: 'json',
                success: function (data) {
                    ST.Util.hideMsgBox();
                    if (!data.Success) {
                        ST.Util.showMsg(data.msg, 5, 3000);
                        return;
                    }
                    if (data.Data.length <= 0) {
                        ST.Util.showMsg("没有查询到记录", 4);
                        return;
                    }
                    var html = '';
                    for (var i in data.Data) {
                        var row = data.Data[i];
                        if (data_type == "buylog") {
                            html += '<tr class="item"> <td align="center">' + row.ConsumeTime + '</td>' +
                                '<td class="msg-con">' + row.StourwebOrderNo + '</td>' +
                                '<td align="center">' + row.BuySMSNum + '</td>' +
                                '<td align="center">' + row.OrderMoney + '</td></tr>';
                        }
                        if (data_type == "uselog") {
                            html += '<tr class="item"> <td align="center">' + row.SendTime + '</td>' +
                                '<td class="msg-con">' + row.SendSMSContent + '</td>' +
                                '<td align="center">' + row.SendTelNo + '</td>' +
                                '<td align="center">' + row.SendStatus + '</td></tr>';
                        }
                        if (data_type == "faillog") {
                            html += '<tr class="item"> <td align="center">' + row.SendTime + '</td>' +
                                '<td class="msg-con">' + row.SendSMSContent + '</td>' +
                                '<td class="msg-con">' + row.Memo + '</td>' +
                                '<td align="center">' + row.SendTelNo + '</td>' +
                                '<td align="center">' + row.SendStatus + '</td></tr>';
                        }
                    }
                    table_result.append(html);
                }
            });
        });
    })
    function helpBox(ele, label, e)
    {
        if (e && e.stopPropagation) {
            e.stopPropagation();
        }
        else {
            window.event.cancelBubble = true;
        }
        parent.parent.window.floatBox('帮助', '/newtravel/user/dialog_help?label=' + label, 600, 300);
    }
</script>
</body>
</html>
