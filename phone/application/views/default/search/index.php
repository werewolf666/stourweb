<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>搜索{$keyword}产品列表-{$webname}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('base.css,search.css,swiper.min.css')}
    {Common::js('jquery.min.js,lib-flexible.js,template.js,layer/layer.m.js,swiper.min.js')}
</head>
<body>
{request "pub/header_new/typeid/0"}
<section>
    <div class="mid_content">
    	
    	<div class="st-search">
	        <div class="st-search-box">
	            <input type="text" class="st-search-text" id="keyword" placeholder="搜索产品/攻略/线路" value="{$keyword}">
	            <input type="button" class="st-search-btn" value="">
	        </div>
	    </div>
    	
       
        
        <div class="swiper-container">
            <div class="swiper-wrapper">
                {loop $typeArr $v}
                <div class="swiper-slide" data-id="{$v['typeid']}">{$v['channelname']}({$v['count']})</div>
                {/loop}
            </div>
        </div>
        <!--搜索-->

        <div class="fex" id="list-content">
            <!--list开始-->
            <!--list结束-->
        </div>
        <div class="tuan_more" id="btn_getmore"><a class="cursor">加载更多</a></div>
        <!--没有相关信息-->
        
        
        <div class="no-content-page" id="no-content" style="display: none">
            <div class="no-content-icon"></div>
            <p class="no-content-txt">啊哦，暂时没有相关信息</p>
        </div>
        
        

    </div>
    <input type="hidden" id="typeid" value="{$typeid}"/>
    <input type="hidden" id="page" value="{$page}"/>

</section>
<script type="text/html" id="tpl_item">
    {{each list as value i}}
    <div class="pdt_list">
        <a href="{{value.url}}">
            {{if value.litpic!=''}}
            <div class="pdt_img"><img src="{{value.litpic}}"></div>
            {{/if}}
            <div class="pdt_txt">
                <div class="pdt_box">
                    <p class="p_tit">
                        <em><i class="bq bg_{{value.bg_type}}c">{{value.channelname}}</i>{{value.title}}</em>
                        <span>{{value.description}}</span>
                    </p>
                </div>
            </div>
        </a>
    </div>
    {{/each}}
</script>

{request "pub/footer"}

<script>
    //初始页码
    var initpage = '{$page}';
    $(function () {
        if(initpage !=  $("#page").val()){
            $("#page").val(initpage);
        }
        var swiper = new Swiper('.swiper-container', {
            slidesPerView: 4,
            paginationClickable: true,
            spaceBetween: 0
        });

        //默认加载数据
        get_data();
        //全局搜索
        var contentNum = 0;
        $('.st-search-btn').click(function () {
            var keyword = $("#keyword").val();
            if (keyword == '') {
                layer.open({
                    content: '{__("error_keyword_not_empty")}',
                    btn: ['{__("OK")}']
                });
                return false;
            } else {
                $("#page").val(1);
                $("#typeid").val(0);
                contentNum = 0;
                $("#list-content").html("");
                get_data();
            }
        });
        //分类搜索
        $("body").delegate('.swiper-slide','click',function(){
            contentNum = 0;
            $("#typeid").val($(this).attr('data-id'));
            $("#page").val(1);
            $("#list-content").html("");
            get_data();
        });

        $("#btn_getmore").click(function () {
            var keyword = $("#keyword").val();
            if (keyword == '') {
                layer.open({
                    content: '{__("error_keyword_not_empty")}',
                    btn: ['{__("OK")}']
                });
                return false;
            }
            get_data();
        });
        //加载更多
        function get_data() {
            layer.open({
                type: 2,
                content: '',
                time: 20
            });
            var url = SITEURL + "search/ajax_search_more";
            var cur_typeid=$("#typeid").val();
            $.getJSON(url, {keyword: $("#keyword").val(),typeid: cur_typeid, page: $("#page").val()}, function (data) {
                //分类搜索
                var typeaArr = data.typeArr;
                var html='';
                for(i=0; i<typeaArr.length; i++){
                    var row = typeaArr[i];
                    html+='<div class="swiper-slide" data-id="'+row.typeid+'">'+row.channelname+'('+row.count+')</div>';
                }
                $(".swiper-wrapper").html(html);

               swiper= new Swiper('.swiper-container', {
                    slidesPerView: 4,
                    paginationClickable: true,
                    spaceBetween: 0
                });
                swiper.slideTo(0);
                //搜索结果
                if (data.list.length > 0) {
                    var itemHtml = template('tpl_item', data);
                    $("#list-content").append(itemHtml);
                    contentNum++;
                }

                //设置分页
                if (data.page != -1) {
                    $("#page").val(data.page);
                } else {
                    $("#btn_getmore").hide();
                }
                //设置内内容显示
                if (contentNum == 0) {
                    $('#list-content').hide();
                    $('#btn_getmore').hide();
                    $("#no-content").show();
                }else{
                    $('#list-content').show();
                    $('#btn_getmore').show();
                    $("#no-content").hide();
                }
                layer.closeAll();
            });
        }
    });
</script>
</body>
</html>
