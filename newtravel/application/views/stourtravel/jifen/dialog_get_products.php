<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,plist.css'); }
    {php echo Common::getCss('base.css,dialog_basic.css'); }
</head>
<body  top_padding=XFJwOs >
<div class="s-main">
    <div class="s-search">
        <div class="txt-wp">
            <input type="text" name="keyword" placeholder="产品标题"  id="keyword" class="s-txt"/><a href="javascript:;" id="search_btn" class="s-btn"></a>
        </div>
    </div>
    <div class="s-list" style="height:355px">
        <table id="dlg_tb" class="dlg-list">
            <tr class="dlg-hd"><th class="hd-last">选择</th><th width="20%">产品编号</th><th width="60%">产品标题</th></tr>
        </table>
        <div id="page_info" class="page-info">

        </div>
    </div>
    <div class="save-con clear">
        <a href="javascript:;" class="confirm-btn">确定</a>
    </div>
</div>
<script>
    var data_pool=[];
    var typeid="{$typeid}";
    var jifenid="{$jifenid}";
    $(function(){
        load(1);
        $("#search_btn").click(function(){
            load(1);
        });
        //确定
        $(".confirm-btn").click(function(){
            saveIds();
            if(data_pool.length==0)
            {
                ST.Util.showMsg('请先选择产品','5',1500);
                return;
            }
            var url=SITEURL+'jifen/ajax_set_jifenids';
            $.ajax({
                type: "post",
                url: url,
                dataType: 'json',
                data: {typeid:typeid,jifenid:jifenid,productids:data_pool.join(',')},
                success: function (result, textStatus){
                    ST.Util.responseDialog(null,true);
                }
            });
        });

    })

   function load(page)
   {
       var keyword = $("#keyword").val();
       var url=SITEURL+'jifen/ajax_get_products';
       $.ajax({
           type: "post",
           url: url,
           dataType: 'json',
           data: {page: page, keyword: keyword,typeid:typeid,jifenid:jifenid},
           success: function (result, textStatus){
               saveIds();
               gen_list(result);
           }
       });
   }

   function gen_list(result)
   {
       var html='';

       for(var i in result.list)
       {
           var row=result.list[i];
           var check_str= $.inArray(row['id'],data_pool)!=-1?'checked="checked"':'';
           html+="<tr class='tb-item'><td align='center'><input type='checkbox' class='tb-ck' name='productid' value='"+row['id']+"' "+check_str+"/></td><td align='center'>"+row['series']+"</td><td><a href='"+row['url']+"' target='_blank'>"+row['title']+"</a></td></tr>"
       }

       $("#dlg_tb .tb-item").remove();
       $("#dlg_tb").append(html);
       var selectHtml = '';
       if (html.length > 0) {
           selectHtml = '<div class="panel_bar fl mt-4"><a class="abtn" id="select_all" href="javascript:void(0);">全选</a><a class="abtn" id="select_reverse" href="javascript:void(0);">反选</a></div>';
       }
       var pageHtml = selectHtml + ST.Util.page(result.pagesize, result.page, result.total, 5);
       $("#page_info").html(pageHtml);
       $("#page_info a").click(function () {
           var page = $(this).attr('page');
           load(page);
       });
       //全选
       $('#select_all').click(function () {
           $('input[name="productid"]').each(function () {
               $(this).attr('checked', 'checked');
           });
       });
       //反选
       $('#select_reverse').click(function () {
           $('input[name="productid"]').each(function () {
               var checked = $(this).attr('checked');
               if (checked == 'checked') {
                   $(this).removeAttr('checked')
               } else {
                   $(this).attr('checked', 'checked');
               }
           });
       });
   }
   function saveIds()
   {
       $(".tb-ck").each(function(){
           var id = $(this).val();
           if($.inArray(id,data_pool)==-1 && $(this).is(":checked"))
           {
               data_pool.push(id);
           }
           else if($.inArray(id,data_pool)!=-1 && !$(this).is(":checked"))
           {
               data_pool.splice($.inArray(id,data_pool),1);
           }
       })
   }


</script>
</body>
</html>
