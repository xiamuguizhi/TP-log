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

use app\common\logic\LogicBase;
use \tpfcore\Core;
use think\Validate;
/**
 * Admin基础逻辑
 */
class AdminBase extends LogicBase
{
	/*
		传递的数据必须要有下面的一些值 ，不然就不通过
		$table 	表名
		$colum  列名
		$columval  列值
		$key 主键名
		$keyval  主键值
	*/
	public function ajaxdata($data){
		$validate = new Validate(["table"=>"require","colum"=>"require","columval"=>"require","key"=>"require","keyval"=>"require|regex:\d+"]);
		if(!$validate->check($data)){
		    return [-4,$validate->getError(),null];
		}
		extract($data);
		$result=Core::loadModel($table)->saveObject([$key=>$keyval,$colum=>$columval]);
		if($result){
			return [1, '操作成功',null];
		}
		return [0, '操作失败',null];
	}
}