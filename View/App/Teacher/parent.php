<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>家长留言/Parents' Messages</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="/app/css/reset.css">
    <link rel="stylesheet" href="/app/css/parentMessages.css">
    <style>
        .mainbody{
            margin:10px auto
        }
        .box{
            display:block
        }
    </style>
</head>
<body style="margin:auto;padding:0">
    <div class="box">
        
    </div>
<script src="/app/js/main.js"></script>
<script src="/js/jquery.min.js"></script>
<script>
    var page = 1;
    function getPage(){
        $.post('/teacher/get_parent_message',{page:page},function(d){

            for(var e in d.data.list)
                $('.box').append('<div class="mainbody"><h1>'+d.data.list[e].title+'</h1><p>'+d.data.list[e].reply+'</p><span>'+d.data.list[e].date+'</span></div>')
            $(window).unbind('scroll');
            if(d.data.list.length){
                page++;
                $(window).bind('scroll',function(e){
                    if($('body').scrollTop()+$(window).height() == $('body').height()+20){
                        getPage();
                    }
                })
            }
        },'json')
    }
    
    getPage();
                


</script>
</body>
</html>