<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta id="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport">
    <meta name="apple-themes-web-app-capable" content="yes">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <meta name="format-detection" content="telephone=no">
    <title>董事长专区</title>
    <link rel="stylesheet" href="css/swiper.min.css">
    <link href="css/reset.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/main.css">
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script src="js/swiper.jquery.min.js"></script>
</head>
<body>
<header>
    <div class="header-left" onclick="getBackShuaXin()"></div>
    <div class="header-center"></div>
    <div class="header-right"></div>
</header>
<p class="fenge"></p>
<!--div class="group-banner">
    <img src="img/group/banner1.png">
</div>
<div class="chairman-search">
    <input type="search" placeholder="输入关键字">
</div-->
<div class="main-box">
    <h1>董事长简历</h1>
    <h3>CHAIRMAN RESUME</h3>
    <div class="chairman-resume">
        <div class="chairman-resumeName">
            <?php echo langV($page,'content');?>
        </div>
    </div>
</div>
<div class="main-box">
    <h1>董事长图片</h1>
    <h3>CHAIRMAN PICTURE</h3>
    <div class="chairman-picture">
        <ul class="chairman-picturehead">
            <li>
                <div class="chairman-pictureimg">
                    <img src="img/group/chairman1.png">
                    <span>5张</span>
                </div>
                <h2>钱金耐董事长肖像</h2>
            </li>
            <li>
                <div class="chairman-pictureimg">
                    <img src="img/group/chairman2.png">
                    <span>5张</span>
                </div>
                <h2>钱金耐董事长肖像</h2>
            </li>
            <li>
                <div class="chairman-pictureimg">
                    <img src="img/group/chairman3.png">
                    <span>5张</span>
                </div>
                <h2>钱金耐董事长肖像</h2>
            </li>
        </ul>
        <ul class="chairman-picturehead">
            <li>
                <div class="chairman-pictureimg2">
                    <img src="img/group/chairman4.png">
                    <span>5张</span>
                </div>
                <p>致力打造新疆纺织服装产业新高地;德汇集团董事长钱金耐媒体见面会访谈</p>

            </li>
            <li>
                <div class="chairman-pictureimg2">
                    <img src="img/group/chairman5.png">
                    <span>5张</span>
                </div>
                <p>钱金耐在自治区工商联（总商会）十届执行委员会一次会议上当选副主席</p>
            </li>
            <li>
                <div class="chairman-pictureimg2">
                    <img src="img/group/chairman6.png">
                    <span>5张</span>
                </div>
                <p>钱金耐在德汇火灾原址重建大楼"德汇名品广场"试营业仪式上发表讲话</p>
            </li>
            <li>
                <div class="chairman-pictureimg2">
                    <img src="img/group/chairman7.png">
                    <span>5张</span>
                </div>
                <p>钱金耐在第二届亚欧丝绸之路服装节上接受记者采访</p>
            </li>
            <li>
                <div class="chairman-pictureimg2">
                    <img src="img/group/chairman8.png">
                    <span>5张</span>
                </div>
                <p>钱金耐在2014年亚欧丝绸之路服装节接收专访</p>
            </li>
            <li>
                <div class="chairman-pictureimg2">
                    <img src="img/group/chairman9.png">
                    <span>5张</span>
                </div>
                <p>钱金耐在德汇集团召开2013年上半年工作总结表彰大会上发表讲话</p>
            </li>
        </ul>
    </div>
</div>
<?php include('side_slider.php');?>
<script>
    $('.productline').find('h5').click(function(){
        var that = $(this).parent();
        that.find('.productline-detail').toggle('100',function(){
            var obj = $(this);
            if (obj.css("display") == "none") {
                obj.siblings("h5").css({'background':'url("img/group/bottom.png") no-repeat','background-position':'5.3rem center','background-size':'0.3rem'});
            } else {
                obj.siblings("h5").css({'background':'url("img/group/sanjiao1.png") no-repeat','background-position':'5.3rem center','background-size':'0.3rem'});

            }
        });

    })
</script>
</body>
</html>