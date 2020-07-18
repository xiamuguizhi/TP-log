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
namespace app\backend\service;
use \tpfcore\Core;
/**
 * 会员信息
 */
class Member extends AdminServiceBase
{
	public function uppwd($data){
		return Core::loadModel($this->name)->uppwd($data);
	}
	public function upUserInfo($data){
		return Core::loadModel($this->name)->upUserInfo($data);
	}
	public function getInfo($where){
		return Core::loadModel($this->name)->getInfo($where);
	}
	public function getUserList($data,$field="*"){
		return Core::loadModel($this->name)->getUserList($data,$field);
	}
	public function addUser($data){
		return Core::loadModel($this->name)->addUser($data);	
	}
	public function addAdmin($data){
		return Core::loadModel($this->name)->addAdmin($data);	
	}
	public function ban($data){
		return Core::loadModel($this->name)->ban($data);
	}
	public function priv($data){
		return Core::loadModel($this->name)->priv($data);
	}
	public function editUser($data){
		return Core::loadModel($this->name)->editUser($data);
	}
	public function delUser($data){
		return Core::loadModel($this->name)->delUser($data);
	}
	public function delAdmin($data){
		return Core::loadModel($this->name)->delAdmin($data);
	}
}
