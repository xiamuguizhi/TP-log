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
namespace app\frontend\logic;
use \think\Request;
use \tpfcore\util\Tree;
use \tpfcore\util\Data;
use \tpfcore\Core;

class Tag extends FrontendBase
{
	/**
	 * 获取标签
	 */
   	public function getTagList($data){
		return self::getList($data);
	}
	
	/**
	 * 获取指定文章标签
	 */
    public function getBloTags($data){
		$blogId = Core::loadModel("Blog")->where(["alias"=>$data['alias']])->value('id');
		$tags = array();
        $tag_ids = $this->getTagIdsFromBlogId($blogId);		
        $tag_names = $this->getNamesFromIds($tag_ids);       
        foreach ($tag_names as $key => $value)
        {
            $row = array();
            $row['tagname'] = htmlspecialchars($value);
			$row['id'] = intval($key);
            $tags[] = $row;
        }
		//print_r($tags);
        return $tags;
		//$tags = Core::loadModel($this->name)->where('find_in_set(:gid,gid)',['gid'=>$id])->select();
		//return $tags;
	 }
	 
	 /**
     * 获取标签
     *
     * @param int $blogId
     * @return array
     */
    public function getTag($blogId = NULL) {
        $tags = array();
        $tag_ids = $this->getTagIdsFromBlogId($blogId);		
        $tag_names = $this->getNamesFromIds($tag_ids);       
        foreach ($tag_names as $key => $value)
        {
            $row = array();
            $row['tagname'] = htmlspecialchars($value);
			 $row['tid'] = intval($key);
            $tags[] = $row;
        }
		//print_r($tags);
        return $tags;
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
	
	 /**
     * 从一堆标签ID查找一堆标签名
     * @param array $tagIds 标签ID
     * @return array
     */
    public function getNamesFromIds($tagIds = NULL) {
        $names = array();
        if (!empty($tagIds)) {
			$where['id']=["in",implode(",", $tagIds)];
			$result  = Core::loadModel("Tag")->where($where)->select();
            if ($result) {
                foreach ($result as $value)
                {
                    $names[$value['id']] = $value['tagname'];
                }
            }
        }
        return $names;
    }	
	
	 
	 
}