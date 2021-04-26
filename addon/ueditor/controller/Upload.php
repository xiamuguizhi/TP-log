<?php
namespace addon\ueditor\controller;
use app\common\controller\AddonAdminBase;
//use think\Image;
//use gmars\qiniu\Qiniu;
/**
* Ueditor插件上传
*/
class Upload extends AddonAdminBase
{
	public $UploadPath = './data/uploads/';//这个.不能少,不然写入不了文件
	public function uploads(){
		if(isset($this->param['action'])){
			$action = $this->param['action'];
		}else{
			die(json_encode(['state'=> '请求出错']));
		}
		$ConfigPath = str_replace('\\','/',ADDON_PATH.'ueditor'.DS.'UE_config.json');
		$config = json_decode(preg_replace('/\/\*[\s\S]+?\*\//','',file_get_contents($ConfigPath)), true);
		switch ($action) {
			case 'config':
				$result = $config;
				break;
			/* 上传图片 */
		    case 'uploadimage':
		    /* 上传视频 */
		    case 'uploadvideo':
		    /* 上传文件 */
		    case 'uploadfile':
		    	$prefix = substr($action,6);
		    	$this->UploadPath = $config[$prefix.'PathFormat'];
		    	$fieldName = $config[$prefix.'FieldName'];
		    	$validate  = [
		    		'size'	=>$config[$prefix.'MaxSize'],
		    		'ext'	=>str_replace('.', '', join(',', $config[$prefix.'AllowFiles']))
		    	];	
		        $result = $this->FileUpload($fieldName,$validate);
		    	break;
		    /* 上传涂鸦 */
		    case 'uploadscrawl':
		    	$this->UploadPath = $config['scrawlPathFormat'];
		    	$fieldName = $config['scrawlFieldName'];
		    	$scrawl_config  = [
		    		'maxSize'	=>$config['scrawlMaxSize'],
		    		'allowFiles'=>$config['scrawlAllowFiles'],
		    		'oriName'	=>'scrawl.png'
		    	];
		    	$result = $this->UploadBase64($fieldName,$scrawl_config);
		    	break;
		    /* 列出图片 */
		    case 'listimage':
		    /* 列出文件 */
		    case 'listfile':
		    	$prefix = substr($action,4);
		    	$path = $config[$prefix.'ManagerListPath'];
		    	$allowFiles = $config[$prefix.'ManagerAllowFiles'];
		    	$listSize = $config[$prefix.'ManagerListSize'];
		    	$result = $this->FileList($path,$allowFiles,$listSize);
		        break;
		    /* 抓取远程文件 */
		    case 'catchimage':
		    	$this->UploadPath = $config['catcherPathFormat'];
		    	$CatcherConfig = [
				    "pathFormat" => $config['catcherPathFormat'],
				    "maxSize" => $config['catcherMaxSize'],
				    "allowFiles" => $config['catcherAllowFiles'],
				    "oriName" => "remote.png"
				];
		    	$list = [];
		    	$fieldName = $config['catcherFieldName'];
		    	isset($this->post[$fieldName])?$source=$this->post[$fieldName]:$source=$this->param[$fieldName];
		    	try {
		    		foreach($source as $imgUrl){
				        $info = $this->saveRemote($imgUrl, $CatcherConfig);
				        array_push($list, array(
				            "state" => $info["state"],
				            "url" => $info["url"],
				            "size" => $info["size"],
				            "title" => htmlspecialchars($info["title"]),
				            "original" => htmlspecialchars($info["original"]),
				            "source" => htmlspecialchars($imgUrl)
				        ));
				    }
				    $result = [
				        'state' => count($list) ? 'SUCCESS':'ERROR',
				        'list' => $list
				    ];
		    	} catch (\Exception $e) {
		    		$result = [
				        'state' => $e->getMessage()
				    ];
		    	}
		        break;
		    /*上传七牛oss*/
		    case 'qiniu':
		       	$result = $this->Qiniu();
		       	break;
			default:
				$result = ['state'=> '请求地址出错'];
				break;
		}
		if(isset($this->param['callback'])){
			if (preg_match("/^[\w_]+$/",$this->param['callback'])) {
		        echo htmlspecialchars($this->param['callback']) . '(' . $result . ')';
		    } else {
		        die(json_encode(['state'=> 'callback参数不合法']));
		    }
		}else{
			die(json_encode($result));
		}
	}

