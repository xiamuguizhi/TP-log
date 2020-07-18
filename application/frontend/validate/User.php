<?php
/**
 * ============================================================================
 * 版权所有 2017-2077 tpframe工作室，并保留所有权利。
 * @link http://www.tpframe.com/
 * @copyright Copyright (c) 2017 TPFrame Software LLC
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 */
namespace app\frontend\validate;
class User extends FrontendBase
{
	// 验证规则
    protected $rule = [
        'username'              => 'require',
        'password'              => 'require',
    ];

    // 验证提示
    protected $message = [
        'username.require'          => '用户名必须',
        'password.require'          => '密码必须',
    ];

    // 应用场景
    protected $scene = [
        'add'  =>  ['username','password']
    ];
}