<div id="scoreDet" class="page out">
    <div class="header_top bar-nav">
        <a class="back-link-icon" href="#myScore" data-rel="back"></a>
        <h1 class="page-title-bar">积分明细</h1>
    </div>
    <!-- 公用顶部 -->
    <div class="page-content">

        <div class="score-nav">
            <ul class="clearfix" id="log_nav">
                <li data="0"><a href="#">全部记录</a></li>
                <li data="2"><a href="#">获取记录</a></li>
                <li data="1"><a href="#">使用积分</a></li>
            </ul>
        </div>

        <div class="score-list" id="has_content">
            <ul id="score_content">

            </ul>
        </div>
        <div class="no-content-page" id="no_content" style="display: none">
            <div class="no-content-icon"></div>
            <p class="no-content-txt">此页面暂无内容</p>
        </div>
        <div class="no-info-bar" style="display: none">没有更多内容了！</div>

    </div>
</div>
<script>
    var page={
        current:1,
        type:0,
        is_scroll:true
    };
    $('#log_nav').find('li').click(function () {
        page.current = 1;
        $('#no_content').css('display','none');
        $('#no-info-bar').css('display','none');
        $('#has_content').css('display','block');
        page.type=$(this).attr('data')
        $(this).addClass('on').siblings().removeClass('on');
        load();
    });
    $('#log_nav').find('li:eq(0)').click();
    //下拉更新
    $('.page-content').scroll( function() {
        var totalheight = parseFloat($(this).height()) + parseFloat($(this).scrollTop());
        var scrollHeight = $(this)[0].scrollHeight;//实际高度
        if(page.is_scroll && totalheight-scrollHeight>= -10){
            page.is_scroll=false;
            load();
        }
    });
    //加载数据
    function load(){
        if(page.current<0){
            return;
        }
        $.getJSON(SITEURL + 'member/club/ajax_jifen_logs', page, function (data) {
            if (data.status) {
                var html = '';
                for (var i = 0; i < data['list'].length; i++) {
                    var item = data['list'][i];
                    html += '<li><div class="txt">';
                    html += ' <strong class = "name" > ' + item['content'] + '</strong > ';
                    html += '<span class="date">' + item['addtime'] + '</span></div>';
                    if (item['type'] == 1) {
                        html += '<div class="num"><p class="green">-'+item['jifen']+'</p></div>';
                    }
                    else {

                        html += '<div class="num"><p class="red">+'+item['jifen']+'</p></div>';
                    }
                    html += '</li>';
                }
                if (page.current > 1) {
                    $('#score_content').append(html);
                }
                else {
                    $('#score_content').html(html);
                }
                page.current=data.page;
            }
            else {
                if (page.current ==1) {
                    $('#no_content').css('display','block');
                    $('#has_content').css('display','none');
                }
                else
                {
                    $('#no-info-bar').css('display','block');
                }
                page.current=-1;
            }
            page.is_scroll=true;
        });
    }
</script>