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
 * 导航管理
 */
class Nav extends AdminServiceBase
{
	public function getNavList($type="list",$data=[]){
		if($type=="list")
			return Core::loadModel($this->name)->getNavList($data);
		else
			return Core::loadModel($this->name)->getTreeNav($data);
	}
	public function saveNav($data){
		return Core::loadModel($this->name)->saveNav($data);	
	}
	public function getNavListByid($data){
		return Core::loadModel($this->name)->getNavListByid($data);
	}
	public function getNavArrTree($where,$filter=false,$returnarr=false){
		return Core::loadModel($this->name)->getNavArrTree($where,$filter,$returnarr);	
	}
	public function delNav($data){
		return Core::loadModel($this->name)->delNav($data);
	}
}
