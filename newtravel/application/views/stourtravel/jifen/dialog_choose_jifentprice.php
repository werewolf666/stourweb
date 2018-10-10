<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('base.css,dialog_basic.css'); }
</head>
<body >
<div class="s-main">
    <div class="s-search">
        <div class="txt-wp">
            <input type="text" name="keyword" placeholder="策略标题"  id="keyword" class="s-txt"/><a href="javascript:;" id="search_btn" class="s-btn"></a>
        </div>
    </div>
    <div class="s-list" style="height:355px">
        <table id="dlg_tb" class="dlg-list">
            <tr class="dlg-hd"><th width="20%">调用标识</th><th width="50%">策略标题</th><th width="15%">抵现分数</th><th class="hd-last">选择</th></tr>
        </table>
        <div id="page_info" class="page-info"></div>
    </div>
    <div class="save-con">
        <a href="javascript:;" class="confirm-btn">确定</a>
    </div>
</div>
<script>
    var data_pool={};
    var typeid="{$typeid}";
    var jifenid="{$jifenid}";
    var selector="{$selector}"
    $(function(){
        load(1);
        $("#search_btn").click(function(){
            load(1);
        });
        //确定
        $(".confirm-btn").click(function(){
            var jifenid=$(".tb-ck:checked").val();
            if(!jifenid)
            {
                ST.Util.showMsg('请先选择策略','5',1500);
                return;
            }
            var jifen_info = data_pool[jifenid];
            var params={};
            params['selector']=selector;
            params['data']=jifen_info;
            ST.Util.responseDialog(params,true);
        });

    })

   function load(page)
   {
       var keyword = $("#keyword").val();
       var url=SITEURL+'jifen/ajax_choose_jifentprice';
       $.ajax({
           type: "post",
           url: url,
           dataType: 'json',
           data: {page: page, keyword: keyword,typeid:typeid,jifenid:jifenid},
           success: function (data, textStatus)
           {
               gen_list(data.result);
           }
       });
   }

   function gen_list(result)
   {
       var html='';

       for(var i in result.list)
       {

           var row=result.list[i];
           data_pool[row['id']]=row;
           var check_str= jifenid==row['id']?'checked="checked"':'';
           html+="<tr class='tb-item'><td align='center'>"+row['label']+"</td>" +
           "<td>"+row['title']+"</td>" +
           "<td align='center'>"+row['toplimit']+"</td>"+
           "<td align='center'><input type='radio' class='tb-ck' name='productid' value='"+row['id']+"' "+check_str+"/></td></tr>";
       }

       $("#dlg_tb .tb-item").remove();
       $("#dlg_tb").append(html);
       var pageHtml = ST.Util.page(result.pagesize, result.page, result.total, 5);
       $("#page_info").html(pageHtml);
       $("#page_info a").click(function () {
           var page = $(this).attr('page');
           load(page);
       });
   }



</script>
</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=6.0.201612.1201&DomainName=&ServerIP=unknown&SerialNumber=70247748" ></script>
