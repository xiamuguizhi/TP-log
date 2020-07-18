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
namespace app\backend\logic;

/**
 *  用户逻辑
 */
class User extends AdminBase
{
	public function logout(){
		\think\Session::delete("backend_author_sign");
		return [0, '注销成功', url('User/login')];
	}
	public function login($data){
		$admin_login_limit_ip=config("config.ADMIN_LOGIN_LLIMIT_IP");
		//如果设置了IP限制登录
		if(!empty($admin_login_limit_ip)){
			$request_ip=request()->ip();
			if(stripos($request_ip,"127.0.0.")===false && !in_array($request_ip, explode(",", $admin_login_limit_ip))){
				return [RESULT_ERROR,'该IP禁止登录',url('User/login')];
			}
		}
		$scene=config('config.ADMIN_LOGIN_VERIFY_SWITCH')?"select":"no_captcha";
		$validate=\think\Loader::validate($this->name);
		$validate_result = $validate->scene($scene)->check($data);
        if (!$validate_result) {    
            return [RESULT_ERROR, $validate->getError(), null];
        }

        $user=self::getOneObject(["username"=>$data['username'],"password"=>'###'.md5($data['password'].DATA_ENCRYPT_KEY),'type'=>1]);
        if(empty($user)){
        	return [RESULT_ERROR, '用户名或密码错误', url('User/login')];
        }
        self::saveObject(['last_login_time'=>time(),"id"=>$user['id']]);
        \think\Session::set("backend_author_sign",array("username"=>$data['username'],"userid"=>$user['id']));
		return [RESULT_SUCCESS, '登录成功', url('User/login')];
	}
	public function getUserList($where = [], $field = true, $order = '', $is_paginate = true){
		$paginate_data = $is_paginate ? ['rows' => DB_LIST_ROWS] : false;
		return self::getObject($where, $field, $order, $paginate_data);
	}
	
	public function getCountUsers($where=[]){
        return self::getStatistics($where);
    }	
	
}