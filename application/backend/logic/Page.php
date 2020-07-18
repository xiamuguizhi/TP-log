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
use \tpfcore\util\Config;
use think\Db;

class Page extends AdminBase
{
	
	#删除页面
	public function delPosts($data){
		if(empty($data)){
			return null;
		}
		if(is_array($data['id'])){
			$data['id'] = ['in',implode(',',$data['id'])];//批量删除
		}		
		$this->delComments($data);		
		return Core::loadModel("Blog")->deleteObject($data,true)?[RESULT_SUCCESS, '删除成功', url('Page/index')]:[RESULT_ERROR, '删除失败', url('page/index')];
	}
	
	
	#删除评论
    public function delComments($data){		
		if(empty($data)){
			return null;
		}
       	if(is_array($data['id'])){
			$where['gid'] = ['in',implode(',',$data['id'])];//批量删除
		}else{
		$where = ['gid'=>$data['id']];
		}
		Core::loadModel("Comment")->where($where)->delete();
	}

    //审核
	public function checkPosts($data){
		if(empty($data)){
			return null;
		}
		if(is_array($data['id'])){
			$arr=[];
			foreach ($data['id'] as $k => $v) {
				$arr[$v]['id']=$v;
				$arr[$v]['hide']=$data['ischeck'];
			}
			return Core::loadModel("Blog")->saveAll($arr)?[RESULT_SUCCESS,'操作成功',url('Page/index')]:[RESULT_ERROR,'操作失败',url('Page/index')];
		}
	}
	
}