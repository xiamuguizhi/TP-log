<?php
use tpfcore\Core;
use tpfcore\helpers\StringHelper;



//判断内容页是否百度收录
function baidu($url){
	$url='http://www.baidu.com/s?wd='.$url;
	$curl=curl_init();curl_setopt($curl,CURLOPT_URL,$url);
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
	$rs=curl_exec($curl);
	curl_close($curl);
	if(!strpos($rs,'没有找到')){
		return 1;
		}else{
		return 0;
	}
}
function logurl($url){
	if(baidu($url)==1){
		echo "百度已收录";
	}else{
		echo "<a style=\"color:red;\" rel=\"external nofollow\" title=\"点击提交收录！\" target=\"_blank\" href=\"http://zhanzhang.baidu.com/sitesubmit/index?sitename=$url\">百度未收录</a>";
	}
}





//获取指定月份的天数
function getMonthDayNum($month, $year) {
    $month = (int)$month;
    $year = (int)$year;
    
    $months_map = array(1=>31, 3=>31, 4=>30, 5=>31, 6=>30, 7=>31, 8=>31, 9=>30, 10=>31, 11=>30, 12=>31);
    if (array_key_exists($month, $months_map)) {
        return $months_map[$month];
    }
    else {
        if ($year % 100 === 0) {
            if ($year % 400 === 0) {
                return 29;
            } else {
                return 28;
            }
        }
        else if ($year % 4 === 0) {
            return 29;
        }
        else {
            return 28;
        }
    }
}


//文章最新图标
function view_news($time){
	if((date('Ymd',time())-date('Ymd',$time))< 1){
		return  true;
	}else {
		return false;
	}
}

//获取文章图片数量
function pic($data){
    if(preg_match_all("/<img.*src=[\"'](.*)[\"']/Ui", $data, $img) && !empty($img[1])){
        $imgNum = count($img[1]);
    }else{
        $imgNum = "0";
    }
    return $imgNum;
}



//文章内添加索引/标签添加链接/外链添加nofollow
function article_index($content) {
            $matches = array();
            $ul_li = '';
            $r = "/<h2>([^<]+)<\/h2>/im";
    if(preg_match_all($r,$content,$matches)) {
           foreach($matches[1] as $num => $title) {
           $content = str_replace($matches[0][$num], '<h2 id="title-'.$num.'">'.$title.'</h2>', $content);
           $ul_li .= '<li><a href="#title-'.$num.'" title="'.$title.'">'.$title."</a></li>\n";
             }
 $content = "\n<div id=\"article-index\">
 <b>[文章目录]</b>
 <ul id=\"index-ul\">\n" . $ul_li . "</ul>
 </div>\n" . $content;
 }

return $content;
}



function html2text($str){
	$str = preg_replace("/<style .*?<\\/style>/is", "", $str);  
	$str = preg_replace("/<script .*?<\\/script>/is", "", $str);
	$str = preg_replace("/<br \\s*\\/>/i", "", $str);
	$str = preg_replace("/<\\/?p>/i", "", $str);
	$str = preg_replace("/<\\/?td>/i", "", $str);
	$str = preg_replace("/<\\/?div>/i", "", $str);
	$str = preg_replace("/<\\/?blockquote>/i", "", $str);
	$str = preg_replace("/<\\/?li>/i", "", $str);
	$str = preg_replace("/ /i", " ", $str);
	$str = preg_replace("/ /i", " ", $str);
	$str = preg_replace("/&/i", "&", $str);
	$str = preg_replace("/&/i", "&", $str);
	$str = preg_replace("/</i", "<", $str);
	$str = preg_replace("/</i", "<", $str);
	$str = preg_replace("/“/i", '"', $str);
	$str = preg_replace("/&ldquo/i", '"', $str);
	$str = preg_replace("/‘/i", "'", $str);
	$str = preg_replace("/&lsquo/i", "'", $str);
	$str = preg_replace("/’/i", "'", $str);
	$str = preg_replace("/&rsquo/i", "'", $str);
	$str = preg_replace("/>/i", ">", $str);
	$str = preg_replace("/>/i", ">", $str);
	$str = preg_replace("/”/i", '"', $str);
	$str = preg_replace("/&rdquo/i", '"', $str);
	$str = strip_tags($str);
	$str = html_entity_decode($str, ENT_QUOTES, "utf-8");
	$str = preg_replace("/&#.*?;/i", "", $str);
	return $str;
}



