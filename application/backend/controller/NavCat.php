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
class NavCat extends AdminBase
{
    public function index()
    {
        return $this->fetch("index",[
            'list'=>Core::loadModel($this->name)->getNavCatList()
        ]);
    }
    public function edit(){
        IS_POST && $this->jump(Core::loadModel($this->name)->saveNavCat($this->param));
        return $this->fetch("edit",[
            'list'=>Core::loadModel($this->name)->getNavCatList(['id'=>$this->param['id']]),
            'id'=>$this->param['id']
        ]);
    }
    public function add()
    {
    	IS_POST && $this->jump(Core::loadModel($this->name)->saveNavCat($this->param));
        return $this->fetch("add");
    }
    public function del()
    {
    	$this->jump(Core::loadModel($this->name)->delNavCat($this->param));
    }
} 
