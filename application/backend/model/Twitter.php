<?php
namespace app\backend\model;
use \think\Request;
use tpfcore\helpers\StringHelper;
/**
 * ============================================================================
 * 版权所有 2017-2077 疯狂老司机，并保留所有权利。
 * @link https://crazyus.net
 * ----------------------------------------------------------------------------
 * #emlog_twitter
 * ============================================================================
 */
class Twitter extends AdminBase
{
    protected $auto = ['date','author','useragent'];

	#自动获取时间
    protected function setDateAttr()
    {
        return time();
    }
	
	 protected function setUseragentAttr()
    {
        return Request::instance()->header('user-agent');
    }
	
	

	#获取作者
    protected function setAuthorAttr()
    {
        return session("backend_author_sign")['userid'];
    }

    
}
