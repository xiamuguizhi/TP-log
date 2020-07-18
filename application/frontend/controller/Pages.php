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
use \think\Request;
use \tpfcore\Core;
use \think\Cookie;

class Pages extends FrontendBase
{
    /**
     * 显示页面内容
     */	
	public function show()
    {
			$alias = isset($this->param['alias'])?$this->param['alias']:0;
			$id = Core::loadModel("Blog")->where(["alias"=>$alias])->value('id');
			$title = Core::loadModel("Blog")->where(["alias"=>$alias])->value('title');
			if(empty($title)){
			return $this->fetch('404/404');
			}else{
			$this->assign(['title' =>$title]);
			}
			$theme = Core::loadModel("Blog")->where(["alias"=>$alias])->value('template');			
			if(empty($id)){
			return $this->fetch('404/404');
			}else{
			$list=Core::loadModel("Blog")->getPostsList([
        			"where"=>['__BLOG__.id'=>$id,'channel'=>'page','hide'=>'n'],
        			'field'=>"__BLOG__.id,__BLOG__.title,__BLOG__.alias,__BLOG__.content,__BLOG__.author,__BLOG__.iscomment,__BLOG__.datetime,__BLOG__.likes,__BLOG__.comnum,__BLOG__.view,u.nickname,u.username,u.email,u.url,u.signature",
        			"join"      =>["join"=>["__USER__ u"],"condition"=>["u.id=__BLOG__.author"],"type"=>["left","left"]],
					]
        		);
    		if(empty($list)){
				return $this->fetch('404/404');
    		}
    		Core::loadModel("Blog")->where(["id"=>$id])->setInc("view");			
			$template = preg_replace("/page_/","",$theme);   
			$template = $template?$template:'page';
			return $this->fetch($template,[
        		"list"=>$list,
                "comments"=>Core::loadModel("Comment")->getBlogComments($this->param),
				"has_favorite"=>Cookie::has("favorite_digg_".$id)?1:0,
				"ccheck"=>Cookie::has("ccheck_".$id)?1:0,
				$this->assign('gid',$id),
				
      		]);
    }
}



}