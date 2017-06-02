<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>活动详情/Active Details</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="/app/css/reset.css">
    <link rel="stylesheet" href="/app/css/vote.css">
    <link rel="stylesheet" href="/app/css/voteDEtails.css">
    <link rel="stylesheet" href="/app/css/popup.css">
</head>
<style>
    .hide{display:none;}
</style>
<body>
<div class="voteactive">
    <div class="Organizers">
        <span></span>
        <!--<div class="organizersName">-->
            <!--<h1>校办中心</h1>-->
            <!--<h2>04-06 15:00</h2>-->
        <!--</div>-->
    </div>
    <div class="activeName">
        <!--期中家长会-->
        <!--<p>-->
            <!--亲爱的家长：<br>-->
            <!--其中家长会将于11月25日下午14点30分开始，请确认您能否准时参加。-->
        <!--</p>-->
    </div>
    <div class="xuanze">
        <p>单选/Single Selection</p>
        <div class="attend">
            <!--<label class="checkbox_label down" id="attend"><i class="checked"></i> <input type="radio"  name="agree" class="hidden"/>参加/attend</label>-->
            <!--<label class="checkbox_label down"><i class="checked"></i> <input type="radio"  name="agree" class="hidden"/>不参加/not attend</label>-->
        </div>
    </div>
    <div class="remainingTime">
        <span class="timeSpan">&nbsp;&nbsp;剩余时间/Remaining Time &nbsp;</span>
        <!--<div class="fnTimeCountDown" data-end="2017/04/25 23:00:13">-->
            <!--&lt;!&ndash;<span class="year">00</span>年&ndash;&gt;-->
            <!--&lt;!&ndash;<span class="month">00</span>月&ndash;&gt;-->
            <!--<span class="day">00</span>:-->
            <!--<span class="hour">00</span>:-->
            <!--<span class="mini">00</span>:-->
            <!--<span class="sec">00</span>-->
            <!--&lt;!&ndash;<span class="hm">000</span>&ndash;&gt;-->
        <!--</div>-->
    </div>
</div>
<div class="footer">
    <a href="javascript:void (0)">投票/Vote</a>
</div>
<!--弹出框及蒙层-->
<div id="fullbg"></div>
<div class="con hide" id="logIn">
    <textarea placeholder='备注'></textarea><a href='javascript:void(0)'>提交/Submit</a>
</div>
<script src="/app/js/main.js"></script>
<script src="/app/js/jquery-1.8.3.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        //显示蒙层的函数
        function showBg(){
            var bh = $("body").height();
            var bw = $("body").width();
            $("#fullbg").css({
                height:bh,
                width:bw,
            })
        }
        //接口
        var a=new URL(location);
        var id = a.searchParams.get('id')
        // function time(){
        //     $('[data-countdown]').each(function() {
        //         var $this = $(this), finalDate = $(this).data('countdown');
        //         $this.countdown(finalDate, function(event) {
        //             $this.html(event.strftime('%D:%H:%M:%S'));
        //             if($this.text()=='00:00:00:00'){
        //                 $this.html('投票结束')
        //             }
        //         });
        //     });
        // }
        $.ajax({
            url:"/notice/get_activity_info",
            type:"post",
            data:{
                id:id,

            },
            dataType:"json",
            cache: false,
            success:function(e){
                if (e.code==200){
                    $.getScript("/app/js/daojishi.js")
                    var result = e.data.info;
                    var attrav = '<img src="'+result.fullAvatar+'" alt=""><div class="organizersName"><h1>'+result.name+' '+result.name_en+'</h1><h2>'+result.date+'</h2></div>'
                    var title = '<span>'+result.title+'</span><p>'+result.content+'</p>'
                    var time = '<div data-countdown="'+result.end_date+'" class="time"></div>'
                    $(".Organizers").append(attrav)
                    $(".activeName").append(title)
                    $(".remainingTime").append(time)
                    // time();
                    $('[data-countdown]').each(function() {
                        var $this = $(this), finalDate = $(this).data('countdown');
                        $this.countdown(finalDate, function(event) {
                            $this.html(event.strftime('%D:%H:%M:%S'));
                            if($this.text()=='00:00:00:00'){
                                $this.html('投票结束')
                            }
                        });
                    });
                    var options = result.options.split(';');
//                    console.info(options)
                    for (var i=0; i<options.length;i++){
                        var option  ='<label class="checkbox_label down"><i class="checked"></i><input type="checkbox"  name="agree" class="hidden"/>'+options[i]+'</label>'
//                        console.info(options[1])
                        $(".attend").append(option)
                    }
                //弹出框开始
                    $(".checkbox_label").click(function(){
                        $(this).find("i").addClass('check').removeClass('checked');
                        $(this).siblings().find("i").removeClass('check').addClass("checked")
                    })
                    $(".down").click(function(e){
                        e.stopPropagation();
                        $("div.con").removeClass("hide");
                        showBg()
                    });
                    $("div.con").click(function(even){
                        even.stopPropagation();//阻止冒泡
                    });
                    $(document).on('click',function(){
                        if(!$("div.con").hasClass("hide")){
                            $("div.con").addClass("hide")
                            $('#fullbg').html('').css({height:0,width:0})
                        }
                    });
                    $('#logIn a').on('click',function(){
                        if(!$("div.con").hasClass("hide")){
                            $("div.con").addClass("hide")
                            $('#fullbg').html('').css({height:0,width:0})
                        }
                    })
                //弹窗狂结束
                //判断是否投票，并做相应处理
                    if(result.voted == 0){
                        //点击投票按钮，进行投票
                        $(".footer").find("a").click(function(){
                            var remark = $("#logIn").find("textarea").val();
                            var asswer = $('.checkbox_label .check').parent().index()+1
                            $.ajax({
                                url:"/notice/to_activity",
                                type:"post",
                                data:{
                                    id:id,
                                    student_id:10,
                                    answer:asswer,
                                    remark:remark,
                                },
                                dataType:"json",
                                cache: false,
                                success:function(e){
                                    if (e.code==200){
                                        show_alert("投票成功")
                                        location.reload()
                                    }else{
                                        show_alert("出状况喽")
                                    }
                                }
                            })
                        })

                    }else{
                        //获取投票总人数
                        $(".checkbox_label").eq(result.voted-1).click();
                        $(".checkbox_label").unbind()
                        var allnum = '<span>投票人数/Number of Voters:'+e.data.countAll+'</span>'
                        $(".xuanze").find("p").append(allnum)
                        //获取每一个选项的投票人数
                        var votenum = e.data.count;
                        var xuanze = $(".checkbox_label")
                        for(var i=0;i<xuanze.length;i++){
                            var singlenum = '<span style="float: right;">'+votenum[i+1]+'票/tickets</span>'
                            $(".checkbox_label").eq(i).append(singlenum)
                        }
                        $(".footer").css('display','none');
                        $("#logIn").css('display','none');
                        $('#fullbg').html('').css({height:0,width:0})

                    }


                }
            }
        })

    });
</script>
<script src="/app/js/countdown.js"></script>
</body>
</html>