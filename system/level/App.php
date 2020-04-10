<?php
// +----------------------------------------------------------------------
// | weelevel
// +----------------------------------------------------------------------
// | Copyright (c) 2015~2020 http://yanghuangsheng.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: iYang <260287826@qq.com>
// +----------------------------------------------------------------------
// | Time: 2020/4/6 8:37 下午


namespace level;

class App {
    /**
     * 版本
     */
    const VERSION = '0.1.0';
    /**
     * 应用根目录
     * @var
     */
    protected $rootPath;

    /**
     * 应用目录
     * @var
     */
    protected $appPath;

    /**
     * 运行目录
     * @var
     */
    protected $runtimePath;

    /**
     * App constructor.
     * @param string $rootPath 应用根目录
     */
    public function __construct($rootPath = '')
    {
        //初始根目录
        $pathExt = DIRECTORY_SEPARATOR;
        $this->rootPath = $rootPath ? rtrim($rootPath, $pathExt) : $this->getRootPath($pathExt);
        $this->appPath = $this->rootPath . 'app' . $pathExt;
        $this->runtimePath = $this->rootPath . 'runtime' . $pathExt;

    }

    /**
     * 执行应用
     */
    public function run()
    {

        echo '执行应用';
    }

    /**
     * 初始应用根目录
     * @param string $pathExt
     * @return string
     */
    public function getRootPath($pathExt = '')
    {
        //获取应用根目录
        if ($this->rootPath) {
            return $this->rootPath;
        }
        //composer依赖、直接引用或测试情况
        $needle = ['vendor', 'system'];
        $path = __DIR__;
        //寻找节点
        foreach ($needle as $key => $value) {
            $value = $pathExt . $value . $pathExt;
            if (strpos($path, $value) !== false) {
                //截取目录
                $ex = explode($value, $path);
                $this->rootPath = $ex[0] . $pathExt;
                break;
            }
        }

        return $this->rootPath;
    }

}