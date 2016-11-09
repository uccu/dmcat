<?php
/**
 * 一个用于测试的类
 * Class TestController
 */
class TestController {
    /**
     * 嗯，著名的hello world
     * @param $name
     */
    public function helloWordAction($name)
    {
        echo "hello {$name}!".PHP_EOL;
    }
}

# 通过反射进行参数绑定调起类的方法
# @see http://php.net/manual/zh/book.reflection.php

# 方法,从路由获取的,类也是由路由获取的，这里意思一下就好了
$action = 'helloWordAction';
# 传进来的参数，从路由获取的
$paramsInput['name'] = 'toozy';

# 获取类的反射
$controllerReflection = new ReflectionClass(TestController::class);
# 不能实例化，就是不能new一个的话，这个游戏就玩不下去了啊
if (!$controllerReflection->isInstantiable()) {
    throw new RuntimeException("{$controllerReflection->getName()}不能被实例化");
}

# 获取对应方法的反射
if (!$controllerReflection->hasMethod($action)) {
    throw new RuntimeException("{$controllerReflection->getName()}没有指定的方法:{$action}");
}
$actionReflection = $controllerReflection->getMethod($action);
# 获取方法的参数的反射列表（多个参数反射组成的数组）
$paramReflectionList = $actionReflection->getParameters();
# 参数，用于action
$params = [];
# 循环参数反射
# 如果存在路由参数的名称和参数的名称一致，就压进params里面
# 如果存在默认值，就将默认值压进params里面
# 如果。。。没有如果了，异常
foreach ($paramReflectionList as $paramReflection) {
    # 是否存在同名字的路由参数
    if (isset($paramsInput[$paramReflection->getName()])) {
        $params[] = $paramsInput[$paramReflection->getName()];
        continue;
    }
    # 是否存在默认值
    if ($paramReflection->isDefaultValueAvailable()) {
        $params[] = $paramReflection->getDefaultValue();
        continue;
    }
    # 异常
    throw new RuntimeException(
        "{$controllerReflection->getName()}::{$actionReflection->getName()}的参数{$paramReflection->getName()}必须传值"
    );
}

# 调起
$actionReflection->invokeArgs($controllerReflection->newInstance(), $params);