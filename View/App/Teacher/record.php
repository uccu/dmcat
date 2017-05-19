<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>学生记录 student record</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="/app/css/reset.css">
    <link rel="stylesheet" href="/app/css/ChildRecord.css">
    <link rel="stylesheet" href="/app/css/popup.css">
    <script type="text/javascript" src="/app/jeDate/jedate.js"></script>
</head>
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
            学习能力 <span>learning ability</span>
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
            吃饭能力 <span>ability to eat</span>
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
            生活能力 <span>viability</span>
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
        <h1>老师评语 <span>teacher comments</span></h1>
        <!--<div class="teacherName">-->
        <!--<span>头像</span>张一白老师-->
        <!--</div>-->
        <div class="liuyan">
            <p class="sanjiao"></p>
            <!--<textarea name="" readonly id="liuyan"></textarea>-->
        </div>
    </div>
    <div class="reply down">
        <span>回复/reply</span>
    </div>
    <div class="huifu">
        <!-- <textarea name="" readonly></textarea> -->
        <div class="texta" contenteditable = "false"></div>
    </div>
</div>
<hr>
<div class="layout">
    <div class="album">
        <h1>相册<span>album</span></h1>
        <div class="childPicture">
            <input type="file" id="file" style="display:none" />
            <div class="childimg">
                <div class="addimg">
                    <!--<img src="img/jl.png" alt="">-->
                </div>
                <div class="addalubm" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>
<div class="footer" style="display: none;">
    <a href="javascript:void (0)" id="change">提交/submit</a>
</div>
<script src="/app/js/main.js"></script>
<script src="/app/js/jquery-1.8.3.min.js"></script>
<script type="text/javascript">
    var imgurl='/pic/'
    $(function(){
//        console.info(ability.eat)
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
                location.href = '/teacher/record?date='+$('.datainp').val()
            },
            okfun:function(val){alert(val)}
        })
        /*************************************日历**************************************/
        var a=new URL(location);
        var dateval = a.searchParams.get('date')
        /*************************************日历**************************************/

        $.ajax({
            url:"/student/view_comment",
            type:"post",
            data:{
                date:dateval
            },
            dataType:"json",
            cache: false,
            success:function(e){
                if (e.code==200){
                    var result=e.data.info;
                    /*************************************日历**************************************/
                    $('.datainp').val(result.date)
                    var subdate = result.subdate;
                    var adddate = result.adddate;
                    var timeHtml='<a href="/teacher/record?date='+subdate+'" class="time-l"></a><a href="/teacher/record?date='+adddate+'" class="time-r"></a>'
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
                    var liuyan = '<div contenteditable="false" id="liuyan">'+result.comment+'</div>'
                    $(".liuyan").append(liuyan)
                    var xiangce = '<a href="album?date='+result.date+'"></a>'
                    $(".album").find('h1').append(xiangce)
                    if(!(result.comment==undefined)){
                        for(i=0 ; i<4 &&i<result.pic2Array.length; i++){
                            var pic = '<img src="'+imgurl+result.pic2Array[i]+'">'
                            $('.childimg').append(pic)
                        }
                    }
//                    $(".liuyan").find("textarea").append(result.comment)
//                    没有记录的情况下，编写信息
                    if(result.comment==undefined){
                        $.getScript("/app/js/star.js")
                        $("#liuyan").css('display','none')
                        var writeliuyan = '<div contenteditable="true" id="writeliuyan"></div>'
                        $(".liuyan").append(writeliuyan)
                        $(".addalubm").css('display','block')
                        $(".footer").css('display','block')
                    }
                }
            }
        })
        //编写评价时上传图片
        $('.addalubm').click(function(){
            $('input[type="file"]').click();
            $("#file").unbind('change').bind('change',function() {
                var form = new FormData();
                var file = $("input[type=\"file\"]")[0].files[0];//获取type="file"类型的input
                form.append("file", file);
                $.ajax({
                    url: "/student/upPic",
                    data: form,
                    contentType: false,
                    processData: false,
                    type: "post",
                    dataType: "json",
                    success: function (e) {
                        if (e.code == 200) {
                            var result = e.data;
                            var childimg = '<img src="'+ result.apath +'" data-z="'+result.path+'">'
                            $(".addimg").append(childimg)
                        }
                    }
                })
            })
        })
        //  提交评价
        var a=new URL(location);
        var id = a.searchParams.get('id')
        $(".footer").find("a").click(function(){
            $.getScript("/app/js/star.js")
            var date = $(".datainp").val();
            var eat = ability.eat;
            var learn = ability.learning;
            var life = ability.life;
            var comment = $("#writeliuyan").html();
            var pic = [];
            //each()用于遍历，未知循环长度时，可用来进行循环操作
            $('.addimg img').each(function(){
//                pic += ';'+$(this).attr('data-z');
                pic.push($(this).attr('data-z'))
            })
           var danImg =  pic.join(";")
            $.ajax({
                url:"/teacher/add_comment",
                type:"post",
                data:{
                    date:date,
                    eat:eat,
                    life:life,
                    learning:learn,
                    comment:comment,
                    pic:danImg
                },
                dataType:"json",
                cache: false,
                success:function(data){
                    if (data.code==200){
                        show_alert("提交成功")
                        location.reload()
                    }else {
                        show_alert("不开心，提交失败")
                    }
                }
            })
        })
    })

</script>
</body>
</html>