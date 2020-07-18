<?php
namespace app\backend\model;
use \think\Request;
use tpfcore\helpers\StringHelper;
/**
 * ============================================================================
 * 版权所有 2017-2077 疯狂老司机，并保留所有权利。
 * @link https://crazyus.net
 * ----------------------------------------------------------------------------
 * #emlog_reply
 * ============================================================================
 */
class reply extends AdminBase
{
    protected $auto = ['date'];

	#自动获取时间
    protected function setDateAttr()
    {
        return time();
    }
	

    
}
