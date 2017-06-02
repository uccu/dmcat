<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>申请删除/Apply</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="/app/css/reset.css">
    <link rel="stylesheet" href="/app/css/applyDelete.css">
</head>
<body>
<textarea placeholder="请输入您申请删除的内容...&nbsp;&nbsp;Please enter your comments or suggestions to the school..."></textarea>
<a href="javascript:void (0)" id="submitLy">提交/Submit</a>
<script src="/app/js/main.js"></script>
<script src="/app/js/jquery-1.8.3.min.js"></script>
<script>

    $('a').click(function(){

        $.post('/school/add_school_message',{message:$('textarea').val()},function(){
            alert('申请成功！/Success!');location = 'index'
        })
    })

</script>
</body>
</html>