-- ----------------------------
-- Table structure for tpf_ad
-- ----------------------------
DROP TABLE IF EXISTS `tpf_ad`;
CREATE TABLE IF NOT EXISTS `tpf_ad` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '广告id',
  `name` varchar(200) NOT NULL COMMENT '广告名称',
  `content` text COMMENT '广告内容',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态，1显示，0不显示',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for tpf_addon
-- ----------------------------
DROP TABLE IF EXISTS `tpf_addon`;
CREATE TABLE IF NOT EXISTS `tpf_addon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(100) DEFAULT NULL COMMENT '插件模块',
  `title` varchar(100) DEFAULT NULL COMMENT '插件标题',
  `describe` varchar(255) DEFAULT NULL COMMENT '插件描述',
  `config` varchar(255) DEFAULT NULL COMMENT '插件配置',
  `author` varchar(100) DEFAULT NULL COMMENT '作者',
  `version` varchar(20) DEFAULT NULL COMMENT '插件版本',
  `status` tinyint(4) DEFAULT '0' COMMENT '状态 1启用 0禁用',
  `type` varchar(50) DEFAULT NULL COMMENT '插件分类  行为插件 模块插件  行为模块插件',
  `create_time` varchar(30) DEFAULT NULL,
  `update_time` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;


-- ----------------------------
-- Table structure for tpf_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `tpf_admin_log`;
CREATE TABLE IF NOT EXISTS `tpf_admin_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `old_value` mediumtext COMMENT '原值',
  `new_value` mediumtext COMMENT '新值',
  `module` varchar(15) NOT NULL COMMENT '模块',
  `controller` varchar(20) NOT NULL COMMENT '控制器',
  `action` varchar(20) NOT NULL COMMENT '操作',
  `data` mediumtext NOT NULL COMMENT '数据',
  `url` varchar(255) DEFAULT NULL COMMENT '链接',
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `username` varchar(20) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `datetime` varchar(30) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `module` (`module`,`controller`,`action`),
  KEY `username` (`username`,`action`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for tpf_blog
-- ----------------------------
DROP TABLE IF EXISTS `tpf_blog`;
CREATE TABLE IF NOT EXISTS `tpf_blog` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '标题',
  `datetime` varchar(30) CHARACTER SET utf8mb4 DEFAULT '0' COMMENT '添加时间，不可更改，一般不显示给用户',
  `content` longtext CHARACTER SET utf8mb4 NOT NULL COMMENT '内容',
  `abstract` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '摘要',
  `thumb` varchar(255) NOT NULL COMMENT '缩略图',
  `alias` varchar(200) NOT NULL DEFAULT '' COMMENT '别名',
  `author` int(10) NOT NULL DEFAULT '1' COMMENT '作者',
  `cateid` int(11) DEFAULT '0' COMMENT '分类id',
  `source` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '文章来源',
  `channel` varchar(20) NOT NULL DEFAULT 'blog' COMMENT '类型',
  `view` int(11) UNSIGNED DEFAULT '0' COMMENT '浏览量',
  `comnum` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '评论数',
  `hide` enum('n','y') NOT NULL DEFAULT 'n' COMMENT '状态',
  `template` varchar(255) NOT NULL DEFAULT '' COMMENT '模板',
  `tag` text NOT NULL COMMENT '标签',
  `likes` int(10) NOT NULL DEFAULT '0' COMMENT '喜欢数',
  `short_link` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '短链接',
  `updatetime` varchar(30) CHARACTER SET utf8mb4 DEFAULT '0' COMMENT '更新时间，可更改，一般显示给用户',
  `iscomment` tinyint(1) DEFAULT '1' COMMENT '是否可评论，1允许，0不允许',
  `istop` tinyint(1) DEFAULT '0' COMMENT '置顶 1置顶； 0不置顶',
  `isrecommend` tinyint(1) DEFAULT '0' COMMENT '推荐 1推荐 0不推荐',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '文章加密',
   PRIMARY KEY (`id`),
   KEY `datetime` (`datetime`),
   KEY `author` (`author`),
   KEY `cateid` (`cateid`),
   KEY `channel` (`channel`),
   KEY `view` (`view`),
   KEY `comnum` (`comnum`),
   KEY `hide` (`hide`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tpf_blog
-- ----------------------------
INSERT INTO `tpf_blog` (`id`, `title`, `datetime`, `content`, `abstract`, `thumb`, `alias`, `author`, `cateid`, `source`, `channel`, `view`, `comnum`, `hide`, `template`, `tag`, `likes`, `short_link`, `updatetime`, `iscomment`, `istop`, `password`) VALUES
(1, '欢迎使用TP-log',UNIX_TIMESTAMP(now()), '<p>恭喜您成功安装了TP-log，这是系统自动生成的演示文章。编辑或者删除它，然后开始您的创作吧！</p>', '', '', 1, 1, 1, '', 'blog', 0, 0, 'n', '', '', 0,NULL,UNIX_TIMESTAMP(now()), 1, 0, ''),
(2, '归档', UNIX_TIMESTAMP(now()), '<p>对应模板page文件里的arcive</p>', '', '', 'archive', 1, -1, '', 'page', 0, 0, 'n', 'page_archive', '', 0, NULL,UNIX_TIMESTAMP(now()), 1, 0, ''),
(3, '邻居', UNIX_TIMESTAMP(now()), '<p style=\"text-align: center;\">链接说明</p>', '链接说明...', '', 'neighbor', 1, -1, '', 'page', '', 0, 'n', 'page_links', '', 0, NULL, UNIX_TIMESTAMP(now()), 0, 0, ''),
(4, '留言', UNIX_TIMESTAMP(now()), '<p style=\"text-align:center;\">\r\n	同志们，加油！你的评论越多，位置就越靠前。&nbsp;\r\n</p>', '', '', 'waterwall', 1, -1, '', 'page', 0, 0, 'n', 'page_message', '', 0, NULL,UNIX_TIMESTAMP(now()), 1, 0, '');


-- ----------------------------
-- Table structure for tpf_config
-- ----------------------------
DROP TABLE IF EXISTS `tpf_config`;
CREATE TABLE IF NOT EXISTS `tpf_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;


-- ----------------------------
-- Table structure for tpf_hook
-- ----------------------------
DROP TABLE IF EXISTS `tpf_hook`;
CREATE TABLE IF NOT EXISTS `tpf_hook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL COMMENT '钩子名称',
  `describe` varchar(255) DEFAULT NULL COMMENT '描述',
  `module` varchar(100) DEFAULT NULL COMMENT '插件模块',
  `update_time` varchar(30) DEFAULT '0' COMMENT '更新时间',
  `create_time` varchar(30) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;


-- ----------------------------
-- Table structure for tpf_link
-- ----------------------------
DROP TABLE IF EXISTS `tpf_link`;
CREATE TABLE IF NOT EXISTS `tpf_link` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sitename` varchar(30) NOT NULL DEFAULT '',
  `siteurl` varchar(75) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `hide` tinyint(1) NOT NULL DEFAULT '0',
  `taxis` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `sitepic` varchar(225) NOT NULL,
  `linksortid` int(10) NOT NULL,
  `target` varchar(255) CHARACTER SET utf8mb4 DEFAULT '_blank' COMMENT '友情链接打开方式',
  `datetime` varchar(30) CHARACTER SET utf8mb4 DEFAULT '0' COMMENT '添加时间，不可更改，一般不显示给用户',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tpf_link
-- ----------------------------
INSERT INTO `tpf_link` (`id`, `sitename`, `siteurl`, `description`, `hide`, `taxis`, `sitepic`, `linksortid`, `target`, `datetime`) VALUES
(1, '疯狂老司机', 'https://crazyus.net/', '努力挣钱,养家糊口,才是王道,其他都是浮云', 1, 0, 'https://crazyus.net/logos/crazyus.jpg', 1, '_blank',UNIX_TIMESTAMP(now()));



-- ----------------------------
-- Table structure for tpf_comment
-- ----------------------------
DROP TABLE IF EXISTS `tpf_comment`;
CREATE TABLE IF NOT EXISTS `tpf_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `date` bigint(20) NOT NULL,
  `poster` varchar(20) NOT NULL DEFAULT '',
  `comment` text CHARACTER SET utf8mb4 NOT NULL,
  `mail` varchar(60) NOT NULL DEFAULT '',
  `url` varchar(75) NOT NULL DEFAULT '',
  `ip` varchar(128) NOT NULL DEFAULT '',
  `display` tinyint(1) DEFAULT '1' COMMENT '是否审核 1 是 0 否',
  `useragent` varchar(255) NOT NULL DEFAULT '',
  `likes` int(10) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for tpf_mail_queue
-- ----------------------------
DROP TABLE IF EXISTS `tpf_mail_queue`;
CREATE TABLE IF NOT EXISTS `tpf_mail_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_to` varchar(120) NOT NULL,
  `mail_name` text NOT NULL,
  `mail_subject` varchar(255) NOT NULL,
  `mail_body` text NOT NULL,
  `priority` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `err_num` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `add_time` int(10) UNSIGNED NOT NULL,
  `lock_expiry` int(10) UNSIGNED NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for tpf_menu
-- ----------------------------
DROP TABLE IF EXISTS `tpf_menu`;
CREATE TABLE IF NOT EXISTS `tpf_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL COMMENT '菜单名称',
  `module` varchar(30) DEFAULT 'backend' COMMENT '模块名',
  `controller` varchar(30) DEFAULT 'Index' COMMENT '控制器',
  `action` varchar(30) DEFAULT 'index' COMMENT '默认操作',
  `type` tinyint(4) DEFAULT '1' COMMENT '菜单类型  1：权限认证+菜单；0：只作为菜单',
  `urlext` varchar(100) DEFAULT NULL COMMENT '扩展参数',
  `display` tinyint(4) DEFAULT '1' COMMENT '状态，1显示，0不显示',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `icon` varchar(50) DEFAULT NULL COMMENT '菜单图标',
  `sort` smallint(6) DEFAULT '0' COMMENT '排序ID',
  `parentid` int(11) DEFAULT '0' COMMENT '父id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tpf_menu
-- ----------------------------
INSERT INTO `tpf_menu` (`id`, `name`, `module`, `controller`, `action`, `type`, `urlext`, `display`, `remark`, `icon`, `sort`, `parentid`) VALUES
(1, '控制面板', 'backend', 'Index', 'index', 1, '', 1, '主菜单', '', 0, 0),
(2, '后台首页', 'backend', 'Index', 'index', 0, '', 1, '', 'home', 0, 1),
(3, '内容管理', 'backend', 'blog', 'index', 1, '', 1, '', 'library-books', 0, 1),
(4, '微语管理', 'backend', 'twitter', 'index', 1, '', 1, '', '', 0, 3),
(5, '文章管理', 'backend', 'blog', 'index', 1, '', 1, '', '', 0, 3),
(6, '页面管理', 'backend', 'page', 'index', 1, '', 1, '', '', 0, 3),
(7, '分类管理', 'backend', 'sort', 'index', 1, '', 1, '', '', 0, 3),
(8, '标签管理', 'backend', 'tag', 'index', 1, '', 1, '', '', 0, 3),
(9, '评论管理', 'backend', 'Comment', 'index', 1, '', 1, '', 'message-text-outline', 0, 1),
(10, '评论列表', 'backend', 'Comment', 'index', 1, '', 1, '', '', 0, 9),
(11, '评论设置', 'backend', 'Spam', 'index', 1, '', 1, '', '', 0, 9),
(12, ' 友情管理', 'backend', 'Link', 'index', 1, '', 1, '', 'lan', 0, 1),
(13, '友情链接', 'backend', 'Link', 'index', 1, '', 1, '', '', 0, 12),
(14, '友情分类', 'backend', 'Sortlink', 'index', 1, '', 1, '', '', 0, 12),
(15, '广告管理', 'backend', 'Slide', 'index', 1, '', 1, '', 'cards', 0, 1),
(16, ' 广告列表', 'backend', 'Slide', 'index', 1, '', 1, '', '', 0, 15),
(17, ' 广告分类', 'backend', 'SlideCat', 'index', 1, '', 1, '', '', 0, 15),
(18, '菜单管理', 'backend', 'Menu', 'index', 1, '', 1, '', 'locker-multiple', 0, 1),
(19, '后台菜单', 'backend', 'Menu', 'index', 1, '', 1, '', '', 0, 18),
(20, '前台菜单', 'backend', 'Nav', 'index', 1, '', 1, '', '', 0, 18),
(21, '菜单分类', 'backend', 'NavCat', 'index', 1, '', 0, '', '', 0, 18),
(22, '用户管理', 'backend', 'Member', 'index', 1, '', 1, '', 'human-male-female', 0, 1),
(23, '注册用户', 'backend', 'Member', 'index', 1, '', 1, '', '', 0, 22),
(24, '管理用户', 'backend', 'Member', 'admin', 1, '', 1, '', '', 0, 22),
(25, '角色管理', 'backend', 'Role', 'index', 1, '', 1, '', '', 0, 22),
(26, '系统设置', 'backend', 'Setting', 'index', 1, '', 1, '', 'settings', 0, 1),
(27, '网站信息', 'backend', 'Setting', 'site', 1, '', 1, '', '', 0, 26),
(28, '模板设置', 'backend', 'Template', 'index', 1, '', 1, '', '', 0, 26),
(29, '邮件配置', 'backend', 'Mail', 'index', 1, '', 1, '', '', 0, 26),
(30, '个人信息', 'backend', 'Member', 'userinfo', 1, '', 0, '', '', 0, 26),
(31, '修改密码', 'backend', 'Member', 'uppwd', 1, '', 0, '', '', 0, 26),
(32, ' 插件管理', 'backend', 'addon', 'index', 1, '', 1, '', 'cloud', 0, 1),
(33, '插件中心', 'backend', 'addon', 'addonlist', 1, '', 1, '', '', 0, 32),
(34, ' 其他操作', 'backend', 'Upgrade', 'index', 1, '', 1, '', 'lightbulb-outline', 0, 1),
(35, ' 更新系统', 'backend', 'Upgrade', 'index', 1, '', 1, '', '', 0, 34),
(36, '后台操作日志', 'backend', 'AdminLog', 'index', 1, '', 1, '', '', 0, 34);


-- ----------------------------
-- Table structure for tpf_nav
-- ----------------------------
DROP TABLE IF EXISTS `tpf_nav`;
CREATE TABLE IF NOT EXISTS `tpf_nav` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parentid` int(11) NOT NULL COMMENT '导航父 id',
  `label` varchar(255) NOT NULL COMMENT '导航标题',
  `target` varchar(50) DEFAULT NULL COMMENT '打开方式',
  `nav_type` varchar(20) DEFAULT NULL COMMENT '导航类型',
  `href` varchar(255) NOT NULL COMMENT '导航链接',
  `icon` varchar(255) NOT NULL COMMENT '导航图标',
  `display` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态，1显示，0不显示',
  `sort` int(6) DEFAULT '0' COMMENT '排序',
  `path` varchar(255) NOT NULL COMMENT '层级关系',
  `cid` int(11) NOT NULL DEFAULT '0' COMMENT '导航分类 id',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='前台导航表';

-- ----------------------------
-- Records of tpf_nav
-- ----------------------------
INSERT INTO `tpf_nav` (`id`, `parentid`, `label`, `target`, `nav_type`, `href`, `icon`, `display`, `sort`, `path`, `cid`) VALUES
(1, 0, '首页', '_self', 'href_text', '/', '', 1, 0, '0', 1),
(2, 0, '微语', '_self', 'href_text', '/t', '', 1, 1, '0', 1),
(3, 0, '归档', '_self', NULL, '/pages/archive', '', 1, 2, '', 1),
(4, 0, '邻居', '_self', NULL, '/pages/neighbor', '', 1, 3, '', 1),
(5, 0, '留言', '_self', NULL, '/pages/waterwall', '', 1, 4, '', 1),
(6, 0, '管理', '_self', 'href_text', '/backend', '', 1, 5, '0', 1);

-- ----------------------------
-- Table structure for tpf_nav_cat
-- ----------------------------
DROP TABLE IF EXISTS `tpf_nav_cat`;
CREATE TABLE IF NOT EXISTS `tpf_nav_cat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '导航分类名',
  `active` tinyint(4)  NOT NULL DEFAULT '0' COMMENT '是否为主菜单，1是，0不是',
  `remark` varchar(255) NOT NULL COMMENT '备注',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='前台导航分类表';

-- ----------------------------
-- Records of tpf_nav_cat
-- ----------------------------
INSERT INTO `tpf_nav_cat` VALUES ('1', '主导航', '1', '主导航');


-- ----------------------------
-- Table structure for tpf_reply
-- ----------------------------
DROP TABLE IF EXISTS `tpf_reply`;
CREATE TABLE IF NOT EXISTS `tpf_reply` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tid` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '微语id',
  `date` bigint(20) NOT NULL COMMENT '时间',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '回复者',
  `content` text NOT NULL COMMENT '内容',
  `hide` enum('n','y') NOT NULL DEFAULT 'n' COMMENT '显示隐藏',
  `ip` varchar(128) NOT NULL DEFAULT '' COMMENT 'IP',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='微语回复';



-- ----------------------------
-- Table structure for tpf_role
-- ----------------------------
DROP TABLE IF EXISTS `tpf_role`;
CREATE TABLE IF NOT EXISTS `tpf_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(200) DEFAULT NULL COMMENT '角色名',
  `privs` varchar(255) DEFAULT NULL COMMENT '权限列表',
  `role_describe` varchar(255) DEFAULT NULL COMMENT '权限描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='管理员角色表';


-- ----------------------------
-- Table structure for tpf_setting
-- ----------------------------
DROP TABLE IF EXISTS `tpf_setting`;
CREATE TABLE IF NOT EXISTS `tpf_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sign` varchar(30) DEFAULT NULL COMMENT '配置名',
  `options` text COMMENT '配置选项',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for tpf_slide
-- ----------------------------
DROP TABLE IF EXISTS `tpf_slide`;
CREATE TABLE `tpf_slide` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL COMMENT '幻灯片分类 id',
  `name` varchar(255) NOT NULL COMMENT '幻灯片名称',
  `pic` varchar(255) DEFAULT NULL COMMENT '幻灯片图片',
  `url` varchar(255) DEFAULT NULL COMMENT '幻灯片链接',
  `des` varchar(255) DEFAULT NULL COMMENT '幻灯片描述',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '状态，1显示，0不显示',
  `sort` int(10) DEFAULT '0' COMMENT '排序',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='幻灯片表';

-- ----------------------------
-- Table structure for tpf_slide_cat
-- ----------------------------
DROP TABLE IF EXISTS `tpf_slide_cat`;
CREATE TABLE IF NOT EXISTS `tpf_slide_cat` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '幻灯片分类名称',
  `sign` varchar(255) NOT NULL COMMENT '幻灯片分类标识',
  `remark` text COMMENT '分类备注',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '状态，1显示，0不显示',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='幻灯片分类表';

-- ----------------------------
-- Records of tpf_slide_cat
-- ----------------------------
INSERT INTO `tpf_slide_cat` (`id`, `name`, `sign`, `remark`, `status`) VALUES
(1, '默认', 'banner', '首页幻灯显示', 1);


-- ----------------------------
-- Table structure for tpf_sms_templete
-- ----------------------------
DROP TABLE IF EXISTS `tpf_sms_templete`;
CREATE TABLE IF NOT EXISTS `tpf_sms_templete` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `send_data` text COMMENT '发送的内容，以json格式存储',
  `datetime` varchar(30) DEFAULT NULL COMMENT '添加时间',
  `sms_id` int(11) DEFAULT '0' COMMENT '短信通道ID',
  `module` varchar(100) DEFAULT NULL COMMENT '模块',
  `send_scene` int(11) DEFAULT '0' COMMENT '发送场景',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tpf_sms_templete
-- ----------------------------
INSERT INTO `tpf_sms_templete` (`id`, `send_data`, `datetime`, `sms_id`, `module`, `send_scene`) VALUES
(1, '{\"subject\":\"\\u8bc4\\u8bba\\u901a\\u77e5\",\"send_scene\":\"1\",\"tpl_content\":\"<div bgcolor=\\\"#FFF\\\" style=\\\"clear:both!important;display:block!important;max-width:600px!important;margin:0 auto;padding:16px;border-width:0\\\">\\r\\n<h1 style=\\\"font-weight:400;font-size:1.35em;color:#333;margin:0 0 10px;padding-bottom:10px;border-bottom:1px solid rgba(0,0,0,.08)\\\">\\r\\n<a style=\\\"text-decoration:none;color:#333\\\" href=\\\"${site_url}\\\" target=\\\"_blank\\\">${site_name}<\\/a><\\/h1>\\r\\n<p style=\\\"font-size:14px;color:#354450;font-weight:400;margin:20px 0 0;padding:0\\\">${poster} \\u5728\\u300a${title} \\u300b\\u4e0b\\u7684\\u8bc4\\u8bba\\uff1a<\\/p>\\r\\n<p style=\\\"background-color:#efefef;padding:15px;margin:10px 0;font-size:14px;color:#354450;line-height:1.6em;font-weight:normal\\\">\\r\\n${comment} \\r\\n<\\/p>\\r\\n<p style=\\\"font-size:14px;color:#354450;line-height:1.6em;font-weight:400;margin:20px 0;padding:0\\\">\\u60a8\\u53ef\\u4ee5\\u70b9\\u51fb <a style=\\\"text-decoration:none;color:#5692bc\\\" href=\\\"${url}\\\" target=\\\"_blank\\\" >\\u67e5\\u770b\\u5b8c\\u6574\\u56de\\u590d<\\/a>\\uff0c\\u4e5f\\u6b22\\u8fce\\u60a8\\u518d\\u6b21\\u5149\\u4e34 <a style=\\\"text-decoration:none;color:#5692bc\\\" href=\\\"${site_url}\\\" target=\\\"_blank\\\" >${site_name}<\\/a>\\u3002\\u795d\\u60a8\\u5929\\u5929\\u5f00\\u5fc3\\uff01<\\/p><p style=\\\"color:#999;font-size:12px;font-weight:400;margin:0;padding:10px 0 0;border-top:1px solid rgba(0,0,0,.08)\\\">\\u672c\\u90ae\\u4ef6\\u7531\\u535a\\u5ba2\\u8bc4\\u8bba\\u7cfb\\u7edf\\u81ea\\u52a8\\u53d1\\u51fa\\uff0c\\u610f\\u5728\\u65b0\\u8bc4\\u8bba\\u901a\\u77e5\\u3002\\u8bf7\\u52ff\\u76f4\\u63a5\\u56de\\u590d\\uff0c<wbr>\\u8c22\\u8c22\\u3002<\\/p>\\r\\n<\\/div>\"}', '1592731801', 0, 'mail', 1),
(2, '{\"subject\":\"\\u8bc4\\u8bba\\u56de\\u590d\",\"send_scene\":\"2\",\"tpl_content\":\"<div bgcolor=\\\"#FFF\\\" style=\\\"clear:both!important;display:block!important;max-width:600px!important;margin:0 auto;padding:16px;border-width:0\\\">\\r\\n<h1 style=\\\"font-weight:400;font-size:1.35em;color:#333;margin:0 0 10px;padding-bottom:10px;border-bottom:1px solid rgba(0,0,0,.08)\\\">\\r\\n<a style=\\\"text-decoration:none;color:#333\\\" href=\\\"${site_url}\\\" target=\\\"_blank\\\">${site_name}<\\/a><\\/h1>\\r\\n<p style=\\\"font-size:14px;color:#354450;font-weight:400;margin:20px 0 0;padding:0\\\">${author}\\uff0c\\u60a8\\u597d\\uff01\\u60a8\\u5728\\u300a${title}\\u300b\\u4e0b\\u7684\\u8bc4\\u8bba\\uff1a<\\/p>\\r\\n<p style=\\\"background-color:#efefef;padding:15px;margin:10px 0;font-size:14px;color:#354450;line-height:1.6em;font-weight:normal\\\">\\r\\n${reply}<\\/p>\\r\\n<p style=\\\"font-size:14px;color:#354450;font-weight:400;margin:20px 0 0;padding:0\\\">${poster}\\u7ed9\\u60a8\\u56de\\u590d\\u5982\\u4e0b\\uff1a<\\/p>\\r\\n<p style=\\\"background-color:#efefef;padding:15px;margin:10px 0;font-size:14px;color:#354450;line-height:1.6em;font-weight:normal\\\">${comment} <\\/p>\\r\\n<p style=\\\"font-size:14px;color:#354450;line-height:1.6em;font-weight:400;margin:20px 0;padding:0\\\">\\u60a8\\u53ef\\u4ee5\\u70b9\\u51fb <a style=\\\"text-decoration:none;color:#5692bc\\\" href=\\\"${url}\\\" target=\\\"_blank\\\" >\\u67e5\\u770b\\u5b8c\\u6574\\u56de\\u590d<\\/a>\\uff0c\\u4e5f\\u6b22\\u8fce\\u60a8\\u518d\\u6b21\\u5149\\u4e34 <a style=\\\"text-decoration:none;color:#5692bc\\\" href=\\\"${site_url}\\\" target=\\\"_blank\\\" >${site_name}<\\/a>\\u3002\\u795d\\u60a8\\u5929\\u5929\\u5f00\\u5fc3\\uff01<\\/p><p style=\\\"color:#999;font-size:12px;font-weight:400;margin:0;padding:10px 0 0;border-top:1px solid rgba(0,0,0,.08)\\\">\\u672c\\u90ae\\u4ef6\\u7531\\u535a\\u5ba2\\u8bc4\\u8bba\\u7cfb\\u7edf\\u81ea\\u52a8\\u53d1\\u51fa\\uff0c\\u610f\\u5728\\u65b0\\u8bc4\\u8bba\\u901a\\u77e5\\u3002\\u8bf7\\u52ff\\u76f4\\u63a5\\u56de\\u590d\\uff0c<wbr>\\u8c22\\u8c22\\u3002<\\/p>\\r\\n<\\/div>\"}', '1592787645', 0, 'mail', 2),
(3, '{\"subject\":\"\\u81ea\\u4e3b\\u94fe\\u63a5\\u7533\\u8bf7\\u901a\\u77e5\",\"send_scene\":\"3\",\"tpl_content\":\"<div bgcolor=\\\"#FFF\\\" style=\\\"clear:both!important;display:block!important;max-width:600px!important;margin:0 auto;padding:16px;border-width:0\\\">\\r\\n<h1 style=\\\"font-weight:400;font-size:1.35em;color:#333;margin:0 0 10px;padding-bottom:10px;border-bottom:1px solid rgba(0,0,0,.08)\\\">\\r\\n<a style=\\\"text-decoration:none;color:#333\\\" href=\\\"${site_url}\\\" target=\\\"_blank\\\">${site_name}<\\/a><\\/h1>\\r\\n<p style=\\\"font-size:14px;color:#354450;font-weight:400;margin:20px 0 0;padding:0\\\">\\u6709\\u4eba\\u81ea\\u4e3b\\u7533\\u8bf7\\u94fe\\u63a5\\uff1a<\\/p>\\r\\n<p style=\\\"background-color:#efefef;padding:15px;margin:10px 0;font-size:14px;color:#354450;line-height:1.6em;font-weight:normal\\\">\\r\\n\\u7f51\\u7ad9\\uff1a${sitename} <br\\/>\\r\\n\\u94fe\\u63a5\\uff1a${siteurl}  <br\\/>\\r\\n\\u56fe\\u6807\\uff1a${sitepic}  <br\\/>\\r\\n\\u63cf\\u8ff0\\uff1a${description} \\r\\n<\\/p>\\r\\n<p style=\\\"color:#999;font-size:12px;font-weight:400;margin:0;padding:10px 0 0;border-top:1px solid rgba(0,0,0,.08)\\\">\\u672c\\u90ae\\u4ef6\\u7531\\u535a\\u5ba2\\u7cfb\\u7edf\\u81ea\\u52a8\\u53d1\\u51fa\\u3002\\u8bf7\\u52ff\\u76f4\\u63a5\\u56de\\u590d\\uff0c<wbr>\\u8c22\\u8c22\\u3002<\\/p>\\r\\n<\\/div>\"}', '1595008719', 0, 'mail', 6);


-- ----------------------------
-- Table structure for tpf_sort
-- ----------------------------
DROP TABLE IF EXISTS `tpf_sort`;
CREATE TABLE IF NOT EXISTS `tpf_sort` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(200) NOT NULL DEFAULT '',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `parentid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `isnav` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否是导航 0否 1是',
  `url` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '链接地址',
  `display` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否显示 0：不显示 1显示',
  `channel_id` smallint(6) NOT NULL DEFAULT '1' COMMENT '内容模型ID',
   PRIMARY KEY (`id`)  
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tpf_sort
-- ----------------------------
INSERT INTO `tpf_sort` (`id`, `title`, `alias`, `sort`, `parentid`, `isnav`, `url`, `display`, `channel_id`) VALUES
(1, '默认分类', 'default', 1, 0, 0, NULL, 1, 1);


-- ----------------------------
-- Table structure for tpf_sortlink
-- ----------------------------
DROP TABLE IF EXISTS `tpf_sortlink`;
CREATE TABLE IF NOT EXISTS `tpf_sortlink` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `taxis` int(10) UNSIGNED NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tpf_sortlink
-- ----------------------------
INSERT INTO `tpf_sortlink` (`id`, `name`, `taxis`) VALUES
(1, '默认分类', 0);


-- ----------------------------
-- Table structure for tpf_tag
-- ----------------------------
DROP TABLE IF EXISTS `tpf_tag`;
CREATE TABLE IF NOT EXISTS `tpf_tag` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tagname` varchar(60) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `gid` text,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;


-- ----------------------------
-- Table structure for tpf_twitter
-- ----------------------------
DROP TABLE IF EXISTS `tpf_twitter`;
CREATE TABLE IF NOT EXISTS `tpf_twitter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text CHARACTER SET utf8mb4 NOT NULL,
  `likes` int(10) NOT NULL DEFAULT '0',
  `img` text,
  `author` int(10) NOT NULL DEFAULT '1',
  `date` varchar(30) CHARACTER SET utf8mb4 DEFAULT '0',
  `useragent` varchar(255) DEFAULT NULL,
  `replynum` mediumint(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tpf_sortlink
-- ----------------------------
INSERT INTO `tpf_twitter` (`id`, `content`, `likes`, `img`, `author`, `date`, `useragent`, `replynum`) VALUES
(1, '使用微语记录您身边的新鲜事', 0, '', 1,UNIX_TIMESTAMP(now()), NULL, 0);



-- ----------------------------
-- Table structure for tpf_user
-- ----------------------------
DROP TABLE IF EXISTS `tpf_user`;
CREATE TABLE IF NOT EXISTS `tpf_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(64) DEFAULT NULL,
  `pay_password` varchar(64) DEFAULT NULL COMMENT '支付密码',
  `headimg` varchar(255) DEFAULT NULL COMMENT '用户头像',
  `nickname` varchar(30) DEFAULT NULL COMMENT '昵称',
  `gender` smallint(6) DEFAULT '0' COMMENT '性别；0：保密，1：男；2：女',
  `type` smallint(6) DEFAULT '0' COMMENT '用户类型 0普通用户  1管理员',
  `grade` smallint(6) DEFAULT '0' COMMENT '用户等级 0普通用户',
  `level` smallint(6) DEFAULT '0' COMMENT '用户等级',
  `parent_id` int(11) DEFAULT '0' COMMENT '上级ID',
  `url` varchar(200) DEFAULT NULL COMMENT '用户个人网站',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `email_is_validate` tinyint(4) DEFAULT '0' COMMENT '邮箱是否验证  0：未验证  1：已验证',
  `city_id` int(11) DEFAULT '0' COMMENT '城市ID',
  `signature` varchar(255) DEFAULT NULL COMMENT '个性签名',
  `last_login_ip` varchar(20) DEFAULT NULL COMMENT '最后登录ip',
  `last_login_time` varchar(30) DEFAULT NULL,
  `create_time` varchar(30) DEFAULT '0' COMMENT '会员创建时间',
  `isban` tinyint(4) DEFAULT '0' COMMENT '是否被拉黑  0正常  1拉黑',
  `privs` varchar(255) DEFAULT NULL COMMENT '用户权限列表',
  `role_id` int(11) DEFAULT '0' COMMENT '权限ID',
  `openid` varchar(200) DEFAULT NULL COMMENT '开放平台ID',
  `token` varchar(100) DEFAULT NULL COMMENT '第三方登录标识',
  `login_way` tinyint(4) DEFAULT '0' COMMENT '登录方式  0：账号密码   1：第三方快捷登录',
  `integral` int(11) DEFAULT '0' COMMENT '积分',
  `money` decimal(4,2) DEFAULT '0.00' COMMENT '余额',
  `profit` decimal(10,2) DEFAULT '0.00' COMMENT '佣金',
  `qq_login_openid` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;





