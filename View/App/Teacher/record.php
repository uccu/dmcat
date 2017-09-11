<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>学生记录 Students' Record</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="/app/css/reset.css">
    <link rel="stylesheet" href="/app/css/ChildRecord.css">
    <link rel="stylesheet" href="/app/css/popup.css">
    <script type="text/javascript" src="/app/jeDate/jedate.js"></script>
</head>
<style type="text/css">
    body,html{
        height:100%;
    }
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
            <!--<textarea name="" readonly id="liuyan"></textarea>-->
        </div>
    </div>
    <div class="reply down">
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
            <div class="childimg">
                <div class="addimg">
                    <!--<img src="img/jl.png" alt="">-->
                </div>
                <div class="addalubm" style="display: none;"><input type="file" id="file" name="file" style="opacity: 0; width: 100%; height: 100%;" accept="image/*" onchange="uploadFile();" /></div>
            </div>
        </div>
    </div>
</div>
<div class="footer" style="display: none;">
    <a href="javascript:void (0)" id="change">提交/Submit</a>
</div>


<div class="mengceng" style="width:100%;display: none;background: #333333;opacity: 0.8;position: fixed;top: 0"></div>
<div class="delete-img" style="margin:0 0.2rem;width:6rem;border-radius: 6px;height:3rem;position:fixed;top:50%;margin-top:-1.5rem;display: none;text-align: center;background:#ffffff">
    <p style="text-align: center;line-height:0.8rem;font-size: 0.3rem;margin-bottom:0.4rem;margin-top:0.6rem">确定要删除这张图片吗？</p>
    <a href = "javascript:void(0)" style="line-height: 0.5rem;font-size: 0.23rem;background: #1b2b69;color:#ffffff;display: inline-block;width:1rem;border-radius: 3px;margin-right:0.3rem">取消</a>
    <a href = "javascript:void(0)" style="line-height: 0.5rem;font-size: 0.23rem;background: #1b2b69;color:#ffffff;display: inline-block;margin:auto;width:1rem;border-radius: 3px;">确定</a>
</div>
<script src="/app/js/main.js"></script>
<script src="/app/js/jquery-1.4.2.min.js"></script>
<script src="/app/js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src='/app/js/star.js'></script>
<script type="text/javascript" src='/app/js/jquery.upload.js'></script>

