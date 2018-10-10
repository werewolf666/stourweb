<!doctype html>
<html>
<head border_ul=Kyvz8B >
<meta charset="utf-8">
<title>关键词管理-思途CMS{$coreVersion}</title>
 {template 'stourtravel/public/public_js'}
 {php echo Common::getCss('style.css,base.css,base2.css,plist.css,base_new.css'); }

</head>
<body style="overflow:hidden">

    <table class="content-tab">
        <tr>
            <td width="119px" class="content-lt-td"  valign="top">
                {template 'stourtravel/public/leftnav'}
                <!--右侧内容区-->
            </td>
            <td valign="top" class="content-rt-td" style="overflow:hidden">
            	
            	<div class="cfg-header-bar">
            		<div id="agent_choose">
                    	<span class="select-box w150 mt-5 ml-10 fl">
	                        <select name="channelid" id="channelid" class="select" onchange="goSearch(this.value,'channelid')"></select>
                        </span>
                        <span class="select-box w150 mt-5 ml-10 fl">
	                        <select name="type" id="type" class="select" onchange="goSearch(this.value,'type')">
	                            <option value="0">全部</option>
	                            <option value="1">未设置关键词</option>
	                        </select>
                        </span>
                    </div>
                    <div class="cfg-header-search">
                    	<input type="text" id="searchkey" value="关键字" datadef="关键字" class="search-text" />
                        <a href="javascript:;" class="search-btn" onclick="searchKeyword()">搜索</a>
                    </div>	
            		<a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                    <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" title="导出Excel报表">导出Excel</a>
            	</div>
                <div id="product_grid_panel" class="content-nrt">

                </div>
            </td>
        </tr>
    </table>

