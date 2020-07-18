<?php
// +----------------------------------------------------------------------
// | Author: yaoyihong <510974211@qq.com>
// +----------------------------------------------------------------------

namespace app\backend\model;

use app\common\model\ModelBase;
use \tpfcore\Core;
/**
 * Admin基础模型
 */
class User extends AdminBase
{
    protected $insert = ['create_time','last_login_time','last_login_ip'];
    protected $update = ['last_login_time','last_login_ip'];

    protected function setCreateTimeAttr(){
        return time();
    }

    protected function setLastLoginIpAttr(){
        return request()->ip();
    }

    protected function setNicknameAttr($value){
        if(empty($value)){
            return "Exblog_".rand(100,99999);
        }
        return $value;
    }

    protected function setPasswordAttr($value){
        return '###'.md5($value.DATA_ENCRYPT_KEY);
    }

    protected function setLastLoginTimeAttr(){
        return time();
    }
}
