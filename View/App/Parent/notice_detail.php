<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <title>通知详情/Notice Details</title>
    <link rel="stylesheet" href="/app/css/reset.css">
    <!--<link rel="stylesheet" href="css/public.css">-->
    <link rel="stylesheet" href="/app/css/noticedetails.css">
</head>
<body>
<!--<div class="header">-->
<!--<em></em>-->
<!--<span>报名详情</span>-->
<!--<a href="#"></a>-->
<!--</div>-->
<div class="xq-main">
    <!--<h1>关于五一劳动节放假通知</h1>-->
    <!--<p>各班：</p>-->
    <!--<p style="color: #6f6f6f;">-->
        <!--1.填写资料时请注意不要有任何空格。<br>-->
        <!--2.正确填写个人资料信息，和孩子的资料信息。<br>-->
        <!--3.在线报名成功后会安排入园考试，考试成功后即可安排后续入园事项。<br>-->
        <!--4.填写资料时请注意不要有任何空格。<br>-->
        <!--5.正确填写个人资料信息，和孩子的资料信息。<br>-->
        <!--6.在线报名成功后会安排入园考试，考试成功后即可安排后续入园事项。<br>-->
        <!--7.填写资料时请注意不要有任何空格。<br>-->
        <!--8.正确填写个人资料信息，和孩子的资料信息。<br>-->
        <!--9.在线报名成功后会安排入园考试，考试成功后即可安排后续入园事项。<br>-->
        <!--10.填写资料时请注意不要有任何空格。<br>-->
        <!--11.正确填写个人资料信息，和孩子的资料信息。<br>-->
        <!--12.在线报名成功后会安排入园考试，考试成功后即可安排后续入园事项。<br>-->
    <!--</p>-->
</div>

<script src="/app/js/jquery-1.8.3.min.js"></script>
<script src="/app/js/main.js"></script>
<script>
    $(function(){
//        function GetQueryString(name){
//            var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
//            var r = window.location.search.substr(1).match(reg);
//            if(r!=null)return  unescape(r[2]); return null;
//        }
//        var id = GetQueryString('id');
        var a=new URL(location);
        var id = a.searchParams.get('id')
//        console.info(id)
        $.ajax({
            url:"/parent/get_notice",
            type:"post",
            data:{
                id:id,
                student_id:2,
            },
            dataType:"json",
            cache: false,
            success:function(e){
                if (e.code==200){
                    var result = e.data.info;
                    var html = '<h1>'+result.title+'</h1><p style="color: #6f6f6f;">'+result.content+'</p>'
                    $(".xq-main").append(html);
                }
            }
        })
    })

</script>

</body>
</html>