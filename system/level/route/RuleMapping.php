<?php
// +----------------------------------------------------------------------
// | weelevel
// +----------------------------------------------------------------------
// | Copyright (c) 2015~2020 http://yanghuangsheng.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: iYang <260287826@qq.com>
// +----------------------------------------------------------------------
// | Time: 2020/4/12 9:37 下午


namespace level\route;


trait RuleMapping {

    /**
     * 当前访问url
     * @var
     */
    protected $url;

    /**
     * 匹配路由
     * @param $url
     * @param $method
     * @return bool|mixed
     */
    public function seekMap($url, $method)
    {
        $this->url = $url;
        $ruleList = $this->ruleList[$method];

        foreach ($ruleList as $key => $value) {
            if ( false !== $var = $this->match($value)) {
                $route = $this->pregVar($var, $value);
                break;
            }
        }
        return isset($route) ? $route : false;
    }

    /**
     * 匹配参数到路由
     * @param $var
     * @param $item
     * @return mixed
     */
    public function pregVar($var, $item)
    {
        foreach ($var as $key => $value) {
            $node = ":$key";
            if (false !== strpos($item['route'], $node)) {
                $item['route'] = str_replace($node, $value, $item['route']);
                unset($var[$key]);
            }
        }
        $item['option'] = array_merge($item['option'], $var);

        return $item;
    }


    /**
     * URL和规则路由是否匹配
     * @param $ruleItem
     * @return array|bool
     */
    public function match($ruleItem)
    {
        //参数
        $var  = [];
        $divide = '/';
        $url = $divide . trim($this->url, '/');
        $rule = $ruleItem['name'];
        $completeMatch = $ruleItem['complete'];
        //规则不存在变量
        if (strpos($rule, '<') === false) {
            if (strcasecmp($url, $rule) === 0) {
                return $var;
            }
            return false;
        }
        //对比静态部份
        $slash = preg_quote('/-' . $divide, '/');
        if ($matchRule = preg_split('/[' . $slash . ']?<\w+\??>/', $rule, 2)) {
            if ($matchRule[0] && 0 !== strncasecmp($rule, $url, strlen($matchRule[0]))) {
                return false;
            }
        }
        //对比整体
        if (preg_match_all('/[' . $slash . ']?<?\w+\??>?/', $rule, $matches)) {
            $regex = $this->itemRegex($rule, $matches[0], $ruleItem['pattern'], $completeMatch);
            try {
                if (!preg_match('/^' . $regex . ($completeMatch ? '$' : '') . '/u', $url, $match)) {
                    return false;
                }
            } catch (Exception $e) {
                throw new Exception('route pattern error');
            }
            foreach ($match as $key => $val) {
                if (is_string($key)) {
                    $var[$key] = $val;
                }
            }
        }

        return $var;

    }

    /**
     * 生成路由的验正规则
     * @param $rule
     * @param $match
     * @param $pattern
     * @param $complete
     * @return string
     */
    public function itemRegex($rule, $match, $pattern, $complete)
    {
        $replace = [];
        foreach ($match as $name) {
            $replace[] = $this->nameRegex($name, $pattern);
        }
        // 是否区分 / 地址访问
        if ('/' != $rule) {
            if (substr($rule, -1) == '/') {
                $rule     = rtrim($rule, '/');
                $hasSlash = true;
            }
        }

        $regex = str_replace(array_unique($match), array_unique($replace), $rule);
        $regex = str_replace([')?/', ')/', ')?-', ')-', '\\\\/'], [')\/', ')\/', ')\-', ')\-', '\/'], $regex);

        if (isset($hasSlash)) {
            $regex .= '\/';
        }

        return $regex . ($complete ? '$' : '');
    }

    /**
     * 生成变量规则
     * @param $name
     * @param $pattern
     * @return string
     */
    public function nameRegex($name, $pattern)
    {
        $optional = '';
        $nameRule = '\w+';
        if ( '/' == $slash = substr($name, 0, 1)) {
            $prefix = '\\' . $slash;
            $name = substr($name, 1);
            $slash = substr($name, 0, 1);
        } else {
            $prefix = '';
        }
        //变量
        if ('<' != $slash) {
            return $prefix . preg_quote($name, '/');
        }
        //可选
        if (strpos($name, '?')) {
            $name = substr($name, 1, -2);
            $optional = '?';
        } elseif (strpos($name, '>')) {
            $name = substr($name, 1, -1);
        }
        //变量正则规则
        if (isset($pattern[$name])) {
            $nameRule = $pattern[$name];
            if (0 === strpos($nameRule, '/') && '/' == substr($nameRule, -1)) {
                $nameRule = substr($nameRule, 1, -1);
            }
        }

        return '(' . $prefix . '(?<' . $name . '>' . $nameRule . '))' . $optional;

    }
}