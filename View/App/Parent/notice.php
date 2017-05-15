<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>校内通知/Notice</title>
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
            url:"/parent/get_notice_list",
            type:"get",
            data:{},
            dataType:"JSON",
            cache: false,
            success:function(e){
                if (e.code==200){
                    var result = e.data.list;
                    for(var i=0; i<result.length;i++){
//                        console.info(result.length)
                        var html='<div class="bm zxbm"><a href="notice_detail?id='+result[i].id+'"><h1>'+result[i].title+'</h1></a><p>'+result[i].short_message+'</p><span>'+result[i].create_date+'</span></div>'
                        $("body").append(html);

                    }

                }
            }
        })
    })

</script>
</body>
</html>