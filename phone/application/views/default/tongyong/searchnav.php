<div class="foot-menu">
    <div id="dest-item" class="check-item fl" data-floor="dest">
        <span class="check-hd"><i class="mdd-icon"></i>目的地</span>
    </div>
    <div id="sort-item" class="check-item fl" data-floor="sort">
        <span class="check-hd"><i class="px-icon"></i>排序</span>
    </div>
    <div id="filter-item" class="check-item fl" data-floor="filter">
        <span class="check-hd"><i class="sx-icon"></i>筛选</span>
    </div>
</div>

<div id="dest-page" class="dest-page first-floor">
    <div class="header-top bar-nav">
        <a class="back-link-icon" href="javascript:;"></a>
        <h2 class="page-title-bar">目的地</h2>
    </div>
    <div class="dest-crumbs">

    </div>
    <div class="dest-group">

    </div>
    <div class="control-block">
        <a class="back-btn" href="javascript:;" id="dest_back_btn">返回上一级</a>
        <a class="confirm-btn" href="javascript:;">确定</a>
    </div>
</div>
<!-- 目的地 -->

<div id="filter-page" class="filter-page first-floor">
    <div class="header-top bar-nav bar_nav">
        <a class="back-link-icon" href="javascript:;"></a>
        <h2 class="page-title-bar">筛选</h2>
    </div>
    <div class="filter-item">
        <div class="hd">
            <ul>
                {st:attr action="query" flag="grouplist" row="1000" typeid="$typeid" return="p_attrlist"}
                {loop $p_attrlist $p_attr}
                    <li data-sub="attrid_{$p_attr['id']}">{$p_attr['attrname']}</li>
                {/loop}
            </ul>
        </div>
        <div class="bd">
            {loop $p_attrlist $pattr}
                 <ul id="sub_attrid_{$pattr['id']}" class="sub_attrid">
                     <li data-id="0" selfield="attrid_{$pattr['id']}" class="active">全部<i class="on"></i></li>
                    {st:attr action="query" flag="childitem" row="1000" typeid="$typeid" groupid="$pattr['id']" return="attrlist"}
                    {loop $attrlist $attr}
                        <li data-id="{$attr['id']}" selfield="attrid_{$pattr['id']}">{$attr['attrname']}<i class="on"></i></li>
                    {/loop}
                 </ul>
                     {/st}
            {/loop}
            <!--活动状态-->
        </div>
    </div>
    <div class="control-block">
        <a class="back-btn" href="javascript:;" id="filter_back_btn">恢复默认</a>
        <a class="confirm-btn" href="javascript:;">确定</a>
    </div>
</div>
<!-- 筛选 -->

<div id="sort-page" class="sort-page first-floor">
    <ul class="sort-group">
        <li class="active" data-id="0" selfield="sorttype">综合排序<em class="ico"></em></li>
        <li data-id="1" selfield="sorttype">价格由低到高<em class="ico"></em></li>
        <li data-id="2" selfield="sorttype">价格由高到低<em class="ico"></em></li>
        <li data-id="3" selfield="sorttype">销量优先<em class="ico"></em></li>
        <li data-id="4" selfield="sorttype">人气推荐<em class="ico"></em></li>
    </ul>
</div>

