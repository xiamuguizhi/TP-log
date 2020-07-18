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
use \think\Request;
use \tpfcore\util\Config;

class Twitter extends AdminBase
{
	#获取列表
	public function getTwitter($where = []){
	    return self::getList($where);
	}
	
	#保存/更新
	public function saveTwitter($data=[]){
		$validate=\think\Loader::validate($this->name);
        $validate_result = $validate->scene('add')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $validate->getError(), null];
        }
		
		if(is_array($data['img'])){			
		$data['img'] = implode(",",$data['img']);
		}
		$last_id=Core::loadModel($this->name)->saveObject($data);
		if($last_id){
        	return [RESULT_SUCCESS, '操作成功', url("Twitter/index")];
        }else{
        	return [RESULT_ERROR, '操作失败', null];
        }
	}
	
	#删除微语
	public function delTwitter($data){
		 $where = ['id'=>$data['id']];	
		 $img = Core::loadModel($this->name)->where($where)->value('img');
		 if($img) {
         $imgArray = explode(',', $img);
		 foreach ($imgArray as $vo)
			{		
			unlink('./'.$vo);
			}
		 }
		 Core::loadModel("Reply")->where('tid',$data['id'])->delete();		 
		 return Core::loadModel($this->name)->destroy($data,true)?[RESULT_SUCCESS,'删除成功',url('Twitter/index')]:[RESULT_ERROR,'删除失败',url('Twitter/index')];
	}
	
	#删除回复
	public function delReply($data){
		$tid = Core::loadModel("Reply")->where(["id"=>$data['id']])->value('tid');			
		Core::loadModel("Reply")->destroy($data,true);		
		$replynum = Core::loadModel("Reply")->getStatistics(["tid"=>$tid]);		
		$update['replynum']=$replynum;	
		Core::loadModel($this->name)->where(["id"=>$tid])->update($update);
		return [RESULT_SUCCESS,'删除成功',url('Twitter/index')];		
	}
	

	#待定批量删除
	public function delTwitters($data){
		if(is_array($data['id'])){
			$data['id'] = ['in',implode(',',$data['id'])];//批量删除
		}
		return Core::loadModel($this->name)->destroy($data,true)?[RESULT_SUCCESS,'删除成功',url('Twitter/index')]:[RESULT_ERROR,'删除失败',url('Twitter/index')];
	}
	
	#图片循环
	public function getImg($tid){
		$result = array();
		$result = Core::loadModel($this->name)->where(["id"=>$tid])->value('img');
		if(!$result) return null;
		$result=explode(",", $result);
		return $result;
	 }
	
	#获取回复
	public function getReply($tid){
	$where = ['tid'=>$tid];
	$result = Core::loadModel("Reply")->where($where)->order('date DESC')->select();
	return $result?$result:"";
	}
	
	#回复
	public function saveTwr($data){
		
		$id = \think\Session::get("backend_author_sign")['userid'];
		$info = Core::loadModel("User")->where('id',$id)->find();
		if ($data['content'] == ''){ 
			return [RESULT_ERROR,"评论内容不能为空",null];
		} elseif (!preg_match('/[\x{4e00}-\x{9fa5}]/iu', $data['content'])){
		   return [RESULT_ERROR,"内容必须包含中文",null];
        } elseif (mb_strlen($data['content']) > 100) {
			return [RESULT_ERROR,"内容过长,只允许100以内的字",null];	
		}
		
        $date['tid'] = $data['tid'];
		$date['name'] = $info['nickname']?$info['nickname']:$info['username'];
		if ($data['poster'] != 0 || $data['poster'] != '') {			
            $content = '@' . addslashes($data['poster']) . '：' . $data['content'];
			$date['content'] = $content;
        }else{
		$date['content'] = $data['content'];
		}
		
		$date['ip'] = Request::instance()->ip();
		
		//$isPost = Core::loadModel("Reply")->where(array('ip' =>Request::instance()->ip(), 'date' => array('gt', time() - 60)))->value('id');        
		//if ($isPost){
			//return [RESULT_ERROR,"您评论的太快了,评论间隔必须大于60秒！",null];
        //}
		
        $result=Core::loadModel("Reply")->addObject($date);
		$where = ['tid'=>$data['tid']];	
		$count = Core::loadModel("Reply")->where($where)->count('tid');
		$date = ['replynum'=>$count];
		Core::loadModel($this->name)->where(["id"=>$data['tid']])->update($date);
		return [RESULT_SUCCESS, '发表成功', url('Twitter/index')];
     } 


	#删除
	public function delPic($data){
		 if(file_exists('./'.$data['pic'])){
			  unlink('./'.$data['pic']);
		  }
	}
	
}