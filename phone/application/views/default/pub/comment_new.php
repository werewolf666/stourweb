
<!doctype html>
<html>
<head size_background=zeaorl >
    <meta charset="utf-8">
    <title>评论列表</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    {Common::css('style-new.css,mobilebone.css,swiper.min.css')}
    {Common::js('lib-flexible.js')}
</head>
<body>
<div id="pageHome" class="page out">
    <div class="header_top bar-nav">
        <a class="back-link-icon backgo" href="javascript:;"></a>
        <h1 class="page-title-bar">评论</h1>
    </div>
    <!-- 公用顶部 -->
    <div class="page-content">
        <div class="comment-info-bar">
            <div class="myd">
                <span class="sz">{$info['number']['satisfyscore']}</span>
                <span class="wz">满意度</span>
            </div>
            <div class="attr">
                <a class="active" data-flag="all" href="javascript:;">全部({$info['number']['total']})</a>
                <a data-flag="pic" href="javascript:;">有图({$info['number']['picnum']})</a>
                <a data-flag="well" href="javascript:;">好评({$info['number']['wellnum']})</a>
                <a data-flag="mid" href="javascript:;">中评({$info['number']['midnum']})</a>
                <a data-flag="bad" href="javascript:;">差评({$info['number']['badnum']})</a>
            </div>
        </div>
        <div class="comment-block">
            <ul class="comment-list clearfix" id="dp_list">


            </ul>
        </div>
    </div>
    <!-- 评论列表 -->
</div>
<div  class="showpic page out hide">
    <div class="original-show-page">
        <div class="original-info-bar">
            <span class="back-page exit"><a href="#pageHome" class="ico" data-rel="back"></a><em class="page-num"><span id="current_image_index">1</span> / <span id="total_image_num">2</span></em></span>
        </div>
        <div class="original-show-block">
            <div class="swiper-container">
                <div class="swiper-wrapper">


                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="articleid" value="{$info['id']}"/>
<input type="hidden" id="typeid" value="{$typeid}"/>
<input type="hidden" id="page" value="1"/>
{Common::js('jquery.min.js,mobilebone.js,swiper.min.js,template.js')}
<script type="text/html" id="tpl_comment">
    {{each list as value i}}
    <li>
        <div class="info">
            <span class="data"><i class="level">{{value.level}}</i><em class="name">{{if value.nickname==null}}匿名{{else}}{{value.nickname}}{{/if}}</em></span>
            <span class="date">{{value.addtime}}</span>
        </div>
        <div class="star">
            {{if value.percent=='20%'}}
                <i class="on"></i>
                <i></i>
                <i></i>
                <i></i>
                <i></i>
            {{else if value.percent=='40%'}}
                <i class="on"></i>
                <i class="on"></i>
                <i></i>
                <i></i>
                <i></i>

            {{else if value.percent=='60%'}}
                <i class="on"></i>
                <i class="on"></i>
                <i class="on"></i>
                <i></i>
                <i></i>
            {{else if value.percent=='80%'}}
                <i class="on"></i>
                <i class="on"></i>
                <i class="on"></i>
                <i class="on"></i>
                <i></i>
            {{else}}
                <i class="on"></i>
                <i class="on"></i>
                <i class="on"></i>
                <i class="on"></i>
                <i class="on"></i>
            {{/if}}
        </div>
        <div class="txt">
                {{value.content}}
        </div>
        <div class="show-pic">
            <ul class="clearfix">
                {{each value.picthumb as pic}}
                    <li><a href="#myCommentPic"><img src="{{pic}}" /></a></li>
                {{/each}}

            </ul>
        </div>
    </li>
    {{/each}}
</script>

<script>
    var SITEURL = "{URL::site()}";
    var is_loading = false;
    $(function(){
        //返回
        $('.backgo').click(function(){
            history.back();
        })


        //切换评论
        $('.attr a').click(function(){
            $(this).addClass('active').siblings().removeClass('active');
            $('#page').val(1);
            get_data();
        })
        //查看大图
        $('body').delegate('.show-pic ul li a img','click',function(){

            var html = '';
            var total = 0;
            $(this).parents('ul').first().find('img').each(function(i,obj){
                var img = $(obj).attr('src').replace('_86x86','');
                total++;
                html += '<div class="swiper-slide"><img src="'+img+'"  /></div>';
            })
            $('.swiper-wrapper').html(html);
            $('#total_image_num').html(total);
            $('.showpic').show();
            //查看大图
            var mySwiper = new Swiper ('.swiper-container',{
                loop: true,
                onSlideChangeEnd: function(swiper){

                    $('#current_image_index').html(parseInt(swiper.activeIndex)+1);
                }
            });

        })

        $('.exit').click(function(){
            $('.showpic').hide();
        })




        get_data();

        //滚动加载内容
        $('.page-content').scroll(function(){
            // 当滚动到最底部以上100像素时， 加载新内容
            if ($(document).height() - $(this).scrollTop() - $(this).height()<100){
                var page = $('#page').val();
                if(!is_loading && page!=-1){
                    get_data();
                }

            }
        });
    })

    function get_data(){
        is_loading = true;
        var articleid = $("#articleid").val();
        var typeid = $("#typeid").val();
        var page = Number($("#page").val());
        var url = SITEURL+'pub/ajax_comment';
        var flag = $(".attr a.active").attr("data-flag");
        $.getJSON(url,{articleid:articleid,typeid:typeid,page:page,flag:flag},function(data){
            if(data){
                var html = template("tpl_comment",data);
                if(page == 1){
                    $("#dp_list").html(html);
                }else{
                    $("#dp_list").append(html);
                }

                $("#page").val(data.page);

                is_loading = false;
            }
        })

    }

</script>
</body>
</html>