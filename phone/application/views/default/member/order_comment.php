<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>点评-{$GLOBALS['cfg_webname']}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {php echo Common::css('amazeui.css,style.css,extend.css');}
    {php echo Common::js('jquery.min.js,amazeui.js,jquery.validate.min.js');}
    <script>
        $(function () {
            $('#my-st-slide').offCanvas('close');
        })
    </script>
</head>

<body>

<header>
    <div class="header_top">
        <div class="st_back"><a href="{$cmsurl}member/order/list"></a></div>
        <h1 class="tit">点评</h1>
    </div>
</header>

<section>

    <div class="mid_content">

        <div class="confirm_order_msg">
            <dl>
                <dt><img src="{Common::img($info['litpic'],150,90)}"/></dt>
                <dd>
                    <span>{$info['productname']}</span>
                    <strong><i class="currency_sy">{Currency_Tool::symbol()}</i><b>{$info['price']}</b>起</strong>
                </dd>
            </dl>
            <div class="dp_cp_show">
                <form action="{$cmsurl}member/comment/save?id={$info['id']}" method="post" id="form">
                    <em class="tit">整体满意度：</em>
                  <span class="p_rate" id="p_rate">
                    <i title="1分" style="width: 32px; z-index: 5;" class="select"></i>
                    <i title="2分"></i>
                    <i title="3分"></i>
                    <i title="4分"></i>
                    <i title="5分"></i>
                      <input type="hidden" name="score1" id="score" value="1">
                      <input type="hidden" name="orderid" value="{$info['id']}">
                      <input type="hidden" name="articleid" value="{$info['productautoid']}">
                      <input type="hidden" name="typeid" value="{$info['typeid']}">
                  </span>
                    <strong class="snum" id="snum">1分</strong>
                    <textarea name="content" cols="" rows="" placeholder="请输入评价"></textarea>
                </form>
            </div>
            <div class="pl-btn">
                <a id="submit" class="cursor">确认</a>
            </div>
        </div>

    </div>

</section>
<script type="text/javascript">
    $(document).ready(function () {
        var pRate = function (box, callBack) {
            this.Index = null;
            var B = $("#" + box),
                rate = B.children("i"),
                w = rate.width(),
                n = rate.length,
                me = this;
            for (var i = 0; i < n; i++) {
                rate.eq(i).css({
                    'width': w * (i + 1),
                    'z-index': n - i
                });
            }
            rate.hover(function () {
                var S = B.children("i.select");
                $(this).addClass("hover").siblings().removeClass("hover");
                if ($(this).index() > S.index()) {
                    S.addClass("hover");
                }
            }, function () {
                rate.removeClass("hover");
            })
            rate.click(function () {
                rate.removeClass("select hover");
                $(this).addClass("select");
                me.Index = $(this).index() + 1;
                if (callBack) {
                    callBack();
                }
            })
        }
        var Rate = new pRate("p_rate", function () {
            //alert("点评成功"+Rate.Index+"分")
            var snum = document.getElementById('snum')
            snum.innerHTML = Rate.Index + '分'
            $('#score').val(Rate.Index);
        });
        $('#submit').click(function () {
            $('#form').submit();
        });
        $('#form').validate({
            rules:{
                content:{
                    required:true,
                    minlength:15
                }
            },
            messages:{
                content:{
                    required:'* 评论内容不得低于15个字！',
                    minlength:'* 评论内容不得低于15个字！'
                }
            }

        });
    })
</script>
</body>
</html>
