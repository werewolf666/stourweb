/**
 * Created by Administrator on 2016/8/24 0024.
 * 异步请求函数，可支持普通ajax提交和form的异步提交
 */
(function($)
{
    $.ajaxform=function(paramaters)
    {
        var options = {
            form:'',//form选择器或form的dom元素,如果为空，则执行ajax请求
            url:'',//提交地址
            data:'',//附加参数
            method:'get',//请求方法，默认是get
            type:'',//同method
            dataType:'html', //返回数据类型,默认为html
            success:'', //请求成功时调用的函数
            complete:'',//无论是否成功，都会请求的函数,form提交和ajax提交的参数不同
            error:''//发生错误时调用的函数,form提交和ajax提交的参数不同
        }//为兼容extjs 的Ext.Ajax.Request函数，部分参数名称与jquery.ajax的参数有所区别。

        if(typeof(paramaters) == 'object')
            options = $.extend(options,paramaters);
        options.method = options.method==''||options.method==null?'get':options.method;
        //如果没有form,则执行ajax请求
        if(options.form=='')
        {
            $.ajax({
                type: options.type?options.type:options.method,
                url: options.url ,
                data: options.data ,
                success: options.success ,
                error:options.error,
                dataType: options.dataType,
                complete:options.complete,
                options:options.options,
                beforeSend:options.beforeSend,
                cache:options.cache,
                contentType:options.contentType,
                context:options.context,
                dataFilter:options.dataFilter,
                global:options.global,
                ifModified:options.ifModified,
                jsonp:options.jsonp,
                jsonpCallback:options.jsonpCallback,
                password:options.password,
                processData:options.processData,
                scriptCharset:options.scriptCharset,
                traditional:options.traditional,
                timeout:options.timeout,
                username:options.username,
                xhr:options.xhr
            });
        }
        else
        {
            var removable_eles=[];
            var iframe_id = 'requestform_'+Math.random().toString(36).substring(7);
            var iframe_ele = $("<iframe id='"+iframe_id+"' class='"+iframe_id+"' name='"+iframe_id+"' style='display:none'></iframe>");
            $(document.body).append(iframe_ele);
            removable_eles.push(iframe_ele);
            iframe_ele.load(function(){
                var txt = '';
                var result = null;
                try {
                    var doc = iframe_ele[0].contentWindow.document || iframe_ele[0].contentDocument || window.frames[iframe_id].document;

                    if ((contentNode = doc.body.firstChild) && /pre/i.test(contentNode.tagName)) {
                        txt = contentNode.textContent;
                    }
                    else if ((contentNode = doc.getElementsByTagName('textarea')[0])) {
                        txt = contentNode.value;
                    }
                    else {
                        txt = doc.body.textContent || doc.body.innerText;
                    }
                    result = txt;
                    for (var i in removable_eles) {
                        removable_eles[i].remove();
                    }
                    if (/^json$/i.test(options.dataType) && txt!=null && txt!=undefined && txt!='') {
                        result = eval('(' + txt + ')');
                    }
                    if (typeof(options.success) == 'function') {
                        options.success(result);
                    }
                }catch(error)
                {
                    if (typeof(options.error) == 'function') {
                        options.error(error.message);
                    }
                }
                finally{
                    if (typeof(options.complete) == 'function') {
                        options.complete();
                    }
                }


            });

            $(options.form).attr('target',iframe_id);
            if(options.url!='') {
                $(options.form).attr('action', options.url);
            }

            $(options.form).attr('method', options.method);

            if(typeof(options.data) == 'object')
            {
                for(var i in options.data)
                {
                    var extra_ele = $("<input type='hidden' name='"+i+"' value='"+options.data[i]+"'/>");
                    $(options.form).append(extra_ele);
                    removable_eles.push(extra_ele);
                }
            }
            $(options.form)[0].submit();
        }

    }

})(jQuery)