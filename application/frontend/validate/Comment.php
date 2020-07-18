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
use app\common\validate\ValidateBase;
use think\Db;

class Comment extends ValidateBase
{
	// 验证规则
    protected $rule = [
        'poster'              => 'require|max:10',
        'mail'              => 'require|email',
        'url'              => 'url',
        'comment'              => 'require',
    ];

    // 验证提示
    protected $message = [
        'poster.require'          => '昵称必须填',
		'poster.max'     		=> '昵称最多长度10位',		
        'mail.require'          => '邮箱必须填',
        'mail.email'          => '邮箱格式错误',
        'url.url'          => '地址格式错误',
        'comment.require'          => '内容必须填',
    ];

    // 应用场景
    protected $scene = [
        'add'  =>  ['poster','mail','ur','comment']
    ];
}