<div class="st_search">
    <div class="st_search_box">
        <input type="text" class="st_home_txt" id="keyword" placeholder="请输入关键词" value="{$keyword}" />
        <input type="button" class="st_home_btn btn_search" value="搜索" />
    </div><!--搜索-->
</div>
<div class="tabnav">

    <ul>

        <li><a href="javascript:;" id="destname" data-py="{$destpy}">{$destname}<i class="am-icon-caret-down"></i></a></li>
        <li><a href="javascript:;">综合排序<i class="am-icon-caret-down"></i></a></li>
        <li><a href="javascript:;">筛选<i class="am-icon-caret-down"></i></a></li>
    </ul>
</div>
<div class="tabcon">
    <div class="tablist">
        <div class="dest-mbx-dh" id="dest_nav" >
            <a data-id="0">目的地</a>&gt;
        </div>
        <ul id="dest_content">

        </ul>
    </div>
    <div class="tablist">

        <!--这里可根据typeid添加相应的排序规则-->
        <ul id="list-reorder">
            <li data-id="0" {if $sorttype==0}class="on"{/if} data-type="sorttype">默认</li>
            {if $typeid == "4" || $typeid == "6"}
            <li data-id="1" {if $sorttype==1}class="on"{/if} data-type="sorttype">点击量最高</li>
                {if $typeid == "4"}
                <li data-id="2" {if $sorttype==2}class="on"{/if} data-type="sorttype">编辑时间最新</li>
                {/if}
            {else}
            <li data-id="1" {if $sorttype==1}class="on"{/if} data-type="sorttype">价格从低到高</li>
            <li data-id="2" {if $sorttype==2}class="on"{/if} data-type="sorttype">价格从高到低</li>
            <li data-id="3" {if $sorttype==3}class="on"{/if} data-type="sorttype">销量最高</li>
            <li data-id="4" {if $sorttype==4}class="on"{/if} data-type="sorttype">产品推荐</li>
            {/if}
        </ul>
    </div>
    <div class="tablist">
        <ul id="list-way">
            <!--这里添加对应栏目的标识-->

            {if $cfg_startcity_open==1&&$typeid==1}
            <li data-id="city" class="city"  data-child="city" data-type="city">出发地</li>
            {/if}
            {if $typeid==1}
            <li  data-id="0" data-flag="rank" data-child="gday" data-ajax-div="lsit-child">行程天数</li>
            <li  data-id="0" data-flag="lineprice" data-child="gprice" data-ajax-div="lsit-child">价格</li>
            {/if}
            {if $typeid==2}
             <li  data-id="0" data-flag="rank" data-child="grank" data-ajax-div="lsit-child">星级</li>
             <li  data-id="0" data-flag="hotelprice" data-child="gprice" data-ajax-div="lsit-child">价格</li>
            {/if}
            {if $typeid==3}
            <li  data-id="0" data-flag="kind" data-child="gkind" data-ajax-div="lsit-child">车型</li>
            {/if}
            {if $typeid==5}
              <li  data-id="0" data-flag="spotprice" data-child="gprice" data-ajax-div="lsit-child">价格</li>
            {/if}
            {loop $attrlist $attr}
            <li  data-id="{$attr['id']}" data-flag="attr" data-child="gattr{$attr['id']}" data-ajax-div="lsit-child">{$attr['attrname']}</li>
            {/loop}
            {if $typeid==13}
            <li  data-id="0" data-flag="status" data-child="tuanstatus" data-ajax-div="lsit-child">团购状态</li>
            {/if}

        </ul>
        <ul id="lsit-child">

            <!--根据typeid来添加相应的参数-->
            {if $typeid==1}
                <li data-id="0" class="gday" data-type="dayid">全部</li>
                {st:line action="day_list"}
                    {loop $data $r}
                    <li  data-id="{$r['id']}" class="gday {if $dayid==$r['id']}on{/if}"  data-type="dayid" >{$r['title']}</li>
                    {/loop}
                {/st}

                <li data-id="0" class="gprice" data-type="priceid">全部</li>
                {st:line action="price_list"}
                    {loop $data $r}
                     <li  data-id="{$r['id']}" class="gprice {if $priceid==$r['id']}on{/if}"  data-type="priceid" >{$r['title']}</li>
                    {/loop}
                {/st}
            {/if}

            {if $typeid==2}
                <li data-id="0" class="grank" data-type="rankid">全部</li>
                {st:hotel action="rank_list"}
                  {loop $data $r}
                   <li  data-id="{$r['id']}" class="grank {if $rankid==$r['id']}on{/if}"   data-type="rankid" >{$r['title']}</li>
                  {/loop}
                {/st}

                 <li data-id="0" class="gprice" data-type="priceid">全部</li>
                {st:hotel action="price_list"}
                    {loop $data $r}
                     <li  data-id="{$r['id']}" class="gprice {if $priceid==$r['id']}on{/if}"  data-type="priceid" >{$r['title']}</li>
                    {/loop}
                {/st}
            {/if}
            {if $typeid==3}
                <li data-id="0" class="gkind" data-type="kindid">全部</li>
                {st:car action="kind_list"}
                    {loop $data $r}
                        <li  data-id="{$r['id']}" class="gkind {if $kindid==$r['id']}on{/if}"   data-type="kindid" >{$r['title']}</li>
                    {/loop}
                {/st}
            {/if}

              <!--景点价格筛选-->
             {if $typeid==5}
                <li data-id="0" class="gprice" data-type="priceid">全部</li>
                {st:spot action="price_list"}
                {loop $data $r}
                 <li  data-id="{$r['id']}" class="gprice {if $priceid==$r['id']}on{/if}"  data-type="priceid" >{$r['title']}</li>
                {/loop}
                {/st}
             {/if}

            {if $typeid==13}
                <li data-id="0" class="tuanstatus {if $status==0}on{/if}" data-type="status">进行中</li>
                <li data-id="1" class="tuanstatus {if $status==1}on{/if}" data-type="status">未开始</li>
            {/if}


                <!--属性组-->
                {loop $attrlist $attr}
                          <li data-id="0" class="gattr{$attr['id']}" data-type="attrid">全部</li>
                    {php}$attrArr =!empty($attrid) ? explode('_',$attrid) : array();{/php}
                    {st:attr action="query" flag="childitem" typeid="$typeid" groupid="$attr['id']"}
                        {loop $data $r}
                          <li  data-id="{$r['id']}"  class="gattr{$attr['id']} {if in_array($r['id'],$attrArr)}on{/if}" data-type="attrid" >{$r['attrname']}</li>
                        {/loop}
                    {/st}
                {/loop}
            {if $cfg_startcity_open==1&&$typeid==1}
                {loop  $startcitylist $citylist}
                    <li  data-id="{$citylist['id']}"  class="city {if $startcityid==$citylist['id']} on {/if} " data-type="startcityid"  >{$citylist['cityname']}</li>
                {/loop}
            {/if}
        </ul>
    </div>

