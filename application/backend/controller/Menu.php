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
class Menu extends AdminBase
{
    public function index()
    {
        return $this->fetch("index",[
            'categorys'=>Core::loadModel($this->name)->getMenuList()
        ]);
    }
    public function edit(){
        IS_POST && $this->jump(Core::loadModel($this->name)->saveMenu($this->param));
        return $this->fetch("edit",[
            'list'=>Core::loadModel($this->name)->getMenuListByid(['id'=>$this->param['id']]),
            'categorys'=>Core::loadModel($this->name)->getMenuList("add",$this->param),
            'id'=>$this->param['id']
        ]);
    }
    public function add()
    {
    	IS_POST && $this->jump(Core::loadModel($this->name)->saveMenu($this->param));
        return $this->fetch("add",[
            'categorys'=>Core::loadModel($this->name)->getMenuList("add",$this->param)
        ]);
    }
    public function del()
    {
    	$this->jump(Core::loadModel($this->name)->delMenu($this->param));
    }
    public function role(){
        echo json_encode(Core::loadModel($this->name)->getMenuRole($this->param));
    }
} 
