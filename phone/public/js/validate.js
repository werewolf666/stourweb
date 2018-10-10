/**
 * Created by Administrator on 2017/1/20 0020.
 */
(function (window) {
    var _validate={};

    _validate.mobile=function(mobile)
    {
        var mobile_reg =/^1((([34578])\d{9})|(47\d{8}))$/;// /^1\d{10}$/;
        return mobile_reg.test(mobile);
    }
    _validate.email=function(email)
    {
        var email_reg=/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
        return email_reg.test(email);
    }
    _validate.idcard=function(card)
    {
            card=card.toLowerCase();
            var vcity={ 11:"北京",12:"天津",13:"河北",14:"山西",15:"内蒙古",21:"辽宁",22:"吉林",23:"黑龙江",31:"上海",32:"江苏",33:"浙江",34:"安徽",35:"福建",36:"江西",37:"山东",41:"河南",42:"湖北",43:"湖南",44:"广东",45:"广西",46:"海南",50:"重庆",51:"四川",52:"贵州",53:"云南",54:"西藏",61:"陕西",62:"甘肃",63:"青海",64:"宁夏",65:"新疆",71:"台湾",81:"香港",82:"澳门",91:"国外"};
            var arrint = new Array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
            var arrch = new Array('1', '0', 'x', '9', '8', '7', '6', '5', '4', '3', '2');
            var reg = /(^\d{15}$)|(^\d{17}(\d|x)$)/;
            if(!reg.test(card))return false;
            if(vcity[card.substr(0,2)] == undefined)return false;
            var len=card.length;
            if(len==15)
                reg=/^(\d{6})(\d{2})(\d{2})(\d{2})(\d{3})$/;
            else
                reg=/^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|x)$/;
            var d,a = card.match(new RegExp(reg));
            if(!a)return false;
            if (len==15){
                d = new Date("19"+a[2]+"/"+a[3]+"/"+a[4]);
            }else{
                d = new Date(a[2]+"/"+a[3]+"/"+a[4]);
            }
            if (!(d.getFullYear()==a[2]&&(d.getMonth()+1)==a[3]&&d.getDate()==a[4]))return false;
            if(len=18)
            {
                len=0;
                for(var i=0;i<17;i++)len += card.substr(i, 1) * arrint[i];
                return arrch[len % 11] == card.substr(17, 1);
            }
            return true;
    }
    window.Validate=_validate;
})(window)
