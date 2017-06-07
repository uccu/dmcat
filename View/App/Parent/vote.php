<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>投票/Vote</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="/app/css/reset.css">
    <link rel="stylesheet" href="/app/css/vote.css">
</head>

<body>
<div class="usTab voteclassify">
    <div class="vote-tab active">
        <a href="javascript:void (0)" >全部投票<span>All Vote</span></a>
    </div>
    <div class="vote-tab">
        <a href="javascript:void (0)">我的投票<span>My Vote</span></a>
    </div>

</div>
<div class="tabContent allvote"  style="display: block;" >
</div>
<div class="tabContent myvote">
</div>

<!--<div class="voteactive">-->
<!--<div class="Organizers">-->
<!--<span></span>-->
<!--<div class="organizersName">-->
<!--<h1>校办中心</h1>-->
<!--<h2>04-06 15:00</h2>-->
<!--</div>-->
<!--</div>-->
<!--<div class="activeName">-->
<!--<span>期中家长会</span>-->
<!--<p>-->
<!--亲爱的家长：<br>-->
<!--其中家长会将于11月25日下午14点30分开始，请确认您能否准时参加。-->
<!--</p>-->
<!--</div>-->
<!--<div class="remainingTime">-->
<!--<span class="timeSpan">&nbsp;剩余时间/Remaining Time&nbsp;</span>-->
<!--<div class="fnTimeCountDown">-->
<!--<span class="month">00</span>月-->
<!--<span class="day">00</span>天-->
<!--<span class="hour">00</span>时-->
<!--<span class="mini">00</span>分-->
<!--<span class="sec">00</span>秒-->
<!--</div>-->
<!--</div>-->
<!--<a href="javascript:void (0)" class="voteing">投票/vote</a>-->
<!--</div>-->
<!--<hr>-->
<script src="/app/js/main.js"></script>
<script src="/app/js/jquery-1.8.3.min.js"></script>
<!--<script src="js/daojishi.js"></script>-->
<script type="text/javascript">
    $(function(){
        $(".usTab").find(".vote-tab").click(function(){
//            $(".usTab").find(".vote-tab").attr("class"," ");
            $(".tabContent").css("display","none");
            $(this).attr("class","active vote-tab").siblings().removeClass("active");
            $(".tabContent").eq($(this).index()).css("display","block");

        })
    function time(){
        $('[data-countdown]').each(function() {
            var $this = $(this), finalDate = $(this).data('countdown');
            $this.countdown(finalDate, function(event) {
                $this.html(event.strftime('%D:%H:%M:%S'));
                if($this.text()=='00:00:00:00'){
                    $this.html('投票结束/Vote Over')
                }
            });
        });
    }
        $.ajax({
            url:"/notice/get_vote_lists",
            type:"get",
            data:{},
            dataType:"json",
            cache: false,
            success:function(e){
                if (e.code==200){
                    // $.getScript("/app/js/daojishi.js")
                    var result = e.data.list;
                    for(var i=0; i<result.length;i++){
                        var allvote = '<div class="voteactive"><div class="Organizers"><img src="'+result[i].fullAvatar+'" alt=""><div class="organizersName"><h1>'+result[i].name+result[i].name_en+'</h1><h2>'+result[i].date+'</h2></div></div><div class="activeName"><span>'+result[i].title+'</span><p>'+result[i].short_message+'</p></div><div class="remainingTime"><span class="timeSpan">&nbsp;剩余时间/Remaining Time&nbsp;</span><div data-countdown="'+result[i].end_date+'" class="time"></div></div><a href="vote_detail?id='+result[i].id+'" class="voteing">投票/vote</a></div><hr>'
                        $(".allvote").append(allvote)
                        time()
                    }
                }
            }
        })
        $.ajax({
            url:"/notice/get_vote_lists",
            type:"get",
            data:{},
            dataType:"json",
            cache: false,
            success:function(e){
                if (e.code==200){
                    // $.getScript("/app/js/daojishi.js")
                    var result = e.data.list;
                    for(var i=0; i<result.length;i++){
                        var myvote = '<div class="voteactive"><div class="Organizers"><img src="'+result[i].fullAvatar+'" alt=""><div class="organizersName"><h1>'+result[i].name+result[i].name_en+'</h1><h2>'+result[i].date+'</h2></div></div><div class="activeName"><span>'+result[i].title+'</span><p>'+result[i].short_message+'</p></div><div class="remainingTime"><span class="timeSpan">&nbsp;剩余时间/Remaining Time&nbsp;</span><div data-countdown="'+result[i].end_date+'" class="time"></div></div><a href="vote_detail?id='+result[i].id+'" class="voteing">查看/Look up</a></div><hr>'
                        $(".myvote").append(myvote)
                        time()
                    }
                }
            }
        })
    })
</script>
<script src="/app/js/countdown.js"></script>
</body>
</html>