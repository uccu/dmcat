<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title<?php echo $this->lang->recruit->look_message;?></title>
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
<?php foreach($list as $s){?>
<a href="view_exam_info?id=<?php echo $s->recruit_id;?>">
    <div class="bm">
        <e><h1><?php echo $this->lang->language=='cn'?$s->title:$s->title_en;?></h1></e>
        <p><em style="margin-right: 1.5rem;"><?php echo $this->lang->recruit->parent_name;?>：<?php echo $this->lang->language=='cn'?$s->parent_name:$s->parent_name_en;?></em> <em><?php echo $this->lang->recruit->student_name;?>：<?php echo $this->lang->language=='cn'?$s->student_name:$s->student_name_en;?></em></p>
    </div>
</a>
<?php }?>

<script src="js/main.js"></script>
</body>
</html>