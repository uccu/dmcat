<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>孩子资料/Child information</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="/app/css/reset.css">
    <link rel="stylesheet" href="/app/css/personalProfile.css">
</head>
<body>
<div class="pp-main">
    <ul>
        <input type="file" id="file" style="display:none" />
        <li id="head" style="height: 1.4rem;line-height: 1.4rem;">头像 <span>head portrait</span></li>
        <li id="number">编号 <span>number</span></li>
        <li id="name">姓名 <span>name</span></li>
        <li id="nationality">国籍 <span>nationality</span></li>
        <li id="language">语言<span>language</span></li>
        <li id="birthday">生日<span>birthday</span></li>
        <li id="allergic">过敏史<span>allergic history</span></li>
        <li id="tijian"></li>
    </ul>
</div>
<div class="footer">
    <a href="javascript:void (0)" id="changes">修改并保存信息/change</a>
</div>
<script src="/app/js/main.js"></script>
<script src="/app/js/jquery-1.8.3.min.js"></script>
<script>
    $(function(){
        function GetQueryString(name){
            var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
            var r = window.location.search.substr(1).match(reg);
            if(r!=null)return  unescape(r[2]); return null;
        }
        var id = GetQueryString('id');

        $.ajax({
            url:"/student/get",
            type:"post",
            data:{
                id:<?php echo $id;?>
            },
            dataType:"JSON",
            cache: false,
            success:function(e){
                if (e.code==200){
                    var result = e.data.info;
                    var head = '<a href="javascript:void(0)"><img src="'+result.fullAvatar+'" alt=""></a>';
                    var number = '<input type="text" value="'+result.id+'" readonly>'
                    var name = '<input type="text" value="'+result.name+result.name_en+'" readonly>'
                    var nationality = '<input type="text" value="'+result.nationality+'" readonly>'
                    var language = '<input type="text" value="'+result.language+'" readonly>'
                    var birthday = '<input type="text" value="'+result.birth+'" readonly>'
                    var allergic = '<input type="text" value="'+result.allergy+'">'
                    var tijian='<a href="tijian.html?id='+result.id+'">查看体检</a>'
                    $("#head").append(head);
                    $("#number").append(number)
                    $("#name").append(name)
                    $("#nationality").append(nationality)
                    $("#language").append(language)
                    $("#birthday").append(birthday)
                    $("#allergic").append(allergic)
                    $("#tijian").append(tijian);

                    //换头像
                    $('img').click(function(){
                        $('input[type="file"]').click();

                        $("#file").unbind('change').bind('change',function(){
                            var form = new FormData();
                            var file = $("input[type=\"file\"]")[0].files[0];//获取type="file"类型的input
                            form.append("file",file);
                            $.ajax({
                                url:"/staff/upPic",
                                data:form,
                                contentType:false,
                                processData:false,
                                type:"post",
                                dataType:"json",
                                success:function(e){
                                    if (e.code==200){
                                        var result = e.data;
                                        $('#head img').remove();
                                        var head = '<img src="'+result.apath+'" data-z="'+result.path+'">';
                                        $("#head").append(head)
                                    }
                                }
                            })
                        })
                    })
                }
            }
        })

        //修改个人资料
        $("#changes").click(function(){
            var newhead = $('#head img').attr('data-z');
            var newallergic = $("#allergic").children("input").val();
            $.ajax({
                url:"/parent/student_upd",
                type:"post",
                data:{
                    id:2,
                    avatar:newhead,
                    allergy:newallergic
                },
                dataType:"json",
                cache: false,
                success:function(data){
                    if (data.code==200){
                        alert("信息修改成功，请确认保存")

                    }
                }
            })
        })
    })

</script>
</body>
</html>