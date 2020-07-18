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
use tpfcore\Core;

class Link extends AdminBase
{
	
	#列表
   public function index(){
		$where=[];
		
		#搜索
		if(isset($this->param['act']) && $this->param['act']=="search"){
        $search = $this->param;
        $this->assign('search',$search);       
        !empty($search['keyword']) ? $where['__LINK__.sitename']=['like','%'.$search['keyword'].'%'] : '';      
		}
		
		#列表
		return $this->fetch(':link/index',[
          'list'=>Core::loadModel($this->name)->getFriendLink( 
              [
			    "where"=>$where,
                "field"     =>"__LINK__.*,c.name cname", 
                "order"     =>"__LINK__.datetime desc", 
                "paginate"  =>["rows"=>DB_LIST_ROWS,"config"=>["query"=>$this->param]],
                "join"      =>['join' => "__SORTLINK__ c", 'condition' => "__LINK__.linksortid=c.id",'type' => 'left'],
              ]
          ),
      ]);
    }

   #添加
    public function add_edit(){
      if(IS_POST){
        $this->jump(Core::loadModel($this->name)->saveLink($this->param));
      }
      return $this->fetch(':link/add_edit',[
        "list"=>isset($this->param['id'])?Core::loadModel($this->name)->getFriendLink(["where"  =>["id"=>$this->param['id']]]):null,
		 "categorys"=>Core::loadModel("Sortlink")->getSortlink()
      ]);
    }
	
    #删除 
    public function del(){
      $this->jump(Core::loadModel($this->name)->delLink(['id'=>$this->param['id']]));
    }
	
	
	#审核
    public function check(){
      $this->jump(Core::loadModel($this->name)->checkLink($this->param));
    }
}
