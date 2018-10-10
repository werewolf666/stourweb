<!doctype html>
<html>
<head margin_padding=zVJwOs >
<meta charset="utf-8">
<title>{__('系统消息')}-{$webname}</title>
    {include "pub/varname"}
    {Common::css('user.css,base.css,extend.css')}
    {Common::js('jquery.min.js,base.js,common.js,jquery.validate.js,jquery.cookie.js,jquery.validate.addcheck.js')}
</head>

<body>

{request "pub/header"}
  
  <div class="big">
  	<div class="wm-1200">
    
    	<div class="st-guide">
      	<a href="{$cmsurl}">{__('首页')}</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;{__('常用地址')}
      </div><!--面包屑-->
      
      <div class="st-main-page">
          {include "member/left_menu"}
          <div class="user-message-center fr">
              <div class="user-message-bar">系统消息</div>
              <div class="user-message-container">
                  <ul class="user-message-block" id="msg_con">

                      {loop $list $row}
                      <li class="item {if $row['status']==0}unread{/if} clearfix" data-id="{$row['id']}">
                          <span class="msg-icon"></span>
                          <div class="con-txt">
                              {$row['content']}<a href="javascript:;" data-url="{$row['url']}" class="s-link">【点击查看】</a>
                          </div>
                          <span class="close-btn"></span>
                          <span class="date">{date('Y-m-d H:i',$row['addtime'])}</span>
                      </li>
                      {/loop}
                  </ul>
                  <div class="main_mod_page clear">
                      {$pageinfo}
                  </div><!-- 翻页 -->
              </div>
      </div>
    
    </div>
  </div>
  
{request "pub/footer"}
{Common::js('layer/layer.js')}
<script>
    $(function(){
        //导航选中
        $("#nav_message_index").addClass('on');

        $("#msg_con li .close-btn").click(function(){
            var ele = $(this).parents('li:first');
            var id = ele.attr('data-id');
            $.ajax({
                url:SITEURL+'member/message/ajax_delete',
                type:'POST', //GET
                data:{
                   id:id
                },
                dataType:'json',
                success:function(data,textStatus,jqXHR){
                    if(data.status)
                    {
                        ele.remove();
                    }
                }
            })
        });

        //更多
        $("#msg_con li .s-link").click(function(){

            var ele = $(this).parents('li:first');
            var id = ele.attr('data-id');
            var url = $(this).attr('data-url');
            $.ajax({
                url: SITEURL + 'member/message/ajax_readed',
                type: 'POST', //GET
                data: {
                    id: id
                },
                dataType: 'json',
                success: function (data, textStatus, jqXHR) {
                    window.location.href = url;
                }
            })
        });

    })
</script>
</body>
</html>
