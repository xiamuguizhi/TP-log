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
class SlideCat extends ValidateBase
{
    // 验证规则
    protected $rule = [
        'name'              => 'require',
        'sign'              => 'require|check_sign',
    ];

    // 验证提示
    protected $message = [
        'name.require'          => '分类名称必须',
        'sign.require'          => '广告标识必须'
    ];

    // 应用场景
    protected $scene = [
        'add'  =>  ['name','sign'],
        'edit'  => ['name','sign'],
    ];
    protected function check_sign($value){
        $id=input('id/d',0);
        // 编辑
        if($id){
            if(Db::name("SlideCat")->where("sign='$value' and id!=$id")->count()>0){
                return "该标识已经存在";
            }
        }else{
            if(Db::name("SlideCat")->where("sign='$value'")->count()>0){
                return "该标识已经存在";
            } 
        }
        return true;
    }
}