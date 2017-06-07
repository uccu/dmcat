<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>留言/Leave a Message</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="/app/css/reset.css">
    <link rel="stylesheet" href="/app/css/leaveWord.css">
    <link rel="stylesheet" href="/app/css/vote.css">
</head>
<body>
<div class="usTab voteclassify">
    <div class="vote-tab schoolMessage active">
        <a href="javascript:void (0)" >学校留言<span>School Messages</span></a>
    </div>
    <div class="vote-tab teacherMessage">
        <a href="javascript:void (0)">老师留言<span>Teacher Messages</span></a>
    </div>
</div>
<!-- <div class="tabContent schoolMessage"  style="display: block;" >
</div>
<div class="tabContent teacherMessage">
</div> -->
<textarea placeholder="请输入您对学校的意见或建议 please enter your comments or suggestions to the school"></textarea>
<a href="javascript:void (0)" class="submitrefer">提交/Refer</a>
<script src="/app/js/main.js"></script>
<script src="/app/js/jquery-1.8.3.min.js"></script>
<script>
	$(".usTab").find(".vote-tab").click(function(){
        $(".tabContent").css("display","none");
        $(this).addClass("active").siblings().removeClass("active");
        $(".tabContent").eq($(this).index()).css("display","block");
    	submit()
    })

    var a=new URL(location);
	var id = a.searchParams.get('id');

	function submit(){
		if($(".schoolMessage").hasClass('active')){
			$("textarea").attr('placeholder','请输入您对学校的意见或建议 please enter your comments or suggestions to the school')
	        $('.submitrefer').off('click').click(function(){
	            $.post('/parent/add_school_message',{
	                message:$('textarea').val(),
	                student_id:id,
	            },function(d){
	                if(d.code==200){
	                    alert('留言成功！/ Success!');location = 'index'
	                }
	            },'json')
	        })
		}else{
			$("textarea").attr('placeholder','请输入您对老师的意见或建议  please enter your comments or suggestions to the teacher')
	        $('.submitrefer').off('click').click(function(){
	            $.post('/classes/add_classes_message',{
	                message:$('textarea').val(),
	                student_id:id,
	            },function(d){
	                if(d.code==200){
	                    alert('留言成功！/ Success!');location = 'index'
	                }
	            },'json')
	        })

		}
	}
	submit()
</script>
</body>
</html>