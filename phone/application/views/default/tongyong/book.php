<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{$info['title']}预订-{$webname}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('style-new.css,mobilebone.css')}
    {Common::js('jquery.min.js,lib-flexible.js,swiper.min.js,mobilebone.js,delayLoading.min.js,jquery.layer.js,validate.js')}
    {Common::js('jquery.validate.min.js,template.js,validate.js')}
    <link type="text/css" rel="stylesheet" href="{$cmsurl}public/mui/css/mui.picker.css"/>
    <link type="text/css" rel="stylesheet" href="{$cmsurl}public/mui/css/mui.poppicker.css"/>
    <script src="{$cmsurl}public/mui/js/mui.min.js"></script>
    <script src="{$cmsurl}public/mui/js/mui.picker.js"></script>
    <script src="{$cmsurl}public/mui/js/mui.poppicker.js"></script>
    <script src="{$cmsurl}public/mui/js/city.data-3.js" type="text/javascript" charset="utf-8"></script>
</head>

<body>

<div class="page out" id="pageHome">
    <header>
        <div class="header_top">
            <a class="back-link-icon" href="javascript:;" onclick="history.go(-1)"></a>

            <h1 class="page-title-bar">订单填写</h1>
        </div>
    </header>
    <!-- 公用顶部 -->
    <div class="page-content">
        <section>
            <div class="wrap-content">
                <form action="{$cmsurl}{$pinyin}/create" id="orderfrm" method="post">
                    {if empty($userinfo['mid'])}
                    <div class="login-hint-txt">
                        温馨提示：<a class="login-link" href="/phone/member/login" data-ajax="false">登录</a>可享受预定送积分、积分抵现！
                    </div>
                    {/if}
                    <!-- 温馨提示 -->
                    <div class="booking-info-block clearfix">
                        <h3 class="block-tit-bar"><strong>预定信息</strong></h3>

                        <div class="name-block">
                            <strong class="bt">产品名称</strong>

                            <p class="txt">{$info['title']}</p>
                        </div>
                        <div class="block-item">
                            <ul>
                                <li>
                                    <strong class="item-hd">产品套餐</strong>
                                    <span class="more-type" id="suit_btn"><span class="suitName"></span><i
                                            class="more-ico"></i></span>
                                </li>
                                <li>
                                    <a class="all" href="#choose_date" id="selectUseDate">
                                        <strong class="item-hd">出发日期</strong>
                                        <span class="more-type date-type"><i id="useDate"></i><input type="hidden" id="filed_usedate" name="usedate" value=""><iclass="more-ico"></i></span>
                                    </a>
                                </li>
                                <li>
                                    <strong class="item-hd">预订数量</strong>
                                    <span class="item-jg">{Currency_Tool::symbol()}<i class="unitPrice">0</i></span>
                                    <span class="amount-opt-wrap">
                                        <a href="javascript:;" class="sub-btn">–</a>
                                        <input type="text" name="dingnum" class="num-text" maxlength="4" value="1">
                                        <a href="javascript:;" class="add-btn">+</a>
                                </span>
                                </li>
                            </ul>
                        </div>
                        <div class="foo-box hide" id="suit_list">
                            <div class="tc-container">
                                <div class="tc-tit-bar"><strong class="bt">选择套餐</strong><i class="close-icon"
                                                                                           id="closeSuit"></i></div>
                                <div class="tc-wrapper">
                                    <ul>
                                        {st:tongyong action="suit" productid="$info['id']" row="100" return="suits"}
                                        {loop $suits $v}
                                        {php}if($v['maxnumber']==0){continue;}{/php}
                                        <li {if $v['id']==$suit['id']}class="active"{/if}suit="{id:{$v['id']},suitename:'{$v['suitname']}',day:'{$v['day']}',price:{$v['price']},maxnumber:{$v['maxnumber']},'jifentprice':'{$v['jifentprice']}',paytype:'{$v['paytype']}',dingjin:'{$v['dingjin']}'}"><em
                                            class="item">{$v['suitname']}</em><i class="radio-btn"></i></li>
                                        {/loop}
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- 选择套餐 -->
                    </div>
                    <!-- 预定信息 -->

                    <div class="booking-info-block clearfix">
                        <h3 class="block-tit-bar"><strong>订单联系人</strong></h3>

                        <div class="block-item">
                            <ul>
                                <li>
                                    <strong class="item-hd">联系人：</strong>
                                    <input type="text" name="linkman" id="linkman" class="write-info"
                                           placeholder="请填写真实姓名" value="{$member['nickname']}"/>
                                    <span class="nd">(必填)</span>
                                </li>
                                <li>
                                    <strong class="item-hd">手机号码：</strong>
                                    <input type="text" name="linktel" id="linktel" class="write-info"
                                           placeholder="请输入常用手机号码" value="{$member['mobile']}"/>
                                    <span class="nd">(必填)</span>
                                </li>
                            </ul>
                        </div>
                        <div class="block-remarks">
                            <strong class="item-hd">订单备注：</strong>
                            <textarea name="remark" class="item-txt"></textarea>
                        </div>
                    </div>
                    <!-- 订单联系人 -->
                    {if $userinfo['mid']}
                        {request "member/receive/select"}
                        <!--收货地址-->
                        {include "pub/discount"}
                        <!--营销策略-->
                    {/if}
                    <div class="booking-info-block clearfix">
                        <div class="block-item">
                            <ul>
                                <li>
                                    <strong class="item-hd">支付方式</strong>
                                    <span class="more-type payType"></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- 支付方式 -->
                    <!-- 优惠政策 -->
                    <input type="hidden" name="productid" id="productid" value="{$info['id']}"/>
                    <input type="hidden" name="suitid" id="suitid" value=""/>
                    <input type="hidden" name="typeid" value="{$typeid}"/>
                    <!--附加数据信息-->
                    <i class="hide" id="extParam">
                        <!--是否需要收货地址-->
                        <input type="hidden" name="needaddress" id="needAddress" value="0">
                    </i>
                    {St_Product::form_token()}
                </form>
            </div>


        </section>

        <div class="bom-fixed-content">
            <div class="bom-fixed-block">
                    <span class="total">
                        <em class="jg">应付总额：{Currency_Tool::symbol()}<i class="payPrice">0</i></em>
                    </span>
                <span class="order-show-list" id="order-show-list">明细<i class="arrow-up-ico"></i></span>
                <a class="now-booking-btn" href="javascript:;">立即预定</a>
            </div>
        </div>
        <!-- 立即预定 -->

        <div class="fee-box hide" id="fee-box">
            <div class="fee-container">
                <div class="fee-row">
                    <p class="ze">
                        <strong>应付总额</strong>
                        <em class="fr">{Currency_Tool::symbol()}<i class="payPrice">0</i></em>
                    </p>
                    <p class="sm"><i class="payType"></i> {Currency_Tool::symbol()}<i class="payPrice">0</i></p>
                </div>
                <ul class="mx-list">
                    <li id="suit_info" style="display: none">
                        <strong><span class="suitName">{$suit['suitname']}</span>（<i class="bookNumber">{$info['bookNumber']}</i>）</strong>
                        <em>{Currency_Tool::symbol()}<i class="totalPrice">0</i> </em>
                    </li>
                </ul>
            </div>
        </div>
        <!-- 费用明细 -->
    </div>
