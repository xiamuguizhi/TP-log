<?php
namespace app\backend\model;
use \think\Request;
use app\common\model\AddonBase;
/**
 * ============================================================================
 * 版权所有 2017-2077 疯狂老司机，并保留所有权利。
 * @link https://crazyus.net
 * ----------------------------------------------------------------------------
 * #emlog_Link
 * ============================================================================
 */
class Link extends AddonBase
{
     protected $auto = ['datetime'];

    protected function setDatetimeAttr()
    {
        return time();
    }
}
