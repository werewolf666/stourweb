<div class="header_top bar-nav">
    <a class="back-link-icon" href="#pageHome" data-rel="back"></a>
    <h1 class="page-title-bar">我的咨询</h1>
</div>
<!-- 公用顶部 -->
<div class="page-content consult_list">
    <div class="wd-content">
        <ul class="wd-list clearfix" id="consult_list">
        </ul>
    </div>
    <!-- 我的咨询 -->
    <div class="no-content-page" style="display: none">
        <div class="no-content-icon"></div>
        <p class="no-content-txt">此页面暂无内容</p>
    </div>
    <div class="no-info-bar" style="display: none">没有更多结果了！</div>
</div>

<script type="text/html" id="consult_tpl">
    {{each list as value i}}
    <li>
        <div class="item-question">
            <h4 class="bt"><i class="attr">问</i>{{value.title}}</h4>
            {{if value.product}}
            <div class="con">
                <i class="arrow-ico"></i>
                <strong class="tit">咨询产品：</strong>
                <a class="txt" href="{{value.producturl}}" data-ajax="false">{{value.product}}</a>
            </div>
            {{/if}}
            <div class="date">{{value.addtime}}</div>
        </div>
        <div class="item-answer">
            <div class="txt"><i class="attr">答</i>{{value.answer}}</div>
            <div class="date">{{value.replytime}}</div>
        </div>
    </li>
    {{/each}}
</script>
<script>
    //初始页码
    $(function () {
        var params={
            page:1
        }
        var is_loading = false;
        //ajax获取数据

        get_data();

        function get_data() {

                    var url = SITEURL + 'member/consult/ajax_more';
                    $.getJSON(url, params, function (data) {
                        var itemHtml='';
                        if (data.list.length > 0) {
                            itemHtml = template('consult_tpl', data);

                        }
                        if(params.page==1){
                            $("#consult_list").html(itemHtml);
                        }else{
                            $("#consult_list").append(itemHtml);
                        }
                        var len = $('#consult_list').find('li').length;
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
                        params.page=data.page;
                        is_loading = false;
                    });




        }
        /*下拉加载*/

        $('.consult_list').scroll( function() {
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