<script>
    //选中项缓存
    var cache_new_params={};
    $(document).ready(function(){
        var typeid="{$typeid}";
        //目的地缓存
        var cache_parents={};
        //初始参数缓存
        var cache_init_params={};

        //实始化参数
        if(typeof(get_init_fields)=='function')
        {
            cache_init_params = cache_new_params= get_init_fields();
        }
        get_next_dests(cache_init_params['destpy']);



        $("html,body").css("height","100%");
        //弹出菜单
         $(".foot-menu .check-item").click(function(){
              var floor = $(this).data('floor');
              $('.'+floor+'-page').show();
              $('.'+floor+'-page').siblings('.first-floor').hide();
         });

         //杂项切换
         $("#filter-page .hd ul li").click(function(){
              $(this).addClass('active');
              $(this).siblings().removeClass('active');
              var sub = $(this).data('sub');
              $("#sub_"+sub).show();
              $("#sub_"+sub).siblings().hide();
         });
         //默认选中一个
        $("#filter-page .hd ul li:first").trigger('click');

        $(".first-floor .back-link-icon").click(function(){
             $(this).parents('.first-floor:first').hide();
        });
        //隐藏排序
        $("#sort-page").on("click",function(){
            $(this).hide();
        });


        //确定按钮点击
        $(".first-floor .confirm-btn").click(function(){
             go_search();
        });



        //目的地下一级
        $(document).on('click','.dest-group ul li',function(){
            var destpy=$(this).data('id');
             if($(this).find('.more').length>0)
             {
                 get_next_dests(destpy);
             }

             if($(this).hasClass('active'))
             {
                 $(this).removeClass('active');
                 cache_new_params['destpy']=0;
             }
             else
             {
                 $(".dest-group ul li").removeClass('active');
                 $(this).addClass('active');
                 cache_new_params['destpy']=destpy;
             }
        })

        //排序项点击
        $(".sort-page ul li[selfield]").click(function(){
            $(this).siblings().removeClass('active');
            var field=$(this).attr('selfield');
            var id = $(this).data('id');
            if($(this).hasClass('active'))
            {
                $(this).removeClass('active');
                cache_new_params[field]=0;
            }
            else
            {
                $(this).addClass('active');
                cache_new_params[field]=id;
            }
            go_search();
        });

        //杂项点击选择
          $(".filter-item .bd ul li[selfield]").click(function(){
            $(this).siblings().removeClass('active');
            var field=$(this).attr('selfield');
            var id = $(this).data('id');
            if($(this).hasClass('active'))
            {
                $(this).removeClass('active');
                cache_new_params[field]=0;
            }
            else
            {
                $(this).addClass('active');
                cache_new_params[field]=id;
            }
        });



        //返回上一级
        $("#dest_back_btn").click(function(){
             var pinyin=$(".dest-group .dest-list:visible").data('destpy');
             if($(".dest_item_"+pinyin).length>0)
             {
                 $(".dest_item_"+pinyin).parent().show();
                 $(".dest_item_"+pinyin).parent().siblings().hide();
                 var p_pinyin =  $(".dest_item_"+pinyin).parent().data('destpy');
                 if(p_pinyin=='all')
                 {
                     $("#dest_back_btn").hide();
                 }
                 var parents=cache_parents[p_pinyin];
                 gene_dest_crums(parents);
             }
             else
             {
                 get_next_dests(pinyin,1);
             }
        });

        //恢复默认
        $("#filter_back_btn").click(function () {
            window.clear_search_condition = true;
            refresh_selected(null, '.filter-item .bd ul li[selfield]');
            for (var key in cache_new_params) {
                if (key.indexOf('attrid') != -1) {
                    cache_new_params[key] = 0;
                }
            }
            window.clear_search_condition = false;
        });

        //初始化数据
        refresh_selected();


        //初始化选中项
        function refresh_selected(field,selector)
        {
            if(typeof(get_init_fields)=='function')
            {
                var params = get_init_fields();
                var attrid = params['attrid'];
                params['attrid'] = attrid? attrid.split('_'):'';
                params['destpy'] = !params['destpy']||params['destpy']=='all'? 0:params['destpy'];

                var selector_str=selector;
                if(!selector)
                {
                    selector_str = field ? ".first-floor ul li[selfield='" + field + "']" : ".first-floor ul li[selfield]";
                }
                $(selector_str).each(function(){
                       var fieldname = $(this).attr('selfield');
                       var val = $(this).data('id');
                       val = String(val);
                       var is_selected=false;
                       if(fieldname.indexOf('attrid_')==-1)
                       {
                           if(val==params[fieldname] && val!='0')
                           {
                               $(this).addClass('active');
                               $(this).siblings().removeClass('active');
                           }
                           else if(val!='0' && val!=params[fieldname])
                           {
                               $(this).removeClass('active');
                           }

                       }
                       else
                       {
                           if($.inArray(val,params['attrid'])!=-1)
                           {
                               $(this).addClass('active');
                               $(this).siblings().removeClass('active');
                               cache_new_params[fieldname] = val;
                           }
                           else if(val!=='0' && $.inArray(val,params['attrid'])==-1)
                           {
                               $(this).removeClass('active');
                           }
                       }

                       if(val=='0' && (params[fieldname]==0 || !params[fieldname]))
                       {
                           $(this).addClass('active');
                           $(this).siblings().removeClass('active');
                       }

                       if(selector)
                       {
                           cache_new_params[fieldname]=cache_init_params[fieldname];
                       }
                });

            }
        }


        //获取下级目的地，没有则获取同级
        function get_next_dests(pinyin,isparent)
        {
            pinyin = !pinyin?'all':pinyin;
            if(pinyin=='all')
            {
                $("#dest_back_btn").hide();
            }
            if($("#dests_"+pinyin).length>0 && !isparent)
            {
                $("#dests_"+pinyin).show();
                $("#dests_"+pinyin).siblings('ul').hide();
                if(pinyin!='all')
                {
                    $("#dest_back_btn").show();
                }
                gene_dest_crums(cache_parents[pinyin]);
                return;
            }
            $.ajax({
                url:SITEURL+'{$pinyin}/ajax_get_next_dests',
                type:'POST',
                data:{
                    destpy:pinyin,
                    typeid:typeid,
                    isparent:isparent
                },
                dataType:'json',
                success:function(data){

                    if(data.list && data.list.length>0)
                    {
                        var parent=data.parent;
                        parent =!parent?{id:0,pinyin:'all',kindname:'目的地'}:parent;
                        var html='<ul id="dests_'+parent['pinyin']+'" class="dest-list" data-destpy="'+parent['pinyin']+'">';
                        html+=parent['pinyin']=='all'?'<li data-id="all">全部</li>':'';
                        for(var i in data.list)
                        {
                            var row=data.list[i];
                            var tag=row.subnum>0?'<i class="more"></i>':'<i class="on"></i>';
                            html+='<li data-id="'+row['pinyin']+'" selfield="destpy" class="dest_item_'+row['pinyin']+'">'+row['kindname']+tag+'</li>';
                        }
                        html+="</ul>";
                        var ele = $(html);
                        $("#dest-page .dest-group").append(ele);
                        ele.siblings().removeClass('cur').hide();
                        if(parent['pinyin']!='all')
                        {
                            $("#dest_back_btn").show();
                        }
                        refresh_selected('destpy');
                    }
                    gene_dest_crums(data.parents);
                    if(data.parents)
                    {
                        cache_parents[parent['pinyin']]=data.parents;
                    }
                }
            })
        }

        //生成目的地面包
        function gene_dest_crums(parents)
        {
            var crums_html='<a href="javascript:;">全部</a>';
            if(parents)
            {
                for(var i in parents)
                {
                    var row = parents[i];
                    crums_html+='<a href="javascript:;" data-id="'+row['id']+'" data-pinyin="'+row['pinyin']+'">'+row['kindname']+'</a>';
                }
            }
            $("#dest-page .dest-crumbs").html(crums_html);
            $("#dest-page .dest-crumbs a").click(function(){
                var pinyin = $(this).attr('data-pinyin');
                pinyin = !pinyin?'all':pinyin;
                get_next_dests(pinyin,0);
            });
        }

        function go_search()
        {
            if(typeof(on_selected_search)=='function')
            {
                var attrids=[];
                for(var key in cache_new_params)
                {
                    if(key.indexOf('attrid_')!=-1 && cache_new_params[key])
                    {
                        attrids.push(cache_new_params[key]);
                    }
                }
                if(attrids.length>0)
                {
                    cache_new_params['attrid']=attrids.join('_');
                }
              on_selected_search(cache_new_params);
            }
        }

    });

</script>