/**
 * 网页执行时间
 *
 * @param $now
 * @param $datetemp
 * @param $dstr
 * @return string
 */
function getTime($num){
	$time_end = getmicrotime();
	printf ("执行: %.2f毫秒\n\n",($time_end - $time_start)*$num);
}

/**
 * 建站时间
 *
 * @param $now
 * @param $datetemp
 * @param $dstr
 * @return string
 */
function runTime($date){
	date_default_timezone_set("PRC");
	return floor((time()-strtotime($date))/86400);
}

/**
 * 时间转化函数
 *
 * @param $now
 * @param $datetemp
 * @param $dstr
 * @return string
 */
function smartDate($unixtime, $dstr = 'Y-m-d H:i') {
		$etime = time() - $unixtime;
    if ($etime < 1) return '刚刚';     
    $interval = array (         
        12 * 30 * 24 * 60 * 60  =>  '年前 ('.date('Y-m-d', $unixtime).')',
        30 * 24 * 60 * 60       =>  '个月前 ('.date('m-d', $unixtime).')',
        7 * 24 * 60 * 60        =>  '周前 ('.date('m-d', $unixtime).')',
        24 * 60 * 60            =>  '天前',
        60 * 60                 =>  '小时前',
        60                      =>  '分钟前',
        1                       =>  '秒前'
    );
    foreach ($interval as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            return $r . $str;
        }
    };
}



//emoji
function iSsmilies($str) {
$data = array(
array(
'img' => '<img class="face" src="/data/assets/images/emoticons/icon_mrgreen.gif">', 
'title'=>':mrgreen:'
),
array(
'img' => '<img class="face" src="/data/assets/images/emoticons/icon_neutral.gif">', 
'title'=>':neutral:'
),
array(
'img' => '<img class="face" src="/data/assets/images/emoticons/icon_twisted.gif">', 
'title'=>':twisted:'
),
array(
'img' => '<img class="face" src="/data/assets/images/emoticons/icon_arrow.gif">', 
'title'=>':arrow:'
),
array(
'img' => '<img class="face" src="/data/assets/images/emoticons/icon_eek.gif">', 
'title'=>':shock:'
),
array(
'img' => '<img class="face" src="/data/assets/images/emoticons/icon_smile.gif">', 
'title'=>':smile:'
),
array(
'img' => '<img class="face" src="/data/assets/images/emoticons/icon_confused.gif">', 
'title'=>':???:'
),
array(
'img' => '<img class="face" src="/data/assets/images/emoticons/icon_cool.gif">', 
'title'=>':cool:'
),
array(
'img' => '<img class="face" src="/data/assets/images/emoticons/icon_evil.gif">', 
'title'=>':evil:'
),
array(
'img' => '<img class="face" src="/data/assets/images/emoticons/icon_biggrin.gif">', 
'title'=>':grin:'
),
array(
'img' => '<img class="face" src="/data/assets/images/emoticons/icon_idea.gif">', 
'title'=>':idea:'
),
array(
'img' => '<img class="face" src="/data/assets/images/emoticons/icon_redface.gif">', 
'title'=>':oops:'
),
array(
'img' => '<img class="face" src="/data/assets/images/emoticons/icon_razz.gif">', 
'title'=>':razz:'
),
array(
'img' => '<img class="face" src="/data/assets/images/emoticons/icon_rolleyes.gif">', 
'title'=>':roll:'
),
array(
'img' => '<img class="face" src="/data/assets/images/emoticons/icon_wink.gif">', 
'title'=>':wink:'
),
array(
'img' => '<img class="face" src="/data/assets/images/emoticons/icon_cry.gif">', 
'title'=>':cry:'
),
array(
'img' => '<img class="face" src="/data/assets/images/emoticons/icon_surprised.gif">', 
'title'=>':eek:'
),
array(
'img' => '<img class="face" src="/data/assets/images/emoticons/icon_lol.gif">', 
'title'=>':lol:'
),
array(
'img' => '<img class="face" src="/data/assets/images/emoticons/icon_mad.gif">', 
'title'=>':mad:'
),
array(
'img' => '<img class="face" src="/data/assets/images/emoticons/icon_sad.gif">', 
'title'=>':sad:'
),
array(
'img' => '<img class="face" src="/data/assets/images/emoticons/icon_exclaim.gif">', 
'title'=>':!:'
),
array(
'img' => '<img class="face" src="/data/assets/images/emoticons/icon_question.gif">', 
'title'=>':?:'
),
);

foreach($data as $key=>$value) {
$str = str_replace($value['title'],$value['img'],$str);
}
return $str;
}


