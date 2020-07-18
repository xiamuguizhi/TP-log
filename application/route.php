<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use \think\Request;
$pathinfo=strtolower(Request::instance()->pathinfo());
$pathinfo=$pathinfo=='backend'?$pathinfo.'/':$pathinfo;
if(!preg_match('/^backend\//',$pathinfo) && !preg_match('/^frontend\//',$pathinfo) && !preg_match('/^api/',$pathinfo) && file_exists("data/install.lock")){
    \think\Route::bind('frontend');
};


return [
    '__pattern__' => [
        'name' => '\w+',
    ],


	 //文章模型
    '[posts]'    => [
      '[:alias]/[:name]]'   => ['posts/show', ['method' => 'get'], ['name' => '\w+']],
   ],
   
   	 //页面模型
    '[pages]'    => [
      '[:alias]'   => ['pages/show', ['method' => 'get'], ['alias' => '\w+']],
   ],
   
	//分类
	'[sort]'     => [
       '[:cid]'   => ['sort/index', ['method' => 'get'], ['name' => '\d+']],
  ],
	
	//标签
	'[tag]'     => [
		'[:tid]' =>  ['tag/index', ['method' => 'get'], ['name' => '\w+']],
     ],
	 
	//作者	 
	'[author]'     => [
		':uid' =>  ['index/index', ['method' => 'get'], ['uid' => '\d+']],
     ],
	 
	 //归档
	'[record]'     => [
		':record' =>  ['index/index', ['method' => 'get'], ['record' => '\d+']],
     ],
	 
	 //归档
	
];
