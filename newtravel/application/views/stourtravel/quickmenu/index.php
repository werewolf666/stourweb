<!-- 快捷菜单 -->
<div class="manage-content" id="quick_menu_content">
    <div class="manage-title"><i class="ico01"></i>快捷菜单
             <span class="shut-cz">
                        <a title="添加" class="del-menu" id="add-menu" href="javascript:;"></a>
                         <a title="删除" class="add-menu" id="del-menu"  href="javascript:;"></a>
                    </span>
    </div>
    <div class="manage-block">
        <div class="shut-menu clearfix ">
            {loop $quickmenu $v}
            <a  href="javascript:;" title="{$v['path']}" data-url="{$v['menu'][2]}" data-name="{$v['menu'][1]}" target="_blank">{$v['menu'][1]}</a>
            {/loop}
        </div>
    </div>
</div>
<script>
    $('#add-menu').click(function(){
        ST.Util.showBox('快捷菜单', SITEURL + 'quickmenu/select', 670,420, null, null, document, {loadWindow: window, loadCallback: Insert});
        function Insert(data){
            console.log(data)
           var html='';
            for(var i in data){
                html+='<a  href="javascript:;" data-url="'+data[i]['menu'][2]+'" data-name="'+data[i]['menu'][1]+'" title="'+data[i]['path']+'" target="_blank">'+data[i]['menu'][1]+'</a>';
            }
           $('.shut-menu').html(html);
        }
    });
    $('#del-menu').click(function(){
        ST.Util.confirmBox('关闭确认','<p style="text-align: center; font-size: 13px; font-weight: bold; line-height: 35px;">你是否确定关闭快捷菜单功能？</p>关闭后，该菜单将不会在首页显示，如需再次使用，请到【<span style="color:#f00;">系统设置</span>】-【<span style="color:#f00;">参数开关</span>】中进行开启。',function(){
            $.get(SITEURL + 'quickmenu/ajax_set/',{open:0},function($bool){
              if($bool){
                  $('#quick_menu_content').remove();
              }
            },'json')
        })
    });
    $(".shut-menu a").live('click',function(){
        var url = $(this).data('url');
        var title = $(this).data('name');
        ST.Util.addTab(title,url);
    })


</script>