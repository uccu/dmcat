new function (){
   var _self = this;
   _self.width = 640;//设置默认最大宽度
   _self.fontSize = 100;//默认字体大小
   _self.widthProportion = function(){var p = (document.body&&document.body.clientWidth||document.getElementsByTagName("html")[0].offsetWidth)/_self.width;return  p<0.5?0.5:p; /*p>1?1:p<0.5?0.5:p*/};
	_self.changePage = function(){
       document.getElementsByTagName("html")[0].setAttribute("style","font-size:"+_self.widthProportion()*_self.fontSize+"px");
   }
   _self.changePage();
   window.addEventListener('resize',function(){_self.changePage();},false);
};
//$.ajaxSettings.xhrFields={withCredentials: true};
var config = [];
config['timeOut'] = 1500;
config['host'] = "http://121.199.8.244:8780/";
config['imageUrl'] = "http://121.199.8.244:8780/";


/**
 * 获取url参数
 * @returns {Array}
 */
function get_param(url){
	if (url != '' && url != undefined && url != null){
		var local_url = url;
	} else {
		var local_url = document.location.href;
	}
	var data = local_url.split("?");
	data = data[1];
	var get_data = [];
	if(data != '' && data != undefined){
		data = data.split("&");
		$.each(data, function(i, v){
			var j = v.split("=");
			get_data[j[0]] = decodeURI(j[1]);
		});
	}
	return get_data;
}


/**
 * ajax请求数据
 * @param url
 * @param data
 * @param async
 * @param calBackFunction
 */
function requestData(url, data, calBackFunction, async) {
	var loading_html = '<div class="alert_dialog loading_dialog" style="position:fixed;top:0;left:0;height:100%;width:100%;z-index:999; background:rgba(0,0,0,0.3);"><div class="show_alert" style="background:transparent; padding: 0; border-radius: 0.1rem; overflow: hidden; box-shadow: none;"><img src="images/loading.gif" style="height:0.8rem; width:0.8rem; display:block; margin:0 auto;"/></div></div>';
	url = config.host + url;
	if(async == null || async == undefined){
		async = true;
	}
	var time = new Date();
	var header = {
		"clientVersion": "1.0",
		"requestTime": time,
		"serviceVersion": "1.0",
		"sourceID": "1.0",
		"userToken": "0e212bc5-6e64-4d61-9326-553c9fcffd70"
	};
	$.ajax({
		type: "POST",
		url: url,
		data: data,
		headers: header,
		dataType: "JSON",
		async: async,
		beforeSend : function(){
			$("body").append(loading_html);
		},
		success : function(json){
			calBackFunction(json);
			$(".loading_dialog").remove();
		},
		error : function(XMLHttpRequest){
			$(".loading_dialog").remove();
			// console.log(XMLHttpRequest);
			show_alert('请检查网络');
		}
	});
}

/**
 *
 * @param msg 提示信息
 * @param renovate 是否刷新
 * @param local_url 跳转地址
 * @param rep
 * @param result
 */
function show_alert(msg, renovate, local_url, rep, result){
	if (result != "success" && result != "error") {
		result = "";
	} else {
		result = "alert_" + result;
	}
	var html = '<div class="alert_dialog"><div class="show_alert '+result+'">'+msg+'</div></div>';
	$("body").append(html);
	var i = 0;
	var setI = setTimeout(function(){
		$('.alert_dialog').remove();
		if(renovate == true){
			history.go(0);
		}
		if(local_url != "" && local_url != undefined){
			redirect(local_url, rep);
		}
		if(i >= 1){
			clearTimeout(setI);
		}
		i++;
	}, config.timeOut);
}

/**
 * 提示信息并返回
 * @param msg
 * @param is_sx
 * @param result
 */
function show_alert_back(msg, is_sx, result) {
	if (result != "success" && result != "error") {
		result = "";
	} else {
		result = "alert_" + result;
	}
	var html = '<div class="alert_dialog"><div class="show_alert '+result+'">'+msg+'</div></div>';
	$("body").append(html);
	var i = 0;
	var setI = setTimeout(function(){
		$('.alert_dialog').remove();
		if(is_sx == true){
			location.reload();
		}
		history.back();
		if(i >= 1){
			clearTimeout(setI);
		}
		i++;
	}, config.timeOut);
}

/**
 * 验证是否登陆
 * @param is_redirect
 * @returns {boolean}
 */
function checkLogin(is_redirect) {
	if(user_token == "" || user_token == undefined || user_token == null){
		if(is_redirect != true) {
			show_alert("请登录", "", "index.html", false);
		}
		return false;
	}
	return true;
}

/**
 * 跳转链接
 * @param url 连接地址
 * @param rep 是否替换本页历史
 * @param target 是否在新窗口打开
 */
function redirect(url, rep, target){
	if (target == true) {
		window.open(url);
	} else if (rep == true) {
		location.replace(url);
	} else {
		window.location.href = url;
	}
}

/**
 * 去除首尾空格
 * @param string
 * @returns {XML|void|*}
 */
function tirm(string){
	return string.replace(/(^\s*)|(\s*$)/g, "");
}

/**
 * 价格格式化
 * @param price
 * @returns {string}
 */
function formatPrice(price){
	return parseFloat(price).toFixed(2);
}


/**
 *
 * 返回上一页并刷新
 */
function getBackShuaXin(){
	location.reload();
	history.back();
}

/**
 * 拼接图片地址
 * @param string
 * @returns {object}
 */
function getImagesList(string) {
	var arr = string.split(";");
	$.each(arr, function(i, v){
		arr[i] = config.imageUrl + v;
	});
	return arr;
}

/**
 * 时间戳转换
 * @param time 到秒的时间戳,如果穿传空,则为当前时间
 * @param his 是否到时分秒
 * @returns {string}
 */
function get_date(time, his){
	if(time != "") {
		time = new Date(time * 1000);
	} else {
		time = new Date();
	}
	var year = time.getFullYear();
	var month = parseInt(time.getMonth()) + 1;
	var day = time.getDate();
	month = (month>=10)?month:"0"+month;
	day = (day>=10)?day:"0"+day;
	if(his == true){
		var hours = time.getHours();
		hours = (hours>=10)?hours:"0"+hours;
		var min = time.getMinutes();
		min = (min>=10)?min:"0"+min;
		var sen = time.getSeconds();
		sen = (sen>=10)?sen:"0"+sen;
		return year+'-'+month+'-'+day+' '+hours+':'+min+':'+sen;
	}else{
		return year+'-'+month+'-'+day;
	}
}

/**
 * 是否存在上一步, 存在则返回, 否则跳转到首页
 */
function getReferrer() {
	if(document.referrer == ""){
		redirect("product_list.html", true);
	} else {
		history.back();
	}
}


/**
 * 判断是否是微信浏览器
 * @returns {boolean}
 */
function isWeiXin(){
	var ua = window.navigator.userAgent.toLowerCase();
	if(ua.match(/MicroMessenger/i) == 'micromessenger'){
		return true;
	}else{
		return false;
	}
}

/**
 * 多选框
 * @param object
 * @param className
 */
function checkboxIcon(object, className) {
	var obj = $(object);
	if (obj.hasClass("checked")) {
		obj.removeClass("checked");
		if (className) {
			$("." + className).addClass("no-click");
		}
	} else {
		obj.addClass("checked");
		if (className) {
			$("." + className).removeClass("no-click");
		}
	}
}


imgurl="http://121.199.8.244:8780/hsshop/"