	/*==============================================================================*/
	/**
	 * 文件上传
	 * @param string $fieldName 提交的文件表单名称
	 * @param array  $validate  验证规则,为空为不验证
	 * @return array
	 * POST上传大于8M要改php.ini post_max_size
	 */
	private function FileUpload($fieldName, $validate=[]){
		$file = request()->file($fieldName);
		$info = $file->validate($validate)->move($this->UploadPath);
		if($info){
			$url = substr($this->UploadPath,1).str_replace('\\','/',$info->getSaveName());
			$result = [
				'state'		=> 'SUCCESS',
				'url' 		=> $url,//路径
				'title'		=> $info->getFilename(),//上传后的文件名
				'original' 	=> $info->getFilename(),//原文件名
				'type' 		=> '.' . $info->getExtension(),//文件后缀
				'size' 		=> $info->getSize(),//文件大小
			];
		}else{
			$result = [
				'state' 	=> $file->getError(),//获取错误
			];
		}
		return $result;
	}
	/**
	 * 处理base64编码的图片上传,例如：涂鸦图片上传
	 * @param string $fieldName 提交的文件表单名称
	 * @param array $scrawl    参数配置数组
	 * @return array
	 */
	private function UploadBase64($fieldName, $scrawl){
		$base64Data = $this->post[$fieldName];
		$img = base64_decode($base64Data);

		$dirname = $this->UploadPath.date('Ymd').'/';
		$file['filesize'] = strlen($img);
		$file['oriName'] = $scrawl['oriName'];
		$file['ext'] = strtolower(strrchr($scrawl['oriName'],'.'));
		$file['name'] = uniqid().$file['ext'];
		$fullName = $file['fullName'] = $dirname.$file['name'];

 	    //检查文件大小是否超出限制
		if($file['filesize'] >= ($scrawl['maxSize'])){
			return ['state'=>'文件大小超出网站限制'];
		}
		//创建目录失败
		if(!file_exists($dirname) && !mkdir($dirname,0777,true)){
			return ['state'=>'目录创建失败'];
		}else if(!is_writeable($dirname)){
			return ['state'=>'目录没有写权限'];
		}
		//写入文件
		if(!(file_put_contents($fullName,$img) && file_exists($fullName))){
			return ['state'=>'写入文件内容错误'];
		}else{
			return [
				'state'	=>'SUCCESS',
				'url'	=>substr($file['fullName'],1),
				'title'	=>$file['name'],
				'original' => $file['oriName'],
				'type' => $file['ext'],
				'size' => $file['filesize'],
			];
		}
	}
	
