/**
 * Created by Administrator on 15-9-25.
 */
(function($){

    var dest = {};

    dest.getnext = get_next;

    function get_next(obj,typeid){
        var flag = $(obj).attr('data-flag');
        var id = $(obj).attr('data-id');
        var ajaxdiv = $(obj).attr('data-ajax-div');
        var py = $(obj).attr('data-py');

        $(obj).addClass('on').siblings().removeClass('on');
        //读取热门线路
        if(flag == 'desthot' || flag == 'dest'){

             $("#destpy").val(py);

            //如果不是最后一级
            if($(obj).parents('ul').attr('id')!='list-spot'){
                if(ajaxdiv == 'list-city'){
                    $("#list-city").html('');
                    $("#list-spot").html('');
                }

                //目的地获取
                $.getJSON(SITEURL+'pub/ajax_get_dest',{flag:flag,destid:id,typeid:typeid},function(data){
                    var html = template('tpl_li_item',data);
                    $('#'+ajaxdiv).html(html);

                })
            }

        }else if(flag == 'attr'){//属性组获取
            $("#attrid").val(id);
            $.getJSON(SITEURL+'pub/ajax_get_attr',{attrid:id,typeid:typeid},function(data){
                var html = template('tpl_li_item',data);
                $('#'+ajaxdiv).html(html);

            })
        }else if(flag == 'rank'){
            $.getJSON(SITEURL+'pub/ajax_hotel_rank',{typeid:typeid},function(data){
                var html = template('tpl_li_item',data);
                $('#'+ajaxdiv).html(html);

            })
        }else if(flag == 'hotelprice'){
            $.getJSON(SITEURL+'pub/ajax_hotel_pricelist',{typeid:typeid},function(data){
                var html = template('tpl_li_item',data);
                $('#'+ajaxdiv).html(html);

            })
        }

    }

    window.STDEST = dest;

})(jQuery)