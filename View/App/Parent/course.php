<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>课程/Course</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="/app/css/reset.css">
    <link rel="stylesheet" href="/app/css/menu.css">
    <link rel="stylesheet" href="/app/css/course.css">
</head>
<body>
<div class="layout">
    <h1>上半学期课程安排表:</h1>
    <div class="course">
        <p>节数/星期</p>
        <ul>
            <li>星期一</li>
            <li>星期二</li>
            <li>星期三</li>
            <li>星期四</li>
            <li>星期五</li>
        </ul>
    </div>
    <div class="courseName">
        <p>第一节</p>
        <ul  class="first">
            <!--<li>语文</li>-->
            <!--<li>语文</li>-->
            <!--<li>语文</li>-->
            <!--<li>语文</li>-->
            <!--<li>语文</li>-->
        </ul>
    </div>
    <div class="courseName">
        <p>第二节</p>
        <ul>
            <!--<li>数学</li>-->
            <!--<li>数学</li>-->
            <!--<li>数学</li>-->
            <!--<li>数学</li>-->
            <!--<li>数学</li>-->
        </ul>
    </div>
    <div class="courseName">
        <p>第三节</p>
        <ul>
            <!--<li>英语</li>-->
            <!--<li>英语</li>-->
            <!--<li>英语</li>-->
            <!--<li>英语</li>-->
            <!--<li>英语</li>-->
        </ul>
    </div>
    <div class="courseName">
        <p>第四节</p>
        <ul>
            <!--<li>体育</li>-->
            <!--<li>体育</li>-->
            <!--<li>体育</li>-->
            <!--<li>体育</li>-->
            <!--<li>体育</li>-->
        </ul>
    </div>
    <div class="courseName">
        <p>第五节</p>
        <ul>
            <!--<li>自习</li>-->
            <!--<li>自习</li>-->
            <!--<li>自习</li>-->
            <!--<li>自习</li>-->
            <!--<li>自习</li>-->
        </ul>
    </div>
    <div class="courseName">
        <p>第六节</p>
        <ul>
            <!--<li>美术</li>-->
            <!--<li>美术</li>-->
            <!--<li>美术</li>-->
            <!--<li>美术</li>-->
            <!--<li>美术</li>-->
        </ul>
    </div>
    <div class="courseName">
        <p>第七节</p>
        <ul>
            <!--<li>自然</li>-->
            <!--<li>自然</li>-->
            <!--<li>自然</li>-->
            <!--<li>自然</li>-->
            <!--<li>自然</li>-->
        </ul>
    </div>
    <div class="courseName">
        <p>第八节</p>
        <ul>
            <!--<li>作文</li>-->
            <!--<li>作文</li>-->
            <!--<li>作文</li>-->
            <!--<li>作文</li>-->
            <!--<li>作文</li>-->
        </ul>
    </div>
</div>
<script src="/app/js/main.js"></script>
<script src="/app/js/jquery-1.7.1.min.js"></script>
<script>
    $(function(){
        function GetQueryString(name){
            var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
            var r = window.location.search.substr(1).match(reg);
            if(r!=null)return  unescape(r[2]); return null;
        }
        var id = GetQueryString('id');
        $.ajax({
            url:"/course/getw",
            type:"post",
            data:{
                id:<?php echo $id;?>,
                d:0
            },
            dataType:"json",
            cache: false,
            success:function(data){
                if(data.code==200){
                    var result = data.data.list;
                    for(var d in result){
                        for(var e in result[d]){
                            $('<li>').text(result[d][e]).appendTo('.courseName ul:eq('+d+')')
                        }
                    }
                }

            }
        })

    })
</script>
</body>
</html>