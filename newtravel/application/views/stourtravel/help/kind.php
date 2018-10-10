<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,jqtransform.css,base_new.css'); }
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js,product_add.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
</head>
<body>
<table class="content-tab" script_table=Hyvz8B >
<tr>
    <td width="119px" class="content-lt-td" valign="top">
        {template 'stourtravel/public/leftnav'}
        <!--右侧内容区-->
    </td>
    <td valign="top" class="content-rt-td" style="overflow:auto;">


            <div class="w-set-con">

                <div class="w-set-nr">

                    <div class="cfg-header-bar">
                    	<a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                        <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="addRow()">添加</a>  
                    </div>

                    <div class="">
                     <form id="day_fm">
                       <table width="100%" border="0" cellspacing="0" cellpadding="0" id="day_tab" class="table">
                       <tr style="height: 36px;">
                                <th scope="col" class="text-c x-column-header" align="center" width="20%">排序</th>
                                <th scope="col" class="text-c x-column-header" align="center" width="30%">分类名称{Common::get_help_icon('help_kind_kindname')}</th>
                                <th scope="col" class="text-c x-column-header" align="center" width="30%">图标{Common::get_help_icon('help_kind_litpic')}</th>
                                 <th scope="col" class="text-c x-column-header" align="center" width="10%">是否启用{Common::get_help_icon('help_kind_isopen')}</th>
                                 <th scope="col" class="text-c x-column-header" align="center" width="10%">删除</th>
                               </tr>
                           <?php
						     foreach($list as $k=>$v)
						      {
								   $is_checked=$v['isopen']==1?'checked="checked"':'';
								  ?>
                               <tr>
                                <td align="center" class="text-c"><input class="input-text w80 text-c" name="displayorder[{$v['id']}]" value="{$v['displayorder']}"  size="6"></td>
                                <td align="center" class="text-c"><input class="input-text w200 kindname-item" name="kindname[{$v['id']}]" value="{$v['kindname']}" size="20"> </td>
                                <td align="center" valign="center" class="text-c"><span id="litpic_{$v['id']}">{if !empty($v['litpic'])}<img height="30px" src="{$v['litpic']}"/>{/if}{if !empty($v['litpic'])}<a href="javascript:;" class="image_delbtn" data-id="{$v['id']}">删除</a> {/if}</span><a href="javascript:;" class="image_upbtn" data-id="{$v['id']}">上传</a></td>
                                <td align="center" class="text-c"><input type="checkbox" name="isopen[{$v['id']}]" {$is_checked} value="1" /></td>
                                <td align="center" class="text-c"><a href="javascript:;" class="btn-link"  title="删除" onclick="delRow(this,{$v['id']})">删除</a></td>
                               </tr>
							  <?php
							    }
							  ?>
                       </table>
                     </form>
                    </div>

                    <div class="clear clearfix mt-5">
                        <a class="btn btn-primary radius size-L ml-80 mt-15" href="javascript:;" onclick="rowSave()">保存</a>
                    </div>
                </div>
            </div>
    </td>
</tr>
</table>

