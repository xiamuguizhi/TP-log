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
namespace app\backend\logic;
use \tpfcore\Core;

class Tag extends AdminBase
{
	
	
	public function saveTags($data){
        $validate=\think\Loader::validate($this->name);
        $scene=isset($data['id'])?"edit":"add";
        $validate_result = $validate->scene($scene)->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $validate->getError(), null];
        }

		$last_id=Core::loadModel($this->name)->saveObject($data);
		if($last_id){
        	return [RESULT_SUCCESS, '操作成功', url('tag/index')];
        }else{
        	return [RESULT_ERROR, '操作失败', url('tag/index')];
        }
	}
	
	
	
    public function getTagsList($data){
		return self::getList($data);
	}
	
	public function getTags($gid){
		$Tag_Model = Core::loadModel($this->name)->where('find_in_set(:gid,gid)',['gid'=>$gid])->select();
		$tags = array();
		foreach ($Tag_Model as $val) {
		$tags[] = $val['tagname'];
		}
	    $tagStr = implode(',', $tags);
		return  $tagStr;
	 }
	
	public function getTagsByid($data){
		$where = ['tagname'=>$data];
		$result = Core::loadModel($this->name)->where($where)->find();
        if (!$result) {
            return FALSE;
        }
        return $result;
	}
	

	
   
   public function delTags($data) {
		$tagId = $data['id'];
        // 要删除一个标签，需要先检查哪些文章有引用这个标签，并把这个标签从那些引用中删除
        $linked_blogs = $this->getBlogIdsFromTagId($tagId);

        foreach ($linked_blogs as $blogId) {
            $this->removeTagIdFromBlog($blogId, $tagId);
        }
		$result = Core::loadModel($this->name)->where(['id'=>$tagId])->delete();
		if($result){
        	return [RESULT_SUCCESS, '删除成功', url('tag/index')];
        }else{
        	return [RESULT_ERROR, '删除失败', url('tag/index')];
        }
    }
   
   
       /**
     * 从TagId获取到BlogId列表 (获取到一个Tag下所有的文章)
     * @param int $tagId 标签ID
     * @return array 文章ID列表
     */
    public function getBlogIdsFromTagId($tagId) {
        $blogs = array();
		$result = Core::loadModel($this->name)->where(['id'=>$tagId])->find();
        if ($result) {
            if ( ! empty($result['gid']))
            {
                $blogs = explode(',', $result['gid']);
            }
        }
        return $blogs;
    }	
	
	
		/**
     * 从TagMap表里的gid删除掉一个标签引用
     * @param int $blogId 
     * @param int $tagId 
     */
    public function removeTagIdFromBlog($blogId, $tagId) {
        $tags = $this->getTagIdsFromBlogId($blogId);

        if (empty($tags)) {
            return;
        }

        // 如果tagId存在，则构建一个新的不包含这个TagId的Tag数组，并保存到数据库
        if (in_array($tagId, $tags)) {
            $new_tags = array();

            foreach ($tags as $each) {
                if ($each != $tagId) {
                    $new_tags[] = $each;
                }

                $tag_string = implode(',', $new_tags);
				$where =['id'=>$blogId];
				$data = ['tag'=>$tag_string];
			    Core::loadModel("Blog")->where($where)->update($data);
            }
        }
    }
   
	 /**
     * 从BlogId获取到TagId列表 (获取到文章所使用的Tag列表)
     * @param int $blogId 文章ID
     * @return array 标签ID列表
     */
   public  function getTagIdsFromBlogId($blogId = NULL) {
        $tags = array();
		$result  = Core::loadModel("Blog")->where(['id'=>$blogId])->find();
            if (!empty($result['tag']))
            {
                $tags = explode(',', $result['tag']);
            }
        return $tags;
    }
}
