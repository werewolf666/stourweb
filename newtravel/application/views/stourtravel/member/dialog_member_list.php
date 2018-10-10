<?php defined('SYSPATH') or die();?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>会员列表-思途CMS{$coreVersion}</title>
 {template 'stourtravel/public/public_js'}
 {php echo Common::getScript("choose.js,product_add.js"); }
 {php echo Common::getCss('style.css,base.css,base2.css,plist.css,comment_dialog_product_list.css'); }

</head>
<body style="overflow:hidden">
<div class="s-main">
<div class="search-bar filter" id="search_bar">
    <div class="pro-search ml-10" style=" float:left; margin-top:4px">
        <input type="text" id="searchkey" value="" placeholder="会员昵称/手机号/邮箱" class="sty-txt1 set-text-xh wid_200" style="color: rgb(170, 170, 170);">
        <a href="javascript:;" class="head-search-btn" onclick="goSearch()"></a>
        <span style="color:#999" class="ml-5">*输入会员信息进行搜索，点击搜索，刷新下面的结果列表。若没有列表显示为空</span>
    </div>
</div>
    <div id="product_grid_panel" class="content-nrt">

    </div>
    <div class="save-con">
        <a href="javascript:;" class="confirm-btn">确定</a>
    </div>

</div>

<script>

  Ext.onReady(
    function() 
    {
		 Ext.tip.QuickTipManager.init();

        $(".confirm-btn").click(function(){
            var id = $(".product_check:checked").val();
            if(!id)
            {
                ST.Util.showMsg('请选择会员','5',1000);
                return;
            }
            var record =  window.product_store.getById(id.toString());
            var title = record.get('nickname');
            ST.Util.responseDialog({id:id,title:title},true);
        });


		//产品store
        window.product_store=Ext.create('Ext.data.Store',{
		 fields:[
             'id',
             'mid',
             'nickname',
             'truename',
             'mobile',
             'email',
             'jifen',
             'logintime'
         ],
         proxy:{
		   type:'ajax',
           extraParams: {'virtual':'{$virtual}'},
		   api: {
              read: SITEURL+'member/dialog_member_list/action/read',  //读取数据的URL
			  update:'',
			  destroy:''
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
       layout: 'fit',
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
				   text:'会员昵称',
				   width:'20%',
				   dataIndex:'nickname',
				   align:'left',
                   menuDisabled:true,
				   border:0,
				   sortable:false

			   },
               {
                   text:'真实姓名',
                   width:'20%',
                   dataIndex:'truename',
                   align:'left',
                   menuDisabled:true,
                   border:0,
                   sortable:false

               },
               {
                   text:'手机号码',
                   width:'20%',
                   dataIndex:'mobile',
                   align:'left',
                   menuDisabled:true,
                   border:0,
                   sortable:false

               },
               {
                   text:'邮箱',
                   width:'20%',
                   dataIndex:'email',
                   align:'left',
                   menuDisabled:true,
                   border:0,
                   sortable:false

               },


			   {
				   text:'选择',
				   width:'20%',
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
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=6.0.201708.0402&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
