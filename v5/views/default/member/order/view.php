<?php defined('SYSPATH') or die();?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>订单详情</title>
    {template "pub/varname"}
    {Common::css('base.css,user_new.css')}
    {Common::load_skin()}
    {Common::js('jquery.min.js,common.js,base.js')}
    {Common::js('layer/layer.js',0)}
</head>
<body>
{request "pub/header"}

<div class="big">
    <div class="wm-1200">

        <div class="st-guide">
            <a href="{$cmsurl}">{__('首页')}</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;{__('会员中心')}&nbsp;&nbsp;&gt;&nbsp;&nbsp;{__('订单详情')}
        </div><!--面包屑-->
        <div class="st-main-page">

            {include "member/left_menu"}

    <div class="user-content-wrap fr" style="overflow:hidden">
        <div class="condition-col">
            <span class="item-child fl">{__('订单号')}：{$info['ordersn']}</span>
            <span class="item-child fl">{__('订单状态')}：{$info['statusname']}</span>
            {if $info['status']=='1'}
            <a class="cancel-btn fr" href="javascript:;">{__('取消订单')}</a>
            <a class="pay-btn fr" href="javascript:;">{__('立即付款')}</a>
            {/if}
            {if $info['status']=='0'}
            <a class="cancel-btn fr" href="javascript:;">{__('取消订单')}</a>
            {/if}
            {if $info['status']==2}
            <a id="apply-refund-Click" class="refund-btn fr cursor">申请退款</a>
            {/if}
            {if $info['status']==6}
            <a id="cancel-refund-Click" class="refund-btn fr cursor">取消退款</a>
            {/if}
        </div>
        <!-- 订单状态 -->
        <div class="order-show-speed">
            {if  $info['status']<6 && $info['status']!=4}
            <div class="order-speed-step">
                <ul class="clearfix">
                    <li class="step-first cur">
                        <em></em>
                        <strong></strong>
                        <span>{__('提交订单')}</span>
                    </li>
                    <li class="step-second {if $info['status']>1}cur{elseif $info['status']==1}active{/if}">
                        <em></em>
                        <strong></strong>
                        <span>{__('等待付款')}</span>
                    </li>
                    {if $info['status']==3}
                    <li class="step-third active">
                        <em></em>
                        <strong></strong>
                        <span>{__('已取消')}</span>
                    </li>
                    {elseif $info['status']==4}
                    <li class="step-third cur"  >
                        <em></em>
                        <strong></strong>
                        <span>{__('等待消费')}</span>
                    </li>
                    <li class="step-fourth active"  >
                        <em></em>
                        <strong></strong>
                        <span>{__('已退款')}</span>
                    </li>
                    {else}
                    <li class="step-third {if $info['status']>2}cur{elseif $info['status']==2}active{/if}"  >
                        <em></em>
                        <strong></strong>
                        <span>{__('等待消费')}</span>
                    </li>
                    <li class="step-fourth {if $info['status']==5 && $info['ispinlun']!=1}active{elseif $info['status']==5}cur{/if}">
                        <em></em>
                        <strong></strong>
                        <span>{__('等待评价')}</span>
                    </li>
                    <li class="step-fifth {if $info['status']==5 && $info['ispinlun']==1}active{/if}" >
                        <em></em>
                        <strong></strong>
                        <span>{__('交易完成')}</span>
                    </li>
                    {/if}
                </ul>
            </div>
            {else}
            <div class="order-speed-step">
                <ul class="clearfix">
                    <li class="step-first cur blue">
                        <em></em>
                        <strong></strong>
                        <span>{__('申请退款')}</span>
                    </li>
                    <li class="step-second cur">
                        <strong></strong>
                    </li>
                    <li class="step-third {if $info['status']==6}active{else} cur blue{/if}">
                        <em></em>
                        <strong></strong>
                        <span>{__('退款确认')}</span>
                    </li>
                    <li class="step-fourth {if $info['status']==4}cur{/if}">
                        <strong></strong>
                    </li>
                    <li class="step-fifth {if $info['status']==4} cur active{/if}">
                        <em></em>
                        <strong></strong>
                        <span>{__('已退款')}</span>
                    </li>
                </ul>
            </div>
            {/if}
            <div class="speed-show-list">
                {php $log_list = Model_Member_Order_Log::get_list($info['id']);}
                <ul class="info-list" style="height: auto;">
                    {loop $log_list $log}
                    <li><span>{date('Y-m-d H:i:s',$log['addtime'])}</span><span>{$log['description']}</span></li>
                    {/loop}
                </ul>
                {if count($log_list)>2}
                <div id="more-info" class="more-info up">{__('收起详细进度')}</div>
                {/if}
            </div>
        </div>
        <!-- 订单进度 -->
        <div class="os-term">
            <div class="os-tit">{__('联系人信息')}</div>
            <div class="os-block">
                <div class="linkman-info clearfix">
                    <div class="item-block">
                        <em>{__('联系人')}：</em>
                        <p>{$info['linkman']}</p>
                    </div>
                    <div class="item-block">
                        <em>{__('手机号')}：</em>
                        <p>{$info['linktel']}</p>
                    </div>
                    <div class="item-block">
                        <em>{__('邮箱')}：</em>
                        <p>{$info['linkemail']}</p>
                    </div>
                    {if !empty($info['remark'])}
                    <div class="item-block bz">
                        <em>{__('备注')}：</em>
                        <p>{$info['remark']}</p>
                    </div>
                    {/if}
                </div>
            </div>
        </div>
        {if !empty($address_info)}
        <div class="os-term">
            <div class="os-tit">{__('收货地址')}</div>
            <div class="os-block">
                <div class="order-show-address clearfix">
                    <span class="item"><em>{__('收件人')}：</em>{$address_info['receiver']}</span>
                    <span class="item"><em>{__('联系手机')}：</em>{$address_info['phone']}</span>
                    <span class="item"><em>{__('收货地址')}：</em>{php $postcode=empty($address_info['postcode'])?'':'('.$address_info['postcode'].')';echo $address_info['province'].$address_info['city'].$address_info['address'].$postcode;}</span>
                </div>
            </div>
        </div>
        {/if}


        {st:member action="order_bill" orderid="$info['id']" return="bill"}
        {if !empty($bill)}
        <div class="os-term">
            <div class="os-tit">{__('发票信息')}</div>
            <div class="os-block">
                <div class="order-show-invoice">
                    <ul>
                        <li><em>{__('发票明细')}：</em>{__('旅游费')}</li>
                        <li><em>{__('发票金额')}：</em><i class="currency_sy">{Currency_Tool::symbol()}</i>{$info['payprice']}</li>
                        <li><em>{__('发票抬头')}：</em>{$bill['title']}</li>
                        <li><em>{__('收件人')}：</em>{$bill['receiver']}</li>
                        <li><em>{__('联系电话')}：</em>{$bill['mobile']}</li>
                        <li><em>{__('收货地址')}：</em>{$bill['province']} {$bill['city']} {$bill['address']}</li>
                    </ul>
                </div>
            </div>
        </div>
        {/if}
        <!-- 发票信息 -->
        {if !empty($info['iscoupon'])|| !empty($info['usejifen'])}
        <div class="os-term">
            <div class="os-tit">{__('优惠信息')}</div>
            <div class="os-block">
                <div class="order-show-cheap">
                    <ul>
                        {if !empty($info['iscoupon'])}
                        <li><em>{__('优惠券')}：</em><i class="currency_sy">{Currency_Tool::symbol()}</i>{$info['iscoupon']['cmoney']}（{$info['iscoupon']['name']}）</li>
                        {/if}
                        {if $info['usejifen']}
                        <li><em>{__('积分抵现')}：</em><i class="currency_sy">{Currency_Tool::symbol()}</i>{$info['jifentprice']}</li>
                        {/if}
                    </ul>
                </div>
            </div>
        </div>
        {/if}
        <!-- 优惠信息 -->
        <div class="os-term">
            <div class="os-tit">{__('支付信息')}</div>
            <div class="os-block">
                <div class="order-show-cheap">
                    <ul>
                        <li><em>{__('支付方式')}：</em>{$info['paytype_name']} &nbsp;{if $info['paytype']==2}({$info['dingjin']}/{__('单位')}){/if}</li>
                        {if $GLOBALS['cfg_order_agreement_open']==1&&!empty($GLOBALS['cfg_order_agreement_title'])}
                        <li><em>{__('预定协议')}：</em><span class="check-ht" id="agreement_btn">《{$GLOBALS['cfg_order_agreement_title']}》</span></li>
                        {/if}
                    </ul>
                </div>
            </div>
        </div>
        <!-- 支付信息 -->
        {if !empty($info['eticketno']) && Product::is_app_install('stourwebcms_app_supplierverifyorder') && in_array($info['status'],array(2,5))}
        <div class="os-term">
            <div class="os-tit">{__('消费码')}</div>
            <div class="os-block">
                <div class="order-show-code">
                    <p class="txt"><em>{__('短信消费码')}：</em>{$info['eticketno']}</p>
                    <div class="pic"><img src="/res/vendor/qrcode/make.php?param={$info['eticketno']}"></div>
                </div>
            </div>
        </div>
        {/if}
        <!-- 消费码 -->


        {if $info['refund']}
        <div class="os-term">
            <div class="os-tit">退款信息</div>
            <div class="os-block">
                <div class="order-show-cheap">
                    <ul>
                        <li><em>返款方式：</em>{$info['refund']['platform']}</li>
                        {if $info['refund']['alipay_account']}
                        <li><em>退款账号：</em>{$info['refund']['alipay_account']}</li>
                        {/if}
                        {if $info['refund']['cardholder']}
                        <li><em>持卡人：</em>{$info['refund']['cardholder']}</li>
                        {/if}
                        {if $info['refund']['bank']}
                        <li><em>开户行：</em>{$info['refund']['bank']}</li>
                        {/if}
                        {if $info['refund']['cardnum']}
                        <li><em>银行卡号：</em>{$info['refund']['cardnum']}</li>
                        {/if}

                        <li><em>退款金额：</em>{$info['refund']['refund_fee']}</li>
                        <li><em>退款原因：</em>{$info['refund']['refund_reason']}</li>
                        {if $info['refund']['description']}
                        <li><em>处理结果：</em>{$info['refund']['description']}</li>
                        {/if}
                    </ul>
                </div>
            </div>
        </div>
        {/if}
        <div class="os-term">
            <div class="os-tit">{__('订单信息')}</div>
            <div class="os-block">
                <div class="order-show-info">
                    <table width="100%" border="0" class="order-show-table">
                        <tr>
                            <th width="50%" height="40" scope="col"><span class="l-con">{__('产品名称')}</span></th>
                            <th width="15%" scope="col">{__('使用日期')}</th>
                            <th width="10%" scope="col">{__('数量')}</th>
                            <th width="15%" scope="col">{__('单价')}</th>
                            <th width="10%" scope="col">{__('总价')}</th>
                        </tr>
                        <tr>
                            <td height="40"><span class="l-con">{$info['productname']}</span></td>
                            <td>{$info['usedate']}</td>
                            <td>{$info['dingnum']}</td>
                            <td>{$info['price']}</td>
                            <td><span class="jg"><i class="currency_sy">{Currency_Tool::symbol()}</i>{$info['totalprice']}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>




        <!-- 订单信息 -->
        <div class="order-item-count clearfix">
            <div class="item-nr">
                <div class="item-list"><strong class="hd">{__('商品总额')}：</strong><span class="jg"><i class="currency_sy">{Currency_Tool::symbol()}</i>{$info['totalprice']}</span></div>
                <div class="item-list"><strong class="hd">{__('商品优惠')}：</strong><span class="jg">-{Currency_Tool::symbol()}{$info['privileg_price']}</span></div>
                <hr>
                <div class="item-total"><strong class="hd">{__('应付总额')}：</strong><span class="jg"><i class="currency_sy">{Currency_Tool::symbol()}</i>{$info['actual_price']}</span></div>
                {if $info['paytype']==2}
                <div class="item-way">({__('到店支付')} <i class="currency_sy">{Currency_Tool::symbol()}</i>{php}echo $info['actual_price']-$info['payprice']; {/php} + {__('定金支付')} <i class="currency_sy">{Currency_Tool::symbol()}</i>{$info['payprice']})</div>
                {/if}
            </div>
        </div>
        <div class="condition-col">
            <span class="item-child">{if $info['paytype']==2}{__('定金支付')}{else}{__('应付总额')}{/if}：<strong class="jg"><i class="currency_sy">{Currency_Tool::symbol()}</i>{$info['payprice']}</strong></span>
            {if $info['status']=='1'}
            <a class="pay-btn fr" href="javascript:;">{__('立即付款')}</a>
            {/if}
            {if $info['status']==5 && $info['ispinlun']!=1}
            <a class="pl-btn fr" href="javascript:;">{__('立即评论')}</a>
            {/if}
        </div>
        <!-- 支付结算 -->
    </div>
    <div class="agreement-term-content" style="display: none;">
        <div class="agreement-term-tit"><strong>{$GLOBALS['cfg_order_agreement_title']}</strong><i class="close-ico" onclick="$(this).parents('.agreement-term-content').hide();"></i></div>
        <div class="agreement-term-block">
            <h3 class="agreement-bt">《{$GLOBALS['cfg_order_agreement_title']}》</h3>
            <div class="agreement-nr">
                {$GLOBALS['cfg_order_agreement']}
            </div>
        </div>
    </div>
    <script>
        var orderid="{$info['id']}";
        var module_pinyin = "{$moduleinfo['pinyin']}";
        $(document).ready(function(){
            //订单详细进度
            $("#more-info").on("click",function(){
                if( $(this).hasClass("down") )
                {
                    $(this).addClass("up").removeClass("down").text("{__('收起详细进度')}");
                    $(this).prev().css("height","auto");
                }
                else
                {
                    $(this).addClass("down").removeClass("up").text("{__('查看详细进度')}");
                    $(this).prev().css("height","52px");
                }
            })

            //付款
            $(".pay-btn").click(function(){
                var locateurl = "{$GLOBALS['cfg_basehost']}/member/index/pay/?ordersn={$info['ordersn']}";
                top.location.href = locateurl;
            })


            //显示协议
            $("#agreement_btn").click(function(){

                $(".agreement-term-content").show();
               adjust_agreement_pos();
            });
            $(window).scroll(function(){
                adjust_agreement_pos();
            });


            //取消订单
            $(".cancel-btn").click(function(){
                var LayerDlg = parent && parent.layer ? parent.layer:layer;
                var url = SITEURL +'member/order/ajax_order_cancel?orderid='+orderid;
                LayerDlg.confirm('{__("order_cancel_content")}', {
                    icon: 3,
                    btn: ['{__("Abort")}','{__("OK")}'], //按钮
                    btn1:function(){
                        LayerDlg.closeAll();
                    },
                    btn2:function(){
                        $.getJSON(url,{orderid:orderid},function(data){
                            if(data.status){
                                LayerDlg.msg('{__("order_cancel_ok")}', {icon:6,time:1000});
                                setTimeout(function(){location.reload()},1000);
                            }
                            else{
                                LayerDlg.msg('{__("order_cancel_failure")}', {icon:5,time:1000});
                            }
                        })
                    },
                    cancel:function(){
                        LayerDlg.closeAll();
                    }
                })
            });

            //立即评论
            $(".pl-btn").click(function(){
                var url = "{$GLOBALS['cfg_basehost']}/member/order/pinlun?ordersn={$info['ordersn']}";
                top.location.href = url;
            })



           $(".user-side-menu #nav_"+module_pinyin+"order").addClass('on');


        })



        function adjust_agreement_pos()
        {
            var top = $(window).scrollTop();
            $(".agreement-term-content").css({top:top,'margin':'0px 0 0 -400px'});
        }

    </script>


            {if $info['status']==2}
            <script>
                $(function () {
                    //申请退款
                    $("#apply-refund-Click").on("click", function () {
                        parent.layer.open({
                            type: 2,
                            title: "申请退款",
                            area: ['560px','570px'],
                            content: '{$cmsurl}member/order/order_refund?ordersn={$info['ordersn']}',
                            btn: ['确认', '取消'],
                            btnAlign: 'C',
                            closeBtn: 0,
                            yes: function (index, b) {
                                var frm = parent.layer.getChildFrame('#refund_frm', index);
                                if(check_refund_frm(frm)==false)
                                {
                                    return false;
                                }
                                parent.layer.close(index);
                                var data = $(frm).serialize();
                                refund_status(data);
                            }
                        });
                    });
                });

                /**
                 *
                 * @param frm_data 表单验证
                 */
                function check_refund_frm(frm_data)
                {
                    var refund_reason = $(frm_data).find('textarea[name=refund_reason]').val();
                    if(refund_reason.replace(/(^\s*)|(\s*$)/g, "").length<5)
                    {
                        parent.layer.open({
                            content: '退款原因不能少于五个字',
                            btn: ['{__("OK")}'],
                        });
                        return false;
                    }
                    var refund_auto = $(frm_data).find('input[name=refund_auto]').val();
                    var platform = $(frm_data).find('input[name=platform]:checked').val();
                    if(refund_auto!=1)
                    {
                        if(platform=='alipay')
                        {
                            var alipay_account = $(frm_data).find('input[name=alipay_account]').val();
                            if(alipay_account.replace(/(^\s*)|(\s*$)/g, "").length<5)
                            {
                                parent.layer.open({
                                    content: '请填写正确的支付宝账号',
                                    btn: ['{__("OK")}'],
                                });
                                return false;
                            }
                        }
                        else if(platform=='bank')
                        {
                            var cardholder = $(frm_data).find('input[name=cardholder]').val();
                            var cardnum = $(frm_data).find('input[name=cardnum]').val();
                            var bank = $(frm_data).find('input[name=bank]').val();
                            if(cardholder.length<1||cardnum.length<10||bank.length<2)
                            {
                                parent.layer.open({
                                    content: '请填写正确的银行卡信息',
                                    btn: ['{__("OK")}'],
                                });
                                return false;
                            }
                        }
                    }
                    return true;
                }



                function refund_status(data) {
                    $.post('{$GLOBALS["cfg_basehost"]}/member/order/ajax_order_refund', data, function (result) {
                        parent.layer.open({
                            content: result.message,
                            btn: ['{__("OK")}'],
                            end:function(){
                                window.location.reload();
                            }
                        });
                    }, 'json');
                }
            </script>
            {/if}
            {if $info['status']==6}
            <script>
                $(function () {
                    //取消退款
                    $("#cancel-refund-Click").on("click", function () {
                        parent.layer.open({
                            type: 1,
                            title: "取消退款",
                            area: ['480px', '250px'],
                            content: '<div id="cancel-refund" class="cancel-refund"><p>确定取消退款申请？</p></div>',
                            btn: ['确认', '取消'],
                            btnAlign: 'c',
                            closeBtn: 0,
                            yes: function (index, b) {
                                parent.layer.close(index);
                                //提交信息
                                refund_status({'ordersn': '{$info['ordersn']}'});
                            }
                        });
                    });
                });
                function refund_status(data) {
                    $.post('{$GLOBALS["cfg_basehost"]}/member/order/ajax_order_refund_back', data, function (result) {
                        parent.layer.open({
                            content: result.message,
                            btn: ['{__("OK")}'],
                            end:function(){
                                window.location.reload();
                            }
                        });
                    }, 'json');
                }
            </script>
            {/if}

            </div>
        </div>
</div>
{request "pub/footer"}
</body>
</html>