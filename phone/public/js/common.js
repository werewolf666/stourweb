(function ($) {
    var st = {};
    //验证码URL添加随机数
    st.captcha = captcha;
    function captcha(url) {
        var path = url.split('?');
        return path[0] + '?' + Math.random() * 10000;
    }
    var Tools = {
            get_length:function(str){
            var realLength = 0, len = str.length, charCode = -1;
            for (var i = 0; i < len; i++) {
                charCode = str.charCodeAt(i);
                if (charCode >= 0 && charCode <= 128) realLength += 1;
                else realLength += 2;
            }
            return realLength;
        }
    }
    st.Tools = Tools;


    var STMath={
        add:function(a, b) {
            var c, d, e;
            try {
                c = a.toString().split(".")[1].length;
            } catch (f) {
                c = 0;
            }
            try {
                d = b.toString().split(".")[1].length;
            } catch (f) {
                d = 0;
            }
            return e = Math.pow(10, Math.max(c, d)), (this.mul(a, e) + this.mul(b, e)) / e;
        },
        sub:function(a, b) {
            var c, d, e;
            try {
                c = a.toString().split(".")[1].length;
            } catch (f) {
                c = 0;
            }
            try {
                d = b.toString().split(".")[1].length;
            } catch (f) {
                d = 0;
            }
            return e = Math.pow(10, Math.max(c, d)), (this.mul(a, e) - this.mul(b, e)) / e;
        },
        mul:function(a, b) {
            var c = 0,
                d = a.toString(),
                e = b.toString();
            try {
                c += d.split(".")[1].length;
            } catch (f) {}
            try {
                c += e.split(".")[1].length;
            } catch (f) {}
            return Number(d.replace(".", "")) * Number(e.replace(".", "")) / Math.pow(10, c);
        },
        div: function(a, b){
            var c, d, e = 0,
                f = 0;
            try {
                e = a.toString().split(".")[1].length;
            } catch (g) {}
            try {
                f = b.toString().split(".")[1].length;
            } catch (g) {}
            return c = Number(a.toString().replace(".", "")), d = Number(b.toString().replace(".", "")), this.mul(c / d, Math.pow(10, f - e));
        }
    }
    st.Math=STMath;
    window.ST = st;

})(jQuery)


