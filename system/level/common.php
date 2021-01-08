<?php
// +----------------------------------------------------------------------
// | weelevel
// +----------------------------------------------------------------------
// | Copyright (c) 2015~2020 http://yanghuangsheng.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: iYang <260287826@qq.com>
// +----------------------------------------------------------------------
// | Time: 2021/1/8 5:01 下午
//in_array() 优化方案
/** * in_array is too slow when array is large */
function inArray($item, $array)
{
    $flipArray = array_flip($array);
    return isset($flipArray[$item]);
}

/** * in_array is too slow when array is large */
function inArray2($item, $array)
{
    $str = implode(',', $array);
    $str = ',' . $str . ',';
    $item = ',' . $item . ',';
    return false !== strpos($item, $str) ? true : false;
}