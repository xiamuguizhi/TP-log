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
 *  导航逻辑
 */
class NavCat extends AdminBase
{
	public function saveNavCat($data){
		$validate=\think\Loader::validate($this->name);
		$validate_result = $validate->scene('add')->check($data);
        if (!$validate_result) {    
            return [RESULT_ERROR, $validate->getError(), null];
        }
        !empty($data['active']) && $this->updateMainNav($data);
		$last_id=self::saveObject($data);
		if($last_id){
        	return [RESULT_SUCCESS, '操作成功', url('Nav/index')];
        }
	}
	public function delNavCat($data){
		return self::deleteObject($data,true)?[RESULT_SUCCESS, '删除成功', url('Nav/index')]:[RESULT_ERROR, '删除失败', url('NavCat/index')];
	}
	public function getNavCatList($where = [], $field = true, $order = '', $is_paginate = true){
		$paginate_data = $is_paginate ? ['rows' => DB_LIST_ROWS] : false;
		return self::getObject($where, $field, $order, $paginate_data);
	}
	public function updateMainNav($data){
		if(isset($data['id'])){
			self::updateObject("id!=".$data['id'],['active'=>0]);
		}else{
			self::updateObject(["id"=>1],['active'=>0]);
		}
	}
}