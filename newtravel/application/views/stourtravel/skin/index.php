<!doctype html>
<html>
<head font_background=ziElxl >
<meta charset="utf-8">
<title>皮肤设置</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base_new.css'); }
    {php echo Common::getScript('config.js,jquery.colorpicker.js');}
   <style>
       .color_info{
           border: none;
           margin-top: 10px;
       }
       .color_info tr td {
           padding: 10px 0;
       }
       .grey{
           color: #c6c6c6
       }
   </style>
</head>

<body>

    <table class="content-tab">
        <tr>
            <td width="119px" class="content-lt-td"  valign="top">
                {template 'stourtravel/public/leftnav'}
                <!--右侧内容区-->
            </td>
            <td valign="top" class="content-rt-td">
                <form id="skinfrm">
                    <div class="w-set-con">
                        <div class="cfg-header-bar">
                            <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                        </div>
                        <div class="clear">
                            <div class="clear clearfix">
                                <ul class="info-item-block">
                                    <li>
                                        <span class="item-hd">皮肤方案：</span>
                                        <div class="item-bd">
                                            {loop $skinlist $skin}
                                            <label class="skinitem radio-label mr-20" data-skinid="{$skin['id']}">
                                                <input type="radio" id="skin_id_{$skin['id']}"  name="cfg_skin_id" data-issystem="{$skin['is_system']}" value="{$skin['id']}" {if $cfg_skin_id==$skin['id']}checked{/if}>
                                                <span for="skin_id_{$skin['id']}">{$skin['title']}<span style=" display: inline-block; width: 50px; height: 20px; vertical-align: middle; margin-left: 5px; margin-top: -3px; background-color: {$skin['main_color']}"></span></span>
                                            </label>
                                            {/loop}
                                        </div>
                                    </li>
                                </ul>
                                <div class="rowElem ml-115">
                                    {loop $skinlist $skin}
                                    <table id="color_info_{$skin['id']}" class="color_info" style="{if $cfg_skin_id!=$skin['id']}display: none{/if}">
                                      <tr>
                                          <td>站点主背景色</td>
                                          <td>
                                              {if $skin['is_system'] == 1}
                                                <span style="color:{$skin['main_color']} ">{$skin['main_color']}</span>
                                              {else}
                                                <input type="text" name="main_color" class="select_color"  style="color:{$skin['main_color']} " value="{$skin['main_color']}"/>
                                              {/if}
                                          </td>
                                          <td class="grey">*模块标题背景颜色、详情页栏目页签背景色等</td>
                                      </tr>
                                      <tr>
                                          <td>图标颜色</td>
                                          <td>
                                              {if $skin['is_system'] == 1}
                                                <span style="color:{$skin['icon_color']} ">{$skin['icon_color']}</span>
                                              {else}
                                                <input type="text" name="icon_color" class="select_color"  style="color:{$skin['icon_color']} " value="{$skin['icon_color']}"/>
                                              {/if}
                                          </td>
                                          <td class="grey">*首页、线路详情页、邮轮详情页、会员中心部分图标</td>
                                      </tr>
                                      <tr>
                                          <td>线条颜色</td>
                                          <td>
                                              {if $skin['is_system'] == 1}

                                                <span style="color:{$skin['line_color']} ">{$skin['line_color']}</span>
                                              {else}
                                                <input type="text" name="line_color" class="select_color"  style="color:{$skin['line_color']} " value="{$skin['line_color']}"/>
                                              {/if}

                                          </td>
                                          <td class="grey">*首页、栏目首页栏目线条等</td>
                                      </tr>
                                      <tr>
                                          <td>文字颜色</td>
                                          <td>
                                              {if $skin['is_system'] == 1}
                                                <span style="color:{$skin['font_color']} ">{$skin['font_color']}</span>
                                              {else}
                                                <input type="text" name="font_color" class="select_color"  style="color:{$skin['font_color']} " value="{$skin['font_color']}"/>
                                              {/if}

                                          </td>
                                          <td class="grey">*首页、栏目首页标题文字等</td>
                                      </tr>
                                      <tr>
                                          <td>移入文字颜色</td>
                                          <td>
                                              {if $skin['is_system'] == 1}
                                                <span style="color:{$skin['font_hover_color']} ">{$skin['font_hover_color']}</span>
                                              {else}
                                                <input type="text" name="font_hover_color" class="select_color"  style="color:{$skin['font_hover_color']} " value="{$skin['font_hover_color']}"/>
                                              {/if}
                                          </td>
                                          <td class="grey">*产品文字鼠标移上去显示的颜色</td>
                                      </tr>

                                      <tr>
                                          <td>导航条颜色</td>
                                          <td>
                                              {if $skin['is_system'] == 1}
                                                <span style="color:{$skin['nav_color']} ">{$skin['nav_color']}</span>
                                              {else}
                                                <input type="text" name="nav_color" class="select_color"  style="color:{$skin['nav_color']} " value="{$skin['nav_color']}"/>
                                              {/if}
                                          </td>
                                          <td class="grey">*主导航背景底色</td>
                                      </tr>
                                      <tr>
                                          <td>移入导航条颜色</td>
                                          <td>
                                              {if $skin['is_system'] == 1}
                                                <span style="color:{$skin['nav_hover_color']} ">{$skin['nav_hover_color']}</span>
                                              {else}
                                                <input type="text" name="nav_hover_color" class="select_color"  style="color:{$skin['nav_hover_color']} " value="{$skin['nav_hover_color']}"/>
                                              {/if}
                                          </td>
                                          <td class="grey">*鼠标移入导航菜单显示的颜色</td>
                                      </tr>
                                      <tr>
                                          <td>底部分栏背景色</td>
                                          <td>
                                              {if $skin['is_system'] == 1}
                                                <span style="color:{$skin['footer_level_color']} ">{$skin['footer_level_color']}</span>
                                              {else}
                                                <input type="text" name="footer_level_color" class="select_color"  style="color:{$skin['footer_level_color']} " value="{$skin['footer_level_color']}"/>
                                              {/if}
                                          </td>
                                          <td class="grey">*底部分栏背景色</td>
                                      </tr>
                                      <tr>
                                          <td>自定义导航底色</td>
                                          <td>
                                              {if $skin['is_system'] == 1}
                                                <span style="color:{$skin['usernav_color']} ">{$skin['usernav_color']}</span>
                                              {else}
                                                <input type="text" name="usernav_color" class="select_color"  style="color:{$skin['usernav_color']} " value="{$skin['usernav_color']}"/>
                                              {/if}
                                          </td>
                                          <td class="grey">*自定义导航菜单栏底色，未开启自定义导航，此配色无效</td>
                                      </tr>
                                  </table>
                                    {/loop}
                                </div>
                            </div>
                            <div class="clear clearfix mt-20">
                                <input type="hidden" id="skinid" value="{$cfg_skin_id}">
                                <a class="btn btn-primary radius size-L ml-115" href="javascript:;" id="btn_save">保存</a>
                            </div>
                        </div>
                    </div>
                </form>
            </td>
        </tr>
    </table>

	<script>

	$(document).ready(function(){

        //显示皮肤详细颜色配置
        $('.skinitem').click(function(){
            var skinid = $(this).data('skinid');
            $('.color_info').hide();
            $('#color_info_'+skinid).show();
            $("#skinid").val(skinid);

        });

        //颜色选择器
        $(".select_color").colorpicker({
            ishex:true,
            success:function(o,color){
                $(o).val(color)
            },
            reset:function(o){

            }
        });

        $(".select_color").change(function(){
            $(this).css('color',$(this).val());
        });


        //配置信息保存
        $("#btn_save").click(function(){

            //检测是否自定义颜色,自定义颜色参数必须全部填写
            var flag = 1;
            var skin_id = $("#skinid").val() ;
            if(skin_id == 8){
                $("input[type='text']").each(function(i,obj){
                    var val=$(obj).val();
                    if($(obj).attr('id')!='DisColor'&& trim(val)==''){
                        $(obj).focus();
                        ST.Util.showMsg('自定义皮肤颜色参数必须全部填写',5);
                        flag = 0;
                        return false;
                    }
                });
            }
            if(!flag){
                return false;
            }

            var url = SITEURL+"skin/ajax_save";
            var frmdata = $("#skinfrm").serialize();
            $.ajax({
                type:'POST',
                url:url,
                dataType:'json',
                data:frmdata,
                success:function(data){
                    if(data.status==true)
                    {
                        ST.Util.showMsg('保存成功',4);
                    }

                }
            })

        });

    });

    //去空格
    function trim(str) {
        return str.replace(/(^\s*)|(\s*$)/g, "");
    }










    </script>

</body>
</html>