/**
 * 获取Gravatar头像
 * https://cdn.v2ex.com/gravatar/
 * @param $email
 * @param $s size
 * @param $d default avatar
 * @param $g
 */
function getGravatar($email, $s = 64, $d = 'mm', $g = 'g') {
	$hash = md5($email);
	$avatar = "https://cdn.v2ex.com/gravatar/$hash?s=$s&d=$d&r=$g";
	return $avatar;
}




//开始解析操作系统
function getOS($ua) {    
    $os = null;
    if (preg_match('/Windows NT 6.0/i', $ua)) $os = "Windows Vista";
    elseif(preg_match('/Windows NT 6.1/i', $ua)) $os = "Windows 7";
    elseif(preg_match('/Windows NT 6.2/i', $ua)) $os = "Windows 8";
    elseif(preg_match('/Windows NT 6.3/i', $ua)) $os = "Windows 8.1";
    elseif(preg_match('/Windows NT 10.0/i', $ua)) $os = "Windows 10";
    elseif(preg_match('/Windows NT 5.1/i', $ua)) $os = "Windows XP";
    elseif(preg_match('/Windows NT 5.2/i', $ua) && preg_match('/Win64/i', $ua)) $os = "Windows XP 64 bit";
    elseif(preg_match('/Windows NT 5.0/i', $ua)) $os = "Windows 2000 Professional";
    elseif(preg_match('/Android ([0-9.]+)/i', $ua, $matches)) $os = "Android ".$matches[1];
    elseif(preg_match('/iPhone OS ([_0-9]+)/i', $ua, $matches)) $os = 'iPhone '.$matches[1];
    elseif(preg_match('/iPad/i', $ua)) $os = "iPad";
    elseif(preg_match('/Mac OS X ([_0-9]+)/i', $ua, $matches)) $os = 'Mac OS X '.$matches[1];
    elseif(preg_match('/Windows Phone ([_0-9]+)/i', $ua, $matches)) $os = 'Windows Phone '.$matches[1];
    elseif(preg_match('/Gentoo/i', $ua)) $os = 'Gentoo Linux';
    elseif(preg_match('/Ubuntu/i', $ua)) $os = 'Ubuntu Linux';
    elseif(preg_match('/Debian/i', $ua)) $os = 'Debian Linux';
    elseif(preg_match('/curl/i', $ua)) $os = 'Linux distribution';
    elseif(preg_match('/X11; FreeBSD/i', $ua)) $os = 'FreeBSD';
    elseif(preg_match('/X11; Linux/i', $ua)) $os = 'Linux';
    elseif(preg_match('/X11; SunOS/i', $ua) || preg_match('/Solaris/i', $ua)) $os = 'SunOS';
    elseif(preg_match('/BlackBerry/i', $ua)) $os = 'BlackBerry';
    else $os = '未知操作系统';
	return $os;
}


 //解析浏览器
