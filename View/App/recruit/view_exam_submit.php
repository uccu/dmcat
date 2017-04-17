<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $this->lang->recruit->fill_in;?></title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="css/reset.css">
    <!--<link rel="stylesheet" href="css/public.css">-->
    <link rel="stylesheet" href="css/kszl.css?1">
</head>
<body>
<style>
li p{
    position:absolute;width:1.6rem;text-align:right
}
li{color:#666}
.zl-main ul li input{width:100%;}
.zl-main ul li textarea{width:100%;height: 1rem;line-height: 0.5rem;}
</style>
<div class="zl-main">
    <form>
    <input type="hidden" name="recruit_id">
        <ul>
            <li><input type="text" name="parent_name" placeholder="<?php echo $this->lang->recruit->parent_name.($this->lang->language=='cn'?'(中)':'(cn)');?>"></li>
            <li><input type="text" name="parent_name_en" placeholder="<?php echo $this->lang->recruit->parent_name.($this->lang->language=='cn'?'(英)':'(en)');?>"></li>
            <li><input type="text" name="student_name" placeholder="<?php echo $this->lang->recruit->student_name.($this->lang->language=='cn'?'(中)':'(cn)');?>"></li>
            <li><input type="text" name="student_name_en" placeholder="<?php echo $this->lang->recruit->student_name.($this->lang->language=='cn'?'(英)':'(en)');?>"></li>
            <li><input type="number" name="height" placeholder="<?php echo $this->lang->recruit->height;?>(cm)"></li>
            <li><input type="number" name="weight" placeholder="<?php echo $this->lang->recruit->weight;?>(kg)"></li>
            <li><input type="number" name="age" placeholder="<?php echo $this->lang->recruit->age;?>"></li>
            <li><input type="text" name="phone" placeholder="<?php echo $this->lang->recruit->phone;?>"></li>
            <li style="height: 1.4rem;"><textarea name="address" placeholder="<?php echo $this->lang->recruit->address;?>"></textarea></li>
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
        var he = $('[name="height"]').val();
        if(he < 85 || he > 102){
            alert('<?php echo $this->lang->recruit->height_error;?>');return;
        }
        var we = $('[name="weight"]').val();
        if(we < 12 || we > 23){
            alert('<?php echo $this->lang->recruit->weight_error;?>');return;
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