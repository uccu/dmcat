<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>家长/Genearch</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <!--<link rel="stylesheet" href="css/bootstrap.min.css">-->
    <link rel="stylesheet" href="/app/css/reset.css">
    <style>
        .banner{
            width:100%;
            height:2.8rem;
            margin-bottom: 0.5rem;
        }
        .banner img{
            height:2.8rem;
            width:100%;
        }



        .headpic{
            position: absolute;
            top:1rem;
            /*left:1.6rem;*/
            width:100%;
            text-align: center;
        }
        .headpic span{
            display:inline-block;
            margin:auto;
            /*width:32%;*/
            /*float: left;*/
            position: relative;

            /*width:100%;*/
        }
        .headpic span img{
            display: block;
            border-radius: 100%;
            width:1rem;
            height:1rem;
            margin: 0 10px;
        }
        .headpic span em{
            font-size: 0.25rem;
            display: block;
            color: #ffffff;
            width:100%;
            text-align: center;
        }
        .xuanze{
            display: block;
            width:0.3rem;
            height:0.3rem;
            background: url("/app/img/xx_03.png") no-repeat;
            background-size: 0.3rem 0.3rem;
            position: absolute;
            left:0.5rem;
            top:0.7rem;
            z-index: 9999;
        }

        .parent-main{
            width:6rem;
            height: 2.5rem;
            display: flex;
            flex-flow: row;
            margin:auto;
        }
        .parent-child{
            width:1.98rem;
            height:2.5rem;
        }

        .parent-child a{
            display: block;
            width:100%;
            height:1.5rem;
            /*background: url("/app/img/p1.png") no-repeat;*/
            /*background-position: center 0;*/
            /*background-size: 60%;*/
        }
        .parent-child p{
            font-size: 0.25rem;
            text-align: center;
        }
        .parent-child p span{
            font-size: 0.1rem;
            color: #6f6f6f;
        }
        .personal a{
            background:url("/app/img/p1.png") no-repeat;
            background-position: center 0;
            background-size: 1.2rem;
        }
        .child a{
            background:url("/app/img/p2.png") no-repeat;
            background-position: center 0;
            background-size: 1.2rem;
        }
        .leave a{
            background:url("/app/img/p3.png") no-repeat;
            background-position: center 0;
            background-size: 1.2rem;
        }
        .vote a{
            background:url("/app/img/p4.png") no-repeat;
            background-position: center 0;
            background-size: 1.2rem;
        }
        .activity a{
            background:url("/app/img/p5.png") no-repeat;
            background-position: center 0;
            background-size: 1.2rem;
        }
        .ask a{
            background:url("/app/img/p6.png") no-repeat;
            background-position: center 0;
            background-size: 1.2rem;
        }
        .menu a{
            background:url("/app/img/p7.png") no-repeat;
            background-position: center 0;
            background-size: 1.2rem;
        }
        .course a{
            background:url("/app/img/p8.png") no-repeat;
            background-position: center 0;
            background-size: 1.2rem;
        }
        .notice a{
            background:url("/app/img/p9.png") no-repeat;
            background-position: center 0;
            background-size: 1.2rem;
        }
        .conduct a{
            background:url("/app/img/p10.png") no-repeat;
            background-position: center 0;
            background-size: 1.2rem;
        }


    </style>
</head>
<body>
<div class="banner">
    <img src="/app/img/b1.jpg" alt="">
</div>
<div class="headpic">
    <!--<span>-->
        <!--<a href="javascript:void(0)"></a>-->
        <!--<img src="img/jl.png" alt="">-->
        <!--<em>一一</em>-->
    <!--</span>-->
</div>
<div class="parent-main">
    <div class="parent-child personal">
        <a href="javascript:void(0)"></a>
        <p>个人资料<br><span>personal profile</span></p>
    </div>
    <div class="parent-child child">
        <a href="javascript:void(0)"></a>
        <p>孩子记录<br><span>child record</span></p>
    </div>
    <div class="parent-child leave">
        <a href="javascript:void(0)"></a>
        <p>留言<br><span>leave message</span></p>
    </div>
</div>
<div class="parent-main">
    <div class="parent-child vote">
        <a href="javascript:void(0)"></a>
        <p>投票<br><span>vote</span></p>

    </div>
    <div class="parent-child activity">
        <a href="javascript:void(0)"></a>
        <p>活动<br><span>activity</span></p>
    </div>
    <div class="parent-child ask">
        <a href="javascript:void(0)"></a>
        <p>请假<br><span>ask for leave</span></p>
    </div>
</div>
<div class="parent-main">
    <div class="parent-child menu">
        <a href="javascript:void(0)"></a>
        <p>菜单<br><span>menu</span></p>
    </div>
    <div class="parent-child course">
        <a href="javascript:void(0)"></a>
        <p>课程<br><span>course</span></p>
    </div>
    <div class="parent-child notice">
        <a href="javascript:void(0)"></a>
        <p>校内通知<br><span>notice</span></p>
    </div>
</div>
<div class="parent-main">
    <div class="parent-child conduct">
        <a href="javascript:void(0)"></a>
        <p>校外宣传 <br><span>propaganda</span></p>
    </div>
</div>

<script src="/app/js/main.js"></script>
<script src="/app/js/jquery-1.8.3.min.js"></script>
<script>
    var student_id = 0;
    $(function(){
        function GetQueryString(name){
            var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
            var r = window.location.search.substr(1).match(reg);
            if(r!=null)return  unescape(r[2]); return null;
        }
        var id = GetQueryString('id');
        $.ajax({
            url:"/parent/child",
            type:"post",
            data:{
                id:<?php echo $id;?>
            },
            dataType:"json",
            cache: false,
            success:function(e){
                if (e.code==200){
                    var result=e.data.list;
                    for(var i=0; i<result.length; i++){

                        var headhtml='<span><a href="javascript:void(0)"></a><img data="'+result[i].id+'" src="'+result[i].fullAvatar+'"><em>'+result[i].name+'</em><em>'+result[i].name_en+'</em></span>'
                        $(".headpic").append(headhtml);

                        $(".headpic img").click(function(){
                            student_id = $(this).attr('data');
//                            console.info(student_id)
                            $(this).parent().find("a").addClass('xuanze');
                            $(this).parent().siblings().find("a").removeClass('xuanze')
                        }).eq(0).click();


                        $(".personal").click(function () {
//                            console.info(student_id)
                            location.href='profile?id='+student_id
                        })
                        $(".child").click(function(){
                            location.href='record?id='+student_id
                        })
                        $(".leave").click(function(){
                            location.href='leave?id='+student_id
                        })
                        $(".vote").click(function(){
                            location.href='vote?id='+student_id
                        })
                        $(".activity").click(function(){
                            location.href='activity?id='+student_id
                        })
                        $(".ask").click(function(){
                            location.href='ask?id='+student_id
                        })
                        $(".menu").click(function(){
                            location.href='menu?id='+student_id
                        })
                        $(".course").click(function(){
                            location.href='course?id='+student_id
                        })
                        $(".notice").click(function(){
                            location.href='notice?id='+student_id
                        })

                    }
                }
            }
        })
    })

</script>
</body>
</html>