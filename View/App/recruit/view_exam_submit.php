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
    <input type="hidden" name="id">
        <ul>
            <li>家长姓名（中文）<input type="text" name="parent_name"></li>
            <li>家长姓名（英文）<input type="text" name="parent_name_en"></li>
            <li>孩子姓名（中文）<input type="text" name="student_name"></li>
            <li>孩子姓名（中文）<input type="text" name="student_name_en"></li>
            <li>孩子身高（cm）<input type="number" name="height"></li>
            <li>孩子体重（kg）<input type="number" name="weight"></li>
            <li>孩子年龄（周岁）<input type="number" name="age"></li>
            <li>电话<input type="text" style="width: 5.2rem;" name="phone"></li>
            <li style="height: 1.4rem;"> <span>地址</span><textarea name="地址" name="address"></textarea></li>
        </ul>
        <a href="#">提交并支付500元报名费用</a>
    </form>
</div>

<script src="js/main.js"></script>
<script src="js/jquery-1.8.3.min.js"></script>
<script>
    $('a').bind('click',function(){
        var p = new URL(location),id = p.searchParams.get('id');
        $('[name="id"]').val(id);
        var data = $('form').serializeArray();
        for(v in data){
            if(!data[v].value)alert('请填写完整！');
        }
        
        $.post('/recruit/post',data,function(d){
            if(d.code == 200)location.href='view_my_submit';
            else alert(d.message);
        },'json');
    })
    


</script>
</body>
</html>