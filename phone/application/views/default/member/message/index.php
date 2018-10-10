<div class="header_top bar-nav">
    <a class="back-link-icon" href="javascript:;" data-rel="back"></a>
    <h1 class="page-title-bar">系统消息</h1>
</div>
<div class="page-content" id="message_container">
    <div class="user-message-block">
        <ul class="user-message-list" id="system_message_con">

        </ul>
    </div>
</div>
<script>



    $(document).ready(function(){
        var init_page=1;
        var is_loading=false;

        $('#message_container').scroll( function() {
            var totalheight = parseFloat($(this).height()) + parseFloat($(this).scrollTop());
            var scrollHeight = $(this)[0].scrollHeight;//实际高度
            if(totalheight-scrollHeight>= -10){
               get_list(parseInt(init_page)+1);
            }
        });



        $(document).on("click", "#system_message_con .link",function(){
            var url=$(this).attr('data-url');
            var id=$(this).attr('data-id');
            $.ajax({
                url: SITEURL + 'member/message/ajax_readed',
                type: 'POST', //GET
                data: {id: id},
                dataType: 'json',
                success: function (data, textStatus, jqXHR) {
                    window.location.href = url;
                }
            });
        })



        get_list(init_page);
        function get_list(page)
        {
            if(is_loading)
            {
                return;
            }
            is_loading = true;
            $.ajax({
                url: SITEURL + 'member/message/ajax_more',
                type: 'POST', //GET
                data: {
                    page:page
                },
                dataType: 'json',
                success: function (data, textStatus, jqXHR) {
                    is_loading=false;
                    if(data.status && data.list.length>0)
                    {
                        var html='';
                        for(var i in data.list)
                        {
                            var row=data.list[i];
                            var unread=row['status']==0?'unread':'';
                            html+='<li class="item '+unread+'">';
                            html+='<div class="msg-icon"></div>';
                            html+='<div class="msg-info">';
                            html+='<p class="txt">'+row['content']+'</p>';
                            if(row['url']!='')
                            {
                                html += '<a class="link" href="javascript:;" data-id="' + row['id'] + '" data-url="' + row['url'] + '">【点击查看】</a>';
                            }
                            html+='<p class="date">'+row['addtime']+'</p>';
                            html+='</div></li>';
                        }
                        if(html!='')
                        {
                            $("#system_message_con").append(html);
                            init_page=page;
                        }
                    }
                },
                complete:function()
                {
                    is_loading=false;
                }
            })
        }
    });
</script>