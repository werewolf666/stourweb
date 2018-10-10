(function ($) {
    var st = {};
    st.Util={
        addTab:function(title,url,issingle,options)
        {

        },
        showMsg:function(msg,type,time)
        {
            time = time ? time : 1000;//显示时间
            ZENG.msgbox.show(msg,type,time);

        },
        //隐藏消息框
        hideMsgBox:function(){
            ZENG.msgbox._hide();
        },
        //弹出框
        showBox:function(boxtitle,url,boxwidth,boxheight,closefunc,nofade,fromdocument,params)
        {
            boxwidth = boxwidth != '' ? boxwidth : 0;
            boxheight = boxheight != '' ? boxheight : 0;
            var func = $.isFunction(closefunc) ? closefunc : function () {
            };
            fromdocument = fromdocument ? fromdocument : null;//来源document
            var initParams={
                url: url,
                title: boxtitle,
                width: boxwidth,
                height: boxheight,
                loadDocument:fromdocument,
                onclose: function () {
                    func();
                }
            }
            initParams= $.extend(initParams,params);
            var dlg = dialog(initParams);
            if(typeof(dlg.loadCallback)=='function'&&typeof(dlg.loadWindow)=='object')
            {
                dlg.finalResponse=function(arg,bool,isopen){
                    dlg.loadCallback.call(dlg.loadWindow,arg,bool);
                    if(!isopen)
                        this.remove();
                }
            }
            window.d=dlg;
            if (boxwidth != 0) {
                d.width(boxwidth);
            }
            if (boxheight != 0) {
                d.height(boxheight);
            }
            if (nofade) {
                d.show()
            } else {
                d.showModal();
            }

        },

        //弹出框关闭
        closeBox:function()
        {
            window.d.close().remove();
        },
        //确认框
        confirmBox:function(boxtitle,boxcontent,okfunc,cancelfunc)
        {
            boxcontent='<div class="confirm-box">'+boxcontent+'</div>';
            var d = dialog({
                title: boxtitle,
                content: boxcontent,
                okValue: '确定',
                ok: function () {
                    okfunc();
                },
                cancelValue: '取消',
                cancel: function () {
                    if(typeof(cancelfunc)=='function')
                        cancelfunc();
                }
            });
            d.showModal();

        },
        //信息框
        messagBox:function(boxtitle,boxcontent,nofade,width,height)
        {
            var d = dialog({
                title: boxtitle,
                content: boxcontent,
                width:width,
                height:height
            });
            if(nofade){
                d.show()
            }else
            {
                d.showModal();
            }

        },

        //帮助提示框
        helpBox:function(obj,helpid,e)
        {
            /* if (e && e.stopPropagation)
             //因此它支持W3C的stopPropagation()方法
             e.stopPropagation();
             else
             //否则，我们需要使用IE的方式来取消事件冒泡
             window.event.cancelBubble = true;
             var d = parent.window.dialog({
             content: '帮助ID'+helpid+'帮助信息,这个可以很长很长....',
             quickClose: true,
             align:'bottom left'


             });

             d.show(obj);*/

        },
        getDialog:function()
        {
            var frames = parent.window.document.getElementsByTagName("iframe"); //获取父页面所有iframe
            for(i=0;i<frames.length;i++) { //遍历，匹配时弹出id
                if (frames[i].contentWindow == window) {
                    var dlgEle = $(frames[i]).parents(".ui-popup:first");
                    var dlgId = dlgEle.attr('aria-labelledby');
                    dlgId = dlgId.substr(6);
                    var dialog = parent.dialog.get(dlgId);
                    return dialog;
                }
            }
            return null;
        },
        closeDialog:function()
        {
            var dialog=this.getDialog();
            dialog.remove();

        },
        resizeDialog:function(selector)
        {
            var dialog=this.getDialog();
            var maxHeight=dialog.maxHeight;
            var height=$(selector).height();
            if(maxHeight&&height>maxHeight)
                height=maxHeight;
            dialog.height(height).show();
        }
        ,
        resizeDialogHeight:function(height)
        {
            var dialog=this.getDialog();
            var maxHeight=dialog.maxHeight;
            if(maxHeight&&height>maxHeight)
                height=maxHeight;
            dialog.height(height).show();
        }
        ,responseDialog:function(results,bool)
        {
            var dialog=this.getDialog();
            dialog.finalResponse(results,bool);

        }
        ,prevPopup:function(e,ele)
        {
            var evt = e ? e : window.event;
            if (evt.stopPropagation) {
                evt.stopPropagation();
            }
            else {

                evt.cancelBubble = true;
            }
        },
        page: function(pageSize,currentPage,totalCount,displayNum,params)
        {
            var defaultParams={
                hint:'<span class="pageHint">总共<span class="totalPage">{totalPage}</span>页,共<span class="totalCount">{totalCount}</span>条记录</span>'
            };
            if(params)
            {
                defaultParams= $.extend(defaultParams,params);
            }
            if(!totalCount||totalCount==0)
                return '';

            displayNum=!displayNum?6:displayNum;
            var totalPage=Math.ceil(totalCount/pageSize);
            var html="<div class='pageContainer'><span class='pagePart'>";
            if(currentPage<=1)
            {
                html+='<span class="firstPage short" title="第一页"></span>';
                html+='<span class="prevPage short" title="上一页"></span>';
            }
            else
            {
                html+='<a href="javascript:;" class="firstPage short" title="第一页" page="1"></a>';
                var prevPage=parseInt(currentPage)-1;
                html+='<a  href="javascript:;" class="prevPage short" title="上一页" page="'+prevPage+'"></a>';
            }
            var flowNum=Math.floor(displayNum/2);
            var leftTicks=displayNum%2==0?flowNum:flowNum;
            var rightTicks=displayNum%2==0?flowNum-1:flowNum;

            var minPage=1;
            var maxPage=totalPage;
            if(currentPage>(leftTicks+1)&&totalPage>displayNum)
            {
                minPage=currentPage-leftTicks;
                maxPage=minPage+displayNum-1;
            }
            if(currentPage>totalPage-rightTicks&&totalPage>displayNum)
            {
                maxPage=totalPage;
                minPage=totalPage-displayNum+1;
            }
            if(currentPage<=leftTicks+1&&totalPage>displayNum)
            {
                maxPage=displayNum;
            }
            if(minPage>1)
            {
                html+='<span class="more floor">...</span>';
            }
            for(var i=minPage;i<=maxPage;i++)
            {
                if(i==currentPage)
                {
                    html+='<span class="current floor">'+i+'</span>';
                    continue;
                }
                html+='<a href="javascript:;" class="pageable floor" page="'+i+'">'+i+'</a>';
            }
            if(maxPage<totalPage)
            {
                html+='<span class="more floor">...</span>';
            }
            if(currentPage!=totalPage)
            {
                var nextPage=parseInt(currentPage)+1;
                html+='<a href="javascript:;" title="下一页" class="nextPage short" page="'+nextPage+'"></a>';
                html+='<a href="javascript:;" title="最后一页" class="lastPage short" page="'+totalPage+'"></a>';
            }
            else
            {
                html+='<span class="nextPage short" title="下一页"></span>';
                html+='<span class="lastPage short" title="最后一页"></span>';
            }
            html+='</span>';
            var hint=defaultParams['hint'].replace('{totalPage}',totalPage);
            hint=hint.replace('{totalCount}',totalCount);
            html+=hint;
            html+='</div>';
            return html;

        },
        insertContent : function(myValue, obj,t) {
            var $t = obj[0];
            if (document.selection) { // ie
                this.focus();
                var sel = document.selection.createRange();
                sel.text = myValue;
                this.focus();
                sel.moveStart('character', -l);
                var wee = sel.text.length;
                if (arguments.length == 2) {
                    var l = $t.value.length;
                    sel.moveEnd("character", wee + t);
                    t <= 0 ? sel.moveStart("character", wee - 2 * t
                    - myValue.length) : sel.moveStart(
                        "character", wee - t - myValue.length);
                    sel.select();
                }
            } else if ($t.selectionStart
                || $t.selectionStart == '0') {
                var startPos = $t.selectionStart;
                var endPos = $t.selectionEnd;
                var scrollTop = $t.scrollTop;
                $t.value = $t.value.substring(0, startPos)
                + myValue
                + $t.value.substring(endPos,
                    $t.value.length);
                this.focus();
                $t.selectionStart = startPos + myValue.length;
                $t.selectionEnd = startPos + myValue.length;
                $t.scrollTop = scrollTop;
                if (arguments.length == 2) {
                    $t.setSelectionRange(startPos - t,
                        $t.selectionEnd + t);
                    this.focus();
                }
            } else {
                this.value += myValue;
                this.focus();
            }
        }

    }




    //验证码URL添加随机数
    st.captcha = captcha;
    function captcha(url) {
        var path = url.split('?');
        return path[0] + '?' + Math.random() * 10000;
    }
    st.openUrl=function(url,issingle)
    {
        open(url,'_self');
    }


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


