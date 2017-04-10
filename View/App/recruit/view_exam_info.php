<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <title>报名详情</title>
    <link rel="stylesheet" href="css/reset.css">
    <!--<link rel="stylesheet" href="css/public.css">-->
    <link rel="stylesheet" href="css/bmxq.css">
</head>
<body>
<!--<div class="header">-->
    <!--<em></em>-->
    <!--<span>报名详情</span>-->
    <!--<a href="#"></a>-->
<!--</div>-->
<div class="xq-main">
    <h1><?php echo $info->title?></h1>
    <p>报名时间：<span><?php echo $info->date.' '.$info->time;?></span></p>
    <p>地址：<span><?php echo $info->address;?></span></p>
    <p>报考说明：</p>
    <p style="color: #6f6f6f;">
        <?php echo $info->comment;?>
    </p>

    <p>报名费用：<em>500元</em></p>
    <a href="view_exam_submit?id=<?php echo $info->id;?>">填写报名资料</a>
</div>

<script src="js/main.js"></script>
</body>
</html>