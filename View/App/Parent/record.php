<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>孩子记录/Child Record</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="/app/css/reset.css">
    <link rel="stylesheet" href="/app/css/ChildRecord.css">
    <link rel="stylesheet" href="/app/css/popup.css">
    <script type="text/javascript" src="/app/jeDate/jedate.js"></script>
</head>
<style>

</style>
<body>
<div class="topbanner">
    <img src="/app/img/b1.png" alt="">
    <div class="rili">
    </div>
    <span class="time">
        <!--<a href="#" class="time-l"></a>-->
        <p class="datep"><input class="datainp" id="dateinfo" type="text" placeholder="请选择"  readonly></p>
        <!--<a href="#" class="time-r"></a>-->
    </span>
</div>
<div class="headpic">
    <!--<span>-->
    <!--<a href="javascript:void(0)"></a>-->
    <!--<img src="img/jl.png" alt="">-->
    <!--<em>安吉</em>-->
    <!--</span>-->
</div>
<div class="layout">
    <div class="learning">
        <div class="learningText">
            学习能力 <span>Learning Ability</span>
        </div>
        <div class="star learn" data-type="learning">
            <a><img src="/app/img/star.png"/></a>
            <a><img src="/app/img/star.png"/></a>
            <a><img src="/app/img/star.png"/></a>
            <a><img src="/app/img/star.png"/></a>
            <a><img src="/app/img/star.png"/></a>
        </div>
    </div>
    <div class="learning">
        <div class="learningText">
            吃饭能力 <span>Ability to Eat</span>
        </div>
        <div class="star eat" data-type="eat">
            <a><img src="/app/img/star.png"/></a>
            <a><img src="/app/img/star.png"/></a>
            <a><img src="/app/img/star.png"/></a>
            <a><img src="/app/img/star.png"/></a>
            <a><img src="/app/img/star.png"/></a>
        </div>
    </div>
    <div class="learning">
        <div class="learningText">
            生活能力 <span>Viability</span>
        </div>
        <div class="star life" data-type="life">
            <a><img src="/app/img/star.png"/></a>
            <a><img src="/app/img/star.png"/></a>
            <a><img src="/app/img/star.png"/></a>
            <a><img src="/app/img/star.png"/></a>
            <a><img src="/app/img/star.png"/></a>
        </div>
    </div>
</div>
<hr>
<div class="layout">
    <div class="comments">
        <h1>老师评语 <span>Teacher Comments</span></h1>
        <!--<div class="teacherName">-->
            <!--<span>头像</span>张一白老师-->
        <!--</div>-->
        <div class="liuyan">
            <p class="sanjiao"></p>
            <!-- <textarea name="" readonly></textarea> -->
            <div contenteditable="false" id="liuyan"></div>
        </div>
    </div>
    <div class="reply down">
        <a href="javascript:void (0)">回复/Reply</a>
        <span>回复/Reply</span>
    </div>
    <div class="huifu">
        <!-- <textarea name="" readonly></textarea> -->
        <div class="texta" contenteditable = "false"></div>
    </div>
</div>
<hr>
<div class="layout">
    <div class="album">
        <h1>相册<span>Album</span></h1>
        <div class="childPicture">
            <!--<img src="img/jl.png" alt="">-->
        </div>
    </div>
</div>
<!--弹出框及蒙层-->
<div id="fullbg"></div>
<div class="con hide" id="logIn">
    <textarea placeholder='亲爱的学生家长，回复老师留言只可回复一次.....'></textarea>
    <a href='javascript:void(0)' class="submit">提交/Submit</a>
</div>
<div class="nojilu" style="display: none;">
    竟然没有记录信息
