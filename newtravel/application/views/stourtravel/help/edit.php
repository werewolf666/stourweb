<!doctype html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="admin/public/css/common.css"/>
    <meta charset="utf-8">
<title>帮助 添加/修改</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,base_new.css'); }
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js,product_add.js,imageup.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
</head>
<body>
	<table class="content-tab">
    <tr>
    <td width="119px" class="content-lt-td"  valign="top">
     {template 'stourtravel/public/leftnav'}
    <!--右侧内容区-->
    </td>
     <td valign="top" class="content-rt-td ">

          <form method="post" name="product_frm" id="product_frm">
          <div class="manage-nr">
              <div class="cfg-header-bar" id="nav">
                  <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
              </div>

               <!--基础信息开始-->
                <div class="product-add-div">
	               	<ul class="info-item-block">
	               		<li>
	               			<span class="item-hd">标题{Common::get_help_icon('help_field_title')}：</span>
	               			<div class="item-bd">
	               				<input type="text" name="title" id="articlename" class="input-text w700" value="{$info['title']}"/>
	                            <input type="hidden" name="helpid" id="helpid" value="{$info['id']}"/>
	               			</div>
	               		</li>
	               		<li>
	               			<span class="item-hd">分类{Common::get_help_icon('help_field_kindid')}：</span>
	               			<div class="item-bd">
	               				<select class="item-text" name="kindid">
                                    <option  value="">请选择...</option>
		                            <?php
									  foreach($kindlist as $v)
									  {
										  $is_selected=$v['id']==$info['kindid']?'selected="selected"':'';
										  echo "<option value='".$v['id']."' ".$is_selected.">".$v['kindname']."</option>";  
									  }
									?>
		                        </select>    
	               			</div>
	               		</li>
	               		<li>
	               			<span class="item-hd">显示位置：</span>
	               			<div class="item-bd w800 lh-30" id="pos_con">
	               				<label><input class="pos-all fl mt-8 mr-3" type="checkbox" onClick="chooseAll(this)"/><span class="fl mr-20">全部</span></label>
                                   <?php
								       $selected=is_null($info['type_id']) || $info["type_id"]=="" ? null : explode(',',$info['type_id']);
								       $cnt=1;
                                       foreach($typearr as $k=>$v)
									   {
										  $is_checked=in_array($k,$selected)?'checked="checked"':'';
										  echo "<label><input class='pos-item fl mt-8 mr-3' type='checkbox' name='typeid[]' ".$is_checked." value='".$k."'/><span class='fl mr-20'>".$v."</span></label>";
                                          if($cnt % 10==0)
                                              echo "<br/>";
                                           $cnt++;
									   }
								   ?>
	               			</div>
	               		</li>
	               		<li>
	               			<span class="item-hd">文章内容: </span>
	               			<div class="item-bd">
	               				{php Common::getEditor('body',$info['body'],$sysconfig['cfg_admin_htmleditor_width'],400);}
	               			</div>
	               		</li>
	               	</ul>
               	</div>
              <!--/基础信息结束-->

              <div class="clear clearfix mt-5">
                  <a class="btn btn-primary radius size-L ml-115" id="btn_save" href="javascript:;">保存</a>
              </div>

          </div>
        </form>

    </td>
    </tr>
    </table>

	<script>

	$(document).ready(function(){

      
        var action = "{$action}";


       
        //保存
        $("#btn_save").click(function(){

               var title = $("#articlename").val();

            //验证名称
             if(articlename==''){
                   $("#nav").find('span').first().trigger('click');
                   $("#articlename").focus();
                   ST.Util.showMsg('请填写帮助标题',5,2000);
               }
               else
               {
                   $.ajaxform({
                       url   :  SITEURL+"help/ajax_save",
                       method  :  "POST",
                       form  : "#product_frm",
                       dataType  :  "html",
                       success  :  function(text, opts)
                       {

                           if(text!='no')
                           {
                              $("#helpid").val(text);
                               ST.Util.showMsg('保存成功!','4',2000);
                           }
                       }});
               }

        })


        //全部选择中
        if($('#pos_con input:checkbox').not(':checked').length==1)
        {
            $('#pos_con input:checkbox').attr('checked',true);
        }

        /*$(".pos-item").click(function(){
            if($('.pos-item').not(':checked').length>0)
            {
                $('.pos-all').attr('checked',false);
            }
        })*/

     });

   function chooseAll(dom)
   {
	   if($(dom).is(":checked"))
	   {
		   $('#pos_con input:checkbox').attr('checked',true);
	   }
	   else
	   {
           $('#pos_con input:checkbox').attr('checked',false);
	   }
   } 


</script>
</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.0310&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