</div>
<div class="tab_bottom_btn">
    <a class="cancel_btn" href="javascript:;">取消</a>
    <a class="sure_btn" href="javascript:;">确定</a>
</div>

<input type="hidden" id="destpy" value="{if $curdestpy}{$curdestpy}{else}all{/if}"/>
<input type="hidden" id="destname" value="{$destname}"/>
<input type="hidden" id="sorttype" value="{if $sorttype == ''}0{else}{$sorttype}{/if}"/>
<input type="hidden" id="attrid" value="{$attrid}"/>
{if $typeid==1}
<input type="hidden" id="priceid" value="{$priceid}"/>
<input type="hidden" id="dayid" value="{$dayid}"/>
<input type="hidden" id="startcityid" value="{$startcityid}"/>
{/if}

{if $typeid==2}
<input type="hidden" id="rankid" value="{$rankid}"/>
<input type="hidden" id="priceid" value="{$priceid}"/>
{/if}

{if $typeid==3}
<input type="hidden" id="kindid" value="{$kindid}"/>
{/if}
{if $typeid==5}
<input type="hidden" id="priceid" value="{$priceid}"/>
{/if}

<script>


    var py = '{$destpy}';
    var curdestpy='{$curdestpy}';
    var typeid = '{$typeid}';
    function DestaddClass(py,typeid,curdestpy){
        $.getJSON(SITEURL+'pub/ajax_get_destall',{destpy:py,'curdest':curdestpy,typeid:typeid},function(data){
            var navLen=data['nav'].length;
            var destLen=data['list'].length;
            var navHtml='';
            var destHtml='';
            for(var i=0;i<navLen;i++){
                if(i+1!=navLen){
                    navHtml+='<a class="dest_load" href="javascript:void(0)" data-py="'+data['nav'][i]['pinyin']+'">'+data['nav'][i]['kindname']+'</a> &gt;'
                }else{
                    navHtml+='<span>'+data['nav'][i]['kindname']+'</span>';
                }
            }
            $('#dest_nav').html(navHtml);
            for(var i=0;i<destLen;i++){
                if(data['list'][i]['haschild']){
                    destHtml+='<li class="dest_load" data-py="'+data['list'][i]['pinyin']+'">'+data['list'][i]['kindname']+'<i class="icon-right"></i></li>';
                }else{
                    var selected='';console.log(data['curDest'],data['list'][i]['pinyin']);
                    if(data['curDest']==data['list'][i]['pinyin']){
                        selected='class="icon-pitch-on"'
                    }
                    destHtml+='<li data-id="'+data['list'][i]['id']+'" class="hasnext" data-py="'+data['list'][i]['pinyin']+'" data-flag="dest">'+data['list'][i]['kindname']+'<i '+selected+'></i></li>';
                }
            }
            $('#dest_content').html(destHtml);
        })
    }

    $(function(){
        if($("#list-province li.on").length==0){
            $("#hot_dest").addClass('on');
        }
        var typeid = '{$typeid}';
        //选中上次选中的
        DestaddClass(py,typeid,curdestpy);
        //栏目下拉
        var $tabli = $('.tabnav ul li');
        var $tablist = $('.tablist');


        $tabli.click(function(){

            $('html,body').css({'height':'100%','overflow':'hidden'});
            var $hg = $(window).height();
            $('.tablist ul').css('height',$hg-161)
            $('.tab_bottom_btn').css('display','-webkit-box')

            var index = $($tabli).index(this)
            $(this).addClass('on').siblings().removeClass('on')
            $($tablist).parent('.tabcon').show()
            $($tablist).eq(index).show().siblings().hide()
        })

        //绑定获取下级事件.
        $("body").delegate(".hasnext",'click',function(){
            $("#destpy").val(py);

        });
       $("body").delegate('.dest_load','click',function(){
           var py=$(this).attr('data-py');
           $("#destpy").val(py);
           DestaddClass(py,typeid)
       });

        //热门目的地事件绑定
        $("body").delegate('#dest_content li,#dest_nav a','click',function(){
            var py = $(this).attr('data-py');
            $("#destpy").val(py);
            $(this).addClass('on').siblings().removeClass('on').find('i').removeClass('icon-pitch-on');
            $(this).find('i').not('.icon-right').addClass('icon-pitch-on');
        })
        //属性组点击事件.
        $("body").delegate("#lsit-child li",'click',function(){
            var datatype = $(this).attr('data-type');
            var id = $(this).attr('data-id');
            var childclass = $(this).attr('class')

            if(datatype == 'attr'){
                $('#'+datatype).val()
            }
            $('#'+datatype).val(id);

            $(this).addClass('on').siblings('.'+childclass).removeClass('on');
        })

        //排序子项点击Q
        $('#list-reorder').children('li').click(function(){
            var datatype = $(this).attr('data-type');
            var id = $(this).attr('data-id');
            $('#'+datatype).val(id);
            $(this).addClass('on').siblings().removeClass('on');
        })

        //隐藏属性筛选

        $("#lsit-child").find('li').hide();
        $("#list-way").find('li').click(function(){
           var childclass = $(this).attr('data-child');
           $("#lsit-child").find('li').hide();
           $('#lsit-child').find('.'+childclass).show();
           $(this).addClass('on').siblings().removeClass('on');
        })

       //取消选择
        $('.cancel_btn').click(function(){
            $('.tabcon').hide();
            $('.tab_bottom_btn').hide();
            $('html,body').css({'height':'auto'});
        })

        //默认选中第一个
        if($("#destpy").val() == '0' || $("#destpy").val() == '' || $("#destpy").val() == 'all'){
            $($(".tablist").get(0)).find('li').first().trigger('click');
        }

        //设定第3列属性的选中状态
        var firstChildChoosed=$("#lsit-child li.on:first");
        if(firstChildChoosed.length>=1)
        {
            var childCls = firstChildChoosed.attr('class');
            childCls= $.trim(childCls.replace('on',''));
            var childClsArr=childCls.split(' ');
            for(var i in childClsArr)
            {
                childClsArr[i]= $.trim(childClsArr[i]);
            }
            var hasFound=false;
            $("#list-way li").each(function(index,ele){
                   if(!hasFound)
                   {
                       var dataFlag = $(this).data('child');
                       if($.inArray(dataFlag,childClsArr)!=-1)
                       {
                           $(this).trigger('click');
                           hasFound=true;
                       }
                   }
            });
        }
        else
        {
            $("#list-way li:first").trigger('click');
        }
    })

 </script>

<script type="text/html" id="tpl_li_item">
    <li class="{{liclass}}" data-id="all" data-py="all" data-flag="{{flag}}" data-type="{{type}}">全部</li>
    {{each list as value i }}
    <li class="{{liclass}}" data-id="{{value.id}}" data-py="{{value.pinyin}}" data-flag="{{flag}}" data-type="{{type}}" data-ajax-div="{{ajaxdiv}}">{{value.kindname}}</li>
    {{/each}}
</script>

