<?php defined('SYSPATH') or die();?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title clear_size=zSlDTk >产品列表-思途CMS{$coreVersion}</title>
 {template 'stourtravel/public/public_js'}
 {php echo Common::getScript("choose.js,product_add.js"); }
 {php echo Common::getCss('style.css,base.css,base2.css,plist.css,comment_dialog_product_list.css'); }

</head>
<body style="overflow:hidden">
<div class="s-main">
<div class="search-bar filter" id="search_bar">
    <div class="pro-search ml-10" style=" float:left; margin-top:4px">
        <input type="text" id="searchkey" value="" placeholder="产品名称" class="sty-txt1 set-text-xh wid_200" style="color: rgb(170, 170, 170);">
        <a href="javascript:;" class="head-search-btn" onclick="goSearch()"></a>
    </div>
</div>
 <div id="product_grid_panel" class="content-nrt">
    
  </div>
<div class="save-con">
    <a href="javascript:;" class="confirm-btn">确定</a>
</div>
</div>
<script>
  var typeid="{$typeid}";
  Ext.onReady(
    function() 
    {
		 Ext.tip.QuickTipManager.init();

        $(".confirm-btn").click(function(){
            var id = $(".product_check:checked").val();
            if(!id)
            {
                ST.Util.showMsg('请选择产品','5',1000);
                return;
            }
            var record =  window.product_store.getById(id.toString());
            var title = record.get('title');
            ST.Util.responseDialog({id:id,title:title,typeid:typeid},true);
        });

        //$("#searchkey").focusEffect();
		//产品store
        window.product_store=Ext.create('Ext.data.Store',{
		 fields:[
             'id',
             'series',
             'title',
             'url'
         ],
         proxy:{
		   type:'ajax',
           extraParams: {typeid:typeid},
		   api: {
              read: SITEURL+'comment/dialog_product_list/action/read',  //读取数据的URL
			  update:SITEURL+'comment/dialog_product_list/action/save',
			  destroy:SITEURL+'comment/dialog_product_list/action/delete'
              },
		      reader:{
                type: 'json',   //获取数据的格式 
                root: 'lists',
                totalProperty: 'total'
                }
	         },
		 remoteSort:true,
         autoLoad:true,
		 pageSize:15,
         listeners:{
                load:function( store, records, successful, eOpts )
                {
                    if(!successful){
                        ST.Util.showMsg("{__('norightmsg')}",5,1000);
                    }
                    var pageHtml = ST.Util.page(store.pageSize, store.currentPage, store.getTotalCount(), 10);
                    $("#line_page").html(pageHtml);
                    window.product_grid.doLayout();

                    $(".pageContainer .pagePart a").click(function () {
                        var page = $(this).attr('page');
                        product_store.loadPage(page);
                    });
                }
            }


		  
       });
	   
	  //产品列表 
	  window.product_grid=Ext.create('Ext.grid.Panel',{ 
	   store:product_store,
	   renderTo:'product_grid_panel',
	   border:0,
	   bodyBorder:0,
	   bodyStyle:'border-width:0px',
       scroll:'vertical', //只要垂直滚动条
	   bbar: Ext.create('Ext.toolbar.Toolbar', {
                    store: product_store,  //这个和grid用的store一样
                    displayInfo: true,
                    emptyMsg: "",
					items:[
                        {
                            xtype:'panel',
                            id:'listPagePanel',
                            html:'<div id="line_page"></div>'
                        }
					
					],
				  listeners: {
						single: true,
						render: function(bar) {
							var items = this.items;
							//bar.down('tbfill').hide();


							bar.insert(0,Ext.create('Ext.toolbar.Fill'));
							//items.add(Ext.create('Ext.toolbar.Fill'));
						}
					}	
                 }), 		 			 
	   columns:[

			   {
				   text:'编号',
				   width:'15%',
				   dataIndex:'series',
                   menuDisabled:true,
				   align:'center',
                   sortable:false,
				   border:0,
				   renderer : function(value, metadata,record) {
                       return value;
                   }
			   },
			   {
				   text:'产品名称',
				   width:'70%',
				   dataIndex:'title',
				   align:'left',
                   menuDisabled:true,
				   border:0,
				   sortable:false,
				   renderer : function(value, metadata,record) {

                                var url=record.get('url');
			                   return "<a href='"+url+"' class='product-title' target='_blank'>"+value+"</a>";
						}
			   },

			   {
				   text:'选择',
				   width:'17%',
				   align:'center',
				   border:0,
				   sortable:false,
                   menuDisabled:true,
				  renderer : function(value, metadata,record) {
					     var id=record.get('id');
						 return "<input type='radio' name='product_check' class='product_check' value='"+id+"'/>";

                    }
			      }
	           ],
			 listeners:{
		            boxready:function()
		            {

					  // var height=Ext.dom.Element.getViewportHeight();
					  // this.maxHeight=height-25;
					  // this.doLayout();
		            },
					afterlayout:function(grid)
					{

                        ST.Util.resizeDialog('.s-main');
					   var data_height=0;
					   try{
					     data_height=grid.getView().getEl().down('.x-grid-table').getHeight();
					   }catch(e)
					   {

					   }
					  var height=Ext.dom.Element.getViewportHeight();

					  if(data_height>height-65)
					  {
						  window.has_biged=true;
						  grid.height=height-65;
					  }
					  else if(data_height<height-65)
					  {
						  if(window.has_biged)
						  {


							window.has_biged=false;  
							grid.doLayout();
						  }
					  }

				  }
			 },
			 plugins: [
                Ext.create('Ext.grid.plugin.CellEditing', {
                  clicksToEdit:2,
                  listeners:{
					 edit:function(editor, e)
					 {
						var id=e.record.get('id');
						updateField(0,id,e.field,e.value,0);
						return false;
						  
					 },
					 beforeedit:function(editor,e)
					 {
								   
					 }
				 }
               })
             ],
			viewConfig:{
				//enableTextSelection:true
				}	   
	   });
	   
	  
	  
	})
	
	//实现动态窗口大小
  Ext.EventManager.onWindowResize(function(){
     /* var height=Ext.dom.Element.getViewportHeight();
	  var data_height=window.product_grid.getView().getEl().down('.x-grid-table').getHeight();
      if(data_height>height-65)
          window.product_grid.height=(height-65);
      else
          delete window.product_grid.height;
      window.product_grid.doLayout();*/

	 })
  function goSearch()
  {
      var keyword = $.trim($("#searchkey").val());
      window.product_store.getProxy().setExtraParam('keyword',keyword);
      window.product_store.loadPage(1);
  }
</script>

</body>
</html>
