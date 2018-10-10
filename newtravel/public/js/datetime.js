/**
 * Created by Administrator on 2016/4/20 0020.
 */
(function(window){
    var _Datetime={};
    //获取时间戳
    function getTimeStamp(date)
    {

        var timestamp = date.getTime();
        return timestamp/1000;
    }
    //通过日期字符串获取Date
    function getDateByStr(datestr)
    {
        var dateArr = datestr.split("-");
        var date = new Date(dateArr[0], parseInt(dateArr[1])-1, parseInt(dateArr[2]));
        if(!date)
        {
            return null;
        }
        if(!date.getTime())
        {
            return null;
        }
        return date;
    }

    //获取某日所在月的第一天
    function getMonthFirstDate(date)
    {
        var   year=date.getFullYear();
        var   month=date.getMonth()+1;
        month=month<10?'0'+month:month;
        return getDateByStr(year+'-'+month+'-01');
    }




    /*
     * 对Date的扩展，将 Date 转化为指定格式的String
     * 月(M)、日(d)、12小时(h)、24小时(H)、分(m)、秒(s)、周(E)、季度(q) 可以用 1-2 个占位符
     * 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字)
     * eg:
     * (new Date()).pattern("yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423
     * (new Date()).pattern("yyyy-MM-dd E HH:mm:ss") ==> 2009-03-10 二 20:09:04
     * (new Date()).pattern("yyyy-MM-dd EE hh:mm:ss") ==> 2009-03-10 周二 08:09:04
     * (new Date()).pattern("yyyy-MM-dd EEE hh:mm:ss") ==> 2009-03-10 星期二 08:09:04
     * (new Date()).pattern("yyyy-M-d h:m:s.S") ==> 2006-7-2 8:9:4.18
     */
    function format(fmt,date)
    {
        var o = {
            "M+" : date.getMonth()+1, //月份
            "d+" : date.getDate(), //日
            "h+" : date.getHours()%12 == 0 ? 12 : date.getHours()%12, //小时
            "H+" : date.getHours(), //小时
            "m+" : date.getMinutes(), //分
            "s+" : date.getSeconds(), //秒
            "q+" : Math.floor((date.getMonth()+3)/3), //季度
            "S" : date.getMilliseconds() //毫秒
        };
        var week = {
            "0" : "/u65e5",
            "1" : "/u4e00",
            "2" : "/u4e8c",
            "3" : "/u4e09",
            "4" : "/u56db",
            "5" : "/u4e94",
            "6" : "/u516d"
        };
        if(/(y+)/.test(fmt)){
            fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));
        }
        if(/(E+)/.test(fmt)){
            fmt=fmt.replace(RegExp.$1, ((RegExp.$1.length>1) ? (RegExp.$1.length>2 ? "/u661f/u671f" : "/u5468") : "")+week[this.getDay()+""]);
        }
        for(var k in o){
            if(new RegExp("("+ k +")").test(fmt)){
                fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
            }
        }
        return fmt;
    }
    //格式化秒数，返回一个json对象
    function formatTimestamp(seconds)
    {
        seconds = parseInt(seconds);
        seconds = !seconds?0:seconds;
        var day = Math.floor(seconds/(24*3600));
        var hour = Math.floor((seconds%(24*3600))/3600);
        var minute = Math.floor((seconds%3600)/60);
        var second =  Math.floor(seconds%60);
        var result={day:day,hour:hour,minute:minute,second:second};
        return result;
    }



    //添加天数
    function dateAddDays(date,days)
    {
        var timestamp= getTimeStamp(date);
        var newTimestamp=parseInt(timestamp)+(days-1)*24*3600;
        var now=new Date(parseInt(newTimestamp) * 1000);
        var   year=now.getFullYear();
        var   month=now.getMonth()+1;
        var   date=now.getDate();
        var   hour=now.getHours();
        var   minute=now.getMinutes();
        var   second=now.getSeconds();
        return   year+"-"+month+"-"+date;
     }

    //日期相减，返回天数
    function dateMinus(date1,date2)
    {
        var minusStamp=timestampMinus(date1,date2);
        return Math.ceil(minusStamp/(24*60*60));
    }

    //日期比较
    function dateCompare(date1,date2)
    {
        var timestamp1=parseInt(getTimeStamp(date1));
        var timestamp2=parseInt(getTimeStamp(date2));
        return timestamp1>timestamp2;
    }


    //时间相减，返回时间戳
    function timestampMinus(date1,date2)
    {
        var timestamp1=getTimeStamp(date1);
        var timestamp2=getTimeStamp(date2);

        if(!timestamp1||!timestamp2)
            return null;
        var minusStamp=parseInt(timestamp1)-parseInt(timestamp2);
        return minusStamp;
    }


    _Datetime.getTimeStamp=getTimeStamp;
    _Datetime.dateMinus=dateMinus;
    _Datetime.dateAddDays=dateAddDays;
    _Datetime.getMonthFirstDate=getMonthFirstDate;
    _Datetime.dateCompare=dateCompare;
    _Datetime.getDateByStr = getDateByStr;
    _Datetime.timestampMinus=timestampMinus;
    _Datetime.formatTimestamp= formatTimestamp;
    window.Datetime=_Datetime;
})(window)