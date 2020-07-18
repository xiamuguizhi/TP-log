<?php
// +----------------------------------------------------------------------
// | Author: yaoyihong <510974211@qq.com>
// +----------------------------------------------------------------------
namespace app\backend\model;
use \think\Request;
use Think\Db;

class Comment extends AdminBase
{
    protected $insert = ['date'];

    protected function setDateAttr($value){
    	return time();
    }

    //protected function setPosterAttr($value){
       // if(input("pid")>0){
          //  $list=Db::name("Comment")->field("poster")->where(["cid"=>input("pid")])->find();
           // return $list['author'];
        //}
    //}
}
