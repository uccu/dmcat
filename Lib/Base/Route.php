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


    public static function get($n = null){
        if(!$n)
            return self::$list;
        else 
            return self::$list[$n];
    }
    


    public static function parse(){

        $route = conf('Route')->ROUTE;
        if(!$route)return;

        if(!is_array($route))$route = array($route);

        
        foreach($route as $r){

            preg_match('#(controller|regexp|app|302) +(.*?)(?= +(.*)|$)#',$r,$m);

            $folder = Request::folder();

            if($m[1] == 'controller' || $m[1] == 'app'){
                if(!$m[3]){
                    $m[3] = $m[2];
                    $m[2] = '';
                }
                $arr = $m[2] ? explode('/',$m[2]) : array();
                $in = true;$on = 0;
                
                foreach($arr as $k=>$v){
                    
                    if($v!=$folder[$k]){
                        $in = false;
                    }
                    $on++;
                }

                if($in){
                    if($m[1] == 'app'){
                        $app = $m[3];
                        $controller = $folder[$on];
                        if(!$controller){
                            E::throwEx('Controller Not Exist');
                        }
                        $controller = table($m[3].'\\'.$controller);
                        $method = $folder[$on+1];
                    }else{
                        $controller = table($m[3]);
                        $method = $folder[$on];
                    }
                    
                    if(!$method){
                        return;
                    }
                    elseif(!method_exists($controller,$method)){
                        E::throwEx('Method Not Exist');
                    }else{

                        $get = Request::get();

                        $controllerReflection = new ReflectionClass($controller);

                        $actionReflection = $controllerReflection->getMethod($method);

                        $paramReflectionList = $actionReflection->getParameters();

                        $params = array();

                        foreach ($paramReflectionList as $paramReflection) {

                            if($class = $paramReflection->getClass()){
                                $class = $class->name;
                                if(method_exists($class,'obj')){
                                    $params[] = $class::obj();
                                    continue;
                                }elseif(method_exists($class,'news')){
                                    $params[] = $class::news();
                                    continue;
                                }
                            }
                            if (isset($get[$paramReflection->getName()])) {
                                $params[] = $get[$paramReflection->getName()];
                                continue;
                            }
                            if ($paramReflection->isDefaultValueAvailable()) {
                                $params[] = $paramReflection->getDefaultValue();
                                continue;
                            }
                            
                            
                            $params[] = null;
                        }

                        call_user_func_array(array($controller,$method),$params);

                        return;
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
        header('Location: 404.html');

    }
}

