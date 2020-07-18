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

class Posts extends FrontendBase
{
	
	/**
     * 文章详情
     * @param $id 文章ID
     * @return mixed
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
			if(empty($id)){
			return $this->fetch('404/404');
			}else{
			
			$list=Core::loadModel("Blog")->getPostsList([
        			"where"=>['__BLOG__.id'=>$id,'channel'=>'blog','hide'=>'n'],
        			'field'=>"__BLOG__.id,__BLOG__.title,__BLOG__.alias,__BLOG__.content,__BLOG__.author,__BLOG__.iscomment,__BLOG__.datetime,__BLOG__.likes,__BLOG__.comnum,__BLOG__.view,__BLOG__.cateid,__BLOG__.password,c.title ctitle,u.nickname,u.username,u.email,u.url,u.signature",
        			"join"      =>["join"=>["__SORT__ c","__USER__ u"],"condition"=>["c.id=__BLOG__.cateid","u.id=__BLOG__.author"],"type"=>["left","left"]],
					]
        		);
    		if(empty($list)){
				return $this->fetch('404/404');
    			//$this->jump([RESULT_ERROR,"你访问的文章不存在或被删除",null]);
    		}
			Core::loadModel("Blog")->where(["alias"=>$alias])->setInc("view");
    		return $this->fetch('show',[
        		"list"=>$list,
        		"listPre"=>Core::loadModel("Blog")->getPostsList([
        			"where"=>['__BLOG__.id'=>["<",$id],'channel'=>'blog','hide'=>'n'],
        			"limit"=>1,
        			"order"=>"id desc"
        		]),
        		"listNext"=>Core::loadModel("Blog")->getPostsList([
        			"where"=>['__BLOG__.id'=>[">",$id],'channel'=>'blog','hide'=>'n'],
        			"limit"=>1,
        			"order"=>"id asc"
        		]),
        		"has_favorite"=>Cookie::has("favorite_digg_".$id)?1:0,
				"ccheck"=>Cookie::has("ccheck_".$id)?1:0,
				"password"=>Cookie::get("blog_".$id),
                "comments"=>Core::loadModel("Comment")->getBlogComments($this->param),
				"tags"=>Core::loadModel("Tag")->getBloTags($this->param),
				$this->assign(['gid'=>$id]),				
      		]);
		}
    }
	
	/**
     * 点赞
     * @param $id 文章ID
     * @return mixed
     */
	public function favorite(){
    	$id=isset($this->param['id'])?$this->param['id']:0;
    	if(!Cookie::has("favorite_digg_$id")){
    		Core::loadModel("Blog")->where(["id"=>$this->param['id']])->setInc("likes");
    		Cookie::set("favorite_digg_$id",$id,3600*365);
    	}else{
			Core::loadModel("Blog")->where(["id"=>$this->param['id']])->setDec("likes");
			Cookie::delete("favorite_digg_$id");
		}
    	$list=Core::loadModel("Blog")->getPostsList(["where"=>["id"=>$id],"field"=>"likes"]);
    	$number=empty($list)?0:$list[0]->likes;
    	return $number;
    }
	

	
}
