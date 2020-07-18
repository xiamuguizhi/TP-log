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
use app\common\controller\ControllerBase;

/**
 * Member控制器基类
 */
class Member extends AdminBase
{
	//个人信息
	public function userinfo(){
		IS_POST && $this->jump(Core::loadModel($this->name)->upUserInfo($this->param));
        return $this->fetch('userinfo',['list'=>Core::loadModel($this->name)->getInfo(['id'=>\think\Session::get("backend_author_sign")['userid']])]);
	}
	
	//修改密码
	public function uppwd(){
		IS_POST && $this->jump(Core::loadModel($this->name)->uppwd($this->param));
        return $this->fetch('uppwd');
	}
	
	//会员管理
	public function index(){
		$where=['type'=>0];
		if(isset($this->param['act']) && $this->param['act']=="search"){
        $search = $this->param;
        $this->assign('search',$search);       
        !empty($search['keyword']) ? $where['__USER__.username']=['like','%'.$search['keyword'].'%'] : '';
       
      }
		return $this->fetch("index",[
            'list'=>Core::loadModel($this->name)->getUserList($where)
        ]);
	}
	
    // 添加编辑会员
    public function add_edit(){
      if(IS_POST){
        $this->jump(Core::loadModel($this->name)->addUser($this->param));
      }
      return $this->fetch("add_edit",[
			"list"=>isset($this->param['id'])?Core::loadModel($this->name)->getUserList($this->param):null,
		]);
    }	
	
	//删除会员
	public function delUser(){
        $this->jump(Core::loadModel($this->name)->delUser(['id'=>$this->param['id']]));
    }
		
	//管理员管理
	public function admin(){
		return $this->fetch("admin",[
            'list'=>Core::loadModel($this->name)->getUserList(['type'=>1])
        ]);
	}
	
	//添加管理员
	public function add(){
		IS_POST && $this->jump(Core::loadModel($this->name)->addAdmin($this->param));
		return $this->fetch("add",[
			"listRole"=>Core::loadModel("Role")->getRole()
		]);
	}
	
	//编辑管理员
	public function edit(){
		IS_POST && $this->jump(Core::loadModel($this->name)->editUser($this->param));
		return $this->fetch("edit",[
			'id'=>$this->param['id'],
			"list"=>Core::loadModel($this->name)->getUserList($this->param),
			"listRole"=>Core::loadModel("Role")->getRole()
		]);
	}
	
	//管理员管理
	public function delAdmin(){
		$this->jump(Core::loadModel($this->name)->delAdmin($this->param));
	}
	
	//拉黑用户操作
	public function ban(){
		$this->jump(Core::loadModel($this->name)->ban($this->param));
	}
	
	//权限操作
	public function priv(){
		IS_POST && $this->jump(Core::loadModel($this->name)->priv($this->param));
		return $this->fetch("priv",[
			'listUser'=>Core::loadModel("Member")->getUserList(['id'=>$this->param['id']],"id,username,privs"),
		]);
	}
}