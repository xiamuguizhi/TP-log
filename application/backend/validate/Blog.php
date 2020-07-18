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

class Blog extends ValidateBase
{
    // 验证规则
    protected $rule = [
        'title'                 => 'require',
		'alias'              => 'check_alias',
        'cateid'                => 'gt:0',
        'content'               => 'require',
        'cut_posts_length'=>'require|gt:0',
        'keyword_replace_num'=>'egt:0'

    ];

    // 验证提示
    protected $message = [
        'title.require'         => '文章标题不能为空',
        'cateid'                => '请选择文章分类',
        'content'               => '内容不能为空',
        'html_save_path.require'=>'文档HTML默认保存路径必须',
        'keyword_replace_num.egt'=>'替换次数必须为大于等于0'
    ];

    // 应用场景
    protected $scene = [
        'add'  =>  ['title','alias','cateid','content'],
    ];


	
	protected function check_alias($value){
		 $id=input('cid/d',0);
        // 编辑
        if($id){
            if(Db::name("Blog")->where("alias='$value' and id!=$id")->count()>0){
                return "该别名已经存在";
            }
        }else{
            if(Db::name("Blog")->where("alias='$value'")->count()>0){
                return "该别名已经存在";
            } 
		}
        return true;	
    }
}