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
use \tpfcore\helpers\FileHelper;
use tpfcore\Core;
use \think\Config;

class Template extends AdminBase{


    /**
     * 管理模板
     */
   public function index(){

        if(IS_POST){
        $this->jump(Core::loadModel($this->name)->editSetting($this->param));
		}
		
		//模板列表
		$handle = @opendir(TPLS_PATH) OR die("模板路径错误");
		$tpls = array();
		$i = 0;
		while ($file = @readdir($handle)) {
			 if ($file != "." && $file != ".." && strpos($file,".")===false) {
				$tplData  = @implode('',@file(TPLS_PATH.$file.'/layout.html'));
	            preg_match("/Template Name:([^\r\n]+)/i",  $tplData, $name);
				preg_match("/Version:(.*)/i", $tplData, $tplVersion);
				preg_match("/Author:(.*)/i", $tplData, $tplAuthor);
				preg_match("/Description:(.*)/i", $tplData, $tplDes);
				preg_match("/Author Url:(.*)/i", $tplData, $tplUrl);
				preg_match("/ForTPlog:(.*)/i", $tplData, $tplForTPlog);
				$tplInfo['file'] = $file;
				$tplInfo['tplname'] = !empty($name[1]) ? trim($name[1]) : $file;
			    $tplInfo['tplDes'] = !empty($tplDes[1]) ? $tplDes[1] : '';
				$tplInfo['tplVer'] = !empty($tplVersion[1]) ? $tplVersion[1] : '';
				$tplInfo['tplForTp'] = !empty($tplForTPlog[1]) ? '' . $tplForTPlog[1] : '';

				if (isset($tplAuthor[1])) {
					$tplInfo['tplAuthor'] = !empty($tplUrl[1]) ? "作者：<a href=\"{$tplUrl[1]}\" target=\"_blank\">{$tplAuthor[1]}</a>" : "作者：{$tplAuthor[1]}";
				} else{
					$tplInfo['tplAuthor'] = '';
				}
				$tplInfo['tplfile'] = $file;

				$tpls[] = $tplInfo;
	            }
		
		}
		closedir($handle);
		return $this->fetch(':template/index',[
          'list'=>$tpls,
          'current'=>config('config.DEFAULT_THEME'),
       ]);
		
    }

	#删除 
    public function del(){
      $this->jump(Core::loadModel($this->name)->delTheme(['file'=>$this->param['file']]));
    }
	

    /**
     * 安装模板
     */
    public function installTemplate(){
			
    }
}