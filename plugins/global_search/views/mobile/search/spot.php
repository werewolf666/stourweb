<!doctype html>
<html>
<head strong_body=nyvz8B >
    <meta charset="utf-8">
    <title>搜索{$params['keyword']}产品列表-{$webname}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('swiper.min.css,base.css')}
    {Common::css_plugin('cloud-search.css','global_search')}
    {Common::js('jquery.min.js,lib-flexible.js,template.js,layer/layer.m.js,swiper.min.js')}
</head>
<body>

    {request "pub/header_new/typeid/0"}
    	
    <div class="st-search">
        <div class="st-search-box">
            <input type="text" class="st-search-text" id="keyword" placeholder="搜索产品/攻略/线路" value="{$params['keyword']}">
            <input type="button" class="st-search-btn" value="">
        </div>
    </div>
    <!-- 搜索条 -->


    <div class="swiper-container">
        <div class="swiper-wrapper">
            {loop $searchmodel['has_search'] $m}
            <span class="swiper-slide choose_model" {if $m['issystem']==1}  data-pinyin="{$m['pinyin']}"  {else}  data-pinyin="general/index/{$m['pinyin']}" {/if} href="javascript:;">{$m['modulename']}</span>
            {/loop}
        </div>
    </div>
    <!-- 搜索类型 -->

    <div class="search-content-area">
        <ul class="search-list-area" id="list-content">

        </ul>
    </div>

    <div class="no-content-page" id="no-content" style="display: none">
        <div class="no-content-icon"></div>
        <p class="no-content-txt">啊哦，暂时没有相关信息</p>
    </div>
    <!-- 无数据 -->



    <script type="text/html" id="tpl_item">
        {{each list as value i}}
        <li>
            <a href="{{value.url}}" class="item">
                <div class="pic">
                    <img src="{{value.litpic}}" alt="{{value.title}}" title="{{value.title}}" />
                </div>
                <div class="info">
                    <h4 class="tit">{{value.title}}</h4>
                    <div class="attr">
                        {{each value.attrlist as a k}}
                        <span class="label">{{a.attrname}}</span>
                        {{/each}}

                    </div>
                    <div class="other clearfix">
                        <span class="fl data">满意度{{value.satisfyscore}}%</span>
                        <span class="fl data">销量{{value.sellnum}}</span>
                        {{if value.price>0}}
                        <span class="fr pri"><span class="jg">{Currency_Tool::symbol()}<strong class="num">{{value.price}}</strong></span>起</span>
                        {{else}}
                        <span class="fr pri"><span class="jg">{Currency_Tool::symbol()}<strong class="num">电询</strong></span></span>
                        {{/if}}
                    </div>
                </div>
            </a>
        </li>
        {{/each}}
    </script>


    {request "pub/footer"}

    <script>

        var page = 1;
        var is_allow = 1;
        var contentNum = 0;
        var pinyin = '{$params['pinyin']}';
        $(function(){
            //搜索类型切换
            var swiper = new Swiper('.swiper-container', {
                slidesPerView: 'auto',
                paginationClickable: true,
                spaceBetween: 0,
                slideToClickedSlide: true
            });
            $('.choose_model').each(function (index,obj) {

                if($(obj).attr('data-pinyin')==pinyin)
                {
                    $(obj).addClass('swiper-slide-on');
                    swiper.slideTo(index, 0, true);
                }
            });
            $('.choose_model').click(function () {
                pinyin = $(this).attr('data-pinyin');
                init_page();
            });
            $('.st-search-btn').click(function () {
                init_page();
            });
            go_search();

            $(window).scroll(function(){
                if ($(document).height() - $(this).scrollTop() - $(this).height()<100){
                    go_search();
                }
            });

        });
        //页面初始化
        function  init_page()
        {
            var keyword = $.trim($('#keyword').val());
            if(keyword)
            {
                var url = SITEURL + 'query/'+pinyin+'?keyword='+keyword;
                location.href=url;
            }
        }
        //前往搜索
        function go_search()
        {
            if(is_allow==0||page==-1)
            {
                return false;
            }
            var keyword = $('#keyword').val();
            if(keyword)
            {
                var url = SITEURL + 'query/'+pinyin+'/get_more'+'?keyword='+keyword+'&page='+page;
            }
            else
            {
                return false;
            }
            is_allow = 0 ;
            layer.open({
                content: '加载中',
                time: 10
            });
            $.ajax({
                type:'get',
                dataType:'json',
                url:url,
                success:function (data)
                {
                    //搜索结果
                    if (data.list.length > 0) {
                        var itemHtml = template('tpl_item', data);
                        $("#list-content").append(itemHtml);
                        contentNum++;
                    }
                    page = data.page;
                    if(page==-1)
                    {
                        $("#btn_getmore").hide();
                    }
                    else
                    {
                        $("#btn_getmore").show();
                    }
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
                    is_allow = 1;
                }


            })






        }




    </script>

</body>
</html>
