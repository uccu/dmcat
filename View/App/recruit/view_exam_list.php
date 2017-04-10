<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>在线报名</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/public.css?1">
</head>
<body style="padding-bottom: 50px;">
<!--<div class="header">-->
    <!--<em></em>-->
    <!--<span>在线报名</span>-->
    <!--<a href="#">查看报名信息</a>-->
<!--</div>-->
<?php foreach($list as $v){?>
<div class="bm">
    <a href="view_exam_info?id=<?php echo $v->id;?>"><h1><?php echo $v->title;?></h1></a>
    
    <p>报名时间：<?php echo $v->date.' '.$v->time;?></p>
    <p>地址：<?php echo $v->address;?></p>
    <span><?php echo date('Y年m月d日',$v->create_time);?></span>
</div>
<?php }?>
<div class="footer">
    <a href="view_my_submit">查看报名信息</a>
</div>
<script src="js/main.js"></script>
</body>
</html>