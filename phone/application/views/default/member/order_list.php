<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>订单中心-{$GLOBALS['cfg_webname']}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {php echo Common::css('amazeui.css,style.css,extend.css');}
    {php echo Common::js('jquery.min.js,amazeui.js,template.js,layer/layer.m.js');}
</head>
<body>
{request "pub/header/isparam/$title"}
<section>
    <div class="mid_content">
        <div class="order-topfix-menu" data-am-sticky>
            <span class="on" data="-1">全部</span>
            <span data="0">未付款</span>
            <span data="1">已付款</span>
        </div>
        {if isset($mobile)}
        <div class="order-query-txt">手机号{$mobile}查询到以下全部订单</div>
        {/if}
        <div class="user_order_list" id="list-content">
            <ul id="result_list">
            </ul>
            <div class="list_more"><a href="javascript:;" id="btn_getmore">加载更多</a></div>
        </div>
        {if isset($mobile)}
        <div class="order-empty-container" id="no-content" style="display: none">
            <div class="empty-txt">
                <img src="{$GLOBALS['cfg_public_url']}images/nodata-ico.png">
                <p>手机号码{$mobile}没有订单</p>
            </div>
        </div>
        {else}
            <div class="no-content" id="no-content" style="display: none">
                <img src="{$GLOBALS['cfg_public_url']}images/nocon.png"/>
                <p>空空如也，什么都没有<br/>赶紧去预定，让生活充实起来吧！</p>
            </div>
        {/if}

    </div>
    <input type="hidden" id="page" value="{$page}"/>
</section>
{request 'pub/footer'}

<script type="text/html" id="tpl_list_item">
    {{each list as value i}}
    <li>
        <div class="date_time">{{value.addtime}}</div>
        <div class="pic_txt">
            <a href="{{value.url}}">
                <dl>
                    <dt><img src="{{value.litpic}}"/></dt>
                    <dd>
                        <span>{{value.productname}}</span>
                        <p>{{value.subname}}</p>
                    </dd>
                </dl>
                </dl>
            </a>
        </div>
        <div class="price">
            <span>总额：<em><i class="currency_sy">{Currency_Tool::symbol()}</i>{{value.price}}</em></span>
            <span class="fr"><i data="type">{{value.paytype}}</i>：<em><i class="currency_sy">{Currency_Tool::symbol()}</i>{{value.price}}</em></span>
        </div>
        <div class="cool">
            <span class="zt">订单状态</span>
                {{if value.status == 5}}
                <span class="cz"><em>{{value.statusname}}</em>{{if value.ispinlun == 0}}<a class="pl" href="{{value.commenturl}}">去评论</a>{{/if}}</span>
                {{else if value.status == 4}}
                <span class="cz"><em>{{value.statusname}}</em></span>
                {{else if value.status == 3}}
                <span class="cz"><em>{{value.statusname}}</em></span>
                {{else if value.status == 2}}
                <span class="cz"><em>{{value.statusname}}</em></span>
                {{else if value.status == 1}}
                    <span class="cz"><a class="fk_btn" href="{{value.payurl}}">去付款</a></span>
                {{else}}
                <span class="cz"><em>{{value.statusname}}</em></span>
                {{/if}}
        </div>
    </li>
    {{/each}}
</script>
<script>
    //初始页码
    $(function () {
        var params={
            {if isset($mobile)}
              isquery:{$mobile},
            {/if}
            page:1,
            type:-1
        }
        //ajax获取数据
        var contentNum = 0;
        get_data();
        //获取更新数据
        $('#btn_getmore').click(function () {
            get_data();
        })
        //tab切换
        $('.order-topfix-menu').find('span').click(function(){
            $(this).addClass('on').siblings().removeClass('on');
            contentNum = 0;
            params.type=$(this).attr('data');
            params.page=1;
            get_data();
        });
        function get_data() {
            var url = SITEURL + 'member/order/ajax_order_more';
            layer.open({
                type: 2,
                content: '',
                time: 20

            });
            $.getJSON(url, params, function (data) {
                var itemHtml='';
                if (data.list.length > 0) {
                    itemHtml = template('tpl_list_item', data);console.log(params.page);
                    contentNum++;

                }
                if(params.page==1){
                    $("#result_list").html(itemHtml);
                }else{
                    $("#result_list").append(itemHtml);
                }
                if(contentNum == 0){
                    $('#list-content').hide();
                    $("#no-content").show();
                }else{
                    $('#list-content').show();
                    $("#no-content").hide();
                }
                //设置分页
                if (data.page != -1) {
                    params.page=data.page;
                } else {
                    $("#btn_getmore").hide();
                }
                layer.closeAll();
            });
        }
    });
</script>
</body>
</html>
