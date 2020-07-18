<?php
// +----------------------------------------------------------------------
// | Author: yaoyihong <510974211@qq.com>
// +----------------------------------------------------------------------

namespace app\backend\model;
use \think\Request;
use app\common\model\AddonBase;
class SmsTemplete extends AddonBase
{
    protected $insert = ['datetime','module','send_scene'];
    
    protected function setDatetimeAttr($value)
    {
        return time();
    }
    protected function setModuleAttr($value)
    {
        return "mail";
    }
    protected function setSendDataAttr($value)
    {
        return json_encode($value);
    }
    protected function setSendSceneAttr($value)
    {
        return input('post.')['send_data']['send_scene'];
    }
}
