<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>查看体检</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="/app/css/reset.css">
    <link rel="stylesheet" href="/app/css/personalProfile.css">
</head>
<body>
<div class="pp-main">
    <ul>
        <li id="height">身高 <span>height</span></li>
        <li id="weight"> 体重<span>weight</span></li>
        <li id="sight">视力 <span>sight</span></li>
    </ul>
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
            url:"/student/physical_get",
            type:"post",
            data:{
                id:<?php echo $id;?>
            },
            dataType:"JSON",
            cache: false,
            success:function(e){
                if (e.code==200){
                    var result = e.data.info;
                    var height = '<input type="text" value="'+result.height+'" readonly>'
                    var weight = '<input type="text" value="'+result.weight+'" readonly>'
                    var sight = '<input type="text" value="'+result.sight+'" readonly>'
                    $("#height").append(height)
                    $("#weight").append(weight)
                    $("#sight").append(sight)
                }
            }
        })


    })

</script>
</body>
</html>