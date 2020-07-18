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
namespace app\backend\validate;
use app\common\validate\ValidateBase;
use think\Db;
class Comment extends AdminBase
{
    // 验证规则
    protected $rule = [
        'poster'                 => 'require',
        'mail'                 => 'require',
        'comment'               => 'require',
        'gid'               => 'require',
    ];
    // 验证提示
    protected $message = [
	    'poster.require'             => '姓名不能为空',
	    'mail.require'             => 'Email不能为空',
        'comment.require'             => '还是写点内容吧',
    ];
    // 应用场景
    protected $scene = [
        'add'=>['poster','email','comment','gid']
    ];
}