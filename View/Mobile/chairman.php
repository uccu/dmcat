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
    <link rel="stylesheet" href="/Home/css/swiper.min.css">
    <script type="text/javascript" src="/Home/js/swiper.jquery.min.js"></script>
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
    <style>
    .main-box .chairman-picture .chairman-picturehead li{height:160px}
    </style>
    <div class="chairman-picture">
        <ul class="chairman-picturehead">

                <?php 
                    foreach($chairmanPicture as $v){

                        echo '<li>
                <div class="chairman-pictureimg">
                    <img src="/pic/'.$v->first.'">
                    <span data-image="'.$v->picArray.'">'.$v->count.' 张</span>
                </div>
                <h2>'.langV($v,'description',40).'</h2>
            </li>';
                    }
                
                ?>
            
        </ul>
        
    </div>
</div>
<style>
    .chairman_info{font-size: 12px; color: #676767; margin-top: 15px; line-height: 26px; padding-bottom: 40px; border-bottom: 1px solid #DEDEDE;}
    .chairman_box{position: relative; width: 268px; height: 188px; border: 1px solid #DEDEDE; padding: 10px; margin: 0 15px 60px; float: left;}
    .chairman_box .table{width: 100%; height: 100%; display: table;}
    .chairman_box .table .table-cell{display: table-cell; width: 100%; height: 100%; text-align: center; vertical-align: middle;}
    .chairman_box h1{position: absolute; height: 40px; line-height: 20px; text-align: center; width: 268px; bottom: -46px; overflow: hidden;}
    .chairman_box span{display: block; width: 50px; height: 20px; font-size: 12px; line-height: 20px; background: rgb(233, 103, 125); color: #FFF; text-align: center; position: absolute; border-radius: 14px; right: 15px; bottom: 15px; cursor: pointer;}
    .chairman-images-scan{width: 100%; height: 526px; overflow: hidden; position: relative; top: 50%; margin: -263px auto 0;}
    .chairman-scan-box.chairman_box{padding: 0; height: 526px; width: 800px; margin: 0 auto; border: none;}
    .chairman-scan-box.chairman_box img{border: 3px solid #F4F4F4;}
    .dialog{z-index:10000;position: fixed; width: 100%; height: 100%; background: rgba(0,0,0,0.4); left: 0; top: 0;}
    .dialog .dialog-close{display: block; background: #F4F4F4; padding: 4px 10px; position: absolute; right: 0; top: 0; color: #666; cursor: pointer;}
</style>
<div class="dialog" style="display: none;">
    <div class="dialog-close" onclick="$('.dialog').hide();">✕ 关闭</div>
    <div class="chairman-images-scan">
        <div class="swiper-wrapper">
        </div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
</div>
<?php include('side_slider.php');?>
<script>
    $(document).ready(function () {
        $(".chairman-pictureimg").click(function () {
            var data = $(this).find('span').attr("data-image");
            if (data == "") {
                return false;
            }

            data = data.split(";");
            var html = '';
            $.each(data, function (i, v) {
                html += '<div class="chairman-scan-box chairman_box swiper-slide">'+
                        '<div class="table">'+
                        '<div class="table-cell">'+
                        '<img src="' + v + '">'+
                        '</div></div></div>';
            });
            $(".swiper-wrapper").html(html);
            $(".dialog").show();
            new Swiper('.chairman-images-scan', {
                effect: 'flip',
                grabCursor: true,
                nextButton: '.swiper-button-next',
                prevButton: '.swiper-button-prev'
            });
        });
    });
</script>
</body>
</html>