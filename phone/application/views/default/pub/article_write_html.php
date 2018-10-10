<div class="comment-bom-bar">
    <div class="comment-fix-bar">
        <input type="text" class="comment-fb-link" placeholder="发表评论">
        <a class="comment-list-link" href="{$cmsurl}pub/article_comment_list?typeid={$typeid}&articleid={$info['articleid']}"><em>{$info['commentnum']}</em></a>
    </div>
</div>
<script>
    $(function(){
        $('.comment-fb-link').click(function(){
            var url = SITEURL+'pub/article_write_comment?typeid={$typeid}&articleid='+"{$info['articleid']}";
            window.location.href = url;
        })
    })
</script>