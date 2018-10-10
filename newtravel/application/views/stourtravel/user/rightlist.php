<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,jqtransform.css,base_new.css'); }
    {php echo Common::getCss('ext-theme-neptune-all-debug.css','js/extjs/resources/ext-theme-neptune/'); }
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js,product_add.js,st_validate.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }

</head>
<body>

<table class="content-tab" strong_font=zCtNvk >
<tr>
    <td width="119px" class="content-lt-td" valign="top">
        {template 'stourtravel/public/leftnav'}
        <!--右侧内容区-->
    </td>
    <td valign="top" class="content-rt-td" style="overflow:auto;">
            <div class="list-top-set">
                <div class="list-web-pad"></div>
                <div class="list-web-ct">
                    <table class="list-head-tb">
                        <tr>
                            <td class="head-td-lt">

                            </td>
                            <td class="head-td-rt">
                                <a href="javascript:;" class="btn btn-primary radius" onclick="window.location.reload()">刷新</a>
                                <a href="javascript:;" class="btn btn-primary radius" onclick="addRow()">添加</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="w-set-con">

                <div class="w-set-nr">

                    <div class="clearfix">
                     <form id="day_fm">
                        <table class="table table-bg table-hover table-border" id="day_tab">
                            <thead>
                               <tr>
                                <th scope="col"  width="20%">权限名称</th>
                                <th scope="col"  width="70">权限说明</th>
                                <th class="text-c" scope="col"  width="10%">管理{Common::get_help_icon('user_right_guanli')}</th>
                               </tr>
                            </thead>
                            <tbody>
                            <?php
                               foreach($list as $k=>$v)
                            {
                            ?>
                            <tr>
                                <td>
                                    <?php
                                    if ($v['roleid'] != 1) {
                                        ?>
                                        <input class="input-text" name="rolename[{$v['roleid']}]" value="{$v['rolename']}" size="20" />
                                    <?php
                                    } else
                                        echo $v['rolename'];

                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if ($v['roleid'] != 1) {
                                        ?><input class="input-text" name="description[{$v['roleid']}]" value="{$v['description']}" size="60"/>
                                    <?php
                                    } else
                                        echo $v['description'];
                                    ?>
                                </td>
                                <td class="text-c">
                                    <?php

                                        if ($v['roleid'] != 1)
                                        {
                                    ?>
                                    <a href="javascript:;" onclick="goModify({$v['roleid']},this)" class="btn-link">编辑</a>
                                    &nbsp;&nbsp;&nbsp;
                                    <a href="javascript:;" onclick="delRow(this,{$v['roleid']})" class="btn-link">删除</a>
                                    <?php
                                        }
                                    ?>
                                </td>
                               </tr>
                            <?php
                               }
                            ?>
                            </tbody>
                        </table>
                     </form>
                    </div>

                    <div class="clear clearfix mt-20">
                        <a class="btn btn-primary radius size-L ml-20" href="javascript:;" onclick="rowSave()">保存</a>
                    </div>
                </div>
            </div>
    </td>
</tr>
</table>

<script>
   $(".w-set-tit").find('#tb_cartype').addClass('on');
   var ico_edit="<?php  echo Common::getIco('edit');?>";
   var ico_del="<?php  echo Common::getIco('del');?>";

  function rowSave()
  {
      ST.Util.showMsg('保存中',6,10000);
      Ext.Ajax.request({
          url   :  SITEURL+"user/right/action/save",
          method  :  "POST",
          isUpload :  true,
          form  : "day_fm",
          datatype  :  "JSON",
          success  :  function(response, opts)
          {
              var text = response.responseText;
              if(text='ok')
              {
                  ZENG.msgbox._hide();
                  ST.Util.showMsg("保存成功",4)
              }
              else
              {

              }


          }});

  }
  function addRow()
  {
	  Ext.Ajax.request({
                  url   :  SITEURL+"user/right/action/add",
                  method  :  "POST",
                  datatype  :  "JSON",
                  success  :  function(response, opts)
                  {
                      var id = response.responseText;
                      if(id!='no')
                      {
					 var html='<tr><td><input class="input-text" name="rolename['+id+']" value="" size="20"></td>';
                          html+='<td><input class="input-text" name="description['+id+']" value="" size="60"></td>';
                          html+='<td class="text-c"><a href="javascript:;" class="btn-link" onclick="goModify('+id+',this)">编辑</a>';
                          html+='&nbsp;&nbsp;&nbsp;<a href="javascript:;" class="btn-link" onclick="delRow(this,'+id+')">删除</a>';
                          html+='</td></tr>';
                          $("#day_tab").append(html);
					  }
					 }})

  }
  function delRow(dom,id)
  {
      ST.Util.confirmBox('提示','确定删除？',function(){
          if(id==0)
              $(dom).parents('tr').first().remove();
          else
          {
              Ext.Ajax.request({
                  url   :  SITEURL+"user/right/action/del",
                  method  :  "POST",
                  params:{id:id},
                  datatype  :  "JSON",
                  success  :  function(response, opts)
                  {
                      var text = response.responseText;
                      if(text='ok')
                      {
                          $(dom).parents('tr').first().remove();
                      }
                      else
                      {

                      }
                  }
              });

          }


      });

  }
 function goModify(id,dom)
 {
    var rolename=$(dom).parents("tr:first").find('.rolename').val();
    ST.Util.addTab('修改权限-'+rolename,SITEURL+"user/setright/{if isset($_GET['menuid'])}menuid/{$_GET['menuid']}/{/if}roleid/"+id);
 }

</script>

</body>
</html>
