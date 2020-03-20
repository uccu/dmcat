<?php

use MtHaml\Environment;
use MtHaml\Support\Twig\Loader;
use MtHaml\Support\Twig\Extension;
use Uccu\DmcatTool\Tool\LocalConfig as Config;
use Uccu\DmcatTool\Traits\InstanceTrait;
class View{

    use InstanceTrait;

    private $data = [];

    function construct(){

        

    }

    public static function addData($data = []){

        $obj = self::getSingleInstance();

        $obj->data = array_merge($obj->data,$data);


    }


    public static function hamlReader($name,$dir = '',$data = []){

        require(VENDOR_ROOT.'leafo/lessphp/lessc.inc.php');
        $lessFilter = new LeafoLess(new lessc);
        $markdownFilter = new CebeMarkdown(new \cebe\markdown\MarkdownExtra());
        // $phpFilter = new Php;

        $mt = new Environment('twig', ['enable_escaper' => false],[
            'less'=>$lessFilter,
            'markdown'=>$markdownFilter,
            // 'mthaml_php'=>$phpFilter
        ]);

        $fs = new Twig_Loader_Filesystem(VIEW_ROOT.$dir);

        $loader = new Loader($mt, $fs);

       

        if(Config::get('VIEW_CACHE')){

            $twig = new Twig_Environment($loader, ['cache' => STORAGE_ROOT]);
        }else{

            $twig = new Twig_Environment($loader);
        }

        $twig->addExtension(new Extension());

        $obj = self::getSingleInstance();

        $obj->data = array_merge($obj->data,$data);

        echo $twig->render($name.".haml",$obj->data);

    }


    
}