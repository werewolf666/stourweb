<style>
    table {
        border-collapse: collapse;
        border-spacing: 0;
        float: left;

    }
    table .th{
        height: 1rem;
        font-size: 0.3733rem
    }
    table .num {
        float: left;
        width: 100%;
        height: 0.5333rem;
        line-height: 0.5333rem;
        text-align: center;
        font-size: 0.3733rem
    }

    .tab {
        height: 400px;
        background: #fff
    }

    .tab:after {
        content: '';
        clear: both;
        display: block;
        height: 0;
        overflow: hidden
    }

    table td table td {
        width: 54px;
        max-height: 67px

    }

    .useable {
        background: #fff
    }

    .top_title {
        line-height: 1rem;
        font-size: 0.5333rem;
        background: #f3f3f3;
    }

    table .yes_yd {
        color: #ff7466;
        float: left;
        width: 100%;
        height: 0.6667rem;
        text-align: center;
        font-size: 0.32rem;
        font-weight: 500
    }

    .tab table .line_yes_yd {
        color: #f60;
        float: left;
        width: 100%;
        line-height: 16px;
        text-align: center;
        height: 16px;
    }

    .tab table .roombalance_b {
        color: #f60;
        font-weight: 300;
        font-size: 11px;
    }

    .kucun {
        float: left;
        color: #ccc;
        width: 100%;
        height: 20px;
        line-height: 20px;
        text-align: center;
        font-weight: 400;
    }

    #tabl tr td {
        height: 50px;
    }

    .nouseable {
        color: #d7d7d7;
    }

    .current {
        background-color: #ffc35b;
        color: #fff
    }
    {if !$newversion}
    table .num {
        height: 20px;
        line-height: 20px;
        font-size: 14px
    }
    .top_title {
        font-size: 14px;
    }
    table .yes_yd {
        height: 25px;
        font-size: 12px;
    }
    {/if}
</style>
{if $newversion}
<header table_head=Byvz8B >
    <div class="header_top">
        <a class="back-link-icon" href="javascript:;" onclick="history.go(-1)"></a>
        <h1 class="page-title-bar">选择日期</h1>
    </div>
</header>
{/if}
    <div class="page-content">
        <section>
            {$calendar}
        </section>
    </div>
<input type="hidden" id="typeid" value="{$typeid}">

