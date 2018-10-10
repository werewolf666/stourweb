
		<div class="header_top bar-nav">
	        <a class="back-link-icon"  href="#myWallet" data-rel="back"></a>
	        <h1 class="page-title-bar">收支明细</h1>
	    </div>
	    <!-- 公用顶部 -->
        <div class="page-content" id="money_container">
	      <div class="b-pay-container">
    		<ul class="list-tit clearfix">
    			<li class="fl">类型</li>
    			<li class="fr">金额</li>
    		</ul>
    		<ul class="list-con">

    		</ul>
    		<div class="more hide">
    			<a href="javascript:void(0)">没有更多了</a>
    		</div>
	    	<div class="no-list hide">
	    		<img src="{$GLOBALS['cfg_public_url']}images/no-record.png" alt="" title="" />
	    		<p>暂无收支明细记录</p>
	    	</div>
	    </div>
        </div>
<script>

    $(document).ready(function(){
        var CURRENCY_SYMBOL="{Currency_Tool::symbol()}";
        var SITEURL = "{URL::site()}"
        var is_loading=false;
        var gl_page=1;
        $('.page-content').scroll( function() {
            var totalheight = parseFloat($(this).height()) + parseFloat($(this).scrollTop());
            var scrollHeight = $(this)[0].scrollHeight;//实际高度
            if(totalheight-scrollHeight>= -2){
                get_list(gl_page+1);
            }
        });
        get_list(gl_page);


        function get_list(page)
        {
            if(is_loading)
            {
                return;
            }
            is_loading=true;
            $.ajax({
                url:SITEURL+'member/bag/ajax_get_record',
                type:'POST', //GET
                data:{page:page},
                dataType:'json',
                success:function(data,textStatus,jqXHR){
                     is_loading=false;
                     if(data.list.length==0)
                     {
                         if(page<=1)
                         {
                             $("#money_container .no-list").show();
                         }
                         else
                         {
                             $("#money_container .more").show();
                         }
                     }
                     else
                     {

                         var type_arr=['收入','支出','冻结','解冻'];
                         var html='';
                         for(var i in data.list)
                         {
                             var row=data.list[i];
                             var type=parseInt(row['type']);
                             var type_name=type_arr[type];
                             var cls=type==0||type==3?'audit':'fail';
                             var mark =type==0||type==3?'':'-';
                             html += ' <li class="'+cls+' clearfix"><div class="txt fl">' +
                             '<p class="na">'+row['description']+'</p>' +
                             '</div><div class="num fr">' +
                             '<p class="price">'+mark+CURRENCY_SYMBOL+row['amount']+'</p>' +
                             '<p class="date">'+row['addtime']+'</p>' +
                             '</div>'+
                             '</li>';
                         }
                         $("#money_container .list-con").append(html);

                         //全局项修改
                         gl_page=data['page'];
                         var pagesize= parseInt(data.pagesize);
                         if(data.list.length<pagesize)
                         {
                             $("#money_container .more").show();
                         }
                         else
                         {
                             $("#money_container .more").hide();
                         }

                     }
                }
            })
        }
    });
</script>
