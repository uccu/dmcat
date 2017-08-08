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
<textarea placeholder="回复内容/Reply Content"></textarea>
<a href="javascript:void (0)" class="submitrefer">回复/Reply</a>
<script src="/app/js/main.js"></script>
<script src="/app/js/jquery-1.8.3.min.js"></script>
<script>
    var a=new URL(location);
    var id = a.searchParams.get('id')
    $('a').click(function(){
        if($('textarea').val())
            $.post('/teacher/reply',{id:id,message:$('textarea').val()},function(d){
                if(d.code==200){
                    alert('回复成功！/ Success!');location = 'parent'
                }
            },'json')
    })

</script>
</body>
</html>