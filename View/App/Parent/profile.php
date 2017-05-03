<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>个人资料/Personal profile</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="/app/css/reset.css">
    <link rel="stylesheet" href="/app/css/personalProfile.css">
</head>
<body>
<div class="pp-main">
    <ul>
        <input type="file" id="file" style="display:none" />
        <li id="head" style="height: 1.4rem;line-height: 1.4rem;">我的头像<span>head portrait</span></li>
        <li id="password">密码 <span>password</span></li>
        <li id="tel">手机号 <span>cell-phonenumber</span></li>
        <li id="email">邮箱 <span>mailbox</span></li>
        <li id="childreninfomation">

        </li>
    </ul>
    <div class="footer">
        <a href="javascript:void (0)" id="change">保存修改信息/change</a>
    </div>
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
            url:"/parent/get_my_info",
            type:"post",
            data:{
                id:<?php echo $id;?>
            },
            dataType:"JSON",
            cache: false,
            success:function(e){
                if (e.code==200){
                    var result = e.data.info;
//                    头像
                    var head = '<img src="'+result.avatar+'" alt="">';
                    $("#head").append(head);

                    var password = '<input type="text" value="'+result.password+'">'
                    $('#password').append(password);

                    var tel = '<input type="text" value="'+result.phone+'">'
                    $("#tel").append(tel)

                    var email = '<input type="text" value="'+result.email+'">'
                    $("#email").append(email)

                    //孩子资料/*还没加跳转条件**********************************************************************************/

                    var studentName = '<a href="children?id=<?php echo $stu_id;?>" class="childreninfomation">孩子资料 <span>child information</span></a>'
                    $("#childreninfomation").append(studentName);

                    /*******************************************88还没加跳转条件**********************************************************************************/

//                    var children = e.data.student;
//                    var students = '<select name="" id="student"><option value=0>请选择</option></select>'
//                    $("#childreninfomation").append(students)

//                    for(var i=0; i<children.length; i++){
//                        var studentName = '<a href="children.html?id='+'">孩子资料 <span>child information</span></a>'
//                        var student = '<option value="'+children[i].id+'">'+children[i].name+children[i].name_en+'</option>'
//                        $("#student").append(student)
//                        $("select").bind('change',function(){
//                            if($(this).val())
////                                console.info($(this).val())
//                            location.href= 'children.html?id='+$(this).val()
//                        })
//                    }
//                    $("#childreninfomation").append(studentName);
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
        $("#change").click(function(){
            var newpassword = $("#password").children("input").val();
            var newemail = $("#email").children("input").val();
            var newtel = $("#tel").children('input').val();
            var newhead = $('#head img').attr('data-z');
            console.info(newpassword)
            console.info(newhead)
            $.ajax({
                url:"/parent/upd",
                type:"post",
                data:{
                    id:<?php echo $id;?>,
                    raw_password:newpassword,
                    email:newemail,
                    phone:newtel,
                    avatar:newhead
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