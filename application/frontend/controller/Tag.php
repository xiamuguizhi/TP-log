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

class Tag extends FrontendBase
{
 
	public function index()
    {
		
		$post_num = config("config.post_num");
		$where = ['channel'=>'blog','hide'=>'n'];
		
		if(empty($this->param['tid'])){
			//$this->jump([RESULT_ERROR,"你访问的文章不存在或被删除",null]);
    		return $this->fetch(":404/404",["title" => "错误提示"]);
    	}
		//标签
		if(isset($this->param['tid'])){
			    $tagname = $this->param['tid'];
				//print_r($tagname );
				$id = Core::loadModel("Tag")->where(["tagname"=>$tagname])->value('id');
	    		$this->assign('tagname',$tagname);
				$where[] = ['exp', "instr(CONCAT( ',', tag, ',' ),  ',".$id.",' )" ];
	    }
		//列表
    	return $this->fetch(":public/result",[
          "list"=>Core::loadModel("Blog")->getPostsList([
                "where"     =>$where, 
                "field"     =>"__BLOG__.*,u.nickname,u.username,u.email,c.title ctitle", 
                "order"     =>"__BLOG__.istop desc,__BLOG__.datetime DESC", 
                "paginate"  =>["rows"=>$post_num],
                "join"      =>["join"=>["__SORT__ c","__USER__ u"],"condition"=>["c.id=__BLOG__.cateid","u.id=__BLOG__.author"],"type"=>["left","left"]],
            ]),
		$this->assign(['title' =>$tagname]),
        ]);
    }
}
