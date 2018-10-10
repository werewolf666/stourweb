<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>模块管理--思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base_new.css'); }
    {php echo Common::getScript('ks-switch.pack.js,jquery.jqtransform.js,jqueryui/jquery-ui.min.js');}
    {php echo Common::getCss('jquery-ui.min.css','js/jqueryui/'); }
    {php echo Common::getScript("jquery.buttonbox.js,choose.js"); }

</head>

<body>

	<table class="content-tab">
        <tr>
            <td width="119px" class="content-lt-td"  valign="top">
                 {template 'stourtravel/public/leftnav'}
                 <!--右侧内容区-->
            </td>
            <td valign="top" class="content-rt-td">
                <div class="cfg-header-bar">
                    <span class="cfg-select-box btnbox mt-5 ml-10" id="custom_website" data-url="box/index/type/custom_website" data-result="result_webid">站点切换&nbsp;&gt;&nbsp;<span id="result_webid">主站</span><i class="arrow-icon"></i></span>
                    <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                    <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" id="modulelist"><s></s>管理模块</a>
                </div>

        <form id="configfrm" table_padding=HaJwOs >
         <div class="w-set-con">
          <div class="w-set-nr">
              <div class="module_con">
                  <div class="mod_column">
                      <div class="m_col_menu">
                          <div class="col_tit">网站页面{Common::get_help_icon('module_index_webpage')}</div>
                          <div class="menu_con channellist">
                              <h4>首页模块</h4>
                              <div class="h_con">
                                  <ul data-typeid="0">
                                      <li onclick="getSelectItem(0,this,'index')">网站首页</li>
                                  </ul>
                              </div>

                              {loop $module $row}
                              <h4>{$row['modulename']}模块</h4>
                              <div class="h_con">
                                  <ul data-typeid="{$row['id']}">
                                      <li onclick="getSelectItem({$row['id']},this,'index')">栏目首页</li>
                                      <li onclick="getSelectItem({$row['id']},this,'show')">栏目详细页</li>
                                      <li onclick="getSelectItem({$row['id']},this,'search')">栏目搜索页</li>
                                  </ul>
                              </div>
                              {/loop}
                          </div>
                      </div>
                      <div class="m_col_add">
                          <div class="add_tit">已添加模块{Common::get_help_icon('module_index_added_module')}</div>
                          <ul id="selectlist">

                          </ul>
                      </div>
                  </div>
                  <div class="mod_list">
                      <dl class="mod_tab_dl alllist">
                          <dt>
                              <span class="on">信息模块{Common::get_help_icon('module_index_xinxi_module')}</span>
                              <span>广告模块{Common::get_help_icon('module_index_guanggao_module')}</span>
                          </dt>
                          <dd>
                              <ul id="info_module_list">

                              </ul>
                          </dd>
                          <dd>
                              <ul id="ad_module_list">

                              </ul>
                          </dd>
                      </dl>
                  </div>

              </div>


            <div class="opn-btn">
                <!--<a class="save" href="javascript:;" id="btn_save">保存</a>
                <a class="cancel" href="#">取消</a>-->
            </div>

          </div>
        </div>
        </form>
  </td>
  </tr>
  </table>
    <input type="hidden" id="webid" value="0">
    <input type="hidden" id="pageid" value="0">
    <input type="hidden" id="typeid" value="0">
    <input type="hidden" id="shortname" value="">
	<script>
	//窗口改变
