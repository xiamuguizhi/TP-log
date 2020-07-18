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

class Sort extends FrontendBase
{
 
	public function index()
    {
		
		$post_num = config("config.post_num");
		$where = ['channel'=>'blog','hide'=>'n'];
		
		if(empty($this->param['cid'])){
			//$this->jump([RESULT_ERROR,"你访问的文章不存在或被删除",null]);
    		return $this->fetch(":404/404",["title" => "错误提示"]);
    	}
		//分类
		if(isset($this->param['cid'])){
			    $sort = Core::loadModel("Sort")->getCategoryId($this->param['cid']);
	    		$this->assign('sort',$sort);
	    		$ids=Core::loadModel("Sort")->getCategoryIds($this->param['cid']);
	    		$ids[]=$this->param['cid'];
	    		$where['cateid']=["in",implode(",", $ids)];
	    }
		//列表
    	return $this->fetch(":public/result",[
          "list"=>Core::loadModel("Blog")->getPostsList([
                "where"     =>$where, 
                "field"     =>"__BLOG__.*,u.nickname,u.username,u.email,c.title ctitle", 
                "order"     =>"__BLOG__.datetime DESC", 
                "paginate"  =>["rows"=>$post_num],
                "join"      =>["join"=>["__SORT__ c","__USER__ u"],"condition"=>["c.id=__BLOG__.cateid","u.id=__BLOG__.author"],"type"=>["left","left"]],
            ]),
		$this->assign(['title' =>$sort['title']]),
        ]);
    }
}
