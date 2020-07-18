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
namespace app\backend\controller;
use tpfcore\Core;
use \tpfcore\util\Config;

class Mail extends AdminBase
{
    /**
     *  配置
     */
    public function index()
    {
		
	  IS_POST && $this->jump(Core::loadModel($this->name)->editSetting($this->param));
	  
      return $this->fetch(":mail/index");
    }
	
	

	
	
	    // 添加
    public function add_edit(){
      if(IS_POST){
        $this->jump(Core::loadModel("SmsTemplete")->saveSmsTemplete($this->param));
      }
      if(isset($this->param['id'])){
        $list=Core::loadModel("SmsTemplete")->getSmsTemplete(["where"  =>["id"=>$this->param['id']]]);
        foreach ($list as $key => $value) {
          $list[$key]['send_data']=json_decode($value->send_data,true);
        }
      }else{
        $list=null;
      }
      return $this->fetch(':mail/add_edit',[
        "list"=>$list,
        'send_scene'=>Core::loadModel("SmsTemplete")->getSendScene($this->param)
      ]);
    }


	 public function del()
    {
    	$this->jump(Core::loadModel("SmsTemplete")->delSmsTemplete(['id'=>$this->param['id']]));
    }
}