function getBR($ua) {
    if (preg_match('#(Camino|Chimera)[ /]([a-zA-Z0-9.]+)#i', $ua, $matches)) $browser = 'Camino '.$matches[2];
    elseif(preg_match('#SE 2([a-zA-Z0-9.]+)#i', $ua, $matches)) $browser = '搜狗浏览器 2'.$matches[1];
    elseif(preg_match('#360([a-zA-Z0-9.]+)#i', $ua, $matches)) $browser = '360浏览器 '.$matches[1];
    elseif(preg_match('#Maxthon( |\/)([a-zA-Z0-9.]+)#i', $ua, $matches)) $browser = 'Maxthon '.$matches[2];
    elseif(preg_match('#Edge( |\/)([a-zA-Z0-9.]+)#i', $ua, $matches)) $browser = 'Edge '.$matches[2];
    elseif(preg_match('#MicroMessenger/([a-zA-Z0-9.]+)#i', $ua, $matches)) $browser = '微信 '.$matches[1];
    elseif(preg_match('#QQ/([a-zA-Z0-9.]+)#i', $ua, $matches)) $browser = '手机QQ '.$matches[1];
    elseif(preg_match('#Chrome/([a-zA-Z0-9.]+)#i', $ua, $matches)) $browser = 'Chrome '.$matches[1];
    elseif(preg_match('#CriOS/([a-zA-Z0-9.]+)#i', $ua, $matches)) $browser = 'Chrome '.$matches[1];
    elseif(preg_match('#Chromium/([a-zA-Z0-9.]+)#i', $ua, $matches)) $browser = 'Chromium '.$matches[1];
    elseif(preg_match('#XiaoMi/MiuiBrowser/([0-9.]+)#i', $ua, $matches)) $browser = '小米浏览器 '.$matches[1];
    elseif(preg_match('#Safari/([a-zA-Z0-9.]+)#i', $ua, $matches)) $browser = 'Safari '.$matches[1];
    elseif(preg_match('#opera mini#i', $ua)) {
        preg_match('#Opera/([a-zA-Z0-9.]+)#i', $ua, $matches);
        $browser = 'Opera Mini '.$matches[1];
    }
    elseif(preg_match('#Opera.([a-zA-Z0-9.]+)#i', $ua, $matches)) $browser = 'Opera '.$matches[1];
    elseif(preg_match('#TencentTraveler ([a-zA-Z0-9.]+)#i', $ua, $matches)) $browser = '腾讯TT浏览器 '.$matches[1];
    elseif(preg_match('#QQBrowser ([a-zA-Z0-9.]+)#i', $ua, $matches)) $browser = 'QQ浏览器 '.$matches[1];
    elseif(preg_match('#UCWEB([a-zA-Z0-9.]+)#i', $ua, $matches)) $browser = 'UCWEB '.$matches[1];
    elseif(preg_match('#wp-(iphone|android)/([a-zA-Z0-9.]+)#i', $ua, $matches)) $browser = 'WordPress客户端 '.$matches[1];
    elseif(preg_match('#MSIE ([a-zA-Z0-9.]+)#i', $ua, $matches)) $browser = 'Internet Explorer '.$matches[1];
    elseif(preg_match('#Trident/([a-zA-Z0-9.]+)#i', $ua, $matches)) $browser = 'Internet Explorer 11';
    elseif(preg_match('#(Firefox|Phoenix|Firebird|BonEcho|GranParadiso|Minefield|Iceweasel)/([a-zA-Z0-9.]+)#i', $ua, $matches)) $browser = 'Firefox '.$matches[2];
    elseif(preg_match('/curl/i', $ua)) $browser = 'curl';
    else $browser = '未知浏览器';

     return $browser;
}


/**
 * 字数统计
 * @param $data 
 */
function art_count($data) {
	$text = preg_replace( "/[^\x{4e00}-\x{9fa5}]/u", "", $data);
	return mb_strlen( $text, 'UTF-8' );
}


/**
*
* 内存使用
*
**/
function ramusage(){
  if(function_exists('memory_get_usage')){
    return  round(memory_get_usage() / 1024 / 1024, 2).' MB';
  }else {
    return 0;
  }
}



//获取 IP 来源位置
function get_ip_addr($ip){
	$get_json = file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip=$ip");
	$loc = json_decode($get_json,true);
	//国家：$loc["data"]["country"],省份:$loc["data"]["region"],城市:$loc["data"]["city"],运营商:$loc["data"]["isp"];
	if($loc["data"]["region"]){
		return $loc["data"]["region"];
	}else{
		return "火星";
	}
}