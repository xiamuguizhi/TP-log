<?php
namespace addon\ueditor\controller;
use app\common\controller\AddonAdminBase;
use tpfcore\Core;

/**
 * 编辑器插件
 */
class Ueditor extends AddonAdminBase
{
   	public function doc() {
      return $this->addonTemplate("index:doc");
    }
}
