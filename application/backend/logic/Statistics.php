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

class Statistics extends AdminBase
{	

	// 未审核的文章数
    public function getUncheckPostsNumber(){

    	if($this->checkAddonByName("cms")==0) return 0;

    	return Core::loadAddonModel("Posts","cms","controller")->getStatistics(["ischeck"=>0,"isdelete"=>0]);

    }

    // 订单数据 
    public function getOrderNumber($where=[]){
    	
    	if($this->checkAddonByName("mall")==0) return 0;

    	return Core::loadAddonModel("Order","mall","controller")->getStatistics($where);
    }

    private function checkAddonByName($addon_name=""){
    	return Core::loadModel("Addon")->getStatistics(["module"=>$addon_name,"status"=>1]);
    }



}
?>