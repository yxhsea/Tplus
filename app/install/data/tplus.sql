/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : tplus

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2017-08-01 15:03:37
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tplus_addons
-- ----------------------------
DROP TABLE IF EXISTS `tplus_addons`;
CREATE TABLE `tplus_addons` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL COMMENT '插件名或标识',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '中文名',
  `description` text COMMENT '插件描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `config` text COMMENT '配置',
  `author` varchar(40) DEFAULT '' COMMENT '作者',
  `version` varchar(20) DEFAULT '' COMMENT '版本号',
  `create_time` varchar(50) NOT NULL DEFAULT '0' COMMENT '安装时间',
  `webvisit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否允许对外访问',
  `has_adminlist` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有后台列表',
  `mid` int(11) DEFAULT '0' COMMENT '在菜单中的ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='插件表';

-- ----------------------------
-- Records of tplus_addons
-- ----------------------------

-- ----------------------------
-- Table structure for tplus_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `tplus_auth_group`;
CREATE TABLE `tplus_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户组id,自增主键',
  `module` varchar(20) NOT NULL COMMENT '用户组所属模块',
  `type` tinyint(4) NOT NULL COMMENT '组类型',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '用户组中文名称',
  `description` varchar(80) NOT NULL DEFAULT '' COMMENT '描述信息',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户组状态：为1正常，为0禁用,-1为删除',
  `rules` varchar(500) NOT NULL DEFAULT '' COMMENT '用户组拥有的规则id，多个规则 , 隔开',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Auth分组';

-- ----------------------------
-- Records of tplus_auth_group
-- ----------------------------
INSERT INTO `tplus_auth_group` VALUES ('1', 'System', '1', '默认用户组', '默认分组描述', '1', '1,2,3,4,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,26');

