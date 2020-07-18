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
use \think\Request;
/**
 *  微语逻辑
 */
class Twitter extends FrontendBase
{
	

	public function getTwitterList($where = []){
	    return self::getList($where);
	}


	public function getOneList($where = [], $field = true, $order = '', $is_paginate = true){
		$paginate_data = $is_paginate ? ['rows' => $is_paginate] : false;
		return Core::loadModel($this->name)->getObject($where, $field, $order, $paginate_data);
	}
	
	public function getImg($tid){
		$result = array();
		$result = Core::loadModel($this->name)->where(["id"=>$tid])->value('img');
		if(!$result) return null;
		$result=explode(",", $result);
		return $result;
	 }
	
	public function getReply($tid){
	$where = ['tid'=>$tid,'hide'=>'n'];
	$result = Core::loadModel("Reply")->where($where)->order('date DESC')->select();
	return $result?$result:"";
	}
		
	/**
     * 获取微语点赞cookie
     * @param has_tw
     * @return mixed
     */		
	public function gethas($id=''){
		$has = Cookie::get("favorite_tw_".$id);
        return $has?"done":"";
    }

	public function saveTwr($data){
		
		
		 $tw_reply = config("config.tw_reply");
		
		/** 评论关闭 */
		if($tw_reply != '1'){
			return [RESULT_ERROR,"管理已关闭回复功能",null];
		}
				
		
		
		/** 检测是否登录 */
		$login = \think\Session::has("backend_author_sign");
		
		if(!$login){
			$user = Core::loadModel("user")->where(["id"=>'1'])->find();
		  if($user['username'] == $data['name'] || $user['nickname'] == $data['poster'] ){
			return [RESULT_ERROR,"禁止使用管理员昵称",null];
		  }  
		}
		if ($data['name'] == ''){
		   return [RESULT_ERROR,"昵称不能为空",null];
		} elseif (mb_strlen($data['name']) > 6){
			return [RESULT_ERROR,"昵称只允许6个字以内",null];
        } elseif ($data['content'] == ''){ 
			return [RESULT_ERROR,"回复内容不能为空",null];
		} elseif (!preg_match('/[\x{4e00}-\x{9fa5}]/iu', $data['content'])){
		   return [RESULT_ERROR,"回复内容必须包含中文",null];
        } elseif (mb_strlen($data['content']) > 100) {
			return [RESULT_ERROR,"回复内容过长,只允许100以内的字",null];	
		}
		
		
		  //检测网址
		if(empty($data['check'])){
			return [RESULT_ERROR,"未勾选-我不是机器人",null];
		}
		
        $date['tid'] = $data['tid'];
		$date['date'] = time();
		$date['name'] = $data['name'];
		if ($data['poster'] != 0 || $data['poster'] != '') {			
            $content = '@' . addslashes($data['poster']) . '：' . $data['content'];
			$date['content'] = $content;
        }else{
		$date['content'] = $data['content'];
		}
		$date['ip'] = Request::instance()->ip();
		
		$isPost = Core::loadModel("Reply")->where(array('ip' =>Request::instance()->ip(), 'date' => array('gt', time() - 60)))->value('id');
        if ($isPost){
			return [RESULT_ERROR,"您回复的太快了,回复间隔必须大于60秒！",null];
        }
        $result=Core::loadModel("Reply")->addObject($date);
		
			if($result){
			if(!Cookie::has("reply_name")){
    		Cookie::set("reply_name",$date['name'],3600*365);
			}
			$where = ['tid'=>$data['tid']];	
			$count = Core::loadModel("Reply")->where($where)->count('tid');
			$date = ['replynum'=>$count];
			$result=Core::loadModel($this->name)->where(["id"=>$data['tid']])->update($date);
			
        	return [RESULT_SUCCESS, '发表成功', url('/T')];
        } 
	}
	
}