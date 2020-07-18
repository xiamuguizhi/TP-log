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

class Tag extends AdminBase
{
   public function index(){
	$where = [];
	if(isset($this->param['act']) && $this->param['act']=="search"){
        $search = $this->param;
        $this->assign('search',$search);      
        !empty($search['keyword']) ? $where['__TAG__.tagname']=['like','%'.$search['keyword'].'%'] : '';
       
      }
      return $this->fetch(':tag/index',[
          'list'=>Core::loadModel($this->name)->getTagsList( 
              [
			    "where"=>$where,
                "order"     =>"__TAG__.id DESC", 
                "paginate"  =>["rows"=>DB_LIST_ROWS,"config"=>["query"=>$this->param]],
              ]
          ),
      ]);
    }

    // 添加
    public function add_edit(){
      if(IS_POST){
        $this->jump(Core::loadModel($this->name)->saveTags($this->param));
      }
      return $this->fetch(':tag/add_edit',[
        "list"=>isset($this->param['id'])?Core::loadModel($this->name)->getTagsList(["where"  =>["id"=>$this->param['id']]]):null,
      ]);
    }
	
    //删除 
    public function del(){
      $this->jump(Core::loadModel($this->name)->delTags(['id'=>$this->param['id']]));
    }
	


}
