/**
 * Created by yangxueyao on 2017/4/6.
 */
new function (){
    var _self = this;
    _self.width = 640;//设置默认最大宽度
    _self.fontSize = 100;//默认字体大小
    _self.widthProportion = function(){
        var p =(document.body&&document.body.clientWidth||document.getElementsByTagName("html")[0].offsetWidth)/_self.width;return p>1?1:p<0.5?0.5:p;};
    _self.changePage = function(){        document.getElementsByTagName("html")[0].setAttribute("style","font-size:"+_self.widthProportion()*_self.fontSize+"px");
    }
    _self.changePage();    window.addEventListener('resize',function(){_self.changePage();},false);};



/****************************/
/*msg提示信息
 * qnmlgb是否刷新
 * local_url跳转地址
 *动态创建网页提示信息，1秒后提示清除
 */
function show_alert(msg,qnmlgb,local_url){
    var html = '<div class="alert_dialog"><div class="show_alert">' + msg + '</div></div>';
    $("body").append(html);//讲动态创建的html标签添加到网页
    var i = 0;
    var setI = setTimeout(function(){
        $(".alert_dialog").remove();
        if(qnmlgb == true){
            history.go(0);//history.go(0)刷新   history.go(1)前进    history.go(-1)后退
        }
        if(local_url !="" && local_url !=undefined){
            get_url(local_url);
        }
        if (i>=1){
            clearTimeout(setI);
        }
        i++;
    },3000);


}
//获取跳转路径
function get_url(url){
    window.location.href = url;
}
