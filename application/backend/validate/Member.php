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
use think\Db;
/**
 * 基础验证器
 */
class Member extends AdminBase
{
	// 验证规则
    protected $rule = [
        'username'                  => 'require|unique:User',
        'old_password'              => 'require|length:6,30',
        'password'                  => 'require|length:6,30|different:old_password',
        'repassword'                => 'require|confirm:password',
        'nickname'                  => 'require|length:3,30',
        'email'                     => 'email',
        'id'                        => 'require|checkId'
        // |unique:usrname,password='.$data['account']'
    ];

    // 验证提示
    protected $message = [
        'username.require'          => '用户名不能为空',
        'username.unique'           => '用户名已存在',
        'old_password.require'      => '旧密码不能为空',
		'old_password.length'      => '旧密码长度为6-30个字符之间',
        'password.require'          => '新密码不能为空',
        'password.length'           => '新密码长度为6-30个字符之间',
        'repassword.require'        => '请再次输入密码',
        'repassword.confirm'        => '两次输入的密码不一致',
        'password.different'        => '新密码不能与旧密码相同',

        'nickname.require'          => '昵称必须',
        'nickname.length'           => '昵称长度为3-30个字符之间',
    ];

    // 应用场景
    protected $scene = [
        'update'    =>  ['old_password','password','repassword'],
        'upinfo'    =>  ['nickname'],
        'add'       =>  ['username','password','email','repassword'],
        'edit'      =>  ['password','email','repassword'],
        'del'       =>  ['id']
    ];
    protected function checkId($value){
        if(Db::name("user")->where("id=$value")->count()==0){
            return "非法的ID";
        }
        return true;
    }
}
