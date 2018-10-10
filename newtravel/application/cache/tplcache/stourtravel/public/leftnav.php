<!--左侧导航区-->
<?php if(!isset($_COOKIE['current_version'])||$_COOKIE['current_version']) { ?>
<div class="menu-left">
    <div class="global_nav">
        <div class="kj_tit"><?php $menu = Model_Menu_New::get_left_nav($_GET['menuid']);
            echo $menu['title']; ?></div>
    </div>
    <div class="nav-tab-a leftnav">
        <?php
        foreach (Model_Menu_New::get_config_by_id($menu['child_id'], 1) as $row)
        {
            $class = ($row['id'] == $_GET['menuid'] || in_array($_GET['menuid'], $row['child_id'])) ? " class='active' " : '';
            $data_url = empty($class) ? ' data-url="' . $row['url'] . '" ' : '';
            $alias_title = isset($row['alias_title']) ? ' data_title="' . $row['alias_title'] . '" ' : '';
            echo '<a href="javascript:;"' . $class . $data_url .$alias_title. '>' . $row['title'] . '</a>';
        }
        ?>
    </div>
</div>
<?php } else { ?>
<div class="menu-left">
    <div class="global_nav">
        <div class="kj_tit"><?php $names = Common::getConfig('menu_sub.chinesename');
            echo $names[$parentkey]; ?></div>
    </div>
    <div class="nav-tab-a leftnav">
        <?php
        $menu = Common::getConfig('menu_sub.' . $parentkey);
        foreach ($menu as $row)
        {
            $class = $row['itemid'] == $itemid ? " class='active' " : '';
            echo '<a href="javascript:;"' . $class . ' data-url="' . $row['url'] . '">' . $row['name'] . '</a>';
        }
        if ($parentkey == 'product')
        {
            //$addmodule = ORM::factory('model')->where("id>13")->get_all();
            $addmodule = Model_Model::getAllModule();
            foreach ($addmodule as $row)
            {
                $class = $row['id'] == $itemid ? " class='active' " : '';
                echo '<a href="javascript:;"' . $class . ' data-url="tongyong/index/typeid/' . $row['id'] . '/parentkey/product/itemid/' . $v['id'] . '">' . $row['modulename'] . '</a>';
            }
        }
        if ($parentkey == 'order')
        {
            //$addmodule = ORM::factory('model')->where("id>13")->get_all();
            $addmodule = Model_Model::getAllModule();
            foreach ($addmodule as $row)
            {
                $class = $row['id'] == $itemid ? " class='active' " : '';
                echo '<a href="javascript:;"' . $class . ' data-url="order/index/parentkey/order/itemid/' . $row['id'] . '/typeid/' . $row['id'] . '">' . $row['modulename'] . '</a>';
            }
        }
        ?>
    </div>
</div>
<?php } ?>
<script>
    $(document).ready(function (e) {
        //导航点击
        $(".leftnav").find('a').click(function () {
            var url = $(this).attr('data-url');
            if (typeof(url) == 'undefined') {
                return;
            }
            var data_title=$(this).attr('data_title');
            var title = typeof(data_title)=='undefined'?$(this).html():data_title;
            ST.Util.addTab(title, url);
        })
    })
</script>