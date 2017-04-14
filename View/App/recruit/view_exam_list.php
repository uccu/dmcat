<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $this->lang->recruit->online_register;?></title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/public.css?2">
</head>
<body style="padding-bottom: 50px;">
<!--<div class="header">-->
    <!--<em></em>-->
    <!--<span>在线报名</span>-->
    <!--<a href="#">查看报名信息</a>-->
<!--</div>-->
<?php foreach($list as $v){?>
<a href="view_exam_info?id=<?php echo $v->id;?>">
    <div class="bm">
        <e><h1><?php echo $this->lang->language=='cn'?$v->title:$v->title_en;?></h1></e>
        <p><?php echo $this->lang->recruit->date;?>：<?php echo $v->date;?></p>
        <p><?php echo $this->lang->recruit->address;?>：<?php echo $this->lang->language=='cn'?$v->address:$v->address_en;?></p>
        <span><?php echo date('Y-m-d',$v->create_time);?></span>
    </div>
</a>
<?php }?>
<div class="footer">
    <a href="view_my_submit"><?php echo $this->lang->recruit->look_message;?></a>
</div>
<script src="js/main.js"></script>
</body>
</html>