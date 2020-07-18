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
use tpfcore\helpers\Json;
use think\Request;

class Sort extends AdminBase
{
    
	#分类
	public function index()
    {
         return $this->fetch(":sort/index",[
            'categorys'=>Core::loadModel($this->name)->getCategoryList($this->param)
        ]);
    }	
	
	#编辑
    public function edit(){
        if(IS_POST){
            $this->jump(Core::loadModel($this->name)->saveCategory($this->param));
        }
         return $this->fetch(":sort/edit",[
            'list'=>Core::loadModel($this->name)->getCategoryListByid(['id'=>$this->param['id']]),
            'categorys'=>Core::loadModel($this->name)->getTreeCategory($this->param),
            'id'=>$this->param['id']
        ]);
    }
	
	#添加
    public function add()
    {
        if(IS_POST){
            $this->jump(Core::loadModel($this->name)->saveCategory($this->param));
        }
         return $this->fetch(":sort/add",[
            'categorys'=>Core::loadModel($this->name)->getTreeCategory($this->param)
        ]);
    }
	
	#删除
    public function del()
    {
        $this->jump(Core::loadModel($this->name)->delCategory($this->param));
    }
} 
