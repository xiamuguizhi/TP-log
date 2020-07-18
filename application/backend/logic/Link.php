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

class Link extends AdminBase
{
	#保存
	public function saveLink($data){
        $validate=\think\Loader::validate($this->name);
        $scene=isset($data['id'])?"edit":"add";
        $validate_result = $validate->scene($scene)->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $validate->getError(), null];
        }
		$last_id=Core::loadModel($this->name)->saveObject($data);
		if($last_id){
        	return [RESULT_SUCCESS, '操作成功', url('Link/index')];
        }else{
        	return [RESULT_ERROR, '操作失败', url('Link/index')];
        }
	}
	
	#删除
	public function delLink($data){
		if(is_array($data['id'])){
			$data['id'] = ['in',implode(',',$data['id'])];
		}
		return Core::loadModel($this->name)->deleteObject($data,true)?[RESULT_SUCCESS, '删除成功', url('Link/index')]:[RESULT_ERROR, '删除失败', url('Link/index')];
	}
	
	#获取
	public function getFriendLink($data=[]){
		return self::getList($data);
	}
	
	#审核
	public function checkLink($data){
		if(empty($data)){
			return null;
		}
		if(is_array($data['id'])){
			$arr=[];
			foreach ($data['id'] as $k => $v) {
				$arr[$v]['id']=$v;
				$arr[$v]['hide']=$data['ischeck'];
			}
			return self::saveAll($arr)?[RESULT_SUCCESS,'操作成功',url('Link/index')]:[RESULT_ERROR,'操作失败',url('Link/index')];
		}
	}
	
	#统计
	public function getCountLinks($where=[]){
        return self::getStatistics($where);
    }
		
}