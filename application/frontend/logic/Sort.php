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
namespace app\frontend\logic;
use \tpfcore\util\Tree;
use \tpfcore\Core;
use \tpfcore\helpers\StringHelper;
use \tpfcore\util\Data;
use \think\Config;

class Sort extends FrontendBase
{
	
	public $ids=[];
	public function getCategory($data){
		$listCate=self::getList($data);
		foreach ($listCate as $key => $value) {
			$listCate[$key]=$value->toArray();
		}
		$listCate=Data::genTree($listCate);
		return $listCate;
	}
	
	public function getCategoryIds($pid){
		$list=self::getlist(["where"=>["parentid"=>$pid],"field"=>"id,parentid"]);
		foreach ($list as $key => $value) {
			$this->ids[]=$value['id'];
			$this->getCategoryIds($value['id']);
		}
		return $this->ids;
	}
	
	public function getChilds(){
		return \think\Db::query("SELECT b.id,b.title,count(a.cateid) as num FROM ".DB_PREFIX."blog as a  RIGHT JOIN ".DB_PREFIX."sort as b ON a.cateid=b.id WHERE b.display=1 and a.hide='n' GROUP BY b.id");
	}

	public function getCategoryId($id){
		$result=self::getOneObject(["id"=>$id]);
		return $result?$result->toArray():"";
	}
	
}