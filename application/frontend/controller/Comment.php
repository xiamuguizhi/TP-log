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
use tpfcore\Core;
use think\Cookie;


/**
 * 留言控制器
 */

class Comment extends FrontendBase
{
	/**
     * 添加留言
     * @param $this->param 
     * @return mixed
     */
	public function add()
    {
		if(IS_POST){
        $this->jump(Core::loadModel("Comment")->add($this->param));
		}
    }
 
 	/**
     * 点赞
     * @param $id 评论ID
     * @return $number 点赞数量
    */
	public function favorite()
	{
    	$id=isset($this->param['id'])?$this->param['id']:0;
    	if(!Cookie::has("favorite_com_$id")){
    		Core::loadModel($this->name)->where(["id"=>$this->param['id']])->setInc("likes");
    		Cookie::set("favorite_com_$id",$id,3600*365);
    	}else{
			Core::loadModel($this->name)->where(["id"=>$this->param['id']])->setDec("likes");
			Cookie::delete("favorite_com_$id");
		}
    	$list=Core::loadModel($this->name)->getcomList(["where"=>["id"=>$id],"field"=>"likes"]);
    	$number=empty($list)?0:$list[0]->likes;
    	return $number;
    }
	
}
