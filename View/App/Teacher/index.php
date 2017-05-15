<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>老师/teacher</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <!--<link rel="stylesheet" href="css/bootstrap.min.css">-->
    <link rel="stylesheet" href="/app/css/reset.css">
    <link rel="stylesheet" href="/app/css/index.css">
</head>

<body>
<div class="banner">
    <img src="/app/img/b1.png" alt="">
</div>
<div class="parent-main">
    <div class="parent-child personal">
        <a href="profile"></a>
        <p>个人资料<br><span>personal profile</span></p>
    </div>
    <div class="parent-child child">
        <a href="lists"></a>
        <p>孩子记录<br><span>child record</span></p>
    </div>
    <div class="parent-child leave">
        <a href="parent"></a>
        <p>家长留言<br><span>Leave message</span></p>
    </div>
</div>

<div class="parent-main">
    <div class="parent-child conduct">
        <a href="apply"></a>
        <p>删除申请 <br><span>apply</span></p>
    </div>
</div>

<script src="/app/js/main.js"></script>
<script src="/app/js/jquery-1.8.3.min.js"></script>
<script>
    $(function(){
        $(".headpic img").click(function(){
            $(this).parent().find("a").addClass('xuanze');
            $(this).parent().siblings().find("a").removeClass('xuanze')
        })
    })
</script>
</body>
</html>