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

/**
 * 升级程序
 */
class Upgrade extends AdminBase
{
	public function index(){
		$list=Core::loadModel($this->name)->check();
		return $this->fetch("index",[
			'title'=>'tpframe 系统在线升级',
            'list'=>empty($list[2])?[]:$list[2]
        ]);
	}
	public function check(){
		$this->jump(Core::loadModel($this->name)->check());
	}
	//开始升级操作
	public function doup(){
		$list= Core::loadModel($this->name)->check();
		return $this->fetch("doup",[
			"list"=>empty($list) || $list[0]!=0 ? "":json_encode($list[2])
		]);
	}

	public function doupdate(){
		$this->jump(Core::loadModel($this->name)->doupdate($this->param));
	}
}