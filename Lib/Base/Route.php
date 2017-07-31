<?php

use Uccu\DmcatTool\Traits\InstanceTrait;
use Uccu\DmcatTool\Tool\LocalConfig as Config;
Class Route{

    use InstanceTrait;

    private $request;

    function __construct(){

        $this->request = Request::getSingleInstance();

    }
    


    /* 控制器 */
    public function controller($url,$param2){
        
        return $this->parse('method '.$url.' '.$param2);

    }

    /* 方法 */
    public function method($url,$param2){
        
        return $this->parse('method '.$url.' '.$param2);
    }

    /* 应用 */
    public function app($url,$param2){
        
         return $this->parse('app '.$url.' '.$param2);
         
    }

    /* e302 */
    public function e302($url,$param2){
        
         return $this->parse('302 '.$url.' '.$param2);
         
    }

    /* 获取 */
    public function get($num = null){

        return $type ? $this->list[$num] : $this->list;
        
    }
    

    /* 注册路由 */
    public function parse($route = ''){

        /* 获取路由设置 */
        !$route && $route = Config::route()->ROUTE;
        if(!$route)return;


        /* 当只有一条时转换到数组 */
        !is_array($route) && $route = array($route);

        /* 获取请求信息 */
        $request = $this->request;
        
        foreach($route as $rule){

            preg_match('#(\w+) +(.*?)(?= +(.*)|$)#',$rule,$matches);

            
            $path = $request->path;


            if(in_array($matches[1],['controller','app','method'])){
                
                if(!$matches[3]){
                    $matches[3] = $matches[2];
                    $matches[2] = '';
                }
                
                if($matches[2] && stripos($request->path.'/' ,$matches[2].'/') !== 0)continue;
                
                $matches[2] && $path = substr($request->path,strlen($matches[2])+1);
                
                $pathArray = explode('/',$path);
                
                
                if($matches[1] == 'app'){

                    $app = $matches[3];
                    $controller = $pathArray[0];
                    if(!$controller || !preg_match('#^[a-z0-9]+$#i',$controller))continue;
                    $class = $matches[3].'\\'.ucfirst($controller).'Controller';
                    $controller = $class::getSingleInstance();
                    $method = $pathArray[1];

                }elseif($matches[1] == 'controller'){

                    $class = $matches[3];
                    $controller = $class::getSingleInstance();
                    $method = $pathArray[0];

                }elseif($matches[1] == 'method'){

                    $where = strripos($matches[3], "\\");
                    $class = substr( $matches[3],0,$where );
                    $controller = $class::getSingleInstance();
                    $method = substr( $matches[3],$where+1 );

                }

                if(!$method)return;
                elseif(!method_exists($controller,$method))E::throwEx('Method Not Exist');
                else{

                    $this->getMethod($controller,$method);

                    return;
                }
                continue;
                
            }elseif($matches[1] == 'regexp'){

                if($matches[2] && $matches[3]){
                    $newPath = preg_replace('/'.$matches[2].'/',$matches[3],$request->path);
                    $request->flesh_path($newPath);
                    continue;
                }
                

            }elseif($matches[1] == '302'){
                if(!$matches[3]){
                    header('Location: '.$matches[2]);return;
                }
                    
                elseif($request->path==$matches[2]){
                    header('Location: '.$matches[3]);return;
                }
            }
        }
        header('HTTP/1.1 404 Not Found');

    }



    public function getMethod($controller,$method,$get = []){

        $type = Config::get('CONTROLLER_REQUEST');
        !$get && $get = $this->request->$type;
        $controllerReflection = new ReflectionClass($controller);
        $actionReflection = $controllerReflection->getMethod($method);
        $paramReflectionList = $actionReflection->getParameters();

        $params = [];

        foreach ($paramReflectionList as $paramReflection) {

            $name = $paramReflection->getName();
            if($class = $paramReflection->getClass()){
                $class = $class->name;
                if(method_exists($class,'getSingleInstance')){

                    if($class=='Model'){
                        $params[] = $class::copyMutiInstance($name);
                        continue;
                    }

                    $params[] = $class::getSingleInstance();
                    continue;
                }
            }
            if (isset($get[$name])) {
                $params[] = $get[$name];
                continue;
            }
            if ($paramReflection->isDefaultValueAvailable()) {
                $params[] = $paramReflection->getDefaultValue();
                continue;
            }
                            
                            
            $params[] = null;
        }

        call_user_func_array(array($controller,$method),$params);



    }


}

