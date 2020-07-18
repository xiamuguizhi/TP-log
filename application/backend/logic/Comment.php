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
use app\common\logic\LogicBase;
use tpfcore\Core;
use think\Request;
use \tpfcore\util\MailHandle;

class Comment extends AdminBase
{


	#获取列表
    public function getAllPostsComments($data){
        $comments=$this->getPostsCommentsChilds(0,$data);
        return $comments;
    }
	
    
	#获取单个评论
	public function getCommentById($data){
		$result=self::getOneObject(["id"=>$data['id']]);
		return $result?$result->toArray():"";
	}
	
	
	#保存留言
	public function saveReply($data){
		$validate=\think\Loader::validate($this->name);
        $validate_result = $validate->scene('add')->check($data);		
        if (!$validate_result) {
            return [RESULT_ERROR, $validate->getError(), null];
        }
		$result=Core::loadModel($this->name)->saveObject($data);
		if($result){
        	return [RESULT_SUCCESS,"操作成功",url('comment/index')];
        }else{
        	return [RESULT_ERROR,"操作失败",null];
        }
		
	}
	
	#回复留言
	public function add($data){
    	$validate=\think\Loader::validate($this->name);
        $validate_result = $validate->scene('add')->check($data);		
        if (!$validate_result) {
            return [RESULT_ERROR, $validate->getError(), null];
        }		
		$date['gid'] = $data['gid'];
        $date['pid'] = $data['pid'];
		$date['date'] = time();
		$date['poster'] = $data['poster'];
		if ($data['pid'] != 0) {			
            $content = '@' . addslashes($data['author']) . '：' . $data['comment'];
        }
		$date['comment'] = $content;
		$date['mail'] = $data['mail'];
		$date['url'] = $data['url'];
		$date['ip'] = Request::instance()->ip();
		$date['useragent'] = Request::instance()->header('user-agent');
		
		$comment_need_push = config("config.comment_need_push");
		if($comment_need_push == '1'){
		$site_url = \tpfcore\helpers\Json::jsonValueToArray(Core::loadModel("Setting")->getOneObject(['sign'=>'site_options'])->toArray())['options']['site_host'];
		$site_name = \tpfcore\helpers\Json::jsonValueToArray(Core::loadModel("Setting")->getOneObject(['sign'=>'site_options'])->toArray())['options']['site_name'];
		$send_scene = '2';
		$reply = Core::loadModel("Comment")->where(["id"=>$data['pid']])->value('comment');
		$tomail = Core::loadModel("Comment")->where(["id"=>$data['pid']])->value('mail');
		$mail['poster'] = $data['poster'];
		$mail['reply'] = $reply;
		$mail['comment'] = $content;
		//获取模板
		$listMailTemplete = Core::loadModel("SmsTemplete")->getList(['where'=>['send_scene'=>$send_scene,'module'=>'mail']]);			
		$curr_templete=empty($listMailTemplete[0]->send_data)?[]:json_decode($listMailTemplete[0]->send_data,true);
		$subject=$curr_templete['subject'];
		$content=$curr_templete['tpl_content'];
		//提取发送邮件内容
		if(empty($site_url)){
			$site_url = request()->domain();
		}
		$site_url = trim($site_url,'/').'/';
		$alias = Core::loadModel("Blog")->where(["id"=>$data['gid']])->value('alias');
		$channel = Core::loadModel("Blog")->where(["id"=>$data['gid']])->value('channel');
		$title = Core::loadModel("Blog")->where(["id"=>$data['gid']])->value('title');
		$type = empty(config('default_return_type'))?'':'.'.config('default_return_type');
		if($channel == "blog"){
		$mail['url']	= trim($site_url,'/')."/posts/{$alias}{$type}";	
		}else{
		$mail['url']	= trim($site_url,'/')."/pages/{$alias}{$type}";	
		}
		$mail['author'] = $data['author'];	
		$mail['title'] = $title;	
		$mail['site_name'] = $site_name;
		$mail['site_url'] = $site_url;
		foreach ($mail as $k => $v) {
            $content = str_replace('${' . $k . '}', $v, $content);
		}
		$queue['mail_to']=$tomail;
		$queue['mail_name']= $data['author']?$data['author']:$data['site_name'];
		$queue['mail_subject']=$subject;
		$queue['mail_body']= $content;
		$queue['priority']= 1;
		$queue['add_time']= time();
		$queue['lock_expiry']= time();
		$host=config("config.host");
		$port=config("config.port");
		$username=config("config.username");
		$password=config("config.password");
		$sendtype=config("config.sendtype");
        $mailer=new MailHandle($host,$port,$username,$password,$site_name,$sendtype);
		if ($mailer->sendMail($queue['mail_to'],$queue['mail_name'], $queue['mail_subject'], $queue['mail_body'])) {
			//return [RESULT_ERROR,"发送成功",null];
        } else {
			Core::loadModel("MailQueue")->save($queue);
        }
		}
		
		$result = Core::loadModel($this->name)->addObject($date);	
		
		Core::loadModel("Blog")->where(["id"=>$data['gid']])->setInc("comnum");
		
        //print_r($queue);
		if($result){
        	return [RESULT_SUCCESS,"回复成功",url('comment/index')];
        }else{
        	return [RESULT_ERROR,"回复失败",null];
        }
    }
	
	

