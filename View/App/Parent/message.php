<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>消息/messages</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="/app/css/reset.css">
    <link rel="stylesheet" href="/app/css/parentMessages.css">
    <style>
        .mainbody{
            margin:10px auto;
            position:relative;
            width:auto;
            border-radius:0;
        }
        .mainbody.noread::after{
            content:' ';
            background-color:red;
            position:absolute;
            right:10px;
            top:10px;
            width:6px;
            height:6px;
            border-radius:50%
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
        $.post('/parent/get_message',{page:page},function(d){

            for(var e in d.data.list)
                $('.box').append('<div class="mainbody'+(d.data.list[e].isread=='1'?'':' noread')+'" data-url="'+d.data.list[e].url+'"><p>'+d.data.list[e].content+'</p><span>'+d.data.list[e].date+'</span></div>')
            $(window).unbind('scroll');

            $('.mainbody').unbind('click').bind('click',function(){
                if($(this).attr('data-url')){
                    location = $(this).attr('data-url')
                }
            });

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