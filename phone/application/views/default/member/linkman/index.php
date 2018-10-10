
<div class="header_top bar-nav">
    <a class="back-link-icon"  href="{$cmsurl}member" data-rel="back"></a>
    <h1 class="page-title-bar">我的常用旅客</h1>
</div>
<!-- 公用顶部 -->
<div class="page-content">
    <div class="add-linkman-bar">
        <a class="add-linkman-link" href="{$cmsurl}member/linkman/update?action=add" data-reload="true"><i class="add-ico"></i>新增常用旅客</a>
    </div>
    <div class="linkman-group tab-con" id="linkman-list">


    </div>

    <div class="linkman-group no-content-page hide">
        <div class="no-linkman-info">
            <i class="no-icon"></i>
            <p class="txt">暂无常用旅客，赶紧新增常用旅客</p>
        </div>
    </div>

</div>
<script type="text/html" id="linkman_tpl">
    <ul class="linkman-list clearfix" >
    {{each list as value i}}
        <li>
            <div class="info">
                <strong class="name">{{value.linkman}}</strong>
                <span class="code">{{value.cardtype}}  {{value.idcard}}</span>
            </div>
            <a class="edit-btn" href="{{value.url}}"><i class="ico"></i></a>
        </li>
    {{/each}}
    </ul>
</script>
<script>

    //初始页码
    var is_loading = false;
    var refresh = "{$refresh}";
    var params={
        page:1
    }
    $(function () {

        if(Number(refresh == 1)){
            var url = '{$cmsurl}member#&{$cmsurl}member/linkman/refresh/0';
            location.href = url;
        }

        //ajax获取数据
        get_data();

        function get_data() {
            var url = SITEURL + 'member/linkman/ajax_more';

            $.getJSON(url, params, function (data) {

                var itemHtml='';
                if (data.list.length > 0) {
                    itemHtml = template('linkman_tpl', data);
                }

                if(params.page==1){
                    $("#linkman-list").html(itemHtml);
                }else{
                    $("#linkman-list").append(itemHtml);

                }

                if(data.list.length == 0){
                    $(".no-content-page").show();
                }else{
                    $(".no-content-page").hide();
                }

                //设置分页
                if (data.page != -1) {
                    params.page=data.page;
                    $('.no-info-txt').hide();
                } else {
                    $('.no-info-txt').show();
                }
                is_loading = false;

            });
        }
        /*下拉加载*/

        $('.tab-con').scroll( function() {
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
