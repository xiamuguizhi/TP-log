<?php
namespace addon\ueditor;

use app\common\controller\AddonBase;

use app\common\dao\AddonInterface;

/**
 * 富文本编辑器插件
 */
class Ueditor extends AddonBase implements AddonInterface
{
     private $addonName="ueditor编辑器";
    /**
     * 实现钩子
     */
    public function ueditor($param = [])  
    {
        $this->addonTemplate('index/index',[
            "addons_data"=>$param,
            "addons_config"=>$this->addonConfig()
        ]);
    }
    
    /**
     * 插件安装
     */
    public function addonInstall()
    {
        
        return [RESULT_SUCCESS, $this->addonName.'安装成功'];
    }
    
    /**
     * 插件卸载
     */
    public function addonUninstall()
    {
        
        return [RESULT_SUCCESS, $this->addonName.'卸载成功'];
    }
    
    /**
     * 插件基本信息
     */
    public function addonInfo()
    {
        
        return [
            'name' => 'ueditor', 
            'title' => $this->addonName, 
            'describe' => 'ueditor富文本编辑器', 
            'author' => 'yaosean', 
            'version' => '2.0' ,
            'require'=>'>=3.0',  
            "config" => false,
            'type'=>'behavior'
        ];
    }
    
    /**
     * 插件配置信息
     */
    public function addonConfig()
    {
        
        $addons_config['editor_width'] = 1000;
        
        $addons_config['editor_height'] = 280;
        
        return $addons_config;
    }
}
