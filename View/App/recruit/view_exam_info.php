<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <title><?php echo $this->lang->recruit->online_register;?></title>
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
    <h1><?php echo $this->lang->language=='cn'?$info->title:$info->title_en?></h1>
    <p><?php echo $this->lang->recruit->date;?>：<span><?php echo $info->date.' '.$info->time;?></span></p>
    <p><?php echo $this->lang->recruit->address;?>：<span><?php echo $this->lang->language=='cn'?$info->address:$info->address_en;?></span></p>
    <p><?php echo $this->lang->recruit->explain;?>：</p>
    <p style="color: #6f6f6f;">
        <?php echo $this->lang->language=='cn'?$info->comment:$info->comment_en;?>
    </p>

    <p><?php echo $this->lang->recruit->fee;?>：<em>500</em></p>
    <a href="view_exam_submit?id=<?php echo $info->id;?>"><?php echo $this->lang->recruit->fill_in;?></a>
</div>

<script src="js/main.js"></script>
</body>
</html>