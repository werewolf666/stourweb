
<style>
    .kefu_top{
        width:233px;
        height:130px}
    .kefu_left{
        width:25px;
        height:66px;
        position:absolute;
        left:-30px;
        top:0;
        cursor:pointer;
        background:url({$GLOBALS['cfg_res_url']}/images/tpl2/12_left.png) left no-repeat}
    .kefu_con{
        float:left;
        width:133px;
        height:auto;
        display:inline;
        margin-left:72px;
        position:relative;
        background:#f2f2f2;
        border-left:5px solid #6f9d1f;
        border-right:5px solid #6f9d1f;
        border-bottom:5px solid #6f9d1f}
    .kefu_con dl{
        float:left;
        width:114px;
        margin:0 8px;}
    .kefu_con dl dt{
        color:#333;
        height:30px;
        line-height:30px;
        font-weight:bold}
    .kefu_con dl dd{
        float:left;
        width:110px;
        height:24px;
        line-height:24px;
        padding:5px 0 8px;
        display:inline;
       }
    .kefu_con dl dd a{
        color:#6a931e}
    .kefu_con dl dd img{
        float:left;
        margin-right:10px}
    .phone_num{
        float:left;
        width:133px;
        padding:10px 0;
        text-align:center}
    .phone_num p{
        color:#de3a60;
        font-family:"微软雅黑";
        font-size:16px;
        font-weight:bold;}

</style>

<script type="text/javascript">
    $(function(){
        $(".kefu_left").click(function(){
            $(".kefu_div").hide();
            $(".close").show();
            $(".close").attr("style","position:fixed;{$conf['pos']}:{$conf['posh']};top:{$conf['post']};z-index:9999");
            $(".close").css("cursor","pointer");
        });
        $(".close").click(function(){
            $(".kefu_div").show();
            $(".close").hide();
        });
    })



</script>

<div class="close" style=" display:none; float:right; cursor:pointer"><img src="{$GLOBALS['cfg_res_url']}/images/tpl2/12_close.png" /></div>
<div class="kefu_div" style="position:fixed;{$conf['pos']}:{$conf['posh']};top:{$conf['post']};z-index:9999">
    <div class="kefu_top"><img src="{$GLOBALS['cfg_res_url']}/images/tpl2/12.png" /></div>
    <div class="kefu_con">
        <div class="kefu_left"></div>
            {loop $group $g}
            <dl>
                <dt>{$g['qqname']}</dt>

                    {loop $g['qq'] $q}
                    <dd><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin={$q['qqnum']}&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:{$q['qqnum']}:52" alt="点击这里给我发消息" title="点击这里给我发消息"/>{$q['qqname']}</a></dd>
                    {/loop}
            </dl>

           {/loop}

        <div class="phone_num">
            <span><img src="{$GLOBALS['cfg_res_url']}/images/tpl2/lianxidh.png" /></span>
            <p>{$conf['phonenum']}</p>
        </div>
    </div>
</div>
