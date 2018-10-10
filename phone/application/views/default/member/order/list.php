<div class="header_top bar-nav">
    <a class="back-link-icon" href="javascript:;" data-rel="back"></a>
    <h1 class="page-title-bar">我的订单</h1>
</div>
<!-- 公用顶部 -->
<div class="page-content">
    <div class="order-content">
        <div class="tab-bar">
            <ul class="tab-list clearfix">
                <li {if $type==-1}class="on"{/if} data="-1">全部</li>
                <li {if $type==0}class="on"{/if} data="0">待付款</li>
                <li {if $type==1}class="on"{/if} data="1">待消费</li>
                <li {if $type==2}class="on"{/if} data="2">待点评</li>
            </ul>
        </div>
        <div class="tab-con order-list">

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
            {{if value.typeid==107}}
            <span class="num fl">积分：<em>{{value.usejifen}}</em></span>
            <span class="num fr">全部积分：<em>{{value.needjifen}}</em></span>
            {{else}}
            <span class="num fl">总额：<em>{Currency_Tool::symbol()}{{value.price}}</em></span>
            <span class="num fr">全额支付：<em>{Currency_Tool::symbol()}{{value.price}}</em></span>
            {{/if}}
        </div>
        <div class="item-state {{if value.cancel_order}}cancel_order{{/if}}" data="{id:{{value.id}} }" id="item_{{value.id}}">
            <span class="zt">订单状态：{{value.statusname}}</span>

            {{if value.status == 5 && value.is_commentable}}
             {{if value.ispinlun == 0}}<a href="{{value.commenturl}}" class="pj fr">马上评价</a>{{/if}}
            {{else if value.status == 1}}
            <a href="{{value.payurl}}" data-ajax="false" class="fk fr">去付款</a>
            {{if value.cancel_order}}
            <a class="cz fr cancel_order_btn" style="cursor: pointer">取消订单</a>
            {{/if}}
            {{/if}}
        </div>
    </li>
    {{/each}}
</script>
<script>

    var params={
        page:1,
        type:'{$type}'
    };
    var is_loading = false;
    var id='result_list_'+Math.ceil(Math.random()*100000);
    $('#result_list').attr('id',id);
    //初始页码
    $(function () {

            //ajax获取数据
            var contentNum = 0;
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
                            contentNum++;

                        }
                        if(params.page==1){
                            $("#"+id).html(itemHtml);
                        }else{
                            $("#"+id).append(itemHtml);
                        }
                        var len = $('#'+id).find('li').length;
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
                        //取消订单
                        $('.cancel_order_btn').click(function(){
                            var data=eval('('+$(this).parent('.cancel_order').attr('data')+')');
                            $.layer({
                                type:3,
                                text:'确定取消订单？',
                                ok:function(){
                                   $.post(SITEURL + 'member/order/ajax_cancel',data,function(result){
                                       order_cancel(result);
                                   },'json')
                                }
                            });
                        });
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

    //取消订单
    function order_cancel(result) {
        var msg;
        if (result.status) {
            msg = '取消订单成功';
            $('#item_' + result.data['id']).html('<span class="zt">订单状态：订单取消</span>');
        }
        else {
            msg = '取消订单失败';
        }
        $.layer({
            type: 2,
            text: msg,
            time: 1000
        });
    }
</script>