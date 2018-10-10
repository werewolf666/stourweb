<!doctype html>
<html>
<head>
    <meta charset="utf-8">
<title>问答查看</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('base_new.css'); }
    <style>
        .info-item-block{
            padding:0 !important;
        }
        .info-item-block .item-hd{
            width:60px !important;
        }
        .info-item-block .item-bd{
            padding-left:60px !important;
        }
    </style>
</head>

<body style=" width: 450px; height: 183px; overflow: hidden">

    <div class="content-nr mt-0">
        <form method="post" name="product_frm" id="product_frm" margin_padding=XFJwOs >

                <ul class="info-item-block">
                    <li>
                        <span class="item-hd">客户电话：</span>
                        <div class="item-bd">
                            <span class="item-text c-666">{$info['phone']}</span>
                        </div>
                    </li>
                    <li>
                        <span class="item-hd">处理说明：</span>
                        <div class="item-bd">
                            <textarea class="textarea" name="description">{$info['description']}</textarea>
                        </div>
                    </li>
                </ul>
                <div class="clear clearfix text-c">
                    <input type="hidden" name="id" id="id" value="{$info['id']}"/>
                    <a class="btn btn-primary radius" id="btn_save" href="javascript:;">保存</a>
                </div>
        </form>
    </div>

	<script>
    var id="{$info['id']}";
	$(document).ready(function(){
        //保存
        $("#btn_save").click(function(){
                  $.ajaxform({
                       url   :  SITEURL+"kefu/ajax_freekefu_save",
                       method  :  "POST",
                       form  : "#product_frm",
                       success  :  function(response, opts)
                       {
                           setTimeout(function(){ST.Util.responseDialog({id:id},true)},1000);
                       }
                   });
               });


    });
    </script>
</body>
</html>
