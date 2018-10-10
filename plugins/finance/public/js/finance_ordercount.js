var SITEURL='/newtravel/';
$(function(){
    //日期datepicker初始化
    $("#starttime").focus(function(){
        $("#endtime").attr('value','');
        $("#endtime").blur();
        WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'#F{$dp.$D(\'endtime\',{d:-1});}',doubleCalendar:false,isShowClear:true,readOnly:true,errDealMode:1})
    });

    $("#endtime").focus(function(){
        $("#starttime").blur();
        WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(\'starttime\',{d:+1});}',doubleCalendar:false,isShowClear:true,readOnly:true,errDealMode:1})
    });

    //搜索列表选中单选框
    $(document).on('click','.popup-layer .search-list tr:gt(0)',function(){
        var $this = $(this);
        $this.find("input").prop("checked",true);
    });
    //选中项目取消和列表选择框隐藏
    $(document).on("click",'.close-btn',function(){
        var $this = $(this);
        var $p = $this.parent();
        $p.attr("data-id",null);
        if($p.hasClass("popup-tit"))
        {
            $p.parent().parent().hide();
        }
        else
        {
            $p.hide();
        }

        //分类重置
        //$("#category").val(0);

    })

    //选中条件
    $("#category").change(function(){
        var $this =$(this);
        if($this.val()==0)
        {
            $("#choose-btn").hide();
        }
        else
        {
            $("#choose-btn").show();
        }
    });
    //$("#category").change();

    //展示搜索条件列表
    $("#choose-btn").click(function(){
        var category = $("#category").val();
        var data = undefined;
        var typeid = '';
        var pagesize = 10;
        var pageno = 1;

        if(category==1)
        {
            typeid = $(".module-select").val();
            data = get_search_list(category, typeid, '',1);
            full_product_list('product', data, pagesize, pageno);
        }
        else if(category==2)
        {
            data = get_search_list(category, typeid, '',1);
            full_product_list('supplier', data, pagesize, pageno);
        }
        else if(category==3)
        {
            data = get_search_list(category, typeid, '',1);
            full_product_list('fenxiao', data, pagesize, pageno);
        }
    });

    //产品搜索条件展示列表
    $(".search-btn-product").click(function (e, param) {
        var category = $("#category").val();
        var typeid = $(".module-select").val();
        var keyword = $(".search-text-product").val();
        var pagesize = 10;
        var pageno = typeof(param) == 'undefined' ? 1 : param;
        var data = get_search_list(category, typeid, keyword,pageno);
        full_product_list('product', data, pagesize, pageno);
    });
    $(document).on("click",".pm-btm-msg-product a",function(){
        var $this = $(this);
        var pageno = $this.attr('data-pageno')-0;
        if($this.hasClass('current'))
        {
            return;
        }
        $(".search-btn-product").trigger("click",pageno);
    });
    //供应商搜索条件展示列表
    $(".search-btn-supplier").click(function (e, param) {
        var category = $("#category").val();
        var typeid = '';
        var keyword = $(".search-text-supplier").val();
        var pagesize = 10;
        var pageno = typeof(param) == 'undefined' ? 1 : param;
        var data = get_search_list(category, typeid, keyword,pageno);
        full_product_list('supplier', data, pagesize, pageno);
    });
    $(document).on("click",".pm-btm-msg-supplier a",function(){
        var $this = $(this);
        var pageno = $this.attr('data-pageno')-0;
        if($this.hasClass('current'))
        {
            return;
        }
        $(".search-btn-supplier").trigger("click",pageno);
    });
    //分销商搜索条件展示列表
    $(".search-btn-fenxiao").click(function (e, param) {
        var category = $("#category").val();
        var typeid = '';
        var keyword = $(".search-text-fenxiao").val();;
        var pagesize = 10;
        var pageno = typeof(param) == 'undefined' ? 1 : param;
        var data = get_search_list(category, typeid, keyword,pageno);
        full_product_list('fenxiao', data, pagesize, pageno);
    });
    $(document).on("click",".pm-btm-msg-fenxiao a",function(){
        var $this = $(this);
        var pageno = $this.attr('data-pageno')-0;
        if($this.hasClass('current'))
        {
            return;
        }
        else
        {
            pageno=$(this).attr("data-pageno");
        }

        $(".search-btn-fenxiao").trigger("click",pageno);
    });


    //确定搜索条件
    $(".sure-btn").click(function(){
        var $this = $(this);
        var $list = $this.parent().parent().find(".search-list .table-list tr:gt(0)");
        var $chk = $list.find("input:checked");
        var $tr = $chk.closest('tr');
        var supplier_name = $tr.find('td:eq(0)').text();
        var link_name = $tr.find(".pd-name:eq(0)").text();
        var phone = $tr.find(".pd-name:eq(1)").text();
        var s_obj = new Object();
        s_obj.id=$chk.attr("data-id");
        if(link_name != '' && phone != '')
        {
            s_obj.title = supplier_name+' | '+link_name+' | '+phone;
        }else if(link_name == '' && phone != '')
        {
            s_obj.title = supplier_name+' | '+phone;
        }else if(link_name == '' && phone == ''){
            s_obj.title = supplier_name;
        }else{
            s_obj.title = supplier_name+' | '+link_name;
        }

        $(".cp-title").html(s_obj.title + '<i class="close-btn"></i>').attr("data-id",s_obj.id).show();
        $(".popup-layer .close-btn").click();
    });

    //统计字段选中状态改变
    $("#create-table").click(function(){
        get_count_orderlist(1);
    });


    /*$("#settle_status").change(function(){
        var $this = $(this);
        if($this.val()==)
    });*/

    //获取分页列表
    //get_count_orderlist(1);

    //导出excel
    $("#export_excel").click(function(){
        var $chked = $("#count_fields input:checked");
        var p = [];
        for(var i=0; i<$chked.length; i++)
        {
            var chk = $chked[i];
            p.push(chk.name);
        }
        p = p.join(',');

        var type = 0;
        var typeid = 0;
        var id = 0;
        var $cp_title = $(".cp-title");
        id = $cp_title.attr("data-id");
        if(id)
        {
            type = $("#category").val();
            typeid = $(".module-select").val();
        }
        else
        {
            id=0;
        }
        var starttime = $('#starttime').val();
        var endtime = $('#endtime').val();
        console.log(starttime);
        console.log(endtime);

        var url = SITEURL+'finance/admin/financeextend/ordercount_export_excel/?fields='+p+'&type='+type+'&typeid='+typeid+'&id='+id+'&starttime='+starttime+'&endtime='+endtime;
        window.open(url);
    })

    //订单列表点分页
    $(document).on("click",'.pm-btm-msg-order-list .page_right a',function(){
        var $this = $(this);
        if($this.hasClass("current"))
        {
            return;
        }
        var pageno = $this.attr("data-pageno");
        get_count_orderlist(pageno);
    });
    //订单列表页码变化
    $(document).on('change','#order_pagesize',function(){
        get_count_orderlist(1);
    });
    //切换产品
    $('.module-select').change(function () {
        $('.search-btn-product').trigger('click');
    })


    //显示或影藏列
    count_fields_check_change(true);
});

