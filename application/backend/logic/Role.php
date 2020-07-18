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
use \tpfcore\Core;
/**
 *  管理员角色
 */
class Role extends AdminBase
{
    public function getRoleList($where = [],$field = true, $order = '', $is_paginate = true){
        $paginate_data = $is_paginate ? ['rows' => DB_LIST_ROWS] : false;
	    return self::getObject($where ,$field, $order, $paginate_data);
    }
    public function addRole($data){
        $validate=\think\Loader::validate($this->name);
        $validate_result = $validate->scene('add')->check($data);
        if (!$validate_result) {    
            return [RESULT_ERROR, $validate->getError(), null];
        }
        $last_id=Core::loadModel($this->name)->saveObject($data);
        if($last_id){
            return [RESULT_SUCCESS, '操作成功', url('Role/index')];
        }else{
            return [RESULT_ERROR, '操作失败', url('Role/index')];
        }
    }
    public function del($data){
        $result=self::deleteObject($data,true);
        if($result){
            return [RESULT_SUCCESS, '操作成功', url('Role/index')];
        }else{
            return [RESULT_ERROR, '操作失败', url('Role/index')];
        }
    }
    public function getRole(){
        return self::getObject();
    }
}
?>