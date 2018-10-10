<p class="page_right">
    <?php if ($first_page !== FALSE): ?>
        <a class="back-first" title="第一页" href="<?php echo HTML::chars($page->url(1)) ?>"></a>
    <?php endif ?>

    <?php if ($previous_page !== FALSE): ?>
    <a class="prev" title="上一页" href="<?php echo HTML::chars($page->url($previous_page)) ?>"></a>
    <?php endif ?>
    <span class="mod_pagenav_count">

        <?php
           //每页显示数量
           $needpage = 10;
           $coefficient = floor($current_page/$needpage);
           $mod = $current_page % $needpage;
           //开始页码
           $startPage = $coefficient*$needpage + 1;
           $endPage =   $coefficient*$needpage + 10;
           //如果endpage 大于 总页数,则取总页数
           $endPage = $endPage > $total_pages ? $total_pages : $endPage;
        ?>
        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <?php if ($i == $current_page): ?>
                <a title="<?php echo $i ?>" href="<?php echo HTML::chars($page->url($i)) ?>" class="current"><?php echo $i ?></a>
            <?php else: ?>
                <a title="<?php echo $i ?>" href="<?php echo HTML::chars($page->url($i)) ?>"><?php echo $i ?></a>
            <?php endif ?>
        <?php endfor ?>

    </span>
    <?php if ($next_page !== FALSE): ?>
        <a class="next" title="下一页" href="<?php echo HTML::chars($page->url($next_page)) ?>"></a>
    <?php endif ?>

    <?php if ($last_page !== FALSE): ?>
        <a class="go-last" title="最后一页" href="<?php echo HTML::chars($page->url($last_page)) ?>"></a>
    <?php endif ?>
</p>