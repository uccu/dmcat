<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>报名信息</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/public.css">
</head>
<style>
    .bm p{
        border:none;
    }
</style>
<body>
<!--<div class="header">-->
    <!--<em></em>-->
    <!--<span>报名信息</span>-->
    <!--<a href="#"></a>-->
<!--</div>-->
<a href="/wc/testprepay">测试</a>
<?php foreach($list as $s){?>
<a href="view_exam_info?id=<?php echo $s->recruit_id;?>">
    <div class="bm">
        <e><h1>您已报名《<?php echo $s->title;?>》</h1></e>
        <p><em style="margin-right: 1.5rem;">家长姓名：<?php echo $s->parent_name;?></em> <em>孩子姓名：<?php echo $s->student_name;?></em></p>
    </div>
</a>
<?php }?>

<script src="js/main.js"></script>
</body>
</html>