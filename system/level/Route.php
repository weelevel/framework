<?php
// +----------------------------------------------------------------------
// | weelevel
// +----------------------------------------------------------------------
// | Copyright (c) 2015~2020 http://yanghuangsheng.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: iYang <260287826@qq.com>
// +----------------------------------------------------------------------
// | Time: 2020/4/10 4:35 下午


namespace level;

use Exception;
use level\route\RuleItem;
use level\route\RuleMapping;


class Route {

    use RuleItem, RuleMapping;
    /**
     * 规则树
     * @var array
     */
    protected $ruleList = [];

    /**
     * 变量规则
     * @var array
     */
    protected $pattern = [];

    /**
     * 所有请求方式
     * @var array
     */
    protected $method = [
        'get',
        'post',
        'put',
        'delete',
        'patch',
    ];

    public function getRuleList()
    {
        var_dump($this->ruleList);
    }

    /**
     * 增加路由
     * @param $rule 路由规则
     * @param $route 路由地址
     * @param string $method 请求类
     * @param array $option 参数
     * @param array $pattern 变量规则
     * @return $this
     */
    public function rule($rule, $route, $method = '*', array $option = [], array $pattern = [])
    {
        $ruleItem = $this->compileItem($rule);
        $ruleItem['method'] = $method;
        $ruleItem['route'] = $route;
        $ruleItem['option'] = $option;
        $ruleItem['pattern'] = $pattern;

        return $this->addRuleList($ruleItem);
    }

    /**
     * 增加规则到树
     * @param $item
     * @return $this
     */
    protected function addRuleList($item)
    {
        $method = strtolower($item['method']);

        if (in_array($method, $this->method)) {
            //增加到树
            $this->ruleList[$method][] = $item;
        } elseif ($method == '*') {
            //增加所有请求方式
            foreach ($this->method as  $key => $value) {
                $item['method'] = $value;
                $this->ruleList[$value][] = $item;
            }
        }

        return $this;
    }

    /**
     * GET路由
     * @param $rule 路由规则
     * @param $route 路由地址
     * @param array $option 参数
     * @param array $pattern 变量规则
     * @return $this
     */
    public function get($rule, $route, array $option = [], array $pattern = [])
    {

        return $this->rule($rule, $route, 'get', $option, $pattern);
    }

    /**
     * POST路由
     * @param $rule 路由规则
     * @param $route 路由地址
     * @param array $option 参数
     * @param array $pattern 变量规则
     * @return $this
     */
    public function post($rule, $route, array $option = [], array $pattern = [])
    {

        return $this->rule($rule, $route, 'post', $option, $pattern);
    }

    /**
     * PUT路由
     * @param $rule 路由规则
     * @param $route 路由地址
     * @param array $option 参数
     * @param array $pattern 变量规则
     * @return $this
     */
    public function put($rule, $route, array $option = [], array $pattern = [])
    {

        return $this->rule($rule, $route, 'put', $option, $pattern);
    }

    /**
     * DELETE路由
     * @param $rule 路由规则
     * @param $route 路由地址
     * @param array $option 参数
     * @param array $pattern 变量规则
     * @return $this
     */
    public function delete($rule, $route, array $option = [], array $pattern = [])
    {

        return $this->rule($rule, $route, 'delete', $option, $pattern);
    }

    /**
     * PATCH路由
     * @param $rule 路由规则
     * @param $route 路由地址
     * @param array $option 参数
     * @param array $pattern 变量规则
     * @return $this
     */
    public function patch($rule, $route, array $option = [], array $pattern = [])
    {

        return $this->rule($rule, $route, 'patch', $option, $pattern);
    }

}