</div>
<div class="page out" id="choose_date">
    <div class="header_top bar-nav">
        <a class="back-link-icon" href="#pageHome" data-rel="auto"></a>
        <h1 class="page-title-bar">选择日期</h1>
    </div>
    <!-- 公用顶部 -->
    <div class="page-content full-page">
        <div class="calendar-container">
        </div>
        <!-- 选择日期 -->
    </div>
</div>
<script>
    Mobilebone.evalScript = true;
    var currency='{Currency_Tool::symbol()}';
    var SITEURL = '{$cmsurl}';
    //预订信息
    var bookData = {
        id: {$info['id']}, //产品编号
        number: 1, //预订数量
        suit: {}, //所选套餐
        selectAddress: 0,
        payPrice:0, //支付总额
        totalPrice:0, //订单总额
        discount:{}, //优惠信息
        extParam:{} //表单附加参数
    };
    bookData.checkCanUse=function($key){
        var price = 0;
        for (var k in this.discount) {
            if (k != $key) {
                price += this.discount[k];
            }
        }
        return this.payPrice-price;
    };
    //消息队列
    var queue = {messages: []};
    queue.on = function (eventType, message) {
        if (!(eventType in this.messages)) {
            this.messages[eventType] = [];
        }
        this.messages[eventType].push(message);
        return this;
    };
    queue.emit = function (eventType) {
        var params = Array.prototype.slice.call(arguments, 1);
        if(!this.messages[eventType] || this.messages[eventType].length<1){
            return this;
        }
        for (var i = 0; this.messages[eventType][i]; i++) {
            this.messages[eventType][i].apply(this, params)
        }
        return this;
    };
    //订阅营销策略显示状态
    queue.on('discountStatus', function () {
        var payType;
        bookData.suit.paytype=parseInt(bookData.suit.paytype);
        switch (bookData.suit.paytype) {
            case 1:
                payType = '全款支付';
                break;
            case 2:
                payType = '定金支付';
                break;
            case 3:
                payType = '二次确认支付';
                break;
        }
        //更新优化信息
        bookData.discount={};
        $('.mx-list').find('.integralNode,.couponNode').remove();
        if (bookData.suit.paytype == 2) {
            $('#discountNode').hide();
        }
        else
        {
            $('#discountNode').show();
        }
        $('.payType').text(payType);
    });
    //订阅总价计算
    queue.on('totalPrice', function () {
        var price, payPrice;
        var payType = bookData.suit.paytype;
        price = parseInt(bookData.suit.price);
        if(!price){
            price=0;
        }
        bookData.totalPrice = price * bookData.number;
        if (payType == 2) {
            //定金支付
            bookData.payPrice = bookData.suit.dingjin * bookData.number;
            payPrice=bookData.payPrice;
        }
        else {
            //非定金支付
            bookData.payPrice = bookData.totalPrice;
            payPrice = bookData.payPrice;
            for (var k in bookData.discount) {
                payPrice -= bookData.discount[k];
            }
        }
        $('.payPrice').text(payPrice);
        $('.totalPrice').text(bookData.totalPrice);
    });
    //显示套餐选择框
    $("#suit_btn").click(function () {
        $("#suit_list").show();
    });
    //关闭套餐选择框
    $('#closeSuit').click(function () {
        $('#suit_list').hide();
    });
    //选择套餐日期
    $('#selectUseDate').click(function(){
        if(!bookData.suit.id){
            $.layer({type: 1, icon: 2, time: 1000, text: '请选择产品类型'});
            return;
        }
        var url=SITEURL+'pub/ajax_calendar?typeid={$info['typeid']}&suitid='+bookData.suit.id+'&productid={$info['id']}&new_version=1';
        $.ajax({
            type: 'GET',
            url: url,
            data: {},
            dataType: 'html',
            success: function (data) {
                $(".calendar-container").html(data);
            }
        });
    });
    //套餐选择
    $("#suit_list .tc-wrapper ul li").click(function () {
        $(this).addClass('active').siblings('li').removeClass('active');
        //套餐信息注入全局变量
        bookData.suit = eval('(' + $(this).attr('suit') + ')');
        //营销策略
        queue.emit('discountStatus');
        queue.emit('resetDiscount');
        //计算总价

        if(bookData.suit.id){
            $('#suit_info').css('display','block');
        }
        //套餐名称
        $('.suitName').text(bookData.suit.suitename);
        $('.unitPrice').text(bookData.suit.price);
        $('#useDate').text(bookData.suit.day);
        $('#filed_usedate').val(bookData.suit.day);
        $("#suit_list").hide();
        queue.emit('totalPrice');
    });
    //初始化
    $(function(){
        !function init() {
            $("#suit_list li:first").click();
        }();
        //预订数量
        $('.sub-btn').click(function () {
            var num = $('.num-text').val();
            if (num > 0) {
                num--;
                $('.num-text').val(num);
                $('.bookNumber').text(num);
                bookData.number = num;
                queue.emit('totalPrice');
            }

        });
        $('.add-btn').click(function () {
            var num = $('.num-text').val();
            num++;
            if (bookData.suit.maxnumber > 0 && num > bookData.suit.maxnumber) {
                $.layer({type: 1, icon: 2, time: 1000, text: '该套餐最大预定量为' + bookData.suit.maxnumber});
                return false;
            }
            $('.num-text').val(num);
            $('.bookNumber').text(num);
            bookData.number = num;
            queue.emit('totalPrice');
        });
        $('.num-text').change(function () {
            var num = parseInt($(this).val());
            if (isNaN(num)) {
                num = 0;
            }
            if (bookData.suit.maxnumber > 0 && num > bookData.suit.maxnumber) {
                $.layer({type: 1, icon: 2, time: 1000, text: '该套餐最大预定量为' + bookData.suit.maxnumber});
                num = bookData.suit.maxnumber;
            }
            bookData.number = num;
            $(this).val(num);
            $('.bookNumber').text(num);
            queue.emit('totalPrice');
        });
        //选择框
        $(".check-box").on("click", function () {
            var attr = $(this).attr('name');
            if (!$(this).hasClass("on")) {
                $(this).addClass("on");
                if (attr) {
                    bookData[attr] = 1;
                    $('#' + attr).removeClass('hide');
                }
            }
            else {
                $(this).removeClass("on");
                if (attr) {
                    bookData[attr] = 0;
                    $('#' + attr).addClass('hide');
                }
            }
        });
        //提交表单
        $(".now-booking-btn").click(function () {
            var check_status = function () {
                var linkman = $("#linkman").val();
                var linkTel = $("#linktel").val();
                //套餐
                if (!bookData.suit.id) {
                    $.layer({type: 1, icon: 2, time: 1000, text: '请选择产品套餐'});
                    return false;
                }
                $('#suitid').val(bookData.suit.id);
                //预订数量
                if (!bookData.number || bookData.number < 1) {
                    $.layer({type: 1, icon: 2, time: 1000, text: '预订数量不能为0'});
                    return false;
                }
                //联系人
                if (!linkman) {
                    $.layer({type: 1, icon: 2, time: 1000, text: '联系人不能为空'});
                    return false;
                }
                if (!Validate.mobile(linkTel)) {
                    $.layer({type: 1, icon: 2, time: 1000, text: '手机号码格式错误'});
                    return false;
                }
                //收货地址
                if (bookData.selectAddress) {
                    if (address.id < 1) {
                        $.layer({type: 1, icon: 2, time: 1000, text: '请添加收货地址'});
                        return false;
                    }
                    else {
                        bookData.extParam.address = address.id;
                    }
                }
                //附件参数写入表单
                var extParamHtml = '';
                $('#extParam').html('');
                for (var k in bookData.extParam) {
                    extParamHtml += '<input type="hidden" name=' + k + ' value="' + bookData.extParam[k] + '"/>';
                }
                $('#extParam').html(extParamHtml);
                return true;
            };
            if (check_status()) {
                $("#orderfrm").submit();
            }
        });
        $("#orderfrm").validate({
            submitHandler: function (form) {
                $.layer({type: 4, icon: 1, time: 200000, text: '正在提交订单......'});
                form.submit();
            }
        });
        $(".switch-animate").on("click", function () {
            if ($(this).hasClass("switch-off")) {
                $(this).addClass("switch-on").removeClass("switch-off")
            }
            else {
                $(this).addClass("switch-off").removeClass("switch-on")
            }
        });
        //明细列表
        $("#order-show-list").click(function () {
            $("#fee-box").removeClass("hide")
        });
        $("#fee-box").click(function () {
            $(this).addClass("hide")
        });
    });
    //日期选择
    function choose_day(useDate, node) {
        $('#useDate').text(useDate);
        $('#filed_usedate').val(useDate);
        var url = SITEURL + '{$pinyin}/ajax_current_suit';
        $.getJSON(url, {'usedate': useDate, 'suitid': bookData.suit.id, 'productid': bookData.id}, function (data) {
            if (data.result) {
                $(".unitPrice").text(data.price);
                bookData.price = data.price;
                bookData.suit.price=data.price;
                $.ajax({
                    type:'POST',
                    url:SITEURL + '{$pinyin}/ajax_check_stock',
                    async:false,
                    data:{'suitid':bookData.suit.id,'userdate':useDate,'productid': bookData.id},
                    dataType:'josn',
                    success:function(number){
                        if(number==0){
                            $('.num-text').val(0);
                            bookData.number = 0;
                            bookData.suit.maxnumber=0;
                            $.layer({type: 1, icon: 2, time: 1000, text: '该套餐不可预订'});
                        }
                        else
                        {
                            $('.num-text').val(1);
                            bookData.suit.maxnumber=parseInt(number);
                            bookData.number = 1;
                        }
                    }
                });
                queue.emit('totalPrice');
            }
        });
        history.back();
    }
    function choose_null_day() {
        history.back();
    }
</script>
</body>
</html>
