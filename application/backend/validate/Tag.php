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
namespace app\backend\validate;
use app\common\validate\ValidateBase;
use think\Db;

class Tag extends ValidateBase
{
    // 验证规则
    protected $rule = [
        'tagname'              => 'require|check_tagname',
    ];

    // 验证提示
    protected $message = [
        'tagname.require'          => '名称不能为空',
    ];

    // 应用场景
    protected $scene = [
        'add'  =>  ['tagname'],
        'edit'  => ['tagname'],
    ];
	
	protected function check_tagname($value){
		 $id=input('id/d',0);
        // 编辑
        if($id){
            if(Db::name("Tag")->where("tagname='$value' and id!=$id")->count()>0){
                return "该标签已经存在";
            }
        }else{
            if(Db::name("Tag")->where("tagname='$value'")->count()>0){
                return "该标签已经存在";
            } 
		}
        return true;	
    }
}