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
use tpfcore\Core;
/**
 * 模板逻辑
 */
class SmsTemplete extends AdminBase
{
	public function saveSmsTemplete($data){
        $validate=\think\Loader::validate($this->name);
        $scene=isset($data['id'])?"edit":"add";
        //$data['sms_id']=$data['send_data']['send_scene'];
        $validate_result = $validate->scene($scene)->check($data['send_data']);
        if (!$validate_result) {
            return [RESULT_ERROR, $validate->getError(), null];
        }

		$last_id=Core::loadModel($this->name)->saveObject($data);
		if($last_id){
        	return [RESULT_SUCCESS, '操作成功', url("sms/index")];
        }else{
        	return [RESULT_ERROR, '操作失败', url("sms/index")];
        }
	}
	
	
    public function getSmsTemplete($data=[]){
		return self::getList($data);
	}
	
	/*
        获取场景
    */
    public function getSendScene($data){
        $result=self::getObject(['module'=>'mail']);
        if(isset($data['id'])){
        	$list=self::getObject(['id'=>$data['id']]);
        }
        $send_scene=[1=>'评论通知',2=>'评论回复',3 => '微语通知',4=>'微语回复',5=>'链接申请通知',6=>'注册通知',7 =>'用户找回密码', 8=>'修改密码'];
        foreach ($result as $key => $value) {
        	if(isset($data['id']) && $value['id']==$list[0]['id']) continue;
            $arr=json_decode($value['send_data'],true);
            unset($send_scene[$arr['send_scene']]);
        }
        return $send_scene;
    }
	
	
	   public function delSmsTemplete($data){
		return Core::loadModel($this->name)->deleteObject($data,true)?[RESULT_SUCCESS, '删除成功', url("sms/index")]:[RESULT_ERROR, '删除失败', url("sms/index")];
	}

}