</div>
<script src="/app/js/main.js"></script>
<script src="/app/js/jquery-1.8.3.min.js"></script>
<!--<script src="js/star.js?3"></script>-->
<script type="text/javascript">

    $(function(){
        //显示蒙层的函数
        function showBg(){
            var bh = $("body").height();
            var bw = $("body").width();
            $("#fullbg").css({
                height:bh,
                width:bw,
            })
        }
        $(".down").find('a').click(function(e){
            e.stopPropagation();
            $("div.con").removeClass("hide");
            showBg()
        });
        $("div.con").click(function(even){
            even.stopPropagation();//阻止冒泡
        });
        $(document).click(function(){
            if(!$("div.con").hasClass("hide")){
                $("div.con").addClass("hide")
                $('#fullbg').html('').css({height:0,width:0})
            }
        });
    })

    var imgurl='/pic/'
    $(function(){
        function GetQueryString(name){
            var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
            var r = window.location.search.substr(1).match(reg);
            if(r!=null)return  unescape(r[2]); return null;
        }
        var id = GetQueryString('id');


        jeDate({
            dateCell:"#dateinfo",
            format:"YYYY-MM-DD",
//            isinitVal:true,
            isTime:false, //isClear:false,
            isClear: false, //是否显示清空
            maxDate: jeDate.now(0),
            choosefun:function(){
            /*************************************日历**************************************/
                location.href = 'record?date='+$('.datainp').val()
            },
            okfun:function(val){alert(val)}
        })
    /*************************************日历**************************************/
        var a=new URL(location);
        var dateval = a.searchParams.get('date')
        var id = a.searchParams.get('id')
        var eid
    /*************************************日历**************************************/

        $.ajax({
            url:"/student/view_comment",
            type:"post",
            data:{
                id:id,
                date:dateval
            },
            dataType:"json",
            cache: false,
            success:function(e){
                if (e.code==200){
                    var result=e.data.info;
                    eid = result.id;
                    /*************************************日历**************************************/
                    $('.datainp').val(result.date)
                    var subdate = result.subdate;
                    var adddate = result.adddate;
                    var timeHtml='<a href="record?date='+subdate+'" class="time-l"></a><a href="record?date='+adddate+'" class="time-r"></a>'
                    $(".time").append(timeHtml)
                    /*************************************日历**************************************/
                    var headhtml='<span><a href="javascript:void(0)"></a><img data="'+result.id+'" src="'+imgurl+result.avatar+'"><em>'+result.name+'</em><em>'+result.name_en+'</em></span>'
                    $(".headpic").append(headhtml);

                    $('.huifu').find('div').append(result.reply)
                    var reply = $('.huifu').find('div').html()
                    if(!(reply==='')){
                        $(".huifu").css('display','block')
                        $('.huifu').find('div').html(reply);
                        $(".reply").find("a").css('display','none');
                        $(".reply").find("span").css('display','block')
                    }
                    for(var i = 0;i<result.learning;i++){
                        $(".learn").find('img').eq(i).attr('src',"/app/img/star_red.png")
                    }
                    for(var i = 0;i<result.eat;i++){
                        $(".eat").find('img').eq(i).attr('src',"/app/img/star_red.png")
                    }
                    for(var i = 0;i<result.life;i++){
                        $(".life").find('img').eq(i).attr('src',"/app/img/star_red.png")
                    }
//                    var liuyan = result.comment
                    $(".liuyan").find("div").append(result.comment)
                    if(result.comment==undefined){
                        $(".layout").css('display','none')
                        $(".nojilu").css("display","block")
                        show_alert("今天是假日吗，竟然没有记录哎！！");
                    }

                    for(i=0 ; i<4 &&i<result.pic2Array.length; i++){
                        var pic = '<img src="'+imgurl+result.pic2Array[i]+'">'
                        $('.childPicture').append(pic)
                    }
                    var xiangce = '<a href="album?date='+result.date+'"></a>'
                    $(".album").find('h1').append(xiangce)
                }
            }
        })
        //回复老师留言
        $(".submit").click(function(){
            var huifu = $("#logIn").children("textarea").val();
//            console.info(huifu)
            $.ajax({
                url:"/student/reply",
                type:"post",
                data:{
                    id:eid,
                    reply:huifu
                },
                dataType:"json",
                cache: false,
                success:function(data){
                    if (data.code==200){
                        location.reload()
                    }
                }
            })
        })

    })

</script>
</body>
</html>