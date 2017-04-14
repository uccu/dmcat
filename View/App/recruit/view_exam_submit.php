<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>填写考生资料</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="css/reset.css">
    <!--<link rel="stylesheet" href="css/public.css">-->
    <link rel="stylesheet" href="css/kszl.css">
</head>
<body>
<!--<div class="header">-->
    <!--<em></em>-->
    <!--<span>填写考生资料</span>-->
    <!--<a href="#"></a>-->
<!--</div>-->
<div class="zl-main">
    <form>
    <input type="hidden" name="recruit_id">
        <ul>
            <li><?php echo $this->lang->recruit->parent_name;?><input type="text" name="parent_name"></li>
            <li><?php echo $this->lang->recruit->parent_name;?>(EN)<input type="text" name="parent_name_en"></li>
            <li><?php echo $this->lang->recruit->student_name;?><input type="text" name="student_name"></li>
            <li><?php echo $this->lang->recruit->student_name;?>(EN)<input type="text" name="student_name_en"></li>
            <li><?php echo $this->lang->recruit->height;?>(CM)<input type="number" name="height"></li>
            <li><?php echo $this->lang->recruit->weight;?>(KG)<input type="number" name="weight"></li>
            <li><?php echo $this->lang->recruit->age;?><input type="number" name="age"></li>
            <li><?php echo $this->lang->recruit->phone;?><input type="text" style="width: 5.2rem;" name="phone"></li>
            <li style="height: 1.4rem;"> <span><?php echo $this->lang->recruit->address;?></span><textarea name="address"></textarea></li>
        </ul>
        <a href="#"><?php echo $this->lang->recruit->pay;?></a>
    </form>
</div>

<script src="js/main.js"></script>
<script src="js/jquery-1.8.3.min.js"></script>
<script>

    $('a').bind('click',function(){
        var p = new URL(location),id = p.searchParams.get('id');
        $('[name="recruit_id"]').val(id);
        var data = $('form').serializeArray();
        for(v in data){
            if(!data[v].value){
                alert('请填写完整！');return
            }
        }
        
        $.post('/recruit/post',data,function(d){
            if(d.code == 200){
                location = '/wc/pay?out_trade_no='+d.data.out_trade_no;
            }
            else alert(d.message);
        },'json');
    })
    


</script>
</body>
</html>