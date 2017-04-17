<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $this->lang->recruit->fill_in;?></title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="css/reset.css">
    <!--<link rel="stylesheet" href="css/public.css">-->
    <link rel="stylesheet" href="css/kszl.css">
</head>
<body>
<style>
li p{
    position:absolute;width:1.6rem;text-align:right
}
li{color:#666}
.zl-main ul li input{width:4.5rem}
.zl-main ul li textarea{width:4.5rem}
</style>
<div class="zl-main">
    <form>
    <input type="hidden" name="recruit_id">
        <ul>
            <li><p><?php echo $this->lang->recruit->parent_name;?>(cn):</p><input type="text" name="parent_name"></li>
            <li><p><?php echo $this->lang->recruit->parent_name;?>(en):</p><input type="text" name="parent_name_en"></li>
            <li><p><?php echo $this->lang->recruit->student_name;?>(cn):</p><input type="text" name="student_name"></li>
            <li><p><?php echo $this->lang->recruit->student_name;?>(en):</p><input type="text" name="student_name_en"></li>
            <li><p><?php echo $this->lang->recruit->height;?>(CM):</p><input type="number" name="height"></li>
            <li><p><?php echo $this->lang->recruit->weight;?>(KG):</p><input type="number" name="weight"></li>
            <li><p><?php echo $this->lang->recruit->age;?>:</p><input type="number" name="age"></li>
            <li><p><?php echo $this->lang->recruit->phone;?>:</p><input type="text" name="phone"></li>
            <li style="height: 1.4rem;"><p><?php echo $this->lang->recruit->address;?>:</p></span><textarea name="address"></textarea></li>
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
                alert('<?php echo $this->lang->recruit->complete;?>');return
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