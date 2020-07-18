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
class Nav extends AdminBase
{
    public function index()
    {
        return $this->fetch("index",[
            'categorys'=>Core::loadModel($this->name)->getNavList("list",$this->param),
            'listNavCat'=>Core::loadModel("NavCat")->getNavCatList(),
            'cid'=>isset($this->param['cid'])?$this->param['cid']:0
        ]);
    }
    public function edit(){
        IS_POST && $this->jump(Core::loadModel($this->name)->saveNav($this->param));
        return $this->fetch("edit",[
            'list'=>Core::loadModel($this->name)->getNavListByid(['id'=>$this->param['id']]),
            'categorys'=>Core::loadModel($this->name)->getNavList("add",$this->param),
            'listNavCat'=>Core::loadModel("NavCat")->getNavCatList(),
            'id'=>$this->param['id']
        ]);
    }
    public function add()
    {
        IS_POST && $this->jump(Core::loadModel($this->name)->saveNav($this->param));
        return $this->fetch("add",[
            'categorys'=>Core::loadModel($this->name)->getNavList("add",$this->param),
            'listNavCat'=>Core::loadModel("NavCat")->getNavCatList()
        ]);
    }
	
	
	public function adds()
    {
        IS_POST && $this->jump(Core::loadModel($this->name)->saveNav($this->param));
        return $this->fetch("adds",[
            'categorys'=>Core::loadModel($this->name)->getNavList("add",$this->param),
            'sort'=>Core::loadModel("Sort")->getTreeNav($this->param),
            'listNavCat'=>Core::loadModel("NavCat")->getNavCatList()
        ]);
    }
	
	
	public function addp()
    {
		$where = ["channel"=>'page','hide'=>'n'];
        IS_POST && $this->jump(Core::loadModel($this->name)->saveNav($this->param));
        return $this->fetch("addp",[
            'categorys'=>Core::loadModel($this->name)->getNavList("add",$this->param),
            'pages'=>Core::loadModel("Blog")->getPostsList(["where"=>$where]),
            'listNavCat'=>Core::loadModel("NavCat")->getNavCatList()
        ]);
    }
	
    public function del()
    {
        $this->jump(Core::loadModel($this->name)->delNav($this->param));
    }
} 