//获取订单列表
function get_count_orderlist(pageno)
{
//category, typeid, id, starttime, end
    var category = $("#category").val();
    var typeid = $(".module-select").val();
    var id = $(".cp-title").attr("data-id");
    var starttime = $("#starttime").val();
    var endtime = $("#endtime").val();
    var settle_status = $("#settle_status").val();
    var order_status = $("#order_status").val();
    var pagesize = $("#order_pagesize").val();

    var url = SITEURL+'finance/admin/financeextend/ajax_ordercount_list/';
    $.get(url,{
        category: category,
        typeid: typeid,
        id: id,
        starttime: starttime,
        endtime: endtime,
        settle_status: settle_status,
        order_status: order_status,
        pageno: pageno,
        pagesize: pagesize
    },function(data){
        fill_order_list(data, pageno);
    },'json');
}

function fill_order_list(data, pageno)
{//count_fields
    //生成列表
    var html="";
    var count_fields= $.parseJSON(window.count_fields);
    for(var i=0; i<data.list.length; i++)
    {
        var row = data.list[i];
        html += '<tr>';
        for(var key in count_fields)
        {
            var tmp = row[key];
            tmp = tmp ? tmp : '&nbsp;&nbsp;';
            html +='<td>'+tmp+'</td>';
        }
        html += '</tr>';
    }
    $("#order_list_header").siblings().remove().end().after(html);
    //生成分页
    var pagesize = $("#order_pagesize").val();
    var pager = gen_pager(pagesize, pageno, data.total);
    $("#order_list_page").siblings().remove().end().before(pager);

    //设置统计信息
    $("#total").html(data.countinfo.total);

    $("#total").html('￥'+data.countinfo.total);
    $("#totalprice").html('￥'+data.countinfo.totalprice);
    $("#payprice").html('￥'+data.countinfo.payprice);
    $("#jifentprice").html('￥'+data.countinfo.jifentprice);
    $("#basicprice").html('￥'+data.countinfo.basicprice);
    $("#commission").html('￥'+data.countinfo.commission);
    $("#settle_amount").html('￥'+data.countinfo.settle_amount);

    //显示或影藏列
    count_fields_check_change();

    //显示统计信息
    $("#count-msg-container").show();

}

