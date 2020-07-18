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
use \tpfcore\Core;
use \think\Request;
use app\frontend\model\SlideCat;
use app\frontend\model\Slide;
use think\Cookie;


class Index extends FrontendBase
{
	/**
     * 获取文章列表
     * @param $where 显示条件
     * @return mixed
     */	
	public function index()
    {

		$post_num = config("config.post_num");
		$where = ['channel'=>'blog','hide'=>'n'];
		
		//搜索
		if(isset($this->param['act']) && $this->param['act']=="search"){
        $search = $this->param;
        $this->assign('search',$search);
		!empty($search['keyword']) ? $where['__BLOG__.title']=['like','%'.$search['keyword'].'%'] : '';
		}
		
		
		//归档
		if(isset($this->param['record'])){
			    $record = $this->param['record'];
				//print_r($record);
				if (preg_match("/^([\d]{4})([\d]{2})$/", $record, $match)) {
					$days = getMonthDayNum($match[2], $match[1]);
					$record_stime = strtotime($record . '01');
					$record_etime = $record_stime + 3600 * 24 * $days;
				} else {
					$record_stime = strtotime($record);
					$record_etime = $record_stime + 3600 * 24;
				}
			    $where = ['hide' => 'n' ,'channel' => 'blog' ,"datetime"=>["BETWEEN","$record_stime,$record_etime"]];	
	    }
		
		//作者
		if(isset($this->param['uid'])){
			 $uid = $this->param['uid'];
			 $nickname = Core::loadModel("User")->where(["id"=>$uid])->value('nickname');
	    	 $this->assign('nickname',$nickname);
			 $where['author'] =  $uid;
	    }

		//列表
    	return $this->fetch("index",[
          "list"=>Core::loadModel("Blog")->getPostsList([
                "where"     =>$where, 
                "field"     =>"__BLOG__.*,u.nickname,u.username,u.email,c.title ctitle", 
                "order"     =>"__BLOG__.istop desc,__BLOG__.datetime DESC", 
                "paginate"  =>["rows"=>$post_num],
                "join"      =>["join"=>["__SORT__ c","__USER__ u"],"condition"=>["c.id=__BLOG__.cateid","u.id=__BLOG__.author"],"type"=>["left","left"]],
            ]),
		$this->assign(['title' => '首页']),
        ]);
    }
	
	
	/**
     * 试试手气？
     * @param 随机获取文章
     * @return mixed
     */
	public function random(){
			$id = "id";
			$where = ['channel'=>'blog','hide'=>'n'];
            $countcus = Core::loadModel("Blog")->where($where)->field($id)->select();
            $con = '';
            foreach($countcus as $v=>$val){
                $con.= $val[$id].'|';
            }
            $array = explode("|",$con);
            $data = [];
            foreach ($array as $v){
                if (!empty($v)){
                    $data[$v]=$v;
                };
            }
            $a=array_rand($data,1) ;
            $alias = Core::loadModel("Blog")->where($id,'in',$a)->value('alias');
			$type = empty(config('default_return_type'))?'':'.'.config('default_return_type');
			$url ="/posts/{$alias}{$type}";
			return  $this->redirect($url);
    }
	
	/**
     * 查看加密文章
     */
	public function check(){
       if(IS_POST){
        $this->jump(Core::loadModel("Blog")->add($this->param));
		}
    }
	
	/**
     * 退出
     */
	public function loginout(){
		$this->jump(Core::loadModel("User")->logout());
	}
	
    public function changlang(){
       Cookie::set("think_var",$this->param['lang']);
    }
}
