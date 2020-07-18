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
use \think\Request;
use app\common\model\AddonBase;
use tpfcore\helpers\StringHelper;
use tpfcore\Core;

class Comment extends AdminBase
{
	#列表
    public function index(){
		$where = array();
		if(isset($this->param['act']) && $this->param['act']=="search"){  
		$search = $this->param;
		$this->assign('search',$search);
        if($search['hide']==""){
		$where = array();
		}else{
        $where = ['__COMMENT__.display'=>$search['hide']];
		}
		}else{
		$search['hide'] = "";
		$this->assign('search',$search);	
		}
      	return $this->fetch(':comment/index',[
          "list"=>Core::loadModel($this->name)->getPostsComments([
				"where"     => $where, 
          		"order"=>"__COMMENT__.id DESC",
          		'paginate'=>['rows' => 10],
          		'field'=>"__COMMENT__.*,__BLOG__.title,__BLOG__.channel,__BLOG__.alias",
          		'join'=>[
					"join"=>"__BLOG__",
					"condition"=>"__BLOG__.id = __COMMENT__.gid",
					"type"=>"left"
				],
          ])
      	]);
    }
	
	#待审核
	public function verify(){
      	return $this->fetch(':comment/verify',[
          "list"=>Core::loadModel($this->name)->getPostsComments([
				"where"     => "__COMMENT__.display = 0", 
          		"order"=>"__COMMENT__.id DESC",
          		'paginate'=>['rows' => 10],
          		'field'=>"__COMMENT__.*,__BLOG__.title",
          		'join'=>[
					"join"=>"__BLOG__",
					"condition"=>"__BLOG__.id = __COMMENT__.gid",
					"type"=>"left"
				],
          ])
      	]);
    }
	
	#添加/编辑
    public function edit(){
      if(IS_POST){
        $this->jump(Core::loadModel($this->name)->saveReply($this->param));
      }
      return $this->fetch(':comment/edit',[
        'list'=>isset($this->param['id'])?Core::loadModel($this->name)->getCommentById($this->param):null,
      ]);
    }
	
	#回复
	public function reply()
    {   
		if(IS_POST){
        $this->jump(Core::loadModel("Comment")->add($this->param));
		}

		return $this->fetch(":comment/reply",[			
            'list'=>Core::loadModel($this->name)->getCommentById($this->param),
			'poster'=>Core::loadModel("Member")->getInfo(['id'=>\think\Session::get("backend_author_sign")['userid']]),
       ]);
    }
	

	
	#审核
    public function check(){
      $this->jump(Core::loadModel($this->name)->checkComments($this->param));
    }
	
	#删除
    public function del(){
        $this->jump(Core::loadModel($this->name)->delPostsComments(['id'=>$this->param['id']]));
    }
	
		
	#删除IP
    public function delIP(){
        $this->jump(Core::loadModel($this->name)->delCommentByIp(['ip'=>$this->param['ip']]));
    }
}
