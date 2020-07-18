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

class Page extends AdminBase
{
	
	#页面列表
    public function index(){
      $where = ["channel"=>'page'];
      if(isset($this->param['act']) && $this->param['act']=="search"){
        $search = $this->param;
        $this->assign('search',$search);
        !empty($search['keyword']) ? $where['__BLOG__.title']=['like','%'.$search['keyword'].'%'] : '';
        $begin='';$end='';
        !empty($search['start_time']) ? $begin=strtotime($search['start_time']) : '';
        empty($search['end_time']) ? $end=time() : $end=strtotime($search['end_time']);
        if($begin && $end){
          $where['__BLOG__.datetime'] = ['between',"$begin,$end"];
        }
      }
      return $this->fetch('page/index',[
          'list'=>Core::loadModel("Blog")->getPostsList( 
              [
                "where"     =>$where, 
                "field"     =>"__BLOG__.*,u.nickname nickname", 
                "order"     =>"__BLOG__.datetime DESC", 
                "paginate"  =>["rows"=>15,"config"=>["query"=>$this->param]],
                "join"      =>['join' => "__USER__ u", 'condition' => "__BLOG__.author=u.id",'type' => 'left'],
              ]
          ),
      ]);
    }
	

	#添加
    public function add(){
      if(IS_POST){
        $this->jump(Core::loadModel("Blog")->savePages($this->param));
      }
      return $this->fetch(':page/add',[
	  		'editor'=>config("config.editor"),
	  ]);
    }
	
    #编辑
    public function edit(){
        if(IS_POST){
          $this->jump(Core::loadModel("Blog")->savePages($this->post));
        }
        $pageModel=Core::loadModel("Blog");
        $alias_list= $pageModel->getPage(["group"=>['group' => 'alias'],"field"=>"alias"]);
        $list = $pageModel->getPage(["where"=>['id'=>$this->param['cid']]]);
        return $this->fetch(":page/edit",[
            'list'=>$list,
            "alias_list"=>$alias_list,
			'editor'=>config("config.editor"),
        ]);
    }
	
    #删除 
    public function del(){
      $delete_check = config('config.POSTS_RECYCLE_ON')==1?false:true;
      $this->jump(Core::loadModel($this->name)->delPosts(['id'=>$this->param['id']],$delete_check));
    }
	
    #审核
    public function check(){
      $this->jump(Core::loadModel($this->name)->checkPosts($this->param));
    }
   
}
