<?php defined('SYSPATH') or die();?>
<!doctype html>
<html>
<head table_bottom=5WIwOs >
    <meta charset="utf-8">
    <title>咨询列表</title>
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('style-new.css')}
    {Common::js('jquery.min.js,lib-flexible.js,template.js')}
</head>
<body>
<div class="header_top bar-nav">
    <a class="back-link-icon backgo" href="javascript:;" onclick="window.history.back()"></a>
    <h1 class="page-title-bar">问答列表</h1>
</div>
    <div class="faq-page-content">
        <ul class="faq-page-list">


        </ul>
        <div class="no-content-page" style="display: none">
            <div class="no-content-icon"></div>
            <p class="no-content-txt">暂时没有问答</p>
        </div>
        <div class="no-info-bar" style="display: none">没有更多了！</div>

    </div>

    <div class="faq-fix-bar"><a class="faq-fix-btn" href="javascript:;">我要提问</a></div>
    <input type="hidden" id="articleid" value="{$articleid}"/>
    <input type="hidden" id="typeid" value="{$typeid}"/>
    <input type="hidden" id="page" value="1"/>
    <script type="text/html" id="tpl_question_list">
        {{each list as value i}}
            <li>
                <div class="question">
                    <i class="ico">问</i>
                    <p class="txt">{{#value.content}}</p>
                    <p class="info clearfix">
                        <span class="name fl">{{value.nickname}}</span>
                        <span class="date fr">{{value.pubdate}}</span>
                    </p>
                </div>
                <div class="answer">
                    <i class="ico">答</i>
                    <p class="txt">{{#value.replycontent}}</p>
                    <p class="info clearfix">
                        <span class="date fr">{{value.replydate}}</span>
                    </p>
                </div>
            </li>
        {{/each}}

    </script>
    <script>
        var SITEURL = "{URL::site()}";
        var is_loading = false;
        //设置body高度
        $("html,body").css("height","100%");
        get_data();

        //滚动加载内容
        $('.faq-page-content').scroll(function(){
            // 当滚动到最底部以上100像素时， 加载新内容
            if ($(document).height() - $(this).scrollTop() - $(this).height()<100){
                var page = $('#page').val();
                if(!is_loading && page!=-1){
                    get_data();
                }

            }
        });
        //发表问答
        $('.faq-fix-btn').click(function(){
            var typeid = $('#typeid').val();
            var articleid = $('#articleid').val();
            var url = SITEURL+'question/product_question_write'+'?typeid='+typeid+'&articleid='+articleid;
            window.location.href = url;

        })

        function get_data(){
            is_loading = true;
            var articleid = $("#articleid").val();
            var typeid = $("#typeid").val();
            var page = Number($("#page").val());
            var url = SITEURL+'question/ajax_product_question_more';

            $.getJSON(url,{articleid:articleid,typeid:typeid,page:page},function(data){
                if(data){
                    var html = template("tpl_question_list",data);
                    if(page == 1){
                        $(".faq-page-list").html(html);
                    }else{
                        $(".faq-page-list").append(html);
                    }

                    var len = $('.faq-page-list').find('li').length;


                    if(len==0){
                        $('.no-content-page').show();
                    }else{
                        $('.no-content-page').hide();
                    }
                    if (data.page == -1 && len>0) {
                        $('.no-info-bar').show();
                    } else {
                        $('.no-info-bar').hide();
                    }

                    $("#page").val(data.page);

                    is_loading = false;
                }
            })

        }


    </script>

</body>
</html>