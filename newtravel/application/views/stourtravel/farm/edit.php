<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>农家乐添加/修改</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css'); }
    {php echo Common::getScript('config.js');}
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js,choose.js,product_add.js,imageup.js,jquery.validate.js,st_validate.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
</head>

<body>
	<table class="content-tab">
    <tr>
    <td width="119px" class="content-lt-td"  valign="top">
     {template 'stourtravel/public/leftnav'}
    <!--右侧内容区-->
    </td>
     <td valign="top" class="content-rt-td">

          <form method="post" name="product_frm" id="product_frm">
          <div class="manage-nr">
              <div class="w-set-tit bom-arrow" id="nav">
                  <span class="on"><s></s>基础信息</span>
                 <!-- <span data-id="tupian"><s></s>农家乐图片</span>
                  <span data-id="jieshao"><s></s>农家乐介绍</span>
                  <span data-id="fuwu"><s></s>服务项目</span>
                  <span data-id="jiaotong"><s></s>交通指南</span>
                  <span data-id="zhoubian"><s></s>周边景点</span>
                  <span data-id="zhuyi"><s></s>注意事项</span>
                  -->
                  {loop $columns $column}
                  <span data-id="{$column['columnname']}"><s></s>{$column['chinesename']}</span>
                  {/loop}
                  <span data-id="youhua"><s></s>优化设置</span>
                  <span data-id="extend"><s></s>扩展设置</span>
                  <a href="javascript:;" class="refresh-btn" onclick="window.location.reload()">刷新</a>
              </div>

               <!--基础信息开始-->
                  <div class="product-add-div">
                      <div class="add-class">
                          <dl>
                              <dt>站点：</dt>
                              <dd>

                                      <select name="webid">
                                          <option value="0" {if $info['webid']==0}selected="selected"{/if}>主站</option>
                                          {loop $weblist $k}
                                            <option value="{$k['webid']}" {if $info['webid']==$k['webid']}selected="selected"{/if} >{$k['webname']}</option>
                                          {/loop}

                                      </select>

                              </dd>
                          </dl>
                          <dl>
                              <dt>农家乐名称：</dt>
                              <dd>
                                  <input type="text" name="title" id="hotelname" class="set-text-xh text_700 mt-2"  value="{$info['title']}" />
                                  <div class="help-ico mt-9 ml-5">{php echo Common::getIco('help',40); }</div>
                              </dd>
                          </dl>
                          <dl>
                              <dt>农家乐卖点：</dt>
                              <dd>
                                  <input type="text" name="sellpoint" value="{$info['sellpoint']}"  class="set-text-xh text_700 mt-2"/>

                                  <div class="help-ico mt-9 ml-5">{php echo Common::getIco('help',12);}</div>
                              </dd>
                          </dl>
                          <dl>
                              <dt>农家乐地址：</dt>
                              <dd>
                                  <input type="text" name="address" id="address" class="set-text-xh text_700 mt-2" value="{$info['address']}" />
                                  <div class="help-ico mt-9 ml-5">{php echo Common::getIco('help',40); }</div>
                              </dd>
                          </dl>
                          <dl>
                              <dt>农家乐坐标：</dt>
                              <dd>
                                  <span class="fl">经度(Lng):</span>
                                  <input type="text" name="lng" id="lng"  class="set-text-xh text_150 mt-2 ml-10 mr-30 w300" value="{$info['lng']}" />
                                  <span class="fl">纬度(Lat):</span>
                                  <input type="text" name="lat" id="lat" class="set-text-xh text_150 mt-2 ml-10 mr-30 w300" value="{$info['lat']}"  />
                                  <a href="javascript:;" class="choose-btn mt-4" onclick="Product.Coordinates(700,500)"  title="选择">选择</a>
                              </dd>

                          </dl>
                          <dl>
                              <dt>联系电话：</dt>
                              <dd>
                                  <input type="text" name="telephone" id="telephone" class="set-text-xh text_250 mt-2" value="{$info['telephone']}" />
                              </dd>
                          </dl>
                          <dl>
                              <dt>开业时间：</dt>
                              <dd>
                                  <input type="text" name="opentime" id="opentime" class="set-text-xh text_250 mt-2" value="{$info['opentime']}" />
                              </dd>
                          </dl>
                          <dl>
                              <dt>装修时间：</dt>
                              <dd>
                                  <input type="text" name="decoratetime" id="decoratetime" class="set-text-xh text_250 mt-2" value="{$info['decoratetime']}" />
                              </dd>
                          </dl>
                          <dl>
                              <dt>供应商：</dt>
                              <dd>
                                  <a href="javascript:;" class="choose-btn mt-4" onclick="Product.getSupplier(this,'.supplier-sel')" title="选择">选择</a>
                                  <div class="save-value-div mt-2 ml-10 supplier-sel">
                                      {if !empty($info['supplier_arr']['suppliername'])}
                                      <span><s onclick="$(this).parent('span').remove()"></s>{$info['supplier_arr']['suppliername']}<input type="hidden" name="supplierlist[]" value="{$info['supplier_arr']['id']}"></span>
                                      {/if}
                                  </div>
                              </dd>
                          </dl>
                      </div>

                      <div class="add-class">
                          <dl>
                              <dt>目的地选择：</dt>
                              <dd>
                                  <a href="javascript:;" class="choose-btn mt-4" onclick="Product.getDest(this,'.dest-sel',102)" title="选择">选择</a>
                                  <div class="save-value-div mt-2 ml-10 dest-sel">
                                      {loop $info['kindlist_arr'] $k $v}

                                       <span class="{if $info['finaldestid']==$v['id']}finaldest{/if}" title="{if $info['finaldestid']==$v['id']}最终目的地{/if}" ><s onclick="$(this).parent('span').remove()"></s>{$v['kindname']}<input type="hidden" class="lk" name="kindlist[]" value="{$v['id']}"/>
                                           {if $info['finaldestid']==$v['id']}<input type="hidden" class="fk" name="finaldestid" value="{$info['finaldestid']}"/>{/if}</span>
                                      {/loop}
                                  </div>
                              </dd>
                          </dl>
                          <dl>
                              <dt>农家乐属性：</dt>
                              <dd>
                                  <a href="javascript:;" class="choose-btn mt-4" onclick="Product.getAttrid(this,'.attr-sel',102)" title="选择">选择</a>
                                  <div class="save-value-div mt-2 ml-10 attr-sel">
                                      {loop $info['attrlist_arr'] $k $v}
                                      <span><s onclick="$(this).parent('span').remove()"></s>{$v['attrname']}<input type="hidden" name="attrlist[]" value="{$v['id']}"></span>
                                      {/loop}

                                  </div>
                              </dd>
                          </dl>



                      </div>

                      <div class="add-class">
                          <dl>
                              <dt>图标设置：</dt>
                              <dd>
                                  <a href="javascript:;" class="choose-btn mt-4" onclick="Product.getIcon(this,'.icon-sel')" title="选择">选择</a>
                                  <div class="save-value-div mt-2 ml-10 icon-sel">

                                      {loop $info['iconlist_arr'] $k $v}
                                      <span><s onclick="$(this).parent('span').remove()"></s><img src="{$v['picurl']}"><input type="hidden" name="iconlist[]" value="{$v['id']}"></span>
                                      {/loop}

                                  </div>
                              </dd>
                          </dl>
                          <dl>
                            <dt>前台隐藏：</dt>
                            <dd>
                                <label>
                                  <input class="fl mt-8 mr-3" type="radio" name="ishidden"  {if $info['ishidden']==0}checked="checked"{/if} value="0">
                                  <span class="fl mr-20">显示</span>
                                </label>
                                <label>
                                  <input class="fl mt-8 mr-3" type="radio" name="ishidden"  {if $info['ishidden']==1}checked="checked"{/if} value="1">
                                  <span>隐藏</span>
                                </label>
                            </dd>
                          </dl>
                          <dl>
                              <dt>显示模版：</dt>
                              <dd>
                                  <div class="temp-chg" id="templet_list">
                                      <a {if $info['templet']==''}class="on"{/if}  href="javascript:void(0)"  data-value="" onclick="setTemplet(this)">标准</a>
                                      {loop $templetlist $r}
                                      <a {if $info['templet']==$r['path']}class="on"{/if}  href="javascript:void(0)" data-value="{$r['path']}" onclick="setTemplet(this)">{$r['templetname']}</a>
                                      {/loop}
                                      <input type="hidden" name="templet" id="templet" value="{$info['templet']}"/>
                                  </div>
                              </dd>
                          </dl>
                          <dl>
                              <dt>显示数据：</dt>
                              <dd>
                                  <span class="fl">推荐次数</span>
                                  <input type="text" name="recommendnum" id="yesjian" data-regrex="number" data-msg="*必须为数字"  class="set-text-xh text_60 mt-2 ml-10 mr-30" value="{$info['recommendnum']}" />
                                  <span class="fl">满意度</span>
                                  <input type="text" name="satisfyscore" id="satisfyscore" data-regrex="number" data-msg="*必须为数字" class="set-text-xh text_60 mt-2 ml-10 mr-30" value="{$info['satisfyscore']}"  />
                                  <span class="fl">销量</span>
                                  <input type="text" name="bookcount" id="bookcount" data-regrex="number" data-msg="*必须为数字" class="set-text-xh text_60 mt-2 ml-10" value="{$info['bookcount']}" />
                              </dd>
                          </dl>
                      </div>

                  </div>
              <!--/基础信息结束-->
                  <!--农家乐图片开始-->
                  <div class="product-add-div" data-id="tupian">
                      <div class="up-pic">
                          <dl>
                              <dt>农家乐图片：</dt>
                              <dd>
                                  <div class="up-file-div">
                                      <div id="pic_btn" class="btn-file mt-4">上传图片</div>
                                  </div>
                                  <div class="up-list-div">
                                      <input type="hidden" class="headimgindex" name="imgheadindex" value=""/>
                                      <input type="hidden" name="litpic" id="litpic" value="{$info['litpic']}"/>
                                      <ul class="pic-sel">

                                      </ul>
                                  </div>
                              </dd>
                          </dl>


                      </div>
                  </div>
                  <!--农家乐图片结束-->
                  <div class="product-add-div" data-id="content">
                      {php Common::getEditor('content',$info['content'],$sysconfig['cfg_admin_htmleditor_width'],400);}
                  </div>
                  <div class="product-add-div" data-id="fuwu">
                      {php Common::getEditor('fuwu',$info['fuwu'],$sysconfig['cfg_admin_htmleditor_width'],400);}
                  </div>
                  <div class="product-add-div" data-id="traffic">
                      {php Common::getEditor('traffic',$info['traffic'],$sysconfig['cfg_admin_htmleditor_width'],400);}
                  </div>
                  <div class="product-add-div" data-id="aroundspots">
                      {php Common::getEditor('aroundspots',$info['aroundspots'],$sysconfig['cfg_admin_htmleditor_width'],400);}
                  </div>
                  <div class="product-add-div" data-id="notice">
                      {php Common::getEditor('notice',$info['notice'],$sysconfig['cfg_admin_htmleditor_width'],400);}
                  </div>
                  <div class="product-add-div" data-id="equipment">
                      {php Common::getEditor('equipment',$info['equipment'],$sysconfig['cfg_admin_htmleditor_width'],400);}
                  </div>
                  <div class="product-add-div" data-id="youhua">
                      <div class="add-class">
                          <dl>
                              <dt>优化标题：</dt>
                              <dd>
                                  <input type="text" name="seotitle" id="seotitle" class="set-text-xh text_700 mt-2" value="{$info['seotitle']}" >
                              </dd>
                          </dl>
                          <dl>
                              <dt>Tag词：</dt>
                              <dd>

                                  <input type="text" id="tagword" name="tagword" class="set-text-xh text_700 mt-2 " value="{$info['tagword']}" >
                              </dd>
                          </dl>
                          <dl>
                              <dt>关键词：</dt>
                              <dd>
                                  <input type="text" name="keyword" id="keyword" name="keyword" class="set-text-xh text_700 mt-2" value="{$info['keyword']}">
                              </dd>
                          </dl>
                          <dl>
                              <dt>页面描述：</dt>
                              <dd style="height:auto">
                                  <textarea class="set-area wid_695"  name="description" id="description" cols="" rows="">{$info['description']}</textarea>
                              </dd>
                          </dl>

                      </div>
                  </div>
                  {php $contentArr=Common::getExtendContent(102,$extendinfo);}
                  {php echo $contentArr['contentHtml'];}
                  <div class="product-add-div" data-id="extend">
                      {php echo $contentArr['extendHtml'];}
                  </div>


                  <div class="opn-btn">
                      <input type="hidden" name="productid" id="productid" value="{$info['id']}"/>
                      <input type="hidden" name="action" id="action" value="{$action}"/>

                      <a class="normal-btn" id="btn_save" href="javascript:;">保存</a>
                      <!--<a class="save" href="#">下一步</a>-->
                  </div>

          </div>
        </form>


    </td>
    </tr>
    </table>
  
	<script>

	$(document).ready(function(){

        $("#nav").find('span').click(function(){

            Product.changeTab(this,'.product-add-div');//导航切换

        })
        $("#nav").find('span').first().trigger('click');


        var action = "{$action}";
        $('#pic_btn').click(function(){
            ST.Util.showBox('上传图片', SITEURL + 'image/insert_view', 0,0, null, null, document, {loadWindow: window, loadCallback: Insert});
            function Insert(result,bool){
                var len=result.data.length;
                for(var i=0;i<len;i++){
                    var temp =result.data[i].split('$$');
                    Imageup.genePic(temp[0],".up-list-div ul",".cover-div");
                }
            }
        })

        $("#product_frm input").st_readyvalidate();
        //保存
        $("#btn_save").click(function(){

               var farmname = $("#hotelname").val();

            //验证农家乐名称
             if(farmname==''){
                   $("#nav").find('span').first().trigger('click');
                   $("#hotelname").focus();
                   ST.Util.showMsg('请填写农家乐名称',5,2000);
               }
               else
               {
                   Ext.Ajax.request({
                       url   :  SITEURL+"farm/ajax_save",
                       method  :  "POST",
                       isUpload :  true,
                       form  : "product_frm",
                       waitMsg  :  "保存中...",
                       datatype  :  "JSON",
                       success  :  function(response, opts)
                       {

                           var data = $.parseJSON(response.responseText);
                           if(data.status)
                           {
                               if(data.productid!=null){
                                   $("#productid").val(data.productid);
                               }
                               ST.Util.showMsg('保存成功!','4',2000);
                           }


                       }});
               }

        })


        //如果是修改页面
        if(action=='edit')
        {
            //var kindlist_arr = ;
            //var attrlist_arr = ;
           // var iconlist_arr = ;
            //var kindlist = ST.Modify.getSelectDest({$info['kindlist_arr']});
            //var attrlist = ST.Modify.getSelectAttr({$info['attrlist_arr']});
            //var iconlist = ST.Modify.getSelectIcon({$info['iconlist_arr']});
            var piclist = ST.Modify.getUploadFile({$info['piclist_arr']});

            
            $(".pic-sel").html(piclist);
            var litpic = $("#litpic").val();
            $(".img-li").find('img').each(function(i,item){

                        if($(item).attr('src')==litpic){
                            var obj = $(item).parent().find('.btn-ste')[0];
                            Imageup.setHead(obj,i+1);
                        }
            })
            window.image_index= $(".pic-sel").find('li').length;//已添加的图片数量

        }


     });
    //设置模板
    function setTemplet(obj)
    {
        var templet = $(obj).attr('data-value');
        $(obj).addClass('on').siblings().removeClass('on');
        $("#templet").val(templet);

    }



    </script>

</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=6.0.201707.2001&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
