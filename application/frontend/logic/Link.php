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
use \tpfcore\util\MailHandle;

class Link extends FrontendBase
{
	/**
	*  友情链接列表
	*/
	public function getFriendLink($data){
		return self::getList($data);
	}
	
	
	 //邮件通知  
	 private  function send($data){
		$site_url = Core::loadAction("Setting/getSetting",['column'=>"site_host"]);
		$site_name = Core::loadAction("Setting/getSetting",['column'=>"site_name"]);
		$tomail = config("config.toemail");
		$send_scene = 5;
	
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
		$data['site_name'] = $site_name;
		$data['site_url'] = $site_url;
		foreach ($data as $k => $v) {
            $content = str_replace('${' . $k . '}', $v, $content);
		}		
		$queue['mail_to']=$tomail;
		$queue['mail_name']= $data['site_name'];
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
               return [RESULT_ERROR,"发送成功",null];
            } else {
				Core::loadModel("MailQueue")->save($queue);
            }	
			
	  }
	
	
	/**
	*  申请友情链接
	*/
	public function add($data){
        $validate=\think\Loader::validate($this->name);
        $validate_result = $validate->scene("add")->check($data);
		
        if (!$validate_result) {
            return [RESULT_ERROR, $validate->getError(), null];
        }	
		
		if(empty($data['check'])){
			return [RESULT_ERROR,"未勾选-我不是机器人",null];
		}
		$links_push = config("config.links_push");
		if($links_push == '1'){								
		$this->send($data);
		}
		$result=Core::loadModel($this->name)->saveObject($data);
		
		if($result){
        	return [RESULT_SUCCESS, '申请成功，等待管理员审核通过',null];
        }else{
        	return [RESULT_ERROR, '申请失败',null];
        }
	}
}