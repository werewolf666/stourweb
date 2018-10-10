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
                    <div class="w-set-tit bom-arrow">
                        <span class="on"><s></s>底部导航</span>
                        <a href="javascript:;" class="fr btn btn-primary radius mr-10 mt-5" onclick="addNav()">添加</a>
                        <a href="javascript:;" class="fr btn btn-primary radius mr-10 mt-5" onclick="window.location.reload()">刷新</a>
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
                                <tr class="text-c">
                                    <td><input type="text" class="input-text w80 text-c" /></td>
                                    <td><input type="text" class="input-text" /></td>
                                    <td><img class="" src="/newtravel/public/images/show-ico.png" data-show="1"></td>
                                    <td><img class="" src="/newtravel/public/images/show-ico.png" data-show="1"></td>
                                    <td><img class="" src="/newtravel/public/images/show-ico.png" data-show="1"></td>
                                    <td><a href="javascript:;" class="row-del-btn" onclick="del(10,this)" title="删除"></a></td>
                                </tr>
                                <tbody>
                            </table>
                         </form>

                        <div class="clear clearfix">
                            <a class="btn btn-primary size-L radius ml-20" href="javascript:;" onclick="saveFooterNav()">保存</a>
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

            $("#footernav tr:not(:eq(0))").remove();//先清除内容
            var trlist = data.trlist;
            $.each(trlist, function(i, tr){
                $("#footernav tr:last").after(tr);
            });
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
        var ajaxurl = SITEURL+"footernav/addnav"
        ajaxurl = ajaxurl+'/webid/'+webid;


        ST.Util.showBox('添加底部导航',ajaxurl,730,480,function(){
            getFooterNav();});

    }
    //底部导航修改
    function edit(id)
    {
        var boxurl = SITEURL+'footernav/editnav/id/'+id;
        ST.Util.showBox('底部导航修改',boxurl,730,480,function(){ getFooterNav()});


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
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.0.201710.1907&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
