<?php
namespace app\backend\model;
use \think\Request;
use tpfcore\helpers\StringHelper;
/**
 * ============================================================================
 * 版权所有 2017-2077 疯狂老司机，并保留所有权利。
 * @link https://crazyus.net
 * ----------------------------------------------------------------------------
 * #emlog_page
 * ============================================================================
 */
class Page extends AdminBase
{
    protected $auto = ['updatetime','author','abstract'];
    //protected $insert =['ischeck'];

    protected function setUpdatetimeAttr()
    {
        return time();
    }
    protected function setDatetimeAttr($value)
    {
        return strtotime($value);
    }
    protected function setAuthorAttr()
    {
        return session("backend_author_sign")['userid'];
    }
    protected function setIscheckAttr(){
        if(config("config.POSTS_CHECK_WAY")==1){
            return 1;
        }else{
            return 0;
        }
    }
    protected function setAbstractAttr($value){
        $content = input("content");
        if(empty($value)){
            return StringHelper::msubstr($content,0,config("config.cut_posts_length"));
        }else{
            return $value;
        }
    }
}
