<?php
// +----------------------------------------------------------------------
// | weelevel
// +----------------------------------------------------------------------
// | Copyright (c) 2015~2020 http://yanghuangsheng.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: iYang <260287826@qq.com>
// +----------------------------------------------------------------------
// | Time: 2020/4/12 12:04 下午


namespace level\route;



trait RuleItem {

    /**
     * 获取rule
     * @param $rule
     * @return array
     */
    public function compileItem($rule)
    {
        $completeMatch = $this->completeMatch($rule);
        $ruleName = $this->compileRuleName($rule);

        $item = [
            'name' => $ruleName,
            'var' => $this->parseVar($ruleName),
            'complete' => $completeMatch,
        ];

        return $item;

    }

    /**
     * 是否完全匹配
     * @param $rule
     * @return bool
     */
    public function completeMatch(&$rule)
    {
        $completeMatch = false;
        if ('$' == substr($rule, -1, 1)) {
            // 是否完整匹配
            $rule = substr($rule, 0, -1);
            $completeMatch = true;
        }
        return $completeMatch;
    }

    /**
     * 获取编译后路由规则
     * @param $rule 路由规则
     * @return string|string[]|null
     */
    public function compileRuleName($rule)
    {
        if (false !== strpos($rule, ':')) {
            $rule = preg_replace(['/\[\:(\w+)\]/', '/\:(\w+)/'], ['<\1?>', '<\1>'], $rule);
        }

        return '/' . trim($rule, '/');
    }

    /**
     * 分析路由规则中的变量
     * @access protected
     * @param  string    $rule 路由规则
     * @return array
     */
    public function parseVar($rule)
    {
        // 提取路由规则中的变量
        $var = [];

        if (preg_match_all('/<\w+\??>/', $rule, $matches)) {
            foreach ($matches[0] as $name) {
                $optional = false;

                if (strpos($name, '?')) {
                    $name     = substr($name, 1, -2);
                    $optional = true;
                } else {
                    $name = substr($name, 1, -1);
                }

                $var[$name] = $optional ? 1 : 0;
            }
        }

        return $var;
    }
}