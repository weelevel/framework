<?php
// +----------------------------------------------------------------------
// | weelevel
// +----------------------------------------------------------------------
// | Copyright (c) 2015~2020 http://yanghuangsheng.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: iYang <260287826@qq.com>
// +----------------------------------------------------------------------
// | Time: 2020/4/7 2:53 下午


namespace level;


class Facade {

    /**
     * 继承Facade
     * @var string
     */
    protected static $facadeName = '';

    /**
     * 对像实例树
     * @var array
     */
    protected static $bind = [];

    /**
     * 始终创建新对像实例
     * @var bool
     */
    protected static $newInstance;

    /**
     * 绑定类
     * @param $name
     * @param $class
     */
    protected static function bind($name, $class)
    {
        //多个
        if (is_array($name)) {
            self::$bind = array_merge(self::$bind, $name);
        }
        //单个
        self::$bind[$name] = $class;
    }

    protected static function createFacade($class = '', $params = [], $newInstance = false)
    {
        //为空则获取当前绑定的类名
        $class = $class ?: static::class;
        //存在继承
        if (static::$facadeName) {
            $class = static::$facadeName;
        } elseif (isset(self::$bind[$class])){
            $class = self::$bind[$class];
        }

        if (static::$newInstance) {
            $newInstance = true;
        }

        return Container::getInstance()->make($class, $params, $newInstance);
    }

    /**
     * 调用实际类方法
     * @param $method
     * @param $params
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        return call_user_func_array([static::createFacade(), $method], $params);
    }
}