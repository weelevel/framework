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
     * @param $className
     * @param $params
     * @param bool $newInstance
     */
    public function make($className, $params, $newInstance = false)
    {

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