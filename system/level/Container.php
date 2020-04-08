<?php
// +----------------------------------------------------------------------
// | weelevel
// +----------------------------------------------------------------------
// | Copyright (c) 2015~2020 http://yanghuangsheng.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: iYang <260287826@qq.com>
// +----------------------------------------------------------------------
// | Time: 2020/4/7 1:01 下午


namespace level;

use ReflectionClass;
use ReflectionFunctionAbstract;


class Container {

    /**
     * 容器对象实例
     * @var Container
     */
    protected static $instance;

    /**
     * 已实例的对象树
     * @var array
     */
    protected $instanceList = [];

    /**
     * 类库标识树
     * @var array
     */
    protected static $classList = [];

    /**
     * 实例容器
     * @return Container
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * 绑定一个类
     * @param $abstract
     * @param $concrete
     */
    protected static function bindClass($abstract, $concrete)
    {
        if (is_array($abstract)){

        }
    }

    /**
     *
     * @param $className
     * @param array $params
     * @param bool $newInstance
     */
    public function make($className, array $params = [], $newInstance = false)
    {
        //存在的实例直接返回
        if (isset($this->instanceList[$className]) && !$newInstance) {
            return $this->instanceList[$className];
        }

        $object = $this->invokeClass($className, $params);

        if (!$newInstance) {
            $this->instanceList[$className] = $object;
        }

        return $object;

    }

    public function invokeClass($class, $params = [])
    {
        //$class = App::class;
        try {
            $reflect = new ReflectionClass($class);
        }  catch (\Exception $e) {
            throw new \Exception('class not exists: ' . $class);
        }


        var_dump($reflect);
        //获取构造方法
        $constructor = $reflect->getConstructor();
        var_dump($constructor);
        //var_dump($reflect->getNumberOfParameters());
        $params = $constructor ? $this->bindParams($constructor, $params) : [];

        $object = $reflect->newInstanceArgs($params);

        return $object;
    }

    public function bindParams(ReflectionFunctionAbstract $reflect, array $params)
    {
        echo 'getNumberOfParameters<br>';
        var_dump($reflect->getNumberOfParameters());


        reset($params);
        $type   = key($params) === 0 ? 1 : 0;

        echo 'type<br>';
        var_dump($type);


        echo 'getParameters<br>';
        $paramsList= $reflect->getParameters();
        var_dump($paramsList);

        foreach ($paramsList as $param) {
            $name = $param->getName();

        }


        return [];
    }

    /**
     * 设置实例
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        echo "设置: {$name} => {$value} <br>";
    }

    /**
     * 获取实例
     * @param $name
     */
    public function __get($name)
    {
        echo "获取: {$name}";
    }
}