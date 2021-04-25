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
use \tpfcore\helpers\FileHelper;
use \tpfcore\util\Tree;
use \tpfcore\util\Data;
use \think\Cookie;
use \tpfcore\Core;
use \tpfcore\util\MailHandle;

class Comment extends FrontendBase
{

    public function getBlogComments($data){
        $comments=$this->getComments(0,$data);
        return $comments;
    }
	
	/**
     * 获取评论列表
     * @param $comment_num 分页数量
     * @param $alias 文章别名
     * @return mixed
     */
	
    private function getComments($pid,$data){
		$comment_num = config("config.comment_num");		
		$id = Core::loadModel("Blog")->where(["alias"=>$data['alias']])->value('id');
		$comnum = $this->getCountComments(["gid"=>$id,"display"=>1]); 
		
		$page = isset($data['page']) ? intval($data['page']) : 1;
		$comnum= $comnum - ($page - 1)*$comment_num;
        $comments = self::getList(["where"=>["pid"=>0,"gid"=>$id,"display"=>1],"paginate"  =>['rows' =>$comment_num],"order" =>"date DESC"]);
        foreach ($comments as $k => &$v){		
			$v['children'] = $this->getChildComments($v['id']);
			$v['i'] = $comnum--;
			$chilnum = $this->getCountComments(["gid"=>$id,"pid"=>$v['id'],"display"=>1]); 
			$v['k'] = $chilnum; 
        }		
        //print_r($comments);
		return $comments;
    }

    /**
     * 获取父级ID下的所有评论
     * @param $pid 父级ID
     * @return array
     */
    private function getChildComments($pid){
		$comments = self::getList(["where"=>["pid"=>$pid,"display"=>1],"order" =>"date DESC"]);
        $arr = array();
        if($comments){
            foreach ($comments as $k => &$v){
                $arr = $this->getChildComments($v['id']);
            }
        }
        return array_filter(array_merge($comments, $arr));
    }

	/**
     * 获取评论列表
     * @param $where
     * @return mixed
     */
	public function getcomList($where = []){
	    return self::getList($where);
	}
	
	
	public function gethas($id=''){
		$has = Cookie::get("favorite_com_".$id);
        return $has?"done":"";
    }
	
	
	
    public function getBlogCommentsList($limit=1,$where='display=1'){
		$list = $this->getcomList([
                "where"     =>$where, 
                "field"     =>"__COMMENT__.*,b.alias,b.title,.b.channel", 
                "order"     =>"__COMMENT__.date DESC", 
                "paginate"  =>["rows"=>$limit],
                "join"      =>["join"=>["__BLOG__ b"],"condition"=>["b.id=__COMMENT__.gid"],"type"=>["left","left"]],
            ]);
		return $list;
    } 	
	
	
	private  function makePregIP($str){		
        if (strstr($str,"-")) {           
            $aIP = explode(".",$str);           
            foreach ($aIP as $k=>$v) {
                if (!strstr($v,"-")) {
                    $preg_limit .= $this->makePregIP($v);
                    $preg_limit .= ".";
                } else{
                    $aipNum = explode("-",$v);
                    for($i=$aipNum[0];$i<=$aipNum[1];$i++){
                        $preg .=$preg?"|".$i:"[".$i;
                    }
                    $preg_limit .=strrpos($preg_limit,".",1)==(strlen($preg_limit)-1)?$preg."]":".".$preg."]";
                }
            }
        } 
        else {
            $preg_limit = $str;
        }
 
        return $preg_limit;
	}
	
	private  function getAllBlockIP($ips){
        if ($ips){
            $i = 1;
            foreach ($ips as $k=>$v) {
                $ipaddres = $this->makePregIP($v);
                $ip = str_ireplace(".",".",$ipaddres);
                $ip = str_replace("*","[0-9]{1,3}",$ip);
                $ipaddres = "/".$ip."/";
                $ip_list[] = $ipaddres;
                $i++;
            }
        }
        return $ip_list;
    }
	
	
	public function checkIP($Ip) {
		$ips= [config("config.blacklist")];
        $iptable = $this->getAllBlockIP($ips);
        $IsJoined = false;
        if ($iptable) {
            foreach($iptable as $value) {
                if (preg_match("{$value}",$Ip)) {
                    $IsJoined = true;
                    break;
                }
            }
        }
        if( !$IsJoined ){
            return false;
        }
        return true;  
    }


