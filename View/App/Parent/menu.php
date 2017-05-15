<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>菜单/Ask forleave</title>
    <meta id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
    <link rel="stylesheet" href="/app/css/reset.css">
    <link rel="stylesheet" href="/app/css/menu.css">
</head>
<body>
<div class="layout">
    <h1>本周菜单安排:</h1>
    <div class="menu">
        <p>星期一</p>
        <!--<div class="mainMenu">-->
            <ul>
                <!--<li>西红柿炒鸡蛋</li>-->
                <!--<li>芹菜炒肉</li>-->
                <!--<li>水蒸蛋</li>-->
                <!--<li>冬瓜排骨汤</li>-->
                <!--<li>米饭</li>-->
            </ul>
        <!--</div>-->
    </div>
    <div class="menu">
        <p>星期二</p>
        <!--<div class="mainMenu">-->
            <ul>
                <!--<li>西红柿炒鸡蛋</li>-->
                <!--<li>芹菜炒肉</li>-->
                <!--<li>水蒸蛋</li>-->
                <!--<li>冬瓜排骨汤</li>-->
                <!--<li>米饭</li>-->
            </ul>
        <!--</div>-->
    </div>
    <div class="menu">
        <p>星期三</p>
        <!--<div class="mainMenu">-->
            <ul>
                <!--<li>西红柿炒鸡蛋</li>-->
                <!--<li>芹菜炒肉</li>-->
                <!--<li>水蒸蛋</li>-->
                <!--<li>冬瓜排骨汤</li>-->
                <!--<li>米饭</li>-->
            </ul>
        <!--</div>-->
    </div>
    <div class="menu">
        <p>星期四</p>
        <div class="mainMenu">
            <ul class="Monday">
                <!--<li>西红柿炒鸡蛋</li>-->
                <!--<li>芹菜炒肉</li>-->
                <!--<li>水蒸蛋</li>-->
                <!--<li>冬瓜排骨汤</li>-->
                <!--<li>米饭</li>-->
            </ul>
        </div>
    </div>
    <div class="menu">
        <p>星期五</p>
        <div class="mainMenu">
            <ul>
                <!--<li>西红柿炒鸡蛋</li>-->
                <!--<li>芹菜炒肉</li>-->
                <!--<li>水蒸蛋</li>-->
                <!--<li>冬瓜排骨汤</li>-->
                <!--<li>米饭</li>-->
            </ul>
        </div>
    </div>
</div>
<script src="/app/js/main.js"></script>
<script src="/app/js/jquery-1.7.1.min.js"></script>

<script>
    $(function(){
        function GetQueryString(name){
            var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
            var r = window.location.search.substr(1).match(reg);
            if(r!=null)return  unescape(r[2]); return null;
        }
        var id = GetQueryString('id');
        $.ajax({
            url:"/menu/get_menu",
            type:"post",
            data:{
                d:0
            },
            dataType:"json",
            cache: false,
            success:function(data){
                if(data.code==200){
                    var result = data.data.list;

                    for(var d in result){
                        for(var e in result[d]){
                            $('<li>').text(result[d][e]).appendTo('.menu ul:eq('+d+')')
                        }
                    }
//                    for(var i=0; i<result.length; i++){
//                        for(var e in result[d]){
//                            $('<li>').text(result[d][e]).appendTo('.mainMenu ul:eq('+d+')')
//                        }
//                    }



                }

            }
        })

    })
</script>
</body>
</html>