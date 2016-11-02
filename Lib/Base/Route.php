<?php


Class Route{

    private static $list = array();

    public static function regExp($reg,$to){
        
        self::$list[] = array('regexp',$url,$app);

    }

    public static function controller($url,$control){
        
        self::$list[] = array('controller',$url,$app);

    }

    public static function app($url,$app){
        
         self::$list[] = array('app',$url,$app);
         
    }


    public static function get(){

        return self::$list;
    }
    


    public static function parse(){

        $route = conf('Route')->ROUTE;
        if(!$route)return;

        if(!is_array($route))$route = array($route);

        
        foreach($route as $r){

            preg_match('#(controller|regexp|app|302) +(.*?)(?= +(.*)|$)#',$r,$m);


            if($m[1] == 'controller' || $m[1] == 'app'){
                if(!$m[3]){
                    $m[3] = $m[2];
                    $m[2] = '';
                }
                $arr = $m[2] ? explode('/',$m[2]) : array();
                $in = true;$on = 0;
                foreach($arr as $k=>$v){
                    if($v!=Request::folder()[$k]){
                        $in = false;
                    }
                    $on++;
                }

                if($in){
                    if($m[1] == 'app'){
                        $app = $m[3];
                        $controller = Request::folder()[$on];
                        if(!$controller){
                            E::throw('Controller Not Exist');
                        }
                        $controller = table($m[3].'\\'.$controller);
                        $method = Request::folder()[$on+1];
                    }else{
                        $controller = table($m[3]);
                        $method = Request::folder()[$on];
                    }
                    
                    if(!$method){
                        
                    }
                    elseif(!method_exists($controller,$method)){
                        E::throw('Method Not Exist');
                    }else{

                        ($controller->$method)();

                    }
                    continue;
                }
                

            }elseif($m[1] == 'regexp'){

                if($m[2] && $m[3]){
                    $newPath = preg_replace('/'.$m[2].'/',$m[3],REQUEST_PATH);
                    Request::flesh_path($newPath);
                    continue;
                }
                

            }elseif($m[1] == '302'){
                if(!$m[3] && Request::$path==''){
                    header('Location: '.$m[2]);return;
                }
                    
                elseif(Request::$path==$m[2]){
                    header('Location: '.$m[3]);return;
                }
            }
        }
        //header('Location: 404.html');

    }
}

