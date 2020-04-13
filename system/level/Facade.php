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
     * 始终创建新对像实例
     * @var bool
     */
    protected static $newInstance;

    /**
     * 创建Facade实例
     * @param string $class
     * @param array $params
     * @param bool $newInstance
     * @return mixed|object
     * @throws \Exception
     */
    protected static function createFacade($class = '', $params = [], $newInstance = false)
    {
        //为空则获取当前绑定的类名
        $class = $class ?: static::class;

        //存在继承
        if (static::$facadeName) {
            $class = static::$facadeName;
        }

        if (static::$newInstance) {
            $newInstance = true;
        }

        return Container::getInstance()->make($class, $params, $newInstance);
    }

    /**
     * 拆分参数
     * @param $params 参数
     * @param string $keyName 有构造参数标识
     * @return array
     */
    protected static function divideParams($params, $keyName = 'init')
    {
        //初始新参数
        $newParams = [];
        //提取构造参数
        if (isset($params[0]) && $params[0] == $keyName) {
            array_shift($params);
            $initParams = array_shift($params);
            //参数为数组
            if (is_array($initParams)) {
                $newParams[] = $initParams;
            } else {
                $newParams[][] = $initParams;
            }
        } else {
            $newParams[] = [];
        }
        //方法参数
        $newParams[] = $params;

        return $newParams;
    }

    /**
     * 调用实际类方法
     * @param $method
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public static function __callStatic($method, $params)
    {
        list($init, $params) = static::divideParams($params);
        return call_user_func_array([static::createFacade('', $init), $method], $params);
    }
}