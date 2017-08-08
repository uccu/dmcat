<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>消息/Message</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="/app/css/reset.css">
    <link rel="stylesheet" href="/app/css/parentMessages.css">
    <style>
    	html{
    		height:100%;
    	}
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
        .mainbody small:nth-child(2){
            display: block !important;
        }
        .mainbody small{
            display:none;
        }
        
    </style>
</head>
<body style="margin:auto;padding:0；height:100%">
    <div class="box">
        
    </div>
    <div class="lookmessage-box" style="display: none;background:#ffffff;width:100%;position:absolute;margin: auto;top:0;bottom: 0;left:0;right:0;">
    	<div class="loomessage-main" contenteditable="false" style='margin: 0.2rem 0.19rem;width:5.6rem;min-height:2rem;font-size: 0.221rem;line-height: 0.4rem;padding:0 0.2rem'>
    		
    	</div>
    	
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
            /**********弹窗部分**************/
                var lookmessage = $(this).find('p').html();
                    $('.loomessage-main').html(lookmessage);
                    $(".lookmessage-box").css('display','block');
                });

                $('.lookmessage-box').click(function(){
                    $(this).css('display','none');
                })
            /**********弹窗部分**************/
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