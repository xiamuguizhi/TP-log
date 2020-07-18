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
use \tpfcore\Core;
use \tpfcore\web\Curl;
class Index extends AdminBase
{
    public function index()
    {
		 $today_unix_timestamp_start = strtotime(date("Y-m-d")." 00:00:00");
		 $options = \tpfcore\helpers\Json::jsonValueToArray(Core::loadModel("Setting")->getSetting(['sign'=>'site_options'])->toArray());
        return $this->fetch("index",[
            'listMianNav'=>Core::loadModel("Menu")->getMenuArrTree(['parentid'=>0]),
        	'listTree'=>Core::loadModel("Menu")->getMenuArrTree(['display'=>1,'parentid'=>["gt","0"]],true),
			'com_today_number'=>Core::loadModel("Comment","backend","logic")->getStatistics(["mail"=>['neq',$options['options']['site_admin_email']],"date"=>["gt",$today_unix_timestamp_start]]),
			'com_today'=>Core::loadModel("Comment","backend","logic")->getObject(["mail"=>['neq',$options['options']['site_admin_email']],"date"=>["gt",$today_unix_timestamp_start]]),
        ]);
    }
    public function main(){
    	$mysql= \think\Db::query("select VERSION() as version");
    	$mysql=$mysql[0]['version'];
    	$mysql=empty($mysql)?L('UNKNOWN'):$mysql;
    	
        //$validate_state=json_decode(Curl::post("http://validate.tpframe.com/authorize",['url'=>request()->domain(),'ip'=>request()->ip()]),true);
    	//server infomaions
    	$info = array(
    			"操作系统：" => PHP_OS,
    			"运行环境：" => $_SERVER["SERVER_SOFTWARE"],
    	        "PHP版本：" => PHP_VERSION,
    			"PHP运行方式：" => php_sapi_name(),
				"PHP版本：" => phpversion(),
    			"MYSQL版本：" =>$mysql,
    			"程序版本：" => config("version.tpframe_version").config("version.tpframe_release"),
                //"授权状态："=>$validate_state['code']===0?"<font class='green'>已授权</font>":"<font class='red'><a href='https://www.tpframe.com/authorize/buy' target='_blank'>未授权</a></font>",
    			"上传附件限制：" => ini_get('upload_max_filesize'),
    			"执行时间限制：" => ini_get('max_execution_time') . "s",
    			"剩余空间：" => round((@disk_free_space(".") / (1024 * 1024)), 2) . 'M',
    	);

        $today_unix_timestamp_start = strtotime(date("Y-m-d")." 00:00:00");

        // data count
        $statistics = array(
            "blog_number"=>Core::loadModel("Blog","frontend","logic")->getStatistics(["channel"=>'blog',"hide"=>'n',"datetime"=>["gt",$today_unix_timestamp_start]]),
            "user_number"=>Core::loadModel("Member","backend","logic")->getStatistics(["create_time"=>["gt",$today_unix_timestamp_start]]),
            "tw_number"=>Core::loadModel("Twitter","backend","logic")->getStatistics(["date"=>["gt",$today_unix_timestamp_start]]),
            "link_number"=>Core::loadModel("Link","backend","logic")->getStatistics(["datetime"=>["gt",$today_unix_timestamp_start]]),
        );
    	return $this->fetch("main",[
    		"server_info"=>$info,
            "statistics"=>$statistics
    	]);
    }
} 
