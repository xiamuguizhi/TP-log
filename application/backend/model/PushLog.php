<?php
// +----------------------------------------------------------------------
// | Author: yaoyihong <510974211@qq.com>
// +----------------------------------------------------------------------

AddonBase
use \think\Request;
use app\common\model\AddonBase;
class PushLog extends AddonBase
{
	protected $insert = ['datetime'];
    
    protected function setDatetimeAttr($value)
    {
        return time();
    }
}
