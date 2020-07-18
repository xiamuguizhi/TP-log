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
namespace app\backend\controller;
use app\common\controller\ControllerBase;
use tpfcore\Core;
use think\Request;
use think\Cookie;

class Twitter extends AdminBase
{
    
	#列表
	public function index(){
		$where = [];
		if(isset($this->param['act']) && $this->param['act']=="search"){
        $search = $this->param;
        $this->assign('search',$search);
        $begin='';$end='';
        !empty($search['start_time']) ? $begin=strtotime($search['start_time']) : '';
        empty($search['end_time']) ? $end=time() : $end=strtotime($search['end_time']);
        if($begin && $end){
          $where['__TWITTER__.date'] = ['between',"$begin,$end"];
        }
      }
	  IS_POST && $this->jump(Core::loadModel($this->name)->saveTwitter($this->param));
      return $this->fetch('index',[
          "list"=>Core::loadModel("Twitter")->getTwitter(
          		[
				"where"			=> $where,
                "field"     =>"__TWITTER__.*,u.nickname,u.username,u.email", 
                "order"     =>"__TWITTER__.date DESC", 
                "paginate"  =>["rows"=>15,"config"=>["query"=>$this->param]],
                "join"      =>['join' => "__USER__ u", 'condition' => "__TWITTER__.author=u.id",'type' => 'left'],
              ]
          )
      	]);
    }
	
	#添加/编辑待定
    public function add_edit(){
    	IS_POST && $this->jump(Core::loadModel($this->name)->saveTwitter($this->param));
    	return $this->fetch('add_edit',[
          "list"=>!empty($this->param['id'])?Core::loadModel("Twitter")->getTwitter([
          		"where"=>['id'=>$this->param['id']]
          ]):[]
      	]);
    }

	#删除微语
    public function del(){
    	$this->jump(Core::loadModel("Twitter")->delTwitter(['id'=>$this->param['id']]));
    }
	
	#删除微语回复
    public function delr(){
    	$this->jump(Core::loadModel("Twitter")->delReply(['id'=>$this->param['id']]));
    }
	
	#微语回复
    public function reply(){
    	$this->jump(Core::loadModel("Twitter")->saveTwr($this->param));
    }
	
	#删除
    public function clear(){
    	$this->jump(Core::loadModel("Twitter")->delPic($this->param));
    }
	
	
	//点赞
	public function favorite(){
    	$id=isset($this->param['id'])?$this->param['id']:0;
    	if(!Cookie::has("favorite_digg_$id")){
    		Core::loadModel("Twitter")->where(["id"=>$this->param['id']])->setInc("likes");
    		Cookie::set("favorite_digg_$id",$id,3600*365);
    	}
    	$list=Core::loadModel("Twitter")->getTwitterList(["where"=>["id"=>$id],"field"=>"likes"]);
    	$number=empty($list)?0:$list[0]->likes;
    	return $number;
    }
	
}