	/**
	 * 列出文件列表
	 * @param string $path  		目录路径
	 * @param array $allowFiles 	允许的文件后缀数组|多个用,分割
	 * @param string|int $listSize 	列出的文件数量|默认20
	 * @param string $start   		默认从0开始
	 * @return array
	 */
	private function FileList($path,$allowFiles,$listSize=20,$start=0){
		$allowFiles = substr(str_replace('.', '|', join('', $allowFiles)), 1);
		/* 获取文件列表 */
		$path = $_SERVER['DOCUMENT_ROOT'] . (substr($path, 0, 1) == '/' ? '':'/') . $path;
		$files = $this->getFiles($path, $allowFiles);
		if (!count($files)) {
		    return [
		        "state" => "no match file",
		        "list" => [],
		        "start" => $start,
		        "total" => count($files)
		    ];
		}
		/* 获取指定范围的列表 */
		$end = $start + $listSize;
		$len = count($files);
		for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
		    $list[] = $files[$i];
		}
		/* 返回数据 */
		$result = [
		    "state" => "SUCCESS",
		    "list" => $list,
		    "start" => $start,
		    "total" => count($files)
		];
		return $result;
	}
	
	/**
	* 递规遍历获取目录下的指定类型的文件
	* @param string $path 		路径
	* @param string $allowFiles 多个用|分开 如'png|jpg|jpeg|gif'
	* @param array $files
	* @return array
	*/
	private function getFiles($path, $allowFiles, &$files = array())
	{
		if (!is_dir($path)) return null;
		if(substr($path, strlen($path) - 1) != '/') $path .= '/';
		$handle = opendir($path);
		while (false !== ($file = readdir($handle))) {
			if($file != '.' && $file != '..') {
				$path2 = $path . $file;
				if(is_dir($path2)) {
					$this->getFiles($path2, $allowFiles, $files);
				}else {
					if(preg_match("/\.(".$allowFiles.")$/i", $file)) {
						$files[] = array(
							'url'=> substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
							'mtime'=> filemtime($path2)
						);
					}
				}
			}
		}
		return $files;
	}
	/**
	 * 抓取远程文件到本地
	 * @param string $fieldName   source远程链接
	 * @param array $config 抓取远程图片配置数组
	 * @return array
	 */
	private function saveRemote($fieldName,$config){
		//set_time_limit(0);
		$imgUrl = htmlspecialchars($fieldName);
		$imgUrl = str_replace("&amp;","&",$imgUrl);

		//http开头验证
	    if(strpos($imgUrl,"http") !== 0){
	        return ['state'=>'链接非法'];
	    }
	    //获取请求头并检测死链
	    $heads = get_headers($imgUrl,true);//参数true 会解析相应的信息并设定数组的键名
	    if(stristr($heads[0],'403 Forbidden')){
	    	return ['state'=>'链接:403 Forbidden'];
	    }
	    if(!(stristr($heads[0],"200") && stristr($heads[0],"OK"))){
	        return ['state'=>'链接不可用'];
	    }
	    //格式验证(扩展名验证和Content-Type验证)
	    $fileType = strtolower(strrchr($imgUrl,'.'));
	    if(!in_array($fileType,$config['allowFiles']) || !isset($heads['Content-Type']) || !stristr($heads['Content-Type'],'image')){
	        return ['state' => '链接contentType不正确'];
	    }

	    //打开输出缓冲区并获取远程图片
	    ob_start();
	    $context = stream_context_create(
	        array('http' => array(
	            'follow_location' => false // don't follow redirects
	        ))
	    );
	    readfile($imgUrl,false,$context);
	    $img = ob_get_contents();
	    ob_end_clean();
	    preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/",$imgUrl,$m);

	    $dirname = $this->UploadPath.date('Ymd').'/';
	    $file['oriName'] = $m ? $m[1] : "";//原文件名
	    $file['filesize'] = strlen($img);//大小
	    $file['ext'] = strtolower(strrchr($config['oriName'],'.'));//后缀
	    $file['name'] = uniqid().$file['ext'];//新文件名
	    $fullName = $file['fullName'] = $dirname.$file['name'];

	    //检查文件大小是否超出限制
	    if($file['filesize'] >= ($config["maxSize"])){
  		    return ['state' => '文件大小超出网站限制'];
	    }
	    //创建目录失败
		if(!file_exists($dirname) && !mkdir($dirname,0777,true)){
			return ['state'=>'目录创建失败'];
		}else if(!is_writeable($dirname)){
			return ['state'=>'目录没有写权限'];
		}
		//写入文件
		if(!(file_put_contents($fullName,$img) && file_exists($fullName))){
			return ['state'=>'写入文件内容错误'];
		}else{
			return [
				'state'		=> 'SUCCESS',
				'url'		=> substr($fullName,1),
				'title'		=> $file['name'],
				'original' 	=> $file['oriName'],
				'type' 		=> $file['ext'],
				'size' 		=> $file['filesize'],
			];
		}
	}

	/**
	 * 上传到七牛云oss
	 * 在tp5的配置文件config.php中配置七牛云的配置参数,支持实例化时再传入配置参数
	 * 'qiniu' => [
		    'accesskey' => '你自己的七牛云accesskey',
		    'secretkey' => '你自己的七牛云secretkey',
		    'bucket' => '你自己创建的bucket',
		]
	 */
	private function Qiniu($fieldName='', $validate=[], $type='file'){
		try {
			$extinfo=array_values($_FILES);
			$ext = array_reverse(explode('.',$extinfo[0]['name']));//文件原后缀
			$saveName = $type.'/'.date('Ymd').'/'.md5(microtime(true)).'.'.$ext[0];
			$qiniu = new Qiniu();
			$url = $qiniu->upload($saveName);
			$result = [
				'state'		=> 'SUCCESS',
				'url' 		=> '//img.ainiyo.cc/'.$url,//相对协议路径http|https
				'title'		=> 'img',//上传后的文件名(img标签的title)
				'original' 	=> 'ainiyo.cc',//原文件名(img标签的alt)
				'type' 		=> '.png',//文件后缀
				'size' 		=> '',//文件大小
			];
		} catch (\Exception $e) {
			$result = [
				'state' => $e->getMessage(),	//获取错误
			];
		}
		return $result;
	}

	/**
	 * 水印处理
	 * @param [type] $fieldName 提交的文件表单名称
	 * @param [type] $validate  验证规则,为空为不验证
	 * @param string $type      上传分类
	 */
	private function ImageUpload($fieldName, $validate=[], $type='image'){
		//2048000|png,jpg,jpeg,gif,bmp
		$file = request()->file($fieldName);
		//$test = $this->validate($file,'Ueditor');
		//文件验证问题？
		try {
			$image     = Image::open($file);
			//字体文件
			$ttfPaht   = VENDOR_PATH.'endroid'.DS.'qr-code'.DS.'assets'.DS.'noto_sans.otf';
			$dirname   = $this->UploadPath.$type.'/'.date('Ymd').'/';
			$saveName  = md5(microtime(true)).'.png';//文件名
			//创建目录
			if(!file_exists($dirname) && !mkdir($dirname,0777,true)){
				$result = ['state'=>'目录创建失败'];
			}else if(!is_writeable($dirname)){
				$result = ['state'=>'目录没有写权限'];
			}
			$info = $image
						->text('测试水印啊',$ttfPaht,20,'#ffffff',9)
						->save($dirname.$saveName);
			$result = [
				'state'		=> 'SUCCESS',
				'url' 		=> substr($dirname.$saveName,1),//路径
				'title'		=> $saveName,//上传后的文件名
				'original' 	=> $saveName,//原文件名
				'type' 		=> '.png',//文件后缀
				'size' 		=> $file->getSize(),//文件大小
			];
		} catch (\Exception $e) {
			$result = [
				'state' => $e->getMessage(),//获取错误
			];
		}
		return $result;
	}
}