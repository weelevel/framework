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

    public function __construct($name = 'uwywuwyuwu')
    {
        echo '__construct';
    }
    public function show()
    {
        $this->name = '2121';
        echo 'the app show';
    }
}