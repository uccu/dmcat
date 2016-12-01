<?php

use MtHaml\Environment;
use MtHaml\Support\Twig\Loader;
use MtHaml\Support\Twig\Extension;


class View{

    
    public static function render($name){

        $mt = new Environment('twig', ['enable_escaper' => false]);

        $fs = new Twig_Loader_Filesystem(VIEW_ROOT.'Twig');

        $loader = new Loader($mt, $fs);

       

        if(Config::get('VIEW_CACHE')){

            $twig = new Twig_Environment($loader, ['cache' => STORAGE_ROOT]);
        }else{

            $twig = new Twig_Environment($loader);
        }

        $twig->addExtension(new Extension());

        echo $twig->render($name.".twig");

    }


    
}