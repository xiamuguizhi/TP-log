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
use \think\Request;
use app\frontend\model\SlideCat;
use app\frontend\model\Slide;
use think\Cookie;


class T extends FrontendBase
{
	/**
     * 获取微语列表
     * @param $tw_num 分页数
     * @param $where 显示条件
     * @return mixed
     */	
	public function index()
    {	
		$tw_num = config("config.tw_num");	
		if(IS_POST){
			$this->jump(Core::loadModel("Twitter")->saveTwr($this->param));
		}
    	return $this->fetch("index",[
          "list"=>Core::loadModel("Twitter")->getTwitterList([
                "field"     =>"__TWITTER__.*,u.nickname,u.username,u.email", 
                "order"     =>"__TWITTER__.date DESC", 
                "paginate"  =>["rows"=>$tw_num],
                "join"      =>["join"=>["__USER__ u"],"condition"=>["u.id=__TWITTER__.author"],"type"=>["left","left"]],
            ]),
			"has_name"=>Cookie::get("reply_name"),
			$this->assign(['title' =>Core::loadAction("Setting/getSetting",["column"=>"tw_name"])]),
        ]);
    }
	

	
		/**
     * 点赞
     * @param $id 文章ID
     * @return mixed
     */
	public function favorite(){
    	$id=isset($this->param['id'])?$this->param['id']:0;
    	if(!Cookie::has("favorite_tw_$id")){
    		Core::loadModel("Twitter")->where(["id"=>$this->param['id']])->setInc("likes");
    		Cookie::set("favorite_tw_$id",$id,3600*365);
    	}else{
			Core::loadModel("Twitter")->where(["id"=>$this->param['id']])->setDec("likes");
			Cookie::delete("favorite_tw_$id");
		}
    	$list=Core::loadModel("Twitter")->getTwitterList(["where"=>["id"=>$id],"field"=>"likes"]);
    	$number=empty($list)?0:$list[0]->likes;
    	return $number;
    }
	

	
}