//生成统计列表
function count_fields_check_change(is_int)
{
    var $chks = $("#count_fields input");
    var $chk_chks = $("#count_fields input:checked");
    var $productname = $("#count_fields input:checked[name=productname]");

    var index_productname = -1;
    if($productname)
    {
        for(var i=0; i<$chk_chks.length; i++)
        {
            var chk = $chk_chks[i];
            if(chk.name == $productname[0].name)
            {
                index_productname = i;
                break;
            }
        }
    }



    var td_width = 'auto';
    if(!is_int)
    {
        var content_width = $(".finance-block").width();
        var new_width = $chk_chks.length * 130;
        if(new_width > content_width)
        {
            td_width = '130px';
        }
        else
        {
            new_width = '100%';
        }
        $(".finance-table-list").attr("width",new_width);
    }


    var index_str = "";
    for(var i=0; i<$chk_chks.length; i++)
    {
        var index = $($chks).index($chk_chks[i]);
        index_str += ',' + index;//记录要展示的列表字段
    }
    index_str += index_str + ',';

    var $trs = $("#order_list tr");

    var productname_index = -1;
    for(var trcount=0; trcount<$trs.length; trcount++)
    {
        var $tr = $($trs[trcount]);
        var dom = 'td';
        if(trcount==0)
        {
            dom = "th";
        }

        var $txs = $tr.find(dom);
        for(var txcount=0; txcount<$txs.length; txcount++)
        {
            var $tx = $($txs[txcount]);
            $tx.css("width",td_width);
            if(index_str.indexOf(','+ txcount+',')!=-1)
            {
                $tx.show();
            }
            else
            {
                $tx.hide();
            }

            if (txcount == index_productname)
            {
                $tx.css("width",'200px');
               var $span = $("<span title='"+$tx.text()+"'>"+$tx.text()+"</span>")
                $tx.html($span);
            }
        }


    }

}


//展示搜索条件列表
function get_search_list(category, typeid, keyword, pageno)
{
    var rtn=undefined;
    var url = SITEURL+"finance/admin/financeextend/ajax_ordercount_query_list";
    $.ajax({
        type:"GET",
        url:url,
        data:{
            category:category,
            pageno:pageno,
            typeid:typeid,//typeid
            keyword:keyword
        },
        dataType:'json',
        async:false,
        success:function(data){
            rtn=data;
        }
    });
    return rtn;
}

//填充产品,供应商，分销商列表
function full_product_list(target, data, pagesize, pageno)
{
    var html = template('tpl_'+target+'_list',data);
    $("#"+target+"_header").siblings().remove().end().after(html);

    var pager = gen_pager(pagesize, pageno, data.total);
    $(".pm-btm-msg-"+target).html('').html(pager);

    $("#choose-"+target).show();
}