<script type="text/javascript">
    var imgurl='/pic/'
    var mHeight = window.screen.height;
    $('.mengceng').css('height',mHeight)
    $(function(){
        var p = [];
        var pic = [];
        var ps = [];
        var pd = [];

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
                    ability.learning = result.learning;
                    ability.eat = result.eat
                    ability.life = result.life
                    for(var i = 0;i<result.learning;i++){
                        $(".learn").find('img').eq(i).attr('src',"/app/img/star_red.png")
                    }
                    for(var i = 0;i<result.eat;i++){
                        $(".eat").find('img').eq(i).attr('src',"/app/img/star_red.png")
                    }
                    for(var i = 0;i<result.life;i++){
                        $(".life").find('img').eq(i).attr('src',"/app/img/star_red.png")
                    }
                    var liuyan = '<div contenteditable="false" id="liuyan">'+result.comment+'</div>'
                    $(".liuyan").append(liuyan)
                    var xiangce = '<a href="album?date='+result.date+'"></a>'
                    $(".album").find('h1').append(xiangce)
                    
                    if(!(result.comment==undefined)){

                        for(i=0 ; i<result.pic2Array.length; i++){

                            var pic = '<img src="'+imgurl+result.pic2Array[i]+'" data-i="'+result.pic2Array[i]+'">'
                            $('.childimg').append(pic)
                        }
                        $('.childimg img').each(function(){
                            var picture = $(this).attr('data-i');
                            p.push(picture)
                         })

                        !e.data.canUpdate && $(".star img").unbind('click')

                    }

//                    没有记录或评论之后五小时内的情况下，编写信息
                    if(result.comment == undefined){
                        $("#liuyan").text(' ');
                    }
                    if(result.comment == undefined || e.data.canUpdate){
                        // var writeliuyan = '<div contenteditable="true" id="writeliuyan"></div>'
                        $("#liuyan").attr('contenteditable','true');

                        $(".addalubm").css('display','block');
                        $(".album").find("h1 a").css('display','none');
                        $(".footer").css('display','block');

                        
                        $('.childimg').find('img').click(function(){
                            console.info(p);
                            var that = $(this)
                            $('.delete-img').css('display','block');
                            $(".mengceng").css('display','block')

                            var c = that.attr('data-i');
                            $(".delete-img").find('a').eq(1).click(function(){

                                /**********删除图片（删除数组中的指定内容）**********/
                                function removeByValue(p, val) {
                                  for(var i=0; i<p.length; i++) {
                                    if(p[i] == c) {
                                      p.splice(i, 1);
                                      break;
                                    }
                                  }
                                }
                                removeByValue(p, "c");

                                $('.delete-img').css('display','none');
                                $('.mengceng').css('display','none')
                                // location.reload();
                                $(".footer").find("a").click();
                            })
                            $(".delete-img").find("a").eq(0).click(function(){
                                $('.delete-img').css('display','none');
                                $('.mengceng').css('display','none')
                            })

                        })
                        

                    }
                }
            }
        })
        //编写评价时上传图片
        /*$('.addalubm').click(function(){
            $('input[type="file"]').click();
            $("#file").unbind('change').bind('change',function() {
                // var form = new FormData();
                // var file = $("input[type=\"file\"]")[0].files[0];//获取type="file"类型的input
                // form.append("file", file);
               
                // $.ajax({
                //     url: "/student/upPic",
                //     data: form,
                //     contentType: false,
                //     processData: false,
                //     type: "post",
                //     dataType: "json",
                //     success: function (e) {
                //         if (e.code == 200) {
                //             var result = e.data;
                //             var childimg = '<img src="'+ result.apath +'" data-z="'+result.path+'">'
                //             $(".addimg").append(childimg)
                //         }
                //     }
                // })
                $.ajaxFileUpload({
                        url: "/student/upPic",
                        dataType: "json",
                        secureuri: false,
                        fileElementId: 'file',
                        success: function (e) {
                            if (e.code == 200) {
                                var result = e.data;
                                var childimg = '<img src="'+ result.apath +'" data-z="'+result.path+'">'
                                $(".addimg").append(childimg)
                            }else{
                                alert(e.message)
                            }
                        },
                        error: function (e) {
                            alert("请检查网络");
                        }
                    });

            })
        })*/

        //  提交评价
        var a=new URL(location);
        var id = a.searchParams.get('id')

        // function submit(){
            $(".footer").find("a").click(function(){
            var date = $(".datainp").val();
            var eat = ability.eat;
            var learn = ability.learning;
            var life = ability.life;
            var comment = $("#liuyan").html();
            // var pic = [];
             ps = $.merge(p,pic)
            //each()用于遍历，未知循环长度时，可用来进行循环操作
            $('.addimg img').each(function(){
                pic.push($(this).attr('data-z'))
            })
           var danImg =  ps.join(";")
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
                        show_alert(data.message)
                        location.reload()
                    }else {
                        show_alert(data.message)
                    }
                }
            })
        })
        // }
        

        updata();
        function updata(){
            $(".footer").find("a").click(function(){
            $.getScript("/app/js/star.js")
            var date = $(".datainp").val();
            var eat = ability.eat;
            var learn = ability.learning;
            var life = ability.life;

            var comment = $("#liuyan").html();
            // var pic = [];
             ps = $.merge(p,pic)
            $('.addimg img').each(function(){
                pic.push($(this).attr('data-z'))
            })

           var danImg =  ps.join(";")
            $.ajax({
                url:"/teacher/upd_comment",
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
                        show_alert(data.message)
                        location.reload()
                    }else {
                        show_alert(data.message)
                    }
                }
            })
        })
        }
    })


// 编写评价时上传图片
function uploadFile()
{
    $.ajaxFileUpload({
        url: "/student/upPic",
        dataType: "json",
        secureuri: false,
        fileElementId: 'file',
        success: function (e) {
            if (e.code == 200) {
                var result = e.data;
                var childimg = '<img src="'+ result.apath +'" data-z="'+result.path+'">'
                $(".addimg").append(childimg)
            }else{
                alert(e.message)
            }
        },
        error: function (e) {
            alert("请检查网络");
        }
    });
}

</script>
</body>
</html>