/*	$(window).resize(function(){
		 Config.setDivAttr();
    });*/
	$(document).ready(function(){

        $(".btnbox").buttonBox();



        $('form').jqTransform({imgPath:'../images/img/'});
        $('.mod_tab_dl').switchTab();
        init();//初始化
        $("#modulelist").click(function(){
            ST.Util.addTab('模块列表',SITEURL+'module/list/parentkey/templet/itemid/2/{if isset($_GET['menuid'])}menuid/{$_GET['menuid']}/{/if}')
        })






		});


    function  custom_website(obj,webid,webname,resultid) {
        $("#"+resultid).html(webname);
        $(obj).addClass('cur').siblings().removeClass('cur');
        $('#webid').val(webid);
        init();
    }




    //初始化
        function init()
        {
            $(".channellist").find('ul').each(function(i,obj){

                var webid=$("#webid").val();
                var typeid=$(obj).attr('data-typeid');
               /* if(typeid!=''){
                    getPageList(webid,typeid,obj);
                }*/

            });
            $( ".channellist" ).find('h4').click(function(){
                $(".h_con").hide();
                $(this).addClass('cur').siblings().removeClass('cur');
                $(this).next('div').show();
                $(this).next('div').find('li').first().trigger('click');//第一个点击

            });
            $( ".channellist" ).find('h4').first().trigger('click');//默认展开第一个.

            $("#selectlist").html('');//清除已选择项

            getAllItem();//获取全部项
        }

        //获取pagelist
     /*   function getPageList(webid,typeid,obj)
        {

            $.ajax({
                type:'POST',
                data:{webid:webid,typeid:typeid},
                url:SITEURL+'module/ajax_getpagelist/',
                dataType:'json',
                async:false,
                success:function(data){

                    $(obj).html('');
                    $.map(data, function(o) {

                        var li = "<li onclick='getSelectItem("+ o.id +",this)' id='nav_"+ o.id+"'>"+ o.pagename+"</li>";
                        $(obj).append(li);

                    });


                }
            })
        }*/
        //获取选择的项
        function getSelectItem(typeid,obj,shortname)
        {


            var webid=$("#webid").val();
            var items=[];
            $.ajax(
                {
                    type: "post",
                    data: {typeid:typeid,webid:webid,shortname:shortname},
                    dataType:'json',
                    url: SITEURL+"module/ajax_getselect",
                    success: function(data,textStatus)
                    {
                        $("#selectlist").html('');//清除已选择项
                        $(obj).addClass('on').siblings().removeClass('on');

                        $("#typeid").val(typeid);//设置当前typeid
                        $('#shortname').val(shortname);//英文简称


                        $.map(data.selectlist,function(o){
                            var li="<li><s id="+ o.aid+" onclick='del(this)'></s>"+o.modulename+"</li>";
                            $("#selectlist").append(li);
                        })

                        $( "#selectlist" ).sortable({
                            opacity: 0.6, //设置拖动时候的透明度
                            revert: true, //缓冲效果
                            cursor: 'move', //拖动的时候鼠标样式
                            update:updateSort

                        });

                        $( "#selectlist" ).droppable({
                            accept: ".item"

                        });

                        //指向样式
                        $("#selectlist").find('li').hover(
                            function(){
                              $(this).addClass('cur');
                            },
                            function(){
                              $(this).removeClass('cur');
                            }
                        )

                    },
                    error: function()
                    {
                        ST.Util.showMsg("请求出错,请联系管理员",5,1000);
                    }

                }
            );
        }
        //获取所有模块列表
    function getAllItem()
    {


        var webid=$("#webid").val();
        $("#info_module_list").html('');
        $("#ad_module_list").html('');

        $.ajax(
            {
                type: "post",
                data: {webid:webid},
                dataType:'json',
                url: SITEURL+"module/ajax_getallmodule",
                success: function(data,textStatus)
                {

                    $.map(data,function(o){
                        var li="<li class=\"item\" id="+ o.aid+">"+o.modulename+"</li>";
                        if(o.type==0)
                        {
                            $("#info_module_list").append(li);
                        }
                        else
                        {
                            $("#ad_module_list").append(li);
                        }

                    })


                    $( ".item" ).draggable({
                        helper:"clone",
                        connectToSortable: "#selectlist",
                        start:function(event,ui,offset){
                            var myui = ui;
                            var obj = myui.helper.context;
                            var id = obj.id;
                            var text = $(obj).html();

                            var timeid = Date.parse(new Date());
                            var c='cid_'+timeid;
                            $(obj).addClass(c);
                            var html = "<s id="+ id+" onclick='del(this)'></s>"+text;
                            $(obj).removeClass('item');
                            $(obj).html(html);
                            $(obj).attr('data-id',c);
                            ui = myui;
                            items = getSid(); //拖动前获取要选择的项

                        },
                        drag:function(event,ui){


                            if($.inArray(ui.helper.context.id,items)!=-1)
                            {

                                $("#selectlist").find('li').each(function(i,obj){
                                    var id = $(obj).attr('data-id');
                                    if(id == $(ui.helper.context).attr('data-id'))
                                    {
                                        $(obj).remove();
                                        return false;//跳出循环

                                    }

                                })
                                ST.Util.showMsg("已经存在此模块",5,1000);
                                return false;
                            }
                            else
                            {
                                 updateSort();//更新排序

                            }


                        },
                        stop:function(event,ui){
                            $(ui.helper.context).addClass('item');
                            $(ui.helper.context).find('s').remove();
                        }
                    });
                    //鼠标指向样式
                    $(".mod_list").find('li').hover(
                        function(){
                            $(this).addClass('on');
                        },
                        function(){
                            $(this).removeClass('on');

                        }
                    )



                },
                error: function()
                {
                    ST.Util.showMsg("请求出错,请联系管理员",5,1000);
                }

            }
        );
    }


    //删除当前模块
    function del(obj)
    {
        ST.Util.confirmBox('删除模块','确认要删除吗?',function(){

            $(obj).parents('li').remove();
            updateSort();

        });

    }
   //获取已有模块列表
    function getSid()
    {
       var items =[];

        $("#selectlist").find('li').each(function(i,obj){
             var id = $(obj).find('s').attr('id');
             if(id!==undefined)
             {
                 items.push(id);
             }
        })
        return items;
    }
    //更新排序
    function updateSort()
    {
        //items.length = 0//清空数组
        var itemlist = getSid();//获取排序
        var webid=$("#webid").val();
        var orderlist=itemlist.join(',');//新的排序
        //var pageid=$("#pageid").val();
       var typeid = $("#typeid").val();
        var shortname = $("#shortname").val();
        $.ajax({
            type: "post",
            url: SITEURL+"module/ajax_updatesort/", //服务端处理程序
            data: {webid:webid,typeid:typeid,shortname:shortname,orderlist:orderlist},
            beforeSend: function() {
            },
            success: function(msg,textStatus) {
                // alert(msg);
                //$show.html("");
            }
        });

    }









    </script>

</body>
</html>