	private function sensitive($string){
		$count = 0; //违规词的个数
		$list= [config("config.keywords")];
		$pattern = "/".implode("|",$list)."/i"; //定义正则表达式
		if(preg_match_all($pattern, $string, $matches)){ //匹配到了结果
			$patternList = $matches[0]; //匹配到的数组
			$count = count($patternList);
		}
		if($count==0){
			return false;
		}else{
			return true;  
		}
		return true;  
		}
	
	//检测域名格式  
	private function CheckUrl($C_url){  
    $str="/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/";  
    if (!preg_match($str,$C_url)){  
        return false;  
    }else{  
    return true;  
    }  
	}  
	
	 //邮件通知  
	 private  function send($data){
		$site_url = Core::loadAction("Setting/getSetting",['column'=>"site_host"]);
		$site_name = Core::loadAction("Setting/getSetting",['column'=>"site_name"]);
    	if ($data['pid'] != 0) {	
			$send_scene = '2';
			$reply = Core::loadModel("Comment")->where(["id"=>$data['pid']])->value('comment');
			$tomail = Core::loadModel("Comment")->where(["id"=>$data['pid']])->value('mail');
			$data['reply'] = $reply;
			}else{
			$tomail = config("config.toemail");
			$send_scene = '1';
		}
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
		if($channel == "blog"){
		$data['url']	= $site_url.url('Posts/'.$alias);
		}else{
		$data['url']	= $site_url.url('Pages/'.$alias);
		}
		$data['title'] = $title;	
		$data['site_name'] = $site_name;
		$data['site_url'] = $site_url;
		foreach ($data as $k => $v) {
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
               return [RESULT_ERROR,"发送成功",null];
            } else {
				Core::loadModel("MailQueue")->save($queue);
            }	
			
	  }
	
