/**
 * Created by Administrator on 15-12-17.
 */
var index=0;
var length=$("#img img").length;
var i=1;

//关键函数：通过控制i ，来显示图片
function showImg(i){
    $("#img img").eq(i).stop(true,true).fadeIn(1500).siblings("img").hide();
    $("#cbtn li").eq(i).addClass("hov").siblings().removeClass("hov");
}

function slideNext(){
   console.log("index:"+index);

    if(index >= 0 && index < length-1) {
        index++;
        showImg(index);
    }else{
        showImg(0);
        index=0;
        if(length>4){
            aniPx=(length-4)*102+'px'; //所有图片数 - 可见图片数 * 每张的距离 = 最后一张滚动到第一张的距离
            $("#cSlideUl ul").animate({ "left": "+="+aniPx },200);
        }
        i=1;

        return false;
    }
    if(i<0 || i>length-4){
        return false;
    }
    $("#cSlideUl ul").animate({ "left": "-=102px" },200)
    i++;
}

//setInterval(slideNext,5000);

function slideFront(){

    if(index >= 1 ) {
        --index;
        showImg(index);
    }
    if(i<2 || i>length+4){
        return false;
    }
    $("#cSlideUl ul").animate({ "left": "+=102px" },200)
    i--;
}

$("#img img").eq(0).show();
$("#cbtn li").eq(0).addClass("hov");

$("#cbtn tt").each(function(e){
    var str=(e+1)+"/"+length;
    $(this).html(str)
})

$(".picSildeRight,#next").click(function(){
    slideNext();
});

$(".picSildeLeft,#front").click(function(){
    slideFront();
});

$("#cbtn li").mouseover(function(){
    index  =  $("#cbtn li").index(this);
    showImg(index);
});

$("#next,#front").hover(function(){
    $(this).children("a").fadeIn();
},function(){
    $(this).children("a").fadeOut();
});
