	<div class="cfg-header-tab">
	    <span class="item" id="tb_databackup" data-url="databackup/index/parentkey/system/itemid/11" data-name="数据备份"><s></s>数据备份</span>
	    <span class="item" id="tb_datarecovery" data-url="databackup/recovery/parentkey/system/itemid/11" data-name="数据恢复"><s></s>数据恢复</span>
    </div>
    <script>
        $('.item').click(function(){
            var url = $(this).attr('data-url');
            var urlname = $(this).attr('data-name');
            ST.Util.addTab(urlname,url);
        })
    </script>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.0.201711.0102&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
