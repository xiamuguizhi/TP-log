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
use \tpfcore\util\Tree;
use \tpfcore\util\Data;
use \tpfcore\Core;
use \think\Cookie;

class Blog extends FrontendBase
{
	
	/**
	*  首页文章列表
	*/
	public function getPosts($where=['channel'=>'blog','hide'=>'n'],$limit=1,$order='datetime DESC'){
		$list = $this->getPostsList([
                "where"     =>$where, 
                "field"     =>"__BLOG__.*,u.nickname,u.username,u.email,c.title ctitle", 
                "order"     =>$order, 
                "paginate"  =>["rows"=>$limit],
                "join"      =>["join"=>["__SORT__ c","__USER__ u"],"condition"=>["c.id=__BLOG__.cateid","u.id=__BLOG__.author"],"type"=>["left","left"]],
            ]);
		return $list;
	}
	

	/**
	*  根据条件文章列表
	*/
	public function getPostsList($where = []){
	    return self::getList($where);
	}
		

	/**
	*  根据$id获取文章详情
	*/
	public function getPostById($id){
		$result=self::getOneObject(["id"=>$id]);
		return $result?$result->toArray():"";
	}	
	

	/**
	*  累计访问数
	*/
	public function updateView($id){
		Core::loadModel("Blog")->where(["id"=>$id])->setInc("view");
	}


	/**
	*  分类统计
	*/
	public function countPost($id){
		$Category=Core::loadModel("Sort",'','logic');
		$Category->ids=[];
		$ids=$Category->getChildIds($id);
		$ids[]=$id;
		return self::getStatistics("cateid in(".implode(",", $ids).")");
	}
	
	
	/**
	 * 文章归档
	 */
	public function record($data){
	    $archive =  self::getList($data);
		$record = 'xxxx_x';
		$p = 0;
		$lognum = 1;
		$record_s = array();
		foreach($archive as $show){    
			$f_record = date('Y_n', $show['datetime']);
			if ($record != $f_record) {
				$h = $p-1;
				if ($h != -1) {
					$record_s[$h]['lognum'] = $lognum;
				}
				$record_s[$p] = array(
					'record' => date('Y年n月', $show['datetime']),
					'datetime' => date('Ym', $show['datetime'])
					);
				$p++;
				$lognum = 1;
			}else {
				$lognum++;
				continue;
			}
			$record = $f_record;
		}
		$j = $p-1;
		if ($j >= 0) {
			$record_s[$j]['lognum'] = $lognum;
		}
		return $record_s;		
	}
	
	
	/**
	*  文章归档
	*/
	public function archive($data){
		$archive = self::getList($data);
		$year=0; $mon=0; $i=0; $j=0;   
		$all = array();   
		$output = '';   
			foreach($archive as $archive){    
                $year_tmp = date('Y',$archive['datetime']);   
                $mon_tmp = date('n',$archive['datetime']);   
                $y=$year; $m=$mon;   
                if ($mon != $mon_tmp && $mon > 0) $output .= '';   
                if ($year != $year_tmp) {   
                    $year = $year_tmp;   
                    $all[$year] = array();   
                }   
                if ($mon != $mon_tmp) {   
                    $mon = $mon_tmp;   
                    array_push($all[$year], $mon);   
                    $output .= "<h2>$year-$mon</h2>";   
                } 
				$output .= '<table><tbody>';   
                $output .= '<tr><td width="80" style="text-align:right;">'.date('m-d',$archive['datetime']).'</td><td><a href='. url('/Posts/'. $archive['alias']).' rel="external" >'.$archive['title'].'</a><i class="hidden-if-min">围观'.$archive['view'].' / '.$archive['comnum'].'条留言</i></td></tr>';   
				$output .= '</tbody></table>';   
			}
            echo $output; 
	}
	
	
	public function add($data){
		if(empty($data['password'])){
        	return [RESULT_ERROR, '口令不能为空',null];
        }
		$result = self::getOneObject(["id"=>$data['id']]);
		if($result['password'] !== $data['password']){
        	return [RESULT_ERROR, '口令错误',null];
        }else{
		$id = $data['id'];
		if(!Cookie::has("blog_$id")){
		Cookie::set("blog_$id",$id*$data['password'],time()+15 * 60 * 1000);
		return [RESULT_SUCCESS,"口令正确",null];
		}	
		}
	}
	
	
}