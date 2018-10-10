<div class="header_top bar-nav">
    <a class="back-link-icon" href="javascript:;" onclick="history.go(-1)"></a>
    <h1 class="page-title-bar">管理收货地址</h1>
</div>
<!-- 公用顶部 -->

<div class="addrss-container"  id="address_container">
    <div class="no-linkman-info hide">
        <i class="no-icon"></i>
        <p class="txt">暂无常用收货地址，赶紧新增收货地址</p>
    </div>
    <ul class="addrss-wrap manage-addrss-wrap hide" id="address_list">
    </ul>
</div>
<!-- 管理收货地址 -->

<div class="bottom-fix-bar">
    <a class="addrss-fix-btn fix-btn" href="/phone/member/receive/update" data-reload="true">添加新地址</a>
</div>

<script type="text/html" id="address_tpl">
    {{each list as value i}}
    <li>
        <div class="info-bar">
            <span class="name">{{value.receiver}}</span>
            <span class="num">{{value.phone}}</span>
        </div>
        <div class="addrss-bar">
            <span class="show-addrss">{{value.province}}{{value.city}}{{value.address}}</span>
        </div>
        <div class="addrss-console-bar" data="{id:{{value.id}} }">
            <span class="check-label-item {{if value.is_default==1}}checked{{/if}}"><i class="icon"></i>默认地址</span>
            <span class="operate fr">
                <a class="edit-btn" href="{{value.url}}"><i class="edit-icon"></i>编辑</a>
                <a class="del-btn" style="cursor: pointer;"><i class="del-icon"></i>删除</a>
            </span>
        </div>
    </li>
    {{/each}}
</script>
<script>

    //初始页码
    var is_loading = false;
    var refresh = "{$refresh}";
    var params={
        page:1
    };
    $(function () {
        if(Number(refresh == 1)){
            var url = '{$cmsurl}member#&{$cmsurl}member/receive/address/refresh/0';
            location.href = url;
        }
        //ajax获取数据
        get_data();
        function get_data() {
            var url = SITEURL + 'member/receive/ajax_more';
            $.getJSON(url, params, function (data) {
                var itemHtml='';
                if (data.list.length > 0)
                {
                    itemHtml = template('address_tpl', data);
                    $("#address_list").removeClass('hide');
                }
                else
                {
                    $('.no-linkman-info').removeClass('hide');
                }
                if(params.page==1)
                {
                    $("#address_list").html(itemHtml);
                }
                else
                {
                    $("#address_list").append(itemHtml);
                }
                //设置分页
                if (data.page != -1)
                {
                    params.page=data.page;
                    $('.no-info-txt').hide();
                }
                else
                {
                    $('.no-info-txt').show();
                }
                is_loading = false;
                $(".check-label-item").click(function(){
                    var $this = $(this);
                    if (!$this.hasClass("checked")) {
                        $.post('{$cmsurl}member/receive/ajax_default',eval('('+$(this).parent().attr('data')+')'));
                        $this.addClass("checked").parents("li").siblings().find(".check-label-item").removeClass("checked")
                    }
                });
                $('.del-btn').each(function(){
                   $(this).click(function(){
                       var _this=$(this);
                       $.layer({
                           type: 3,
                           icon: 2,
                           text: '确定删除收货地址？',
                           ok:function(){
                               $.post('{$cmsurl}member/receive/ajax_delete_address',eval('('+_this.parents('.addrss-console-bar').attr('data')+')'),function(status){
                                  if(status){
                                      _this.parents('li').remove();
                                      var ulNode=$('#address_list');
                                      if(ulNode.find('li').length<1){
                                          ulNode.addClass('hide');
                                          $('.no-linkman-info').removeClass('hide');
                                      }
                                  }
                               },'json');
                           },
                           cancel:function(){

                           }
                       });
                   });
                });
            });
        }
        /*下拉加载*/
        $('#addrss-container').scroll( function() {
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
