<?php
$configfile = 'menu_sub.'.$menu;
$sub = Common::getConfig($configfile);
foreach($sub as $row)
{
    if(strpos($row['name'],'属性') !== false || $row['selected']===1)
    {
        $attclass = 'isattr';
    }
    else
    {
        $attclass = '';
    }

    $link = "<span class='kinditem ".$attclass."' data-url='".$row['url']."' data-name='".$row['name']."'><s></s>".$row['name']."</span>";
    echo $link;
}

?>
<script>
    $('.kinditem').click(function(){

        var url = $(this).attr('data-url');
        var urlname = $(this).attr('data-name');
        ST.Util.addTab(urlname,url);
    })

</script>
