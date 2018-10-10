/**
 * Created by netman on 15-4-13.
 */

var orderChart,visitChart,memberChart; //图表名称

$(function(){
   // 路径配置
    require.config({
        paths: {
            echarts: 'http://echarts.baidu.com/build/dist'
        }
    });
// 使用
    require(
        [
            'echarts',
            'echarts/chart/line', // 使用线性图就加载line模块，按需加载
            'echarts/chart/bar'
        ],
        function (ec) {

            orderChart = ec.init(document.getElementById('order-count-box'));
            //visitChart = ec.init(document.getElementById('pv-count-box'));
            memberChart = ec.init(document.getElementById('member-count-box'));
            initChart();
        }
    );


})





//初始化图表
function initChart()
{
    //getVisitChart(visitChart);
    getMemberChart(memberChart);
    getOrderChart(orderChart);
}

/*
* 订单图表
* */

function getOrderChart(orderChart)
{
    var starttime = $("#starttime").val();
    var endtime = $("#endtime").val();
    //读取数据
    $.ajax({
        type:'POST',
        url:URL+'index/ajax_order_num_graph',
        data:{starttime:starttime,endtime:endtime},
        dataType:'json',
        beforeSend:function(){
            orderChart.clear();
            orderChart.showLoading({text:'正在加载数据...'});

        },
        success:function(data){
            if (data) {
                orderChart.hideLoading();
                var label = eval(data.attribute.labels);
                var models= eval(data.attribute.models);
                var series = [];
                var legend = [];
                for(var i in models)
                {
                    var m = models[i];
                    // 防止模型名称重名
                    var title = m.title;
                    if( $.inArray(title, legend) > -1)
                    {
                        title += m.id;
                    }

                    var obj = {
                        name: title,
                        type: 'line',
                        smooth:true,
                        data:(function(){
                           return data[m.pinyin];
                        })()
                    };
                    series.push(obj);
                    legend.push(title);
                }


                var option = {

                    title : {
                        text: '',
                        subtext: ''
                    },
                    tooltip : {
                        trigger: 'axis',
                        formatter: function(params, ticket, callback) {
                            var res='<div><p>'+params[0].name+'</p></div>';
                             res += '<ul style="width:150px;">';
                            for(var i=0;i<params.length;i++){
                                res+='<li style="float:left;margin-left:5px">'+params[i].seriesName+':'+params[i].data+'</li>'
                            }
                            res+='</ul>';
                            return res;
                        }
                    },
                    legend: {
                        data:legend
                    },
                    grid: {
                        top: 50,
                        left: 80,
                        right: 80,
                        bottom: 50
                    },

                    toolbox: {
                        show : false,
                        feature : {
                            mark : {show: true},
                            dataView : {show: true, readOnly: false},
                            magicType : {show: true, type: ['line', 'bar']},
                            restore : {show: true},
                            saveAsImage : {show: true}
                        }
                    },
                    calculable : false,
                    xAxis : [{
                        type : 'category',

                        axisTick: {
                            alignWithLabel: true,
                            lineStyle: {
                                color: "#999"
                            }
                        },
                        axisLine: {
                            onZero: false,
                            lineStyle: {
                                color: "#999",
                                opacity: 0.1
                            }
                        },


                        data : label
                    }],
                    yAxis : [{
                        type : 'value',
                        axisLabel : {
                            formatter: '{value} 笔'
                        }
                    }],
                    series : series
                };
                // 为echarts对象加载数据
                orderChart.setOption(option);
            }
        }
    })

}

/*
*访问图表
* */
function getVisitChart(visitChart)
{
    var starttime = $("#starttime").val();
    var endtime = $("#endtime").val();
    $.ajax({
        type:'POST',
        url:URL+'index/ajax_ippv_num',
        data:{starttime:starttime,endtime:endtime},
        beforeSend:function(){
            visitChart.showLoading({text:'正在加载数据...'})
        },
        dataType:'json',
        success:function(data){
            visitChart.hideLoading();
            var ip_num = eval(data.ip);

            var pv_num = eval(data.pv);
            var label = eval(data.labels);
            var option = {
                title : {
                    text: '',
                    subtext: ''
                },
                tooltip : {
                    trigger: 'axis'
                },
                legend: {
                    data:['IP:点击量','PV:访问量']
                },
                toolbox: {
                    show : false,
                    feature : {
                        mark : {show: true},
                        dataView : {show: true, readOnly: false},
                        magicType : {show: true, type: ['line', 'bar', 'stack', 'tiled']},
                        restore : {show: true},
                        saveAsImage : {show: true}
                    }
                },
                calculable : false,
                xAxis : [
                    {
                        type : 'category',
                        boundaryGap : false,
                        data :(function(){
                            return label ? label : ['周一','周二','周三','周四','周五','周六','周日'];
                        })()
                    }
                ],
                yAxis : [
                    {
                        type : 'value'
                    }
                ],
                series : [
                    {
                        name:'IP:点击量',
                        type:'line',
                        smooth:true,

                        data:(function(){
                            return ip_num;
                        })()
                    },{
                        name:'PV:访问量',
                        type:'line',
                        smooth:true,

                        data:(function(){
                            return pv_num;
                        })()
                    }
                ]
            };
            // 为echarts对象加载数据
            visitChart.setOption(option);

        }
    })
}

/*
* 会员图表
* */
function getMemberChart(memberChart)
{
    var starttime = $("#starttime").val();
    var endtime = $("#endtime").val();
    $.ajax({
        type:'POST',
        url:URL+'index/ajax_member_num',
        data:{starttime:starttime,endtime:endtime},
        dataType:'json',
        beforeSend:function(){
            memberChart.showLoading({text:'正在加载数据...'})
            memberChart.clear();

        },
        success:function(data){
            memberChart.hideLoading();
            var membernum = eval(data.member);
            var label = eval(data.labels);
            var option = {
                title : {
                    text: '',
                    subtext: ''
                },
                tooltip : {
                    trigger: 'axis'
                },
                legend: {
                    data:['新增会员']
                },
                toolbox: {
                    show : false,
                    feature : {
                        mark : {show: true},
                        dataView : {show: true, readOnly: false},
                        magicType : {show: true, type: ['line', 'bar', 'stack', 'tiled']},
                        restore : {show: true},
                        saveAsImage : {show: true}
                    }
                },
                calculable : true,
                xAxis : [
                    {
                        type : 'category',
                        boundaryGap : false,
                        data :(function(){
                            return label ? label : ['周一','周二','周三','周四','周五','周六','周日'];
                        })()
                    }
                ],
                yAxis : [
                    {
                        type : 'value'
                    }
                ],
                series : [
                    {
                        name:'新增会员',
                        type:'line',
                        smooth:true,

                        data:(function(){
                            return membernum;
                        })()
                    }
                ]
            };
            // 为echarts对象加载数据
            memberChart.setOption(option);


        }

    });

}