/**
 * Created by yangxueyao on 2017/4/18.
 */

//ability记录当前分数
var ability = {
    learning:0,
    eat:0,
    life:0
};

$('.star img').bind('click',function(){
    var index = $(this).parent().index();
    var top = $(this).parent().parent();
    var type = top.attr('data-type');
    ability[type] = index + 1;
    for(var i = 0;i<=index;i++){
        top.find('img').eq(i).attr('src',"/app/img/star_red.png")
    }
    for(var i = index+1;i<6;){
        top.find('img').eq(i++).attr('src',"/app/img/star.png")
    };
})
