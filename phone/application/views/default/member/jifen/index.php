<div class="header_top bar-nav">
    <a class="back-link-icon" href="#pageHome" data-rel="back"></a>
    <h1 class="page-title-bar">我的积分</h1>
</div>
<!-- 公用顶部 -->
<div class="page-content">
    <div class="order-content">
        <div class="tab-bar">
            <ul class="tab-list clearfix">
                <li class="on" data-type="0">全部</li>
                <li data-type="2">收入</li>
                <li data-type="1">支出</li>
            </ul>
        </div>
        <div class="tab-con">
            <div class="integral-detail">
                <ul class="detail-list" id="jifen_list">

                </ul>
            </div>
            <div class="no-content-page" style="display: none">
                <div class="no-content-icon"></div>
                <p class="no-content-txt">此页面暂无内容</p>
            </div>
            <div class="no-info-bar" style="display: none">没有更多内容了！</div>
        </div>
    </div>
    <!-- 我的积分 -->
</div>

<script type="text/html" id="jf_tpl">
    {{each list as value i}}
    <li>
             <span class="txt">
             <strong class="tit">{{value.content}}</strong>
             <em class="date">{{value.addtime}}</em>
              </span>
        <span class="num">{{value.point}}</span>
    </li>
    {{/each}}
</script>
<script>
    //初始页码
    var is_loading = false;
    $(function () {
        var params={
                page:1,
                type:0
            }

            //ajax获取数据
            get_data();
            //tab切换
            $('.tab-list').find('li').click(function(){
                $(this).addClass('on').siblings().removeClass('on');
                params.type=$(this).attr('data-type');
                params.page=1;
                get_data();
            });

            function get_data() {
                var url = SITEURL + 'member/jifen/ajax_log_more';


                $.getJSON(url, params, function (data) {
                    var itemHtml='';
                    if (data.list.length > 0) {
                        itemHtml = template('jf_tpl', data);
                    }
                    if(params.page==1){
                        $("#jifen_list").html(itemHtml);
                    }else{
                        $("#jifen_list").append(itemHtml);
                    }
                    if(data.list.length == 0){
                        $(".no-content-page").show();
                    }else{
                        $(".no-content-page").hide();
                    }

                    //设置分页
                    if (data.page != -1) {
                        params.page=data.page;
                        $('.no-info-bar').hide();
                    } else {
                        $('.no-info-bar').show();
                    }
                    is_loading = false;

                });
            }
        /*下拉加载*/

        $('.tab-con').scroll( function() {

            var totalheight = parseFloat($(this).height()) + parseFloat($(this).scrollTop());

            var scrollHeight = $(this)[0].scrollHeight;//实际高度

            if(totalheight-scrollHeight>= -10){

                if(params.page!=-1 && !is_loading ){
                    is_loading = true;
                    get_data();
                }

            }
        });
        });
</script>