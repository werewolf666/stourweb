
<html>
<head table_head=Byvz8B >
    <meta charset="utf-8">
    <title>订单查询结果</title>
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('style-new.css,mobilebone.css,swiper.min.css')}
    {Common::js('lib-flexible.js')}

</head>
{include "pub/var"}
<body>

<div class="header_top bar-nav">
    <a class="back-link-icon" href="{$cmsurl}member" data-rel="back" data-ajax="false"></a>
    <h1 class="page-title-bar">订单查询</h1>
</div>
<!-- 公用顶部 -->
<div class="page-content">
    <div class="order-content">
        <div class="tab-bar">
            <ul class="tab-list clearfix">
                <li class="on" data="-1">全部</li>
                <li  data="0">待付款</li>
                <li  data="1">待消费</li>
                <li  data="2">待点评</li>
            </ul>
        </div>
        <div class="tab-con order-list">
            {if !empty($mobile)}
            <div class="inquiry-num-bar">手机号{$mobile}查询到以下订单</div>
            {/if}
            <div class="user-order-page">
                <ul class="order-list clearfix" id="result_list">

                </ul>

            </div>
            <div class="no-content-page" style="display: none">
                <div class="no-content-icon"></div>
                <p class="no-content-txt">此页面暂无内容</p>
            </div>
            <div class="no-info-bar" style="display: none">没有更多结果了！</div>
        </div>
    </div>
    <!-- 我的积分 -->
</div>
<script type="text/html" id="tpl_list_item">
    {{each list as value i}}
    <li>
        <div class="item-date">{{value.addtime}}</div>
        <div class="item-nr">
            <a  href="{{value.url}}" data-ajax="false" class="item-a">
                <div class="pic"><img src="{{value.litpic}}" alt="{{value.productname}}" /></div>
                <div class="con">
                    <span class="bt">{{value.productname}}</span>
                    <p class="txt">{{value.subname}}</p>
                </div>
            </a>
        </div>
        <div class="item-jg">
            <span class="num fl">总额：<em>{Currency_Tool::symbol()}{{value.price}}</em></span>
            <span class="num fr">全额支付：<em>{Currency_Tool::symbol()}{{value.price}}</em></span>
        </div>
        <div class="item-state">
            <span class="zt">订单状态：{{value.statusname}}</span>
            {{if value.status == 5}}
            {{if value.ispinlun == 0}}<a href="#myComment" class="pj fr">马上评价</a>{{/if}}
            {{else if value.status == 1}}
            <a href="{{value.payurl}}" data-ajax="false" class="fk fr">去付款</a>
            {{/if}}
        </div>
    </li>
    {{/each}}
</script>

{Common::js('template.js')}
{Common::js('jquery.min.js,mobilebone.js,swiper.min.js,jquery.layer.js')}
</body>
</html>

<script>
    var params={
        page:1,
        type:-1,
        mobile:{$mobile}
    }
    var is_loading = false;
    //初始页码
    $(function () {

        //ajax获取数据

        get_data();
        //tab切换
        $('.tab-list').find('li').click(function(){
            $(this).addClass('on').siblings().removeClass('on');
            params.type=$(this).attr('data');
            params.page=1;
            get_data();
        });
        function get_data() {
            var url = SITEURL + 'member/order/ajax_order_more';


                $.getJSON(url, params, function (data) {
                    var itemHtml='';
                    if (data.list.length > 0) {
                        itemHtml = template('tpl_list_item', data);


                    }
                    if(params.page==1){
                        $("#result_list").html(itemHtml);
                    }else{
                        $("#result_list").append(itemHtml);
                    }
                    var len = $('#result_list').find('li').length;
                    if(len == 0){
                        $(".no-content-page").show();
                    }else{
                        $(".no-content-page").hide();
                    }
                    //分页
                    if (data.page == -1 && len>0) {
                        $('.no-info-bar').show();
                    } else {
                        $('.no-info-bar').hide();
                    }
                    params.page = data.page;
                    is_loading = false;
                });


        }
        /*下拉加载*/
        $('.order-list').scroll( function() {
            var totalheight = parseFloat($(this).height()) + parseFloat($(this).scrollTop());
            var scrollHeight = $(this)[0].scrollHeight;//实际高度
            if(totalheight-scrollHeight>= -10){
                if(params.page!=-1 && !is_loading){
                    is_loading = true;
                    get_data();
                }

            }
        });

    });

</script>