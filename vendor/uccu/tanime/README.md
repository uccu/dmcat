# Tanime
It can format animation's title


# LICENSE
MIT

# Example
````
/* 简体转换*/
use uccu\Tanime\Title;
$container = new Title('[Leopard-Raws][我的英雄学院S2] Boku no Hero Academia 2 04 RAW (NTV 1280x720 x264 AAC).mp4');
var_dump($container);

````

````
/* 无简体转换*/
use uccu\Tanime\Title;
$container = new Title('[Leopard-Raws][进击的巨人2nd] Shingeki no Kyojin Season 2 - 04 RAW (MBS 1280x720 x264 AAC).mp4',false);
var_dump($container);

````

