<?php defined('SYSPATH') or die();?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{$search_title}</title>
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('base.css,swiper.min.css,tongyong.css')}
    {Common::js('jquery.min.js,lib-flexible.js,swiper.min.js,delayLoading.min.js')}

</head>
<body>
{request "pub/header_new/typeid/$typeid"}
<!-- 公用顶部 -->
<div class="page-content show-content list-content-btm">
    <div class="st-list-content">
        <ul class="st-list-group" id="product_list">

        </ul>
        <div class="no-content-page hide">
            <div class="no-content-icon"></div>
            <p class="no-content-txt">此页面暂无内容</p>
        </div>
    </div>
</div>
{request $pinyin."/ajax_searchnav"}

    <!-- 排序 -->
<script>
    var clear_search_condition=false;
    var CURRENCY_SYMBOL="{Currency_Tool::symbol()}";
    var pinyin='{$pinyin}';
    function get_init_fields()
    {
        var params={
            destpy:clear_search_condition?'all':"{$destpy}",
            attrid:clear_search_condition?0:'{$attrid}',
            sorttype:'{$sorttype}',
            keyword:'{$keyword}',
            typeid:'{$typeid}',
            page:clear_search_condition?1:'{$page}'
        };
        return params;
    }

    function on_selected_search(params)
    {

        var destpy = params['destpy']?params['destpy']:'all';
        var attrid = params['attrid']?params['attrid']:'0';
        var sorttype = params['sorttype']?params['sorttype']:'0';

        var page = params['page']?params['page']:'0';
        var keyword=params['keyword'];
        var url=SITEURL+pinyin+'/';
        url+=destpy+'-'+sorttype+'-'+attrid+'-'+page;
        url+=keyword?'?keyword=' + encodeURIComponent(keyword):'';
        window.location.href = url;
    }


    $(document).ready(function(){
        var init_page="{$page}";
        var init_total=0;
        var is_getting=false;
        init_page=Number(init_page);
        init_page=init_page<=0?1:init_page;
        get_product_list(init_page);

        $(".page-content").scroll(function(){
           var scroll_top=$('.page-content').scrollTop();
           if(init_total>0 && init_total<=$("#product_list li").length)
           {
               return;
           }
           if(scroll_top+3>=($(".page-content")[0].scrollHeight - $('.page-content').outerHeight()))
           {
               get_product_list(init_page+1);
           }
        });
        //获取活动列表
        function get_product_list(page)
        {
            if(is_getting)
            {
                return;
            }
            is_getting=true;
            var params=get_init_fields();
            params['page']=page;

            var url=SITEURL+pinyin+'/ajax_more' ;
            $.ajax({
                url:url,
                type:'POST',
                dataType:'json',
                data:params,
                success:function(data) {
                    is_getting=false;
                     var html='';
                     if(page==1 && data.list.length==0)
                     {
                         $(".no-content-page").show();
                         return;
                     }

                     for(var i in data.list) {
                         var row = data.list[i];
                         html += '<li>'
                         html += '<a class="item" href="'+row['url']+'">';
                         html += '<div class="pic"><img src="'+row.litpic+'" alt="'+row['title']+'"></div>';
                         html += '<div class="info">';
                         html += '<h4 class="bt">' + row['title'] + '</h4>';
                         if (row['attrlist'] && row['attrlist'].length > 0)
                         {
                             html += '<p class="attr">';
                            for(var j in row['attrlist'])
                            {
                                html += '<em class="bor1">'+row['attrlist'][j]['attrname']+'</em>';
                            }
                            html += '</p>';
                         }
                     html+='<p class="data clearfix">';
                     html+= '<span>满意度'+row.satisfyscore+'%</span>';
                     html+='<span>销量'+row.sellnum+'</span>';
                     if(row.price && row.price>0)
                     {
                         html += '<span class="price fr"><strong>' + CURRENCY_SYMBOL + '<em>' + row.price + '</em></strong>起</span>';
                     }
                     else
                     {
                         html += '<span class="price fr"><strong>电询</strong></span>';
                     }
                     html+='</p>';
                     html+='</div></a></li>';
                     }
                     $("#product_list").append(html);
                    // init_total = data.total;
                     if(html!='')
                     {
                         init_page=page;
                     }
                }
            })
        }

    })


</script>
</body>
</html>