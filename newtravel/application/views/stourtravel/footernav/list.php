<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>底部导航-思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base_new.css'); }
</head>
<body>
    <table class="content-tab">
        <tr>
            <td width="119px" class="content-lt-td" valign="top">
                <!--左侧导航区-->
                {template 'stourtravel/public/leftnav'}
                <!--右侧内容区-->
            </td>
            <td valign="top" class="content-rt-td">
                <!--右侧内容区-->
                <!-- {template 'stourtravel/public/weblist'}-->
                <div class="w-set-con">
                    <div class="cfg-header-bar">
                        <a href="javascript:;" class="fr btn btn-primary radius mr-10 mt-6" onclick="window.location.reload()">刷新</a>
                        <a href="javascript:;" class="fr btn btn-primary radius mr-10 mt-6" onclick="addNav()">添加</a>
                    </div>
                    <div class="w-set-nr">
                        <form name="navfrm" id="navfrm">
                            <table class="table table-bg table-hover" border="0" cellspacing="0" cellpadding="0" id="footernav1">
                                <thead>
                                <tr class="text-c">
                                    <th scope="col" width="10%">排序</th>
                                    <th scope="col" width="50%">栏目名称{Common::get_help_icon('footernav_index_servername')}</th>
                                    <th scope="col" width="10%">PC端显示{Common::get_help_icon('footernav_index_isdisplay')}</th>
                                    <th scope="col" width="10%">手机端显示{Common::get_help_icon('footernav_index_mobileshow')}</th>
                                    <th scope="col" width="10%">修改</th>
                                    <th scope="col" width="10%">删除</th>
                                </tr>
                                </thead>
                                <tbody>

                                <tbody>
                            </table>
                         </form>

                        <div class="clear clearfix mt-20">
                            <a class="btn btn-primary size-L radius w100 ml-20" href="javascript:;" onclick="saveFooterNav()">保存</a>
                        </div>

                    </div>
                </div>
            </td>
        </tr>
    </table>


<input type="hidden" name="webid" id="webid" value="0"/>
</body>
<script>

    var url = "{$GLOBALS['cfg_cmspath']}";
    var openIco = "{Common::getIco('show')}";
    var hideIco = "{Common::getIco('hide')}";

    $(function(){

        //子站切换点击
        $(".web-set").find('a').click(function(){
            var webid = $(this).attr('data-webid');
            $("#webid").val($(this).attr('data-webid'));
            $("#webname").html($(this).html());
            $(this).addClass('on').siblings().removeClass('on');
            getFooterNav();//重新读取导航信息

        })
        getFooterNav();
    })

    //获取底部导航
    function getFooterNav()
    {
        var webid=$("#webid").val();

        $.getJSON(SITEURL+"footernav/ajax_getfooternav","webid="+webid,function(data){

            $("#footernav1 tr:not(:eq(0))").remove();//先清除内容
            var trlist = data.list;
            var appendTr = '';
            $.each(trlist, function(i, row){
                var displayIco = row.isdisplay==1 ? openIco : hideIco;
                var mobileDisplayIco = row.mobileshow==1 ? openIco : hideIco;

                appendTr+= '<tr class="text-c">';
                appendTr+= '<td><input type="text" name="displayorder[]" class="input-text w80 text-c" value="'+(row.displayorder==9999?'':row.displayorder)+'" /></td>';
                appendTr+= '<td><input type="text" name="servername[]" class="input-text" value="'+row.servername+'" /></td>';
                appendTr+= '<td onclick="changeShow(this)">'+displayIco+'<input type="hidden" name="isdisplay[]" value="'+row.isdisplay+'"></td>';
                appendTr+= '<td onclick="changeShow(this)">'+mobileDisplayIco+'<input type="hidden" name="mobileshow[]" value="'+row.mobileshow+'"></td>';
                appendTr+= '<td align="center"><a href="javascript:;" class="row-mod-btn" onclick="edit('+row.id+',\''+row.servername+'\')" title="修改"></a></td>';
                appendTr+= '<td><a href="javascript:;" class="btn-link" onclick="del('+row.id+',this)" title="删除">删除</a><input type="hidden" name="id[]" value="'+row.id+'"/></td>';
                appendTr+= '</tr>';
            });
            $("#footernav1 tr:last").after(appendTr);
        });
    }
    //底部导航保存
    function saveFooterNav()
    {
        var webid=$("#webid").val();
        var ajaxurl = SITEURL+'footernav/ajax_savefooternav';
        ST.Util.showMsg('保存中,请稍后...',6,5000);
        $.ajaxform({
            url: ajaxurl,
            data: { webid: webid},
            method: 'POST',
            form : '#navfrm',
            dataType:'json',
            success: function (data) {


                if(data.status)
                {
                    ST.Util.showMsg('保存成功',4);
                }

            }

        });

    }
    //隐藏显示
    function changeShow(obj)
    {
        var url = "{$GLOBALS['cfg_public_url']}";
        var showstatus = $(obj).find('img').attr('data-show');
        if(showstatus == 1)
        {
            var imgurl = url+'images/close-s.png';
            $(obj).find('img').attr('src',imgurl);
            $(obj).find('input').first().val(0);
            $(obj).find('img').attr('data-show',0)
        }
        else
        {
            var imgurl = url+'images/show-ico.png';
            $(obj).find('img').attr('src',imgurl);
            $(obj).find('input').first().val(1);
            $(obj).find('img').attr('data-show',1)
        }
    }

    //添加自定义导航
    function addNav()
    {
        var webid=$("#webid").val();
        var ajaxurl = SITEURL+"footernav/addnav/menuid/{$_GET["menuid"]}"
        ajaxurl = ajaxurl+'/webid/'+webid;
        ST.Util.addTab('添加-底部导航', ajaxurl);

    }
    //底部导航修改
    function edit(id,title)
    {
        ST.Util.addTab('修改-'+title, SITEURL+'footernav/editnav/menuid/{$_GET["menuid"]}/id/'+id);
    }
    //底部导航删除
    function del(id,obj)
    {
        ST.Util.confirmBox('删除导航','确定删除这个底部导航吗?',function(){
             var boxurl = SITEURL+'footernav/ajax_del';
            $.getJSON(boxurl,"id="+id,function(data){

                if(data.status == true){
                    $(obj).parents('tr').first().remove();
                    ST.Util.showMsg('删除成功',4);
                }
                else{
                    ST.Util.showMsg('删除失败',5);
                }

            });
        })
    }

</script>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.0114&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
