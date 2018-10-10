<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途短信接口配置-思途CMS</title>
    <script type="text/javascript" src="/{$admindir}/public/js/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="/{$admindir}/public/js/common.js"></script>
    <script type="text/javascript" src="/{$admindir}/public/js/msgbox/msgbox.js"></script>
    <link type="text/css" href="/{$admindir}/public/js/msgbox/msgbox.css" rel="stylesheet" />
    <script>
        window.SITEURL = "{php echo URL::site();}";
        window.PUBLICURL = "{$GLOBALS['cfg_res_url']}";
        window.BASEHOST = "{$GLOBALS['cfg_basehost']}";
    </script>
    <link type="text/css" href="/{$admindir}/public/css/style.css" rel="stylesheet" />
    <link type="text/css" href="/{$admindir}/public/css/base.css" rel="stylesheet" />
    <link type="text/css" href="/{$admindir}/public/css/plist.css" rel="stylesheet" />
    <link type="text/css" href="/{$admindir}/public/css/sms_sms.css" rel="stylesheet" />
    <link type="text/css" href="/{$admindir}/public/css/sms_dialog.css" rel="stylesheet" />
    <link type="text/css" href="/{$admindir}/public/css/base_new.css" rel="stylesheet" />
    <script type="text/javascript" src="/{$admindir}/public/js/common.js"></script>
    <script type="text/javascript" src="/{$admindir}/public/js/config.js"></script>
    <script type="text/javascript" src="/{$admindir}/public/js/DatePicker/WdatePicker.js"></script>
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
                    <form id="configfrm">
                        <ul class="info-item-block">
                            <li>
                                <span class="item-hd">思途接口开关：</span>
                                <div class="item-bd">
                                    <label class="radio-label"><input type="radio" name="isopen" {if $provider['isopen']=='0'} checked {/if}  value="0" />关</label>
                                    <label class="radio-label"><input type="radio" name="isopen" {if $provider['isopen']=='1'} checked {/if}  value="1" />开</label>
                                </div>
                            </li>
                            <li>
                                <span class="item-hd">思途官网账号<img class="ml-5 va-m" title="查看帮助cfg_sms_username" style="cursor:pointer; margin-top: -3px" src="/{$admindir}/public/images/help-ico.png" onclick="helpBox(this,'cfg_sms_username',event)">：</span>
                                <div class="item-bd">
                                    <input class="input-text w300" type="text" name="cfg_sms_username" id="cfg_sms_username" value="{$cfg_sms_username}" />
                                </div>
                            </li>
                            <li>
                                <span class="item-hd">思途官网密码：</span>
                                <div class="item-bd">
                                    <input class="input-text w300" type="password" name="cfg_sms_password" id="cfg_sms_password" value="{$cfg_sms_password}" />
                                </div>
                            </li>
                        </ul>
                    </form>
                    <div class="clear clearfix">
                        <a href="javascript:;" id="sms_save_btn" class="btn btn-primary radius size-L mt-5 ml-115">保存</a>
                    </div>
                    <div class="sms-num mt-20">剩余短信<img class="ml-5 va-m" title="查看帮助plugins_stoursms_sms_account_balance" style="cursor:pointer; margin-top: -3px" src="/{$admindir}/public/images/help-ico.png" onclick="helpBox(this,'plugins_stoursms_sms_account_balance',event)"/>：<strong>{$balance}</strong>条</div>
                    <div class="sms-set">
                        <div class="msg-bar">
                            <span class="">购买短信</span>
                            <span class="">购买记录</span>
                            <span class="">使用记录</span>
                            <span class="">失败记录</span>
                        </div>
                        <div class="msg-switcher">
                            <div class="info-one clearfix">
                                <div class="s-main">
                                    <div class="set-con">
                                        <div class="msg-tc">
                                            
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
                            <div class="info-one clearfix">
                                <div class="s-main">
                                    <div class="search-con">
                                        <input type="text" class="input-text w200" name="querydate" onclick="WdatePicker({maxDate:'%y-%M-%d'})" />
                                        <span class="item-text ml-10">至今天</span>
                                        <a href="javascript:;" data_type="buylog" class="btn btn-primary radius ml-10">查询</a>
                                    </div>
                                    <div class="s-list">
                                        <table class="tb-list table table-bordered table-border">
                                            <tr>
                                                <th width="20%">时间</th>
                                                <th width="60%" align="left">订单号</th>
                                                <th width="10%">短信条数</th>
                                                <th width="10%">价格</th>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="info-one clearfix">
                                <div class="s-main">
                                    <div class="search-con">
                                        <input type="text" class="input-text w200" name="querydate" onclick="WdatePicker({maxDate:'%y-%M-%d'})" />
                                        <span class="item-text ml-10">至今天</span>
                                        <a href="javascript:;" data_type="uselog" class="btn btn-primary radius ml-10">查询</a>
                                    </div>
                                    <div class="s-list">
                                        <table class="table table-bordered table-border">
                                            <tr>
                                                <th width="20%" scope="col">
                                                    时间
                                                </th>
                                                <th width="60%" scope="col" align="left">
                                                    内容
                                                </th>
                                                <th width="10%" scope="col">
                                                    手机号码
                                                </th>
                                                <th width="10%" scope="col">
                                                    操作状态
                                                </th>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="info-one clearfix">
                                <div class="s-main">
                                    <div class="search-con">
                                        <input type="text" class="input-text w200" name="querydate" onclick="WdatePicker({maxDate:'%y-%M-%d'})" />
                                        <span class="item-text ml-10">至今天</span>
                                        <a href="javascript:;" data_type="faillog" class="btn btn-primary radius ml-10">查询</a>
                                    </div>
                                    <div class="s-list">
                                        <table class="table table-bordered table-border">
                                            <tr>
                                                <th width="20%" scope="col">
                                                    时间
                                                </th>
                                                <th width="38%" scope="col" align="left">
                                                    内容
                                                </th>
                                                <th width="22" scope="col">
                                                    错误原因
                                                </th>
                                                <th width="10%" scope="col">
                                                    手机号码
                                                </th>
                                                <th width="10%" scope="col">
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
    var provider_id = {$provider['id']};

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
        parent.parent.window.floatBox('帮助', '/{$admindir}/user/dialog_help?label=' + label, 600, 300);
    }

</script>
</body>
</html>