<script>


   window.display_mode=1;	//默认显示模式
   window.product_kindid=0;  //默认目的地ID
   var editico = "{php echo Common::getIco('edit');}";
   var delico = "{php echo Common::getIco('del');}";
   var previewico = "{php echo Common::getIco('preview');}";

   window.CHANNELMENU = {$channelArr};


   Ext.onReady(
    function() 
    {
		 Ext.tip.QuickTipManager.init();

        $.each(window.CHANNELMENU,function(i,row){

            var html = "<option value='"+row.typeid+"'>"+row.channelname+"</option>";
            $("#channelid").append(html);
        })

        $("#searchkey").focusEffect();
		//产品store
        window.product_store=Ext.create('Ext.data.Store',{

		 fields:[
             'id',
             'title',
             'keyword'

         ],

         proxy:{
		   type:'ajax',
		   api: {
              read: SITEURL+'keyword/index/action/read',  //读取数据的URL
			  update:SITEURL+'keyword/index/action/save',
              destroy:SITEURL+'keyword/index/delete'

              },
		      reader:{
                type: 'json',   //获取数据的格式 
                root: 'lists',
                totalProperty: 'total'
                }	
	         },
		 remoteSort:true,
         autoLoad:true,
		 pageSize:20,
         listeners: {
                load: function (store, records, successful, eOpts) {

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
                        },
					  {
						  xtype:'combo',
						  fieldLabel:'每页显示数量',
						  width:170,
						  labelAlign:'right',
						  forceSelection:true,
						  value:20,
						  store:{fields:['num'],data:[{num:20},{num:40},{num:60}]},
						  displayField:'num',
						  valueField:'num',
						  listeners:{
							  select:changeNum
						  }
					  }
					
					],
				  listeners: {
						single: true,
						render: function(bar) {
							var items = this.items;
							//bar.down('tbfill').hide();
                            bar.insert(0,Ext.create('Ext.panel.Panel',{border:0,html:''}));

						/*	bar.insert(0,Ext.create('Ext.panel.Panel',{border:0,html:'<div class="panel_bar"><a class="abtn" href="javascript:void(0);" onclick="chooseAll()">全选</a><a class="abtn" href="javascript:void(0);" onclick="chooseDiff()">反选</a><a class="abtn" href="javascript:void(0);" onclick="delLine()">删除</a></div>'}));*/

							bar.insert(1,Ext.create('Ext.toolbar.Fill'));
							//items.add(Ext.create('Ext.toolbar.Fill'));
						}
					}	
                 }), 		 			 
	   columns:[
			   {
				   text:'选择',
				   width:'5%',
				  // xtype:'templatecolumn',
				   tdCls:'product-ch',
				   align:'center',
				   dataIndex:'id',
				   border:0,
                   menuDisabled:true,
				   renderer : function(value, metadata,record) {

					    return  "<input type='checkbox' class='product_check' style='cursor:pointer' value='"+value+"'/>"; 
					 
					}
				  
			   },

			   {

                   text: '<span class="grid_column_text">标题</span>'+ST.Util.getGridHelp('keyword_index_title'),
				   width:'45%',
				   dataIndex:'title',
				   align:'left',
				   border:0,
				   sortable:false,
                   menuDisabled:true,
				   renderer : function(value, metadata,record) {
                       return value;
					}
			   },
               {
                   text: '<span class="grid_column_text">关键词</span>'+ST.Util.getGridHelp('keyword_index_keyword'),
                   width:'50%',
                   dataIndex:'keyword',
                   align:'left',
                   menuDisabled:true,
                   border:0,
                   sortable:false,
                   renderer : function(value, metadata,record) {
                       var id=record.get('id');
                       value=value==null?'':value;
                       return "<input type='text' value='"+value+"' class='row-edit-txt' style='text-align:left' onblur=\"updateField(this,'"+id+"','keyword',0,'input')\"/>";
                   }

               }

	           ],
			 listeners:{
		            boxready:function()
		            {
					
				
					   var height=Ext.dom.Element.getViewportHeight();
					   this.maxHeight=height-40;
					   this.doLayout();
		            },
					afterlayout:function(grid)
					{
						
			

/*
					   var data_height=0;
					   try{
					     data_height=grid.getView().getEl().down('.x-grid-table').getHeight();
					   }catch(e)
					   {
					   }
					  var height=Ext.dom.Element.getViewportHeight();

					  if(data_height>height-90)
					  {
						  window.has_biged=true;
						  grid.height=height-90;
					  }
					  else if(data_height<height-90)
					  {
						  if(window.has_biged)
						  {
							//delete window.grid.height;
							window.has_biged=false;  
							grid.doLayout();
						  }
					  }*/
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
      var height=Ext.dom.Element.getViewportHeight(); 
	  var data_height=window.product_grid.getView().getEl().down('.x-grid-table').getHeight();
	  if(data_height>height-40)
	     window.product_grid.height=(height-40);
	   else
	      delete window.product_grid.height;
	   window.product_grid.doLayout();
	  
	   
	 }) 
	
	
	 
	 	
	

	


  
  
  
  //进行搜索
  function goSearch(val,field)
  {

	  window.product_store.getProxy().setExtraParam(field,val);
	  window.product_store.loadPage(1);
	  
  }

	
	//切换每页显示数量
	function changeNum(combo,records)
	{
		
		var pagesize=records[0].get('num');
		window.product_store.pageSize=pagesize;
        window.product_store.loadPage(1);
		//window.product_grid.down('pagingtoolbar').moveFirst();

	}
	//选择全部 
  function chooseAll()
  {
	 var check_cmp=Ext.query('.product_check');
	 for(var i in check_cmp)
	 {
		if(!Ext.get(check_cmp[i]).getAttribute('checked'))
		    check_cmp[i].checked='checked';
	 } 
	 
	//  window.sel_model.selectAll();
  }
  //反选
  function chooseDiff()
  {
	  var check_cmp=Ext.query('.product_check');
		for(var i in check_cmp)
		  check_cmp[i].click();

  }
  function delLine()
  {
	  //window.product_grid.down('gridcolumn').hide();
	  
	  var check_cmp=Ext.select('.product_check:checked');
	  
	  if(check_cmp.getCount()==0)
	  {
		  return;
	  }
	   Ext.Msg.confirm("提示","确定删除",function(buttonId){
		    if(buttonId!='yes')
		       return;
	  check_cmp.each(
		  function(el,c,index)
				{
						window.product_store.getById(el.getValue()).destroy();	 
				}
			 );
	   })
  }
   //按进行搜索
   function searchKeyword() {
       var keyword = $.trim($("#searchkey").val());
       var datadef = $("#searchkey").attr('datadef');
       keyword = keyword==datadef ? '' : keyword;
       goSearch(keyword,'keyword');

   }

  
  //更新某个字段
  function updateField(ele,id,field,value,type)
  {
	  var record=window.product_store.getById(id.toString());
	  if(type=='select' || type=='input')
	  {
		  value=Ext.get(ele).getValue();
	  }
	  var view_el=window.product_grid.getView().getEl();
	
	  var channelid = $("#channelid").val();
	  Ext.Ajax.request({
						 url   :  SITEURL+"keyword/index/action/update/channelid/"+channelid,
						 method  :  "POST",
						 datatype  :  "JSON",
						 params:{id:id,field:field,val:value,kindid:window.product_kindid},
						 success  :  function(response, opts) 
						 {
							 if(response.responseText=='ok')
							 {
							 
							   record.set(field,value);
							   record.commit(); 
						      
							 }
						 }});

  }

  //生成excel
 $(".btn_excel").click(function(){

     var channelid = $('#channelid').val();
     var url = SITEURL+'keyword/genexcel/typeid/'+channelid;

     window.open(url);

 })
  

  



</script>

</body>
</html>
