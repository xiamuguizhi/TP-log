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
namespace app\frontend\validate;
use app\common\validate\ValidateBase;
use think\Db;

class Link extends ValidateBase
{
    // 验证规则
    protected $rule = [
        'sitename'              => 'require|check_tagname',
        'siteurl'              => 'require|url',
        'sitepic'              => 'require|url',
        'description'              => 'require|max:100',
    ];

    // 验证提示
    protected $message = [
        'sitename.require'          => '链接名称不能为空',
        'siteurl.require'          => '链接地址不能为空',
        'siteurl.url'          => '地址格式错误',
        'sitepic.require'          => '图标不能为空',		
        'sitepic.url'          => '图片格式错误',
        'description.require'          => '描述不能为空',
        'description.max'          => '描述最多不能超过100个字符',
    ];

    // 应用场景
    protected $scene = [
        'add'  =>  ['sitename','siteurl','sitepic','description'],
    ];
	
	protected function check_tagname($value){

        if(Db::name("Link")->where("sitename='$value'")->count()>0){
                return "已经申请过了，请勿重复申请";
        } 
	
        return true;	
    }
}