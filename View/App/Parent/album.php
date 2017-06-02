<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>相册/Album</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="/app/css/reset.css">
    <link rel="stylesheet" href="/app/css/ChildRecord.css">
</head>

<body>
<div class="layout">
    <div class="album">
        <h1>相册<span>Album</span></h1>
        <div class="childPicture">
            <!--<img src="img/jl.png" alt="" class="down">-->
            <!--<img src="img/jl.png" alt="" class="down">-->
            <!--<img src="img/jl.png" alt="" class="down">-->
            <!--<img src="img/jl.png" alt="" class="down">-->

        </div>
    </div>
</div>
<!--弹出框及蒙层-->
<div id="fullbg"></div>
<div class="con hide" id="logIn">
    <div style="display: table;height: 100%;width: 100%;">
        <div style="vertical-align: middle;display: table-cell;text-align: center;" class="popupImg">
            <!--<img src="img/jl.png" alt="">-->
        </div>
    </div>
</div>
<script src="/app/js/main.js"></script>
<script src="/app/js/jquery-1.8.3.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        //显示蒙层的函数
        function showBg(){
            var W = document.documentElement.clientWidth;
            var H = document.documentElement.clientHeight;
//            var H = $("body").height();
//            var W = $("body").width();
            $("#fullbg").css({
                height:H,
                width:W,
            })
        }

        var a=new URL(location);
        var date = a.searchParams.get('date')
        var imgurl='/pic/'
        $.ajax({
            url:"/student/view_comment",
            type:"post",
            data:{
                id:2,
                date:date
            },
            dataType:"json",
            cache: false,
            success:function(e){
                if (e.code==200){
                    var result=e.data.info;
                    /*************************************日历**************************************/
                    for(i=0 ; i<4 &&i<result.pic2Array.length; i++){
                        var pic = '<img src="'+imgurl+result.pic2Array[i]+'" class="down">'
                        $('.childPicture').append(pic)
                    }


                    $(".down").click(function(e){
                        $(".popupImg").html('');
                        var myurl = $(this).attr('src');
                        var popImg = '<img src='+myurl+' style="max-width: 100%">'
                        $(".popupImg").append(popImg);

                        // e.stopPropagation();
                        $("div.con").removeClass("hide");
                        showBg()
                    });
                    // $("div.con").click(function(even){
                    //     even.stopPropagation();//阻止冒泡
                    // });
                    $('.con.hide').click(function(){
                        $("div.con").addClass("hide")
                        $('#fullbg').html('').css({height:0,width:0})
                        
                    });
                }
            }
        })
    });
</script>
</body>
</html>