	#详细
    public function getPostsComments($where=[]){
        return self::getList($where);
    }

	
	#删除评论
    public function delPostsComments($data){
       	if(is_array($data['id'])){
			$data['id'] = ['in',implode(',',$data['id'])];
			$where['pid'] = ['in',implode(',',$data['id'])];
		}else{
			$where=['pid'=>$data['id']];
		}
		
		$row = Core::loadModel($this->name)->where($data)->select();
		foreach ($row as  $v) {
				$blogIds[] = $v['gid'];
		}
		$blogId = $blogIds;		
		$Children = Core::loadModel($this->name)->where($where)->find();
		if($Children){
		$this->delChildrenComment($data);
		}
		$result=Core::loadModel($this->name)->deleteObject($data,true);
		$this->updateCommentNum($blogId);
		//if($result){			
			return [RESULT_SUCCESS,'删除成功',url('Comment/index')];
		// }else{
			//return [RESULT_ERROR,'删除失败',url('Comment/index')];
		//}	
	}
	
	#删除子评论
	public function delChildrenComment($data) {
		if(is_array($data['id'])){
			$data['pid'] = ['in',implode(',',$data['id'])];
		}
		Core::loadModel($this->name)->deleteObject($data,true);				
	}
	
	#删除所有这个ip的评论
	public function delCommentByIp($ip) {
        $blogids = array();
        $row = Core::loadModel($this->name)->where(['ip'=>$ip])->select();
        foreach ($row as  $v) {
				$blogIds[] = $v['gid'];
		}
		$blogId = $blogIds;
		$result=Core::loadModel($this->name)->where(['ip'=>$ip])->delete();
		$this->updateCommentNum($blogids);
	}
	
	
	#更新评论数量
    public function updateCommentNum($blogId) {
        if (is_array($blogId)) {
            foreach ($blogId as $val) {
                $this->updateCommentNum($val);
            }
        } else {
			$where = ['gid'=>$blogId,'display'=>1];
            $data['comnum'] = Core::loadModel($this->name)->getStatistics($where);
			Core::loadModel("Blog")->where(["id"=>$blogId])->setField($data);
			//print_r($data);
        }
	}
	
	
	#审核
	public function checkComments($data){
		if(empty($data)){
			return null;
		}
		if(is_array($data['id'])){
			$arr=[];
			foreach ($data['id'] as $k => $v) {
				$arr[$v]['id']=$v;
				$arr[$v]['display']=$data['ischeck'];
			}
			return self::saveAll($arr)?[RESULT_SUCCESS,'操作成功',url('Comment/index')]:[RESULT_ERROR,'操作失败',url('Comment/index')];
		}
	}
	
	
    // 统计评论数，只统计0
    public function getCountComments($where=[]){
        return self::getStatistics($where);
    }
}