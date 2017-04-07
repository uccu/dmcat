<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>在线报名</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/public.css">
</head>
<body>
<div class="header">
    <em></em>
    <span></span>
    <a href="view_my_submit">查看报名信息</a>
</div>
<?php foreach($list as $v){?>
<div class="bm">
    <h1><?php echo $v->title;?> <a href="#"></a></h1>
    <p>报名时间：<?php echo $v->date.' '.$v->time;?></p>
    <p>地址：<?php echo $v->address;?></p>
    <span><?php echo date('Y年m月d日',$v->date);?></span>
</div>
<?php }?>
<script src="js/main.js"></script>
</body>
</html>