    public function add($data){
		
		
		/** 自带系统验证 */
		$validate=\think\Loader::validate($this->name);
        $validate_result = $validate->scene("add")->check($data);		
        if (!$validate_result) {
            return [1, $validate->getError(), null];
        }
		/** 检测是否登录 */
		$login = \think\Session::has("backend_author_sign");
		
		if(!$login){
			$user = Core::loadModel("user")->where(["id"=>'1'])->find();
		  if($user['username'] == $data['poster'] || $user['nickname'] == $data['poster'] ){
			return [RESULT_ERROR,"禁止使用管理员昵称",null];
		  }
		 if($user['email'] == $data['mail']){
			return [RESULT_ERROR,"禁止使用管理员邮箱",null];
		  }		  
		}
				
		
		
		$iscomment = Core::loadModel("Blog")->where(["id"=>$data['gid']])->value('iscomment');
		
		/** 评论关闭 */
		if($iscomment != '1'){
			return [RESULT_ERROR,"评论已关闭",null];
		}
		
		  //检测网址
		if(empty($data['check'])){
			return [RESULT_ERROR,"未勾选-我不是机器人",null];
		}		
		
		if(!$login){
		/** 检查ip评论间隔 */
		$interval = config("config.comment_time_interval");
		$isPost = Core::loadModel($this->name)->where(array('ip' =>Request::instance()->ip(), 'date' => array('gt', time() - $interval)))->value('id');		
        if ($isPost){
			return [RESULT_ERROR,"对不起, 您的吐槽过于频繁, 请".$interval."秒再次吐槽",null];
        }
		}
		
		//检测来昵称是否被封
        $name_keywords = [config("config.name_keywords")];
        if(in_array($data['poster'],$name_keywords)){
            return [RESULT_ERROR,"你的昵称被系统屏蔽了",null];
        }
		
		//检测来源ip是否被封
		if( !$this->checkIP(Request::instance()->ip())){
			return [RESULT_ERROR,"您的IP已被系统屏蔽，评论发表失败",null];
		}	
		
		//检查内容必须有中文
		$comment_needchinese = config("config.comment_needchinese");
		if ($comment_needchinese == '1' && !preg_match('/[\x{4e00}-\x{9fa5}]/iu', $data['comment'])) {
		return [RESULT_ERROR,"评论内容需包含中文",null];
		}
		
		//检测是否有敏感词
		if($this->sensitive($data['comment'])){
			return [RESULT_ERROR,"您的内容里有敏感词,请讲文明，树新风，谢谢",null];
		}	
		

	   //检测网址
		if(!empty($data['url']) && !$this->CheckUrl($data['url'])){
			return [RESULT_ERROR,"主页地址不符合规范",null];
		}	
		
		
		 //检测内容长度 mb_strlen必须开启这个函数
		$comment_length = config("config.comment_length");
		if (mb_strlen($data['comment']) > $comment_length) {
				return [RESULT_ERROR,"内容最多不能超过".$comment_length."个字符",null];
		}	
		
		
		
		
		
		$date['gid'] = $data['gid'];
       		$date['pid'] = $data['pid'];
		$date['date'] = time();
		$date['poster'] = $data['poster'];
	    //定义author 楼层回复@ID
	   	 $author=$data['author'];
		if ($data['author'] != 0 || $data['author'] != '') {		
            $content = '@' . addslashes($author ) . '：' . $data['comment'];
			$date['comment'] = $content;
        }else{
		$date['comment'] = $data['comment'];
		}
		
		
		$date['mail'] = $data['mail'];
		$date['url'] = $data['url']?$data['url']:Core::loadAction("Setting/getSetting",['column'=>"site_host"]);
		$date['ip'] = Request::instance()->ip();
		$date['useragent'] = Request::instance()->header('user-agent');
		$date['ip'] = Request::instance()->ip();
		
		//开启审核功能
		$comment_check = config("config.comment_check");
		if($comment_check == '1'){
			$date['display'] = '0';
		}
		
		
		$result=Core::loadModel($this->name)->addObject($date);
		
		Core::loadModel("Blog")->where(["id"=>$data['gid']])->setInc("comnum");
		

		
		
		
		
		
		

		if(!$login){
    	if(!Cookie::has("cposter")){
		Cookie::set("cposter",$data['poster'],time()+3600*24);
		}elseif(Cookie::get('cposter') != $date['poster']){
		Cookie::set("cposter",$data['poster'],time()+3600*24);	
		}
		
		if(!Cookie::has("cmail")){
    	Cookie::set("cmail",$data['mail'],time()+3600*24);
		}elseif(Cookie::get('cmail') != $data['mail']){
		Cookie::set("cmail",$data['mail'],time()+3600*24);	
		}
		
		
		if(!Cookie::has("curl")){
    	Cookie::set("curl",$data['url'],time()+3600*24);
		}elseif(Cookie::get('curl') != $data['url']){
		Cookie::set("curl",$data['url'],time()+3600*24);	
		}
		}
		
		$id = $data['gid'];
		if(!Cookie::has("ccheck_$id")){
		Cookie::set("ccheck_$id",$data['gid'],time()+3600*24);
		}
		
		
		
        
		
		
        if($result){		
			if($comment_check == '1'){
				return [RESULT_SUCCESS,"吐槽成功,等待审核！",null];
			}else{
				$comment_need_push = config("config.comment_need_push");
				if($comment_need_push == '1'){								
				ob_end_clean();
				ob_start();
				$res['code'] = 0;
				$res['msg'] = "吐槽成功";
				echo json_encode($res, JSON_UNESCAPED_UNICODE);
				$size = ob_get_length();
				header("Content-Length: $size");
				header('Connection: close');
				header("HTTP/1.1 200 OK");
				header("Content-Type: application/json;charset=utf-8");
				ob_end_flush();
				if(ob_get_length())
				ob_flush();
				flush();
				if (function_exists("fastcgi_finish_request")) { // yii或yaf默认不会立即输出，加上此句即可（前提是用的fpm）
				fastcgi_finish_request(); // 响应完成, 立即返回到前端,关闭连接
				}
				ignore_user_abort(true);//在关闭连接后，继续运行php脚本
				set_time_limit(0);			
				$this->send($data);
				}else{
				return [RESULT_SUCCESS,"吐槽成功",null];	
				}
			}		
        }else{
        	return [RESULT_ERROR,"吐槽失败",null];
        }
    }
 


    // 统计评论数，只统计0
    public function getCountComments($where=[]){
        return self::getStatistics($where);
    }
}
