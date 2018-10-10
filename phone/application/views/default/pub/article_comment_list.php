<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>评论列表</title>
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('style-new.css')}
    {Common::js('jquery.min.js,lib-flexible.js,template.js')}

</head>

<body>

    {request 'pub/header_new/definetitle/评论列表'}
    <!-- 公用顶部 -->
    <div class="other-comment-page">
        <ul class="other-comment-list">

        </ul>
        <div class="no-content-page" style="display: none">
            <div class="no-content-icon"></div>
            <p class="no-content-txt">暂时没有相关评论</p>
        </div>
        <div class="no-info-bar" style="display: none">没有更多了！</div>
    </div>
    <!-- 评论列表 -->

    <div class="faq-fix-bar"><a class="faq-fix-btn" href="javascript:;">发布评论</a></div>

    <input type="hidden" id="articleid" value="{$articleid}"/>
    <input type="hidden" id="typeid" value="{$typeid}"/>
    <input type="hidden" id="page" value="1"/>

    <script type="text/html" id="tpl_comment_list">
        {{each list as value i}}
        <li>
            <p class="hd">
                <em class="pic"><img src="{{value.litpic}}" /></em>
                    <span class="info">
                        <strong class="name">{{value.nickname}}</strong>
                        <em class="date">{{value.addtime}}</em>
                    </span>
                <a class="hf-link" href="javascript:;" data-replyid="{{value.id}}" data-replyname="{{value.nickname}}">回复</a>
            </p>
            {{if value.dockid>0}}
            <p class="quote">
                <em class="qt">回复 {{value.reply.nickname}}：</em>
                <span class="qc">{{value.reply.content}}</span>
            </p>
            {{/if}}
            <p class="txt">
                {{value.content}}
            </p>
        </li>
        {{/each}}

    </script>
    <script>
        var is_loading = false;
        //设置body高度
        $("html,body").css("height","100%");
        get_data();


        //滚动加载内容
        $('.other-comment-page').scroll(function(){
            // 当滚动到最底部以上100像素时， 加载新内容
            if ($(document).height() - $(this).scrollTop() - $(this).height()<100){
                var page = $('#page').val();
                if(!is_loading && page!=-1){
                    get_data();
                }

            }
        });

        //回复内容
        $('body').delegate('.hf-link','click',function(){
            var typeid = $('#typeid').val();
            var articleid = $('#articleid').val();
            var replyid = $(this).data('replyid');
            var replyname = $(this).data('replyname');
            var url = SITEURL+'pub/article_write_comment'+'?typeid='+typeid+'&articleid='+articleid+'&replyid='+replyid+'&replyname='+replyname;
            window.location.href = url;

        })

        //发表新评论
        $('.faq-fix-btn').click(function(){
            var typeid = $('#typeid').val();
            var articleid = $('#articleid').val();
            var url = SITEURL+'pub/article_write_comment'+'?typeid='+typeid+'&articleid='+articleid;
            window.location.href = url;

        })

        function get_data(){
            is_loading = true;
            var articleid = $("#articleid").val();
            var typeid = $("#typeid").val();
            var page = Number($("#page").val());
            var url = SITEURL+'pub/ajax_comment';

            $.getJSON(url,{articleid:articleid,typeid:typeid,page:page,flag:'all'},function(data){
                if(data){
                    var html = template("tpl_comment_list",data);
                    if(page == 1){
                        $(".other-comment-list").html(html);
                    }else{
                        $(".other-comment-list").append(html);
                    }
                    var len = $('.other-comment-list').find('li').length;


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