{Common::css('footer.css')}
<footer>
    <div class="foot">
        <div class="foot_menu">
            {st:footnav action="query"}
              {loop $data $row}
                <a href="{$row['url']}">{$row['title']}</a>
                {if count($data)!=$n}|{/if}
              {/loop}
            {/st}
        </div>
        <div class="txt">{$GLOBALS['cfg_m_icp']}</div>
        <div class="foot_btn clearfix">
            <a class="foot_ico01" href="{$GLOBALS['cfg_m_main_url']}">
                <em></em>
                <span>首页</span>
            </a>
            <a class="foot_ico02" href="/help/">
                <em></em>
                <span>帮助中心</span>
            </a>
            <a class="foot_ico03" href="tel:{$GLOBALS['cfg_m_phone']}">
                <em></em>
                <span>客服电话</span>
            </a>
            <a class="foot_ico04" id="roll_top" href="javascript:;">
                <em></em>
                <span>返回顶部</span>
            </a>
        </div>
    </div>
</footer>
<script>
   $(function(){
       //返回顶部
       $('#roll_top').click(function(){
           $('html,body').animate({scrollTop: '0px'}, 800);
       });

   })
</script>