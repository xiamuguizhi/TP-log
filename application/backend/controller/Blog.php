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
use \tpfcore\util\Config;

class Blog extends AdminBase
{
	

	#列表
    public function index(){
	$where = ["channel"=>'blog'];
      if(isset($this->param['act']) && $this->param['act']=="search"){
        $search = $this->param;
        $this->assign('search',$search);
        if($search['cateid']!=0){
          $cIds=Core::loadModel('Sort')->getChildIds((int)$search['cateid']);
          $cIds[]=(int)$search['cateid'];
          $where['cateid']=['in',implode(',',$cIds)];
        }
        !empty($search['keyword']) ? $where['__BLOG__.title']=['like','%'.$search['keyword'].'%'] : '';
        $begin='';$end='';
        !empty($search['start_time']) ? $begin=strtotime($search['start_time']) : '';
        empty($search['end_time']) ? $end=time() : $end=strtotime($search['end_time']);
        if($begin && $end){
          $where['__BLOG__.datetime'] = ['between',"$begin,$end"];
        }
      }
      return $this->fetch('blog/index',[
          'list'=>Core::loadModel($this->name)->getPostsList( 
              [
                "where"     =>$where, 
                "field"     =>"__BLOG__.*,u.nickname,u.username,c.title ctitle", 
                "order"     =>"__BLOG__.istop desc,__BLOG__.isrecommend desc,__BLOG__.datetime DESC", 
                "paginate"  =>["rows"=>15,"config"=>["query"=>$this->param]],
                "join"      =>["join"=>["__SORT__ c","__USER__ u"],"condition"=>["c.id=__BLOG__.cateid","u.id=__BLOG__.author"],"type"=>["left","left"]],
              ]
          ),
          'categorys'=>Core::loadModel("Sort")->getTreeCategory($this->param)
      ]);
    }
	
    #添加
    public function add(){
      if(IS_POST){
        $this->jump(Core::loadModel($this->name)->savePosts($this->param));
      }
      return $this->fetch(':blog/add',[
        'categorys'=>Core::loadModel("Sort")->getTreeCategory($this->param),
        'editor'=>config("config.editor")
      ]);
    }
	
    #编辑
    public function edit(){
        if(IS_POST){
          $this->jump(Core::loadModel($this->name)->savePosts($this->post));
        }
        return $this->fetch(":blog/edit",[
            'categorys'=>Core::loadModel("Sort")->getTreeCategory($this->param),
            'list'=>Core::loadModel($this->name)->getPostsList(["where"=>['id'=>$this->param['cid']]]),
            'tags'=>Core::loadModel("Tag")->getTags($this->param['cid']),	
            'id'=>$this->param['cid'],
            'parentid'=>$this->param['parentid'],
			'editor'=>config("config.editor")
        ]);
    }
	
    #删除 
    public function del(){
      $this->jump(Core::loadModel($this->name)->delPosts(['id'=>$this->param['id']],false));
    }
	
    #审核
    public function check(){
      $this->jump(Core::loadModel($this->name)->checkPosts($this->param));
    }
	
    #置顶
    public function Settop(){
      $this->jump(Core::loadModel($this->name)->SetTop($this->param));
    }
	
    #推荐
    public function setRec(){
      $this->jump(Core::loadModel($this->name)->SetRecommend($this->param));
    }

	
	#删除缩略图
    public function delpic(){
    	$this->jump(Core::loadModel($this->name)->delPic($this->param));
    }
	
	
}
