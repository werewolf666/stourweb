<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title background_size=zWPAZk >站点管理-思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base_new.css'); }
</head>

<body>

    <table class="content-tab">
        <tr>
            <td width="119px" class="content-lt-td"  valign="top">
                <!--左侧导航区-->
                {template 'stourtravel/public/leftnav'}
                <!--右侧内容区-->
            </td>
            <td valign="top" class="content-rt-td">
                <!--右侧内容区-->

                <div class="w-set-con">
                    <div class="cfg-header-bar">
                        <a href="javascript:;" class="fr btn btn-primary radius mr-10 mt-6 size-MINI" onclick="window.location.reload()">刷新</a>
                    </div>
                    <div class="w-set-nr">
                        <div class="clearfix">
                            <form name="navfrm" id="navfrm">
                                <table class="table table-bg table-hover" border="0" cellspacing="0" cellpadding="0" id="sitelist1">
                                    <thead>
                                    <tr class="text-c">
                                        <th scope="col" width="10%">网站webID</th>
                                        <th scope="col" width="15%">站点名称{Common::get_help_icon('site_index_sitename')}</th>
                                        <th scope="col" width="15%">对应目的地{Common::get_help_icon('site_index_kindname')}</th>
                                        <th scope="col" width="30%">子站域名{Common::get_help_icon('site_index_weburl')}</th>
                                        <th scope="col" width="10%">站点状态{Common::get_help_icon('site_index_web_isopen')}</th>
                                        <th scope="col" width="25%">管理</th>
                                    </tr>
                                    </thead>
                                    <tbody class="table-border" id="site_body">
                                    <!--<tr class="text-c">
                                        <td>36</td>
                                        <td>国内旅游</td>
                                        <td><input type="text" class="input-text" value="http://guoneiyou.v6.com" /></td>
                                        <td><a class="btn-link" href="javascript:;" onclick="addmenu(36,'国内旅游',this);">编辑</a></td>
                                        <td align="center" onclick="del(36,this);">
                                            <img class="" src="/newtravel/public/images/show-ico.png" data-show="1">
                                            <input type="hidden" name="id[]" value="36">
                                        </td>
                                    </tr>-->

                                    </tbody>
                                </table>
                            </form>
                        </div>
                        <div class="clear clearfix">
                            <a class="btn btn-primary size-L radius ml-20 mt-20" href="javascript:;" onclick="saveSite()">保存</a>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>
<input type="hidden" name="webid" id="webid" value="0"/>
</body>
<script>

   
    $(function(){


        getSiteList();

        
    })

    //添加模版管理页面
    function addmenu(webid,webname){

        var url = 'sitetemplet/index/site/'+webid+'/parentkey/templet/menuid/{$menu["id"]}';
        var urlname = webname+'模板';
        ST.Util.addTab(urlname,url);
    }

    //获取站点
    function getSiteList()
    {
        var webid=$("#webid").val();
        $.getJSON(SITEURL+"site/ajax_get","",function(data){
            var trlist = data.trlist;
            var appendTr = '';
            $.each(trlist, function(i, row){
                appendTr+= '<tr class="text-c">\
                    <td>'+row['id']+'</td>\
                    <td>'+row['webname']+'</td>\
                    <td>'+row['kindname']+'</td>\
                    <td>'+row['weburl']+'</td>\
                    <td>已开启</td>\
                    <td webid="'+row['id']+'"><a href="'+row['weburl']+'" target="_blank"  class="btn-link">访问</a><a href="javascript:;" onclick="goClose('+row['id']+')" class="btn-link ml-10">关闭</a><a href="javascript:;" onclick="setTemplate('+row['id']+')" class="btn-link ml-10">设置模板</a><a href="javascript:;" onclick="modWebname('+row['id']+')" class="btn-link ml-10">修改站点名</a></td>\
                    </tr>';
            });
            $("#site_body").append(appendTr);
        });
    }
    //站点保存
    function saveSite()
    {
        var webid=$("#webid").val();
        var ajaxurl = SITEURL+'site/ajax_save';
        ST.Util.showMsg('保存中,请稍后...',6,5000);
        Ext.Ajax.request({
            url: ajaxurl,
            method: 'POST',
            form : 'navfrm',
            success: function (response, options) {

                var data = $.parseJSON(response.responseText);
                if(data.status)
                {
                    ST.Util.showMsg('保存成功',4);
                }
            }
        });
    }

    //跳到目的地关闭页面
    function goClose(id)
    {
        ST.Util.addTab("全局目的地",SITEURL+'destination/destination/menuid/{$dest_menu_id}?jmp_destid='+id);
    }

    //设置模板
    function setTemplate(id)
    {
        ST.Util.addTab("模板高级设置",SITEURL+'templet/index/menuid/{$templet_menu_id}?platform=17&webid='+id);
    }

    //修改网站名称或优化信息
    function modWebname(id)
    {
        ST.Util.addTab("首页设置",SITEURL+'config/base/menuid/{$webname_menu_id}?jmp_webid='+id);
    }

    //删除
    function del(id,obj)
    {
        ST.Util.confirmBox('关闭站点','确定关闭这个站点?',function(){
            var boxurl = SITEURL+'site/ajax_del';
            $.getJSON(boxurl,"id="+id,function(data){

                if(data.status == true){
                    $(obj).parents('tr').first().remove();
                    ST.Util.showMsg('关闭成功',4);
                }
                else{
                    ST.Util.showMsg('关闭失败',5);
                }

            });
        })
    }


</script>
</html>
