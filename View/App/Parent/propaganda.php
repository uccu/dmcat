<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>校外宣传/propaganda</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="/app/css/reset.css">
    <link rel="stylesheet" href="/app/css/notice.css">
</head>
<body>
<!--<div class="header">-->
<!--<em></em>-->
<!--<span>在线报名</span>-->
<!--<a href="#">查看报名信息</a>-->
<!--</div>-->
<div class="bm zxbm">
    <!--<a href=""><h1>关于2017年夏季招生的通知</h1></a>-->
    <!--<p>常春藤幼儿园今年的夏季招生开始啦！！大家赶紧介绍朋友</p>-->
    <!--<p>来报名吧！</p>-->
    <!--<span>校办中心 &nbsp;&nbsp;&nbsp; 2017年5月10日</span>-->
</div>

<script src="/app/js/jquery-1.8.3.min.js"></script>
<script src="/app/js/main.js"></script>
<script>
    $(function(){
        function GetQueryString(name){
            var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
            var r = window.location.search.substr(1).match(reg);
            if(r!=null)return  unescape(r[2]); return null;
        }
        var id = GetQueryString('id');

        $.ajax({
            url:"/notice/get_propaganda_lists",
            type:"get",
            data:{},
            dataType:"JSON",
            cache: false,
            success:function(e){
                if (e.code==200){
                    var result = e.data.list;

                    for(var i=0; i<result.length;i++){
                        var html='<div class="bm zxbm"><a href="propaganda_detail?id='+result[i].id+'"><h1>'+result[i].title+'</h1></a><p>'+result[i].short_message+'</p><span>'+result[i].date+'</span></div>'
                        $("body").append(html);

                    }

                }
            }
        })
    })

</script>
</body>
</html>