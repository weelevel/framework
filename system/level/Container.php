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

use Closure;
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
    protected $classList = [];

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
     * 绑定一个类标识
     * @param $abstract 名字
     * @param $concrete 类、闭包、对象
     * @return $this
     */
    public function bindClass($abstract, $concrete)
    {
        if (is_array($abstract)){
            //批量绑定
            foreach ($abstract as $key => $value) {
                $this->bindClass($key, $value);
            }
        } elseif ($concrete instanceof Closure) {
            //闭包
            $this->classList[$abstract] = $concrete;
        } elseif (is_object($concrete)) {
            //对象实例
            $this->instance($abstract, $concrete);
        } else {

            $this->classList[$abstract] = $concrete;
        }

        return $this;
    }

    /**
     * 绑定实例
     * @param $abstract 类名字
     * @param $instance 对象
     * @return $this
     */
    public function instance($abstract, $instance)
    {
        $this->instanceList[$abstract] = $instance;

        return $this;
    }

    /**
     * 获取实例
     * @param $abstract
     * @return mixed|object
     * @throws Exception
     */
    public function name($abstract)
    {
        if(isset($this->classList[$abstract]) || isset($this->instanceList[$abstract])) {
            return $this->make($abstract);
        }

        throw new Exception('class not exists: ' . $abstract);
    }

    /**
     * 返回类的实例
     * @param $className 类名
     * @param array $params 参数
     * @param bool $newInstance 不缓存到实例树
     * @return mixed|object
     * @throws \Exception
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

    /**
     * 创建对象
     * @param $class 类名
     * @param array $params 参数
     * @return object
     * @throws Exception
     */
    public function invokeClass($class, $params = [])
    {
        try {
            $reflect = new ReflectionClass($class);
        }  catch (Exception $e) {
            throw new Exception('class not exists: ' . $class);
        }

        //获取构造方法
        $constructor = $reflect->getConstructor();
        $params = $constructor ? $this->bindParams($constructor, $params) : [];

        $object = $reflect->newInstanceArgs($params);

        return $object;
    }

    /**
     * 参数绑定
     * @param ReflectionFunctionAbstract $reflect 反射方法
     * @param array $params 参数
     * @return array
     * @throws Exception
     */
    public function bindParams(ReflectionFunctionAbstract $reflect, array $params)
    {
        //初始化返回参数
        $result = [];
        //不存在参数
        if ($reflect->getNumberOfParameters() == 0) {
            return $result;
        }
        //重置指针开始位置
        reset($params);
        $type   = key($params) === 0 ? 1 : 0;
        //获取方法参数
        $paramsList= $reflect->getParameters();
        foreach ($paramsList as $param) {
            $name = $param->getName();
            $class = $param->getClass();

            if ($class) {
                //类
                $result[] = $this->getObjectParam($class->getName(), $params);
            } elseif ($type == 1 && !empty($params)) {
                //有序
                $result[] = array_shift($params);
            } elseif ($type == 0 && isset($params[$name])) {
                //没序 key => $value
                $result[] = $params[$name];
            } elseif ($param->isDefaultValueAvailable()) {
                //带默认值的
                $result[] = $param->getDefaultValue();
            } else {
                //缺失参数
                throw new Exception('method param miss: ' . $name);
            }
        }

        return $result;
    }

    /**
     * 处理类、对象的参数
     * @param string $className 类名
     * @param array $params 参数
     * @return string|mixed|object
     * @throws Exception
     */
    protected function getObjectParam(string $className, array &$params)
    {
        $array = $params;
        $value = array_shift($array);

        if ($value instanceof $className) {
            $result = $value;
            array_shift($params);
        } else {
            $result = $this->make($className);
        }

        return $result;
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