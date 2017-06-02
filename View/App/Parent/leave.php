<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>留言/Leave a Message</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="/app/css/reset.css">
    <link rel="stylesheet" href="/app/css/leaveWord.css">
</head>
<body>
<textarea placeholder="请输入您对学校的意见或建议...&nbsp;&nbsp;Please enter your comments or suggestions to the school..."></textarea>
<a href="javascript:void (0)">提交/Refer</a>
<script src="/app/js/main.js"></script>
<script src="/app/js/jquery-1.8.3.min.js"></script>
<script>

    $('a').click(function(){
        $.post('/parent/add_school_message',{message:$('textarea').val()},function(d){
            if(d.code==200){
                alert('留言成功！/ Success!');location = 'index'
            }
        },'json')
    })

</script>
</body>
</html>