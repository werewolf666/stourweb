/**
 * Created by Netman on 14-5-28.
 * Description:设置中心全局函数
 */
var Config={
    setDivAttr:function()
    {
        //获取宽度
        var contentRigWidth = $(window).width()-120
        $(".content-rig")[0].style.width = contentRigWidth+"px";
        //获取高度
        var menuLeftHeight = $(window).height()
        $(".menu-left")[0].style.height = menuLeftHeight+"px"
        //var contentRigHeight = $(window).height()-136
        //$(".content-nr")[0].style.height = contentRigHeight+"px"


    },
    //fields表示要获取的字段，用逗号隔开
    getConfig:function(webid,succfunc,fields)
    {
            var fields_str = typeof(fields)=='object'?fields.join(','):'';
            fields_str = typeof(fields)=='string'?fields:fields_str;
            var url = SITEURL+"config/ajax_getconfig";
            $.ajax({
                type:'POST',
                url:url,
                dataType:'json',
                data:{webid:webid,fields:fields_str},
                success:function(data){
                       succfunc(data);
                }
            })
    },
    saveConfig:function(webid, successCallback)
    {

        var url = SITEURL+"config/ajax_saveconfig";
        var frmdata = $("#configfrm").serialize();
        var frmdata = frmdata+"&webid="+webid;
        $.ajax({
            type:'POST',
            url:url,
            dataType:'json',
            data:frmdata,
            success:function(data){
                if(data.status==true)
                {
                    ST.Util.showMsg('保存成功',4);
                    $.post(SITEURL+"index/ajax_clearcache");
                    if(successCallback)
                        successCallback();
                }

            }
        })
    },
    getWaterConfig:function(succfunc)
    {
        var url = SITEURL+"config/ajax_getwaterconfig";
        $.ajax({
            type:'POST',
            url:url,
            dataType:'json',
            success:function(data){

                succfunc(data);


            }
        })
    }
}