<script>
   $(".w-set-tit").find('#tb_lineday').addClass('on');


   $(document).ready(function(){
         $(document).on('click','.image_upbtn',function(){
             var id=$(this).attr('data-id');
             ST.Util.showBox('上传图片', SITEURL + 'image/insert_view', 0,0, null, null, document, {loadWindow: window, loadCallback:setKindIcon});

             function setKindIcon(imageStr,bool)
             {
                 var imagePath=imageStr['data'][0];
                 var imagePathArr=imagePath.split('$$');
                 setIcon(id,imagePathArr[0]);
             }
         })
       $(document).on('click','.image_delbtn',function(){
           var id=$(this).attr('data-id');
           delIcon(id);
       })

   });
  function setIcon(id,img)
  {
      $.ajax({
          type:'POST',
          url:SITEURL+'/help/ajax_setkindicon',
          data:{id:id,img:img},
          dataType:'json',
          success:function(data){
              if(data.status){
                  $("#litpic_"+id).html("<img height='30px' src='"+img+"'/><a href='javascript:;' class='image_delbtn' data-id='"+id+"'>删除</a>");
              }
          }
      })
  }
  function delIcon(id)
  {
      $.ajax({
          type:'POST',
          url:SITEURL+'/help/ajax_delkindicon',
          data:{id:id},
          dataType:'json',
          success:function(data){
              if(data.status){
                  $("#litpic_"+id).html("");
                  $("#litpic_"+id).siblings(".image_delbtn").remove();
              }
          }
      })
  }
  function rowSave()
  {
      ST.Util.showMsg('保存中',6,10000);

      var is_checked=true;
      $(".kindname-item").each(function(index,ele)
      {
          if(!is_checked)
          {
              return;
          }
          var name = $.trim($(ele).val());
          if(!name || name=='')
          {
              is_checked=false;
              ST.Util.showMsg("分类名称不能为空",5,1000);
          }
      });
      if(!is_checked)
      {
          return;
      }


      $.ajaxform({
          url   :  SITEURL+"help/kind/action/save",
          method  :  "POST",
          isUpload :  true,
          form  : "#day_fm",
          dataType  :  "html",
          success  :  function(result)
          {

              if(result=='ok')
              {
                  ZENG.msgbox._hide();
                  ST.Util.showMsg("保存成功",4,1000);
              }
              else
              {
                  ST.Util.showMsg("{__('norightmsg')}",5,1000);
              }


          }});

  }
  function addRow()
  {

      $.ajaxform({
                  url   :  SITEURL+"help/kind/action/add",
                  method  :  "POST",
                  datatype  :  "html",
                  success  :  function(result)
                  {
                      var id =result

                      if(Number(id)>0)
                      {
                        var html='<tr><td align="center" class="text-c"><input class="input-text w80 text-c" name="displayorder['+id+']" value="" size="6"></td>';
	  html+='<td align="center" class="text-c"><input class="input-text w200 kindname-item" name="kindname['+id+']" value="" size="20"> </td>';
      html+='<td align="center" valign="center" class="text-c"><span id="litpic_'+id+'"></span><a href="javascript:;" class="image_upbtn" data-id="'+id+'">上传</a></td>';
      html+='<td align="center" class="text-c"><input type="checkbox" name="isopen['+id+']" checked="checked" value="1"></td>';
	  html+='<td align="center" class="text-c"><a href="javascript:;" class="btn-link" onclick="delRow(this,'+id+')" title="删除" >删除</a></td></tr>';
                         $("#day_tab").append(html);
						
						  
                      }
                      else{
                          ST.Util.showMsg("{__('norightmsg')}",5,1000);
                      }
                     
                  }});
	/*			  
      var html='<tr><td align="center"><input name="newdisplayorder[]" value="" size="6"></td>';
	  html+='<td align="center"><input name="newkindname[]" value="" size="20"> </td>'; 
      html+='<td align="center"><input type="checkbox" name="newisopen[]" checked="checked" value="1"></td>';
	  html+='<td align="center"><img src="/admin/public/images/del-ico.gif" onclick="delRow(this,0)"></td></tr>';
      $("#day_tab").append(html);
	  */

  }
  function delRow(dom,id)
  {
      ST.Util.confirmBox('删除分类','确定删除吗?',function(){
          if(id==0)
              $(dom).parents('tr').first().remove();
          else
          {
              $.ajaxform({
                  url   :  SITEURL+"help/kind/action/del",
                  method  :  "post",
                  data:{id:id},
                  dataType  :  "html",
                  success  :  function(result, opts)
                  {
                      var text = result;
                      if(text='ok')
                      {
                          $(dom).parents('tr').first().remove();
                      }
                      else
                      {

                      }
                  }});

          }


      });

  }


</script>

</body>
</html>
