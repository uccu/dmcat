/**
 * Created by ZhuXueSong on 2017/6/7.
 */

var config = [];
config['timeOut'] = 1500;
config['base'] = "/Admin/";

var loadDialog = '<div id="LoadingDialog" style="z-index: 9999; width: 100%; height: 100%; position: fixed; top: 0; cursor: progress; background: transparent;"></div>';

/**
 * ajax 加载数据
 * @param url 地址
 * @param data post数据
 * @param calBackFunction 回掉函数
 */
function requestData(url, data, calBackFunction) {
    url = config.base + url;
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "JSON",
        timeout: 15000,
        beforeSend : function(){
            $("body").append(loadDialog);
        },
        success : function(json){
            $("#LoadingDialog").remove();
            calBackFunction(json);
        },
        error : function(XMLHttpRequest){
            $("#LoadingDialog").remove();
            console.log(XMLHttpRequest);
            alert('请检查网络');
        }
    });
}