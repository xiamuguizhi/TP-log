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
namespace app\frontend\controller;
use \tpfcore\Core;
use app\common\controller\ControllerBase;
use \parsedown\Parsedown; 

class FrontendBase extends ControllerBase
{
	 public function _initialize()
    {
        parent::_initialize();
		if(!\think\Session::has("backend_author_sign")){
        if(config("config.WEB_SITE_CLOSE")){
        	$this->jump([RESULT_ERROR,config("config.WEB_SITE_CLOSE_DES")]);
        }
		}
		$Parsedown = new Parsedown();
		$c = request()->controller();
		$site_name = Core::loadAction("Setting/getSetting",['column'=>"site_name"]);
		$site_host =  Core::loadAction("Setting/getSetting",['column'=>"site_host"]);
		$tw_name = Core::loadAction("Setting/getSetting",['column'=>"tw_name"]);
		$tw_num =  Core::loadModel("Twitter","frontend","logic")->getStatistics();
		$Category = Core::loadModel("Sort")->getChilds();		
		$Userinfo = Core::loadModel("user")->where(['id'=>1])->find();		
		$this->assign(["site_name"=>$site_name,"site_host"=>$site_host,"tw_name"=>$tw_name,"tw_num"=>$tw_num,"category"=>$Category,"controller"=>$c,"userinfo"=>$Userinfo,"Parsedown"=>$Parsedown,]);
    }
}