-- ----------------------------
-- Table structure for tplus_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `tplus_auth_rule`;
CREATE TABLE `tplus_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id,自增主键',
  `module` varchar(20) NOT NULL COMMENT '规则所属module',
  `type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1-url;2-主菜单',
  `name` char(80) NOT NULL DEFAULT '' COMMENT '规则唯一英文标识',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '规则中文描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效(0:无效,1:有效)',
  `condition` varchar(300) NOT NULL DEFAULT '' COMMENT '规则附加条件',
  PRIMARY KEY (`id`),
  KEY `module` (`module`,`status`,`type`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COMMENT='Auth菜单详细';

-- ----------------------------
-- Records of tplus_auth_rule
-- ----------------------------
INSERT INTO `tplus_auth_rule` VALUES ('1', 'system', '2', 'System/Config/group', '系统', '1', '');
INSERT INTO `tplus_auth_rule` VALUES ('2', 'system', '1', 'System/Config/group', '配置设置', '1', '');
INSERT INTO `tplus_auth_rule` VALUES ('3', 'system', '1', 'System/Config/updateConfig', '新增修改', '1', '');
INSERT INTO `tplus_auth_rule` VALUES ('4', 'system', '1', 'System/Config/deleteData', '删除配置', '1', '');
INSERT INTO `tplus_auth_rule` VALUES ('5', 'system', '1', 'System/Addons/index', '插件管理', '1', '');
INSERT INTO `tplus_auth_rule` VALUES ('6', 'system', '1', 'System/Addons/hooks', '钩子管理', '1', '');
INSERT INTO `tplus_auth_rule` VALUES ('7', 'system', '1', 'System/User/index', '管理员列表', '1', '');
INSERT INTO `tplus_auth_rule` VALUES ('8', 'system', '1', 'System/User/AuthManager', '权限管理', '1', '');
INSERT INTO `tplus_auth_rule` VALUES ('9', 'system', '1', 'System/Menu/updateMenu', '新增修改', '1', '');
INSERT INTO `tplus_auth_rule` VALUES ('10', 'system', '1', 'System/Menu/deleteData', '删除菜单', '1', '');
INSERT INTO `tplus_auth_rule` VALUES ('11', 'system', '1', 'System/User/adduser', '新增管理员', '1', '');
INSERT INTO `tplus_auth_rule` VALUES ('12', 'system', '1', 'System/User/updateuser', '修改管理员', '1', '');
INSERT INTO `tplus_auth_rule` VALUES ('13', 'system', '1', 'System/User/deletedata', '删除管理员', '1', '');
INSERT INTO `tplus_auth_rule` VALUES ('14', 'system', '1', 'System/User/addauthmanager', '新增修改分组', '1', '');
INSERT INTO `tplus_auth_rule` VALUES ('15', 'system', '1', 'System/User/deletedata/module/auth_group', '删除分组', '1', '');
INSERT INTO `tplus_auth_rule` VALUES ('16', 'system', '1', 'System/User/power_access', '访问授权', '1', '');
INSERT INTO `tplus_auth_rule` VALUES ('17', 'system', '1', 'System/User/power_category', '分类授权', '1', '');
INSERT INTO `tplus_auth_rule` VALUES ('18', 'system', '1', 'System/User/power_user', '成员授权', '1', '');
INSERT INTO `tplus_auth_rule` VALUES ('19', 'system', '2', 'System/User/index', '管理员', '1', '');
INSERT INTO `tplus_auth_rule` VALUES ('20', 'system', '1', 'System/Config/index', '配置管理', '1', '');
INSERT INTO `tplus_auth_rule` VALUES ('21', 'system', '2', 'System/Addons/index', '扩展', '1', '');
INSERT INTO `tplus_auth_rule` VALUES ('22', 'system', '1', 'System/Menu/index', '菜单管理', '1', '');
INSERT INTO `tplus_auth_rule` VALUES ('23', 'system', '1', 'System/Database/index/type/export', '备份数据库', '1', '');
INSERT INTO `tplus_auth_rule` VALUES ('24', 'system', '1', 'System/Database/index/type/import', '还原数据库', '1', '');

-- ----------------------------
-- Table structure for tplus_config
-- ----------------------------
DROP TABLE IF EXISTS `tplus_config`;
CREATE TABLE `tplus_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '配置名称',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置类型',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '配置说明',
  `group` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置分组',
  `extra` varchar(255) NOT NULL DEFAULT '' COMMENT '配置值',
  `remark` varchar(100) NOT NULL COMMENT '配置说明',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `value` text NOT NULL COMMENT '配置值',
  `sort` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_name` (`name`),
  KEY `type` (`type`),
  KEY `group` (`group`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='配置';

-- ----------------------------
-- Records of tplus_config
-- ----------------------------
INSERT INTO `tplus_config` VALUES ('1', 'CONFIG_TYPE_LIST', '3', '配置类型列表', '1', '', '主要用于数据解析和页面表单的生成', '1378898976', '1379235348', '1', '0:数字\r\n1:字符\r\n2:文本\r\n3:数组\r\n4:枚举', '21');
INSERT INTO `tplus_config` VALUES ('2', 'CONFIG_GROUP_LIST', '3', '配置分组', '1', '', '配置分组', '1379228036', '1470042875', '1', '1:系统', '27');
INSERT INTO `tplus_config` VALUES ('3', 'HOOKS_TYPE', '3', '钩子的类型', '1', '', '类型 1-用于扩展显示内容，2-用于扩展业务处理', '1379313397', '1379313407', '1', '1:视图\r\n2:控制器', '29');
INSERT INTO `tplus_config` VALUES ('4', 'AUTH_CONFIG', '3', 'Auth配置', '1', '', '自定义Auth.class.php类配置', '1379409310', '1379409564', '1', 'AUTH_ON:1\r\nAUTH_TYPE:2', '32');
INSERT INTO `tplus_config` VALUES ('5', 'DATA_BACKUP_PATH', '1', '数据库备份根路径', '1', '', '路径必须以 / 结尾', '1381482411', '1381482411', '1', './data/', '28');
INSERT INTO `tplus_config` VALUES ('6', 'DATA_BACKUP_PART_SIZE', '0', '数据库备份卷大小', '1', '', '该值用于限制压缩后的分卷最大长度。单位：B；建议设置20M', '1381482488', '1381729564', '1', '20971520', '30');
INSERT INTO `tplus_config` VALUES ('7', 'DATA_BACKUP_COMPRESS', '4', '数据库备份文件是否启用压缩', '1', '0:不压缩\r\n1:启用压缩', '压缩备份文件需要PHP环境支持gzopen,gzwrite函数', '1381713345', '1381729544', '1', '1', '34');
INSERT INTO `tplus_config` VALUES ('8', 'DATA_BACKUP_COMPRESS_LEVEL', '4', '数据库备份文件压缩级别', '1', '1:普通\r\n4:一般\r\n9:最高', '数据库备份文件的压缩级别，该配置在开启压缩时生效', '1381713408', '1381713408', '1', '9', '37');
INSERT INTO `tplus_config` VALUES ('9', 'DEVELOP_MODE', '4', '开启开发者模式', '1', '0:关闭\r\n1:开启', '是否开启开发者模式', '1383105995', '1383291877', '1', '1', '38');
INSERT INTO `tplus_config` VALUES ('10', 'ADMIN_ALLOW_IP', '2', '后台允许访问IP', '1', '', '多个用逗号分隔，如果不配置表示不限制IP访问', '1387165454', '1387165553', '1', '', '39');

-- ----------------------------
-- Table structure for tplus_hooks
-- ----------------------------
DROP TABLE IF EXISTS `tplus_hooks`;
CREATE TABLE `tplus_hooks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL DEFAULT '' COMMENT '钩子名称',
  `description` text NOT NULL COMMENT '描述',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `addons` varchar(255) NOT NULL DEFAULT '' COMMENT '钩子挂载的插件 ''，''分割',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='插件钩子';

-- ----------------------------
-- Records of tplus_hooks
-- ----------------------------

-- ----------------------------
-- Table structure for tplus_menu
-- ----------------------------
DROP TABLE IF EXISTS `tplus_menu`;
CREATE TABLE `tplus_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文档ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `url` char(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `param` char(255) DEFAULT '' COMMENT '参数',
  `hide` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `tip` varchar(255) NOT NULL DEFAULT '' COMMENT '提示',
  `group` varchar(50) DEFAULT '' COMMENT '分组',
  `is_dev` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否仅开发者模式可见',
  `font_class` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COMMENT='菜单';

-- ----------------------------
-- Records of tplus_menu
-- ----------------------------
INSERT INTO `tplus_menu` VALUES ('3', '扩展', '0', '2', 'Addons/index', '', '0', '', '', '0', 'plus-square');
INSERT INTO `tplus_menu` VALUES ('2', '管理员', '0', '1', 'User/index', '', '0', '', '', '0', 'users');
INSERT INTO `tplus_menu` VALUES ('1', '系统', '0', '0', 'Config/group', '', '0', '', '', '0', 'cogs');
INSERT INTO `tplus_menu` VALUES ('4', '配置设置', '1', '0', 'Config/group', '', '0', '', '', '0', 'cog');
INSERT INTO `tplus_menu` VALUES ('5', '配置管理', '1', '1', 'Config/index', '', '0', '', '', '0', 'bars');
INSERT INTO `tplus_menu` VALUES ('6', '菜单管理', '1', '2', 'Menu/index', '', '0', '', '', '0', 'bars');
INSERT INTO `tplus_menu` VALUES ('13', '新增修改', '5', '0', 'Config/updateConfig', '', '0', '', '', '0', '');
INSERT INTO `tplus_menu` VALUES ('14', '删除配置', '5', '0', 'Config/deleteData', '', '0', '', '', '0', '');
INSERT INTO `tplus_menu` VALUES ('7', '备份数据库', '1', '3', 'Database/index', 'type=export', '0', '', '', '0', 'database');
INSERT INTO `tplus_menu` VALUES ('8', '还原数据库', '1', '4', 'Database/index', 'type=import', '0', '', '', '0', 'database');
INSERT INTO `tplus_menu` VALUES ('11', '插件管理', '3', '0', 'Addons/index', '', '0', '', '', '0', 'plug');
INSERT INTO `tplus_menu` VALUES ('12', '钩子管理', '3', '0', 'Addons/hooks', '', '0', '', '', '0', 'plug');
INSERT INTO `tplus_menu` VALUES ('9', '管理员列表', '2', '0', 'User/index', '', '0', '', '', '0', 'user');
INSERT INTO `tplus_menu` VALUES ('10', '权限管理', '2', '0', 'User/AuthManager', '', '0', '', '', '0', 'user-secret');
INSERT INTO `tplus_menu` VALUES ('15', '新增修改', '6', '0', 'Menu/updateMenu', '', '0', '', '', '0', '');
INSERT INTO `tplus_menu` VALUES ('16', '删除菜单', '6', '0', 'Menu/deleteData', '', '0', '', '', '0', '');
INSERT INTO `tplus_menu` VALUES ('17', '新增管理员', '9', '0', 'User/adduser', '', '0', '', '', '0', '');
INSERT INTO `tplus_menu` VALUES ('18', '修改管理员', '9', '0', 'User/updateuser', '', '0', '', '', '0', '');
INSERT INTO `tplus_menu` VALUES ('19', '删除管理员', '9', '0', 'User/deletedata', '', '0', '', '', '0', '');
INSERT INTO `tplus_menu` VALUES ('20', '新增修改分组', '10', '0', 'User/addauthmanager', '', '0', '', '', '0', '');
INSERT INTO `tplus_menu` VALUES ('21', '删除分组', '10', '0', 'User/deletedata/module/auth_group', '', '0', '', '', '0', '');
INSERT INTO `tplus_menu` VALUES ('22', '访问授权', '10', '0', 'User/power_access', '', '0', '', '', '0', '');
INSERT INTO `tplus_menu` VALUES ('23', '分类授权', '10', '0', 'User/power_category', '', '0', '', '', '0', '');
INSERT INTO `tplus_menu` VALUES ('24', '成员授权', '10', '0', 'User/power_user', '', '0', '', '', '0', '');
INSERT INTO `tplus_menu` VALUES ('26', '测试中心', '3', '0', 'home/Index/test', '', '0', '', '', '0', 'bars');

-- ----------------------------
-- Table structure for tplus_picture
-- ----------------------------
DROP TABLE IF EXISTS `tplus_picture`;
CREATE TABLE `tplus_picture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id自增',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '路径',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '图片链接',
  `md5` char(32) NOT NULL DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='图片表';

-- ----------------------------
-- Records of tplus_picture
-- ----------------------------

-- ----------------------------
-- Table structure for tplus_power_user
-- ----------------------------
DROP TABLE IF EXISTS `tplus_power_user`;
CREATE TABLE `tplus_power_user` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `group_id` mediumint(8) unsigned NOT NULL COMMENT '用户组id',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='成员授权';

-- ----------------------------
-- Records of tplus_power_user
-- ----------------------------

-- ----------------------------
-- Table structure for tplus_user
-- ----------------------------
DROP TABLE IF EXISTS `tplus_user`;
CREATE TABLE `tplus_user` (
  `id` int(15) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` char(16) NOT NULL COMMENT '用户名',
  `password` char(32) NOT NULL COMMENT '密码',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `reg_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '用户状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='后台管理员表';