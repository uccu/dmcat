/**
 * Created by yangxueyao on 2017/6/22.
 */
$(".header-right,.bbbx").click(function(){
    $(".bbbx").fadeToggle(300)
    $(".sidebar-box").toggle(300).css('right','0')
})
$('.sidebar-main').find('h1').click(function(){
    var that = $(this).parent();
    that.find('ul').toggle('500',function(){
        var obj = $(this);
        if (obj.css("display") == "none") {
            obj.siblings("h1").find("span").html("+");
        } else {
            obj.siblings("h1").find("span").html("-");
        }
    });

})

function setBannerWidth() {
    var htmlSize = parseFloat($('html').css("font-size"));
    var obj = $(".video-nav-top");
    var width = 0;
    $(".video-nav-top a").each(function(){
        width += parseFloat($(this).css("width")) + htmlSize * 0.1;
    });
    obj.css("width", width + "px");
}