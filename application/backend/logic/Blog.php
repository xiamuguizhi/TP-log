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
use \think\Config;
use think\Db;

class Blog extends AdminBase
{
		
	#文章
	public function getPostsList($data){
		return self::getList($data);
	}
	
	#页面
	public function getPage($where=[]){
		return self::getList($where);
	}
	
	#保存文章
	public function savePosts($data){
		$validate=\think\Loader::validate($this->name);
		$validate_result = $validate->scene('add')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $validate->getError(), null];
        }			
		$result= Core::loadModel($this->name)->saveObject($data);
		$lastid = Core::loadModel("Blog")->getLastInsID();	
		$gid = $lastid?$lastid:$data['id'];
		
			#如果别名为空就添加ID
			if(empty($data['alias'])){
				$alias=['alias'=>$gid];
				$filter=['id'=>$gid];
				$result= Core::loadModel($this->name)->where($filter)->update($alias);
			}
			
			#标签添加
			if(!empty($data['tag'])){
				$this->addTag($data['tag'],$gid);
			}
			
			#提交到百度收录
			if($data['hide'] == 'n' && empty($data['id']) && config("config.isauto_submit")==1){
			$site_url =\tpfcore\helpers\Json::jsonValueToArray(Core::loadModel("Setting")->getOneObject(['sign'=>'site_options'])->toArray())['options']['site_host'];
			$type = empty(config('default_return_type'))?'':'.'.config('default_return_type');
			$submit_url[] =  trim($site_url,'/')."/posts/{$data['alias']}{$type}";	
			$this->baidu_submit($submit_url);
			}
			if($data['hide'] == 'n' && empty($data['id'])){
			$this->set_sitemap();
			$this->set_feed();
			}
		if($result){
			return [RESULT_SUCCESS, '操作成功', url('blog/index')];
         }else{
        	return [RESULT_ERROR, "操作失败", null];
		} 							
	}
	
	
	/**
     * 提交到百度收录
     * @param string $urls 地址
     * @return 
     */
	private function baidu_submit($urls){
		$site_url = \tpfcore\helpers\Json::jsonValueToArray(Core::loadModel("Setting")->getOneObject(['sign'=>'site_options'])->toArray())['options']['site_host'];
		$site = trim($site_url,'/');
		$token= config("config.token");
		$api = "http://data.zz.baidu.com/urls?site=$site&token=$token";
		$ch = curl_init();
		$options =  array(
		    CURLOPT_URL => $api,
		    CURLOPT_POST => true,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_POSTFIELDS => implode("\n", $urls),
		    CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
		);
		curl_setopt_array($ch, $options);
		$result = curl_exec($ch);
		return $result;
	}
	
	
	/**
     * 生成stiemap
     * @param string $changefreq 频率
     * @return 
     */
	private function set_sitemap(){	
		$site_url = \tpfcore\helpers\Json::jsonValueToArray(Core::loadModel("Setting")->getOneObject(['sign'=>'site_options'])->toArray())['options']['site_host'];
		$changefreq = config("config.changefreq");
		if(empty($site_url)){
			$site_url = request()->domain();
		}
		$site_url = trim($site_url,'/').'/';

		$type = empty(config('default_return_type'))?'':'.'.config('default_return_type');
			
		$sitemap_str  = '<?xml version="1.0" encoding="UTF-8"?>';
		$sitemap_str .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		$sitemap_str .= '<url>';
     	$sitemap_str .= '<loc>'.$site_url.'</loc>';
      	$sitemap_str .= '<lastmod>'.date('Y-m-d').'</lastmod>';
      	$sitemap_str .= '<changefreq>daily</changefreq>';
      	$sitemap_str .= '<priority>1.0</priority>';
   		$sitemap_str .= '</url>';
		$where = ['channel'=>'blog','hide'=>'n'];
   		$data = $this->getPostsList([
                "where"     =>$where, 
				 "field"     =>"__BLOG__.alias,__BLOG__.datetime",
                "order"     =>"__BLOG__.datetime DESC", 
				]);
	   	foreach ($data as $k => $v) {
				$v['url'] = trim($site_url,'/')."/posts/{$v['alias']}{$type}";	
	   			$sitemap_str .= '<url>';
		     	$sitemap_str .= '<loc>'.$v['url'].'</loc>';
		      	$sitemap_str .= '<lastmod>'.date('D, d M Y H:i:s T',$v['datetime']).'</lastmod>';
		      	$sitemap_str .= '<changefreq>'.$changefreq.'</changefreq>';
		      	$sitemap_str .= '<priority>0.8</priority>';
		   		$sitemap_str .= '</url>';
	   		}
		$sitemap_str .= '</urlset>';
		//print_r($sitemap_str);
		return file_put_contents('sitemap.xml', $sitemap_str);
	}
	
	
	
		/**
     * 生成rss订阅
     * @return 
     */
	private function set_feed(){	
		$site_url = \tpfcore\helpers\Json::jsonValueToArray(Core::loadModel("Setting")->getOneObject(['sign'=>'site_options'])->toArray())['options']['site_host'];
		$site_name = \tpfcore\helpers\Json::jsonValueToArray(Core::loadModel("Setting")->getOneObject(['sign'=>'site_options'])->toArray())['options']['site_name'];
		$site_des = \tpfcore\helpers\Json::jsonValueToArray(Core::loadModel("Setting")->getOneObject(['sign'=>'site_options'])->toArray())['options']['site_des'];
		$today_unix_timestamp_start = strtotime(date("Y-m-d")." 00:00:00");
		if(empty($site_url)){
			$site_url = request()->domain();
		}
		$site_url = trim($site_url,'/').'/';

		$type = empty(config('default_return_type'))?'':'.'.config('default_return_type');
			
		$sitemap_str  = '<?xml version="1.0" encoding="UTF-8" ?>';
		$sitemap_str .= '<rss version="2.0">';
		$sitemap_str .= '<channel>';
     	$sitemap_str .= '<title>'.$site_name.'</title>';
      	$sitemap_str .= '<link>'.$site_url.'</link>';
		$sitemap_str .= '<description>'.$site_des.'</description>';		
		$where = ['channel'=>'blog','hide'=>'n',"datetime"=>["gt",$today_unix_timestamp_start]];
   		$data = $this->getPostsList([
                "where"     =>$where, 
				"paginate"  =>["rows"=>15],
				"field"     =>"__BLOG__.title,__BLOG__.alias,__BLOG__.abstract,__BLOG__.datetime",
                "order"     =>"__BLOG__.datetime DESC", 
				]);
	   	foreach ($data as $k => $v) {
				$v['url'] = trim($site_url,'/')."/posts/{$v['alias']}{$type}";	
	   			$sitemap_str .= '<item>';
		     	$sitemap_str .= '<title>'.$v['title'].'</title>';
		     	$sitemap_str .= '<link>'.$v['url'].'</link>';
		      	$sitemap_str .= '<description>'.$v['abstract'].'</description>';
		      	$sitemap_str .= '<pubDate>'.date('D, d M Y H:i:s T',$v['datetime']).'</pubDate>';
		   		$sitemap_str .= '</item>';
	   		}
		$sitemap_str .= '</channel></rss>';
		//print_r($sitemap_str);
		return file_put_contents('feed.xml', $sitemap_str);
	}
	
	
	
	#添加标签
	public function addTag($tagStr, $blogId) {
        $tagStr = trim($tagStr);
        $tagStr = str_replace('，', ',', $tagStr);
        
        if (empty($tagStr)) {
            return;
        }

        // 将标签string切割成标签array，并且去重
        $tagNameArray = explode(',', $tagStr);
        $tagNameArray = array_unique($tagNameArray);

        $tags = array();
        foreach ($tagNameArray as $tagName)  {
            $tagName = trim($tagName);

            if (empty($tagName)) {
                continue;
            }

            // 从标签名获取到标签Id，如果标签不存在，则创建标签
            $tagId = $this->getIdFromName($tagName);
            
            if (!$tagId) {
                $tagId = $this->createTag($tagName, $blogId);
            }

            // 将当前文章Id插入到标签里
            $this->addBlogIntoTag($tagId, $blogId);

            $tags[] = $tagId;
        }

        // 保存当前文章关联的标签Id列表
        $tag_string = implode(',', $tags);
		$where=['id'=>$blogId];
		$data = ['tag'=>$tag_string];
		Core::loadModel("Blog")->where($where)->update($data);
    }
	
	
	
	
	
	
	 /**
     * 从标签名查找标签ID
     * @param string $tagName 标签名
     * @return int|bool 标签ID | FALSE(未找到标签)
     */
   public  function getIdFromName($tagName){
	    $where = ['tagname'=>$tagName];
		$result = Core::loadModel("Tag")->where($where)->find();	
        if (!$result) {
            return FALSE;
        }else{
        return $result['id'];
		}
    }
	
	
	    /**
     * 创建一个新的标签
     * @param string $tagName 标签名
     * @param string $blogId
     * @return int 标签ID
     */
    public function createTag($tagName, $blogId = ''){
        $existTag = $this->getIdFromName($tagName);
        
        if (!$existTag) {
		   $data['tagname'] = $tagName;
		   $data['gid'] = $blogId;
           $existTag = Core::loadModel("Tag")->save($data);
        }
        return $existTag;
    }
	
	
	
	
	    /**
     * 将BlogId插入到Tag表里
     * @param int $tagId 标签ID
     * @param int $blogId 文章ID
     */
	 
	   public  function addBlogIntoTag($tagId, $blogId) {
        $exist_blogs = $this->getBlogIdsFromTagId($tagId);       
        if ( ! in_array($blogId, $exist_blogs)) {
            $exist_blogs[] = $blogId;

            $blog_string = implode(',', $exist_blogs);
			$where =['id'=>$tagId];
			$data = ['gid'=>$blog_string];
			Core::loadModel("Tag")->where($where)->update($data );
        }
    }

	
	
	
	
	    /**
     * 从TagId获取到BlogId列表 (获取到一个Tag下所有的文章)
     * @param int $tagId 标签ID
     * @return array 文章ID列表
     */
    function getBlogIdsFromTagId($tagId) {
        $blogs = array();
		$where =['id'=>$tagId];
		$result = Core::loadModel("Tag")->where($where)->find();

            if ( ! empty($result['gid']))
            {
                $blogs = explode(',', $result['gid']);
            }

        return $blogs;
    }
	
	
	
	
	
	#保存页面
	public function savePages($data){
        $validate=\think\Loader::validate("Page");
        $validate_result = $validate->scene('add')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $validate->getError(), null];
        }
		$data['channel'] = 'page';
		$result= Core::loadModel($this->name)->saveObject($data);
		$lastid = Core::loadModel("Blog")->getLastInsID();	
		if($result){
			#如果别名为空就添加ID
			if(empty($data['alias'])){
				$alias=['alias'=>$lastid];
				$filter=['id'=>$lastid];
				$result= Core::loadModel($this->name)->where($filter)->update($alias);
			}
        	return [RESULT_SUCCESS, '操作成功', url('page/index')];
        }
	}
	
	#删除图片
	public function delPic($data){
		 if(file_exists('./'.$data['pic'])){
			  unlink('./'.$data['pic']);
		  }
	}
	
	#删除文章
	public function delPosts($data){
		if(is_array($data['id'])){
			$data['id'] = ['in',implode(',',$data['id'])];//批量删除
		}
		$this->delComments($data);
		//$this->set_sitemap();
		//$this->set_feed();
		return Core::loadModel($this->name)->deleteObject($data,true)?[RESULT_SUCCESS, '删除成功', url('Blog/index')]:[RESULT_ERROR, '删除失败', url('Blog/index')];
	}
	
	
	#删除评论
    public function delComments($data){		
       	if(is_array($data['id'])){
			$where['gid'] = ['in',implode(',',$data['id'])];//批量删除
		}else{
		$where = ['gid'=>$data['id']];
		}
		Core::loadModel("Comment")->where($where)->delete();
	}
	
	

	#审核
	public function checkPosts($data){
		if(empty($data)){
			return null;
		}
		if(is_array($data['id'])){
			$arr=[];
			foreach ($data['id'] as $k => $v) {
				$arr[$v]['id']=$v;
				$arr[$v]['hide']=$data['ischeck'];
			}
			return self::saveAll($arr)?[RESULT_SUCCESS,'操作成功',url('Blog/index')]:[RESULT_ERROR,'操作失败',url('Blog/index')];
		}
	}
	
	#顶置
	public function SetTop($data){
		if(empty($data)){
			return null;
		}
		if(is_array($data['id'])){
			$arr=[];
			foreach ($data['id'] as $k => $v) {
				$arr[$v]['id']=$v;
				$arr[$v]['istop']=(int)$data['istop'];
			}
			return self::saveAll($arr)?[RESULT_SUCCESS,'操作成功',url('Blog/index')]:[RESULT_ERROR,'操作失败',url('Blog/index')];
		}
	}
	
	#推荐
	public function SetRecommend($data){
		if(empty($data)){
			return null;
		}
		if(is_array($data['id'])){
			$arr=[];
			foreach ($data['id'] as $k => $v) {
				$arr[$v]['id']=$v;
				$arr[$v]['isrecommend']=(int)$data['isrecommend'];
			}
			return self::saveAll($arr)?[RESULT_SUCCESS,'操作成功',url('Blog/index')]:[RESULT_ERROR,'操作失败',url('Blog/index')];
		}
	}
	
	#统计
	public function getCountBlogs($where=[]){
        return self::getStatistics($where);
    }
}