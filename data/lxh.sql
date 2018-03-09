/*
Navicat MySQL Data Transfer

Source Server         : locahost
Source Server Version : 50714
Source Host           : localhost:3306
Source Database       : lxh

Target Server Type    : MYSQL
Target Server Version : 50714
File Encoding         : 65001

Date: 2018-03-09 19:34:36
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for abilities
-- ----------------------------
DROP TABLE IF EXISTS `abilities`;
CREATE TABLE `abilities` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '权限唯一标识，只能填英文',
  `title` varchar(255) NOT NULL COMMENT '权限名称',
  `created_at` int(11) NOT NULL,
  `modified_at` int(11) unsigned NOT NULL DEFAULT '0',
  `created_by_id` int(11) unsigned NOT NULL DEFAULT '0',
  `forbidden` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0允许的权限，1禁止的权限',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1后台权限，2前台权限',
  `comment` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of abilities
-- ----------------------------
INSERT INTO `abilities` VALUES ('1', 'user.read', '用户查看', '1515207448', '1515207550', '1', '0', '1', '查看用户列表');
INSERT INTO `abilities` VALUES ('2', 'user.update', '用户编辑', '1515207533', '1515671037', '1', '0', '1', '');
INSERT INTO `abilities` VALUES ('3', 'user.add', '用户新增', '1515207584', '0', '1', '0', '1', '');
INSERT INTO `abilities` VALUES ('4', 'user.delete', '用户删除', '1515207646', '0', '1', '0', '1', '');
INSERT INTO `abilities` VALUES ('5', 'menu.read', '菜单查看', '1515207863', '0', '1', '0', '1', '');
INSERT INTO `abilities` VALUES ('6', 'menu.add', '菜单新增', '1515207911', '0', '1', '0', '1', '');
INSERT INTO `abilities` VALUES ('7', 'menu.edit', '菜单编辑', '1515207928', '0', '1', '0', '1', '');
INSERT INTO `abilities` VALUES ('8', 'menu.delete', '菜单删除', '1515207953', '0', '1', '0', '1', '');
INSERT INTO `abilities` VALUES ('9', 'system.manager', '系统管理', '1515213159', '1515760393', '1', '0', '1', '系统设置菜单进入权限');
INSERT INTO `abilities` VALUES ('17', 'admin.read', '管理员查看', '1515230219', '1517881473', '0', '0', '1', '测试123');
INSERT INTO `abilities` VALUES ('30', 'language.read', '语言包查看', '1515760352', '1515760370', '1', '0', '1', '');
INSERT INTO `abilities` VALUES ('23', 'user.manager', '用户管理', '1515758278', '1515758633', '1', '0', '1', '');
INSERT INTO `abilities` VALUES ('24', 'product.manager', '产品系统', '1515758654', '1518144506', '1', '0', '1', '123');
INSERT INTO `abilities` VALUES ('25', 'product.read', '产品查看', '1515758687', '1515758698', '1', '0', '1', '');
INSERT INTO `abilities` VALUES ('26', 'permissions.manager', '权限管理', '1515758747', '1515758758', '1', '0', '1', '');
INSERT INTO `abilities` VALUES ('27', 'role.read', '角色查看', '1515758787', '0', '1', '0', '1', '');
INSERT INTO `abilities` VALUES ('28', 'ability.read', 'Ability.read', '1515758949', '1515760282', '1', '0', '1', '');
INSERT INTO `abilities` VALUES ('29', 'create.module', 'Create.module', '1515759578', '0', '1', '0', '1', '');
INSERT INTO `abilities` VALUES ('31', 'system.setting', 'System.setting', '1515760412', '0', '1', '0', '1', '');

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `password` varchar(150) NOT NULL,
  `email` varchar(50) NOT NULL DEFAULT '',
  `mobile` varchar(30) NOT NULL DEFAULT '',
  `first_name` varchar(20) NOT NULL DEFAULT '',
  `last_name` varchar(20) NOT NULL DEFAULT '',
  `avatar` varchar(100) NOT NULL DEFAULT '',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0未知，1男，2女',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  `modified_at` int(11) NOT NULL DEFAULT '0',
  `last_login_ip` char(15) NOT NULL DEFAULT '',
  `last_login_time` int(11) unsigned NOT NULL DEFAULT '0',
  `reg_ip` char(15) NOT NULL DEFAULT '',
  `is_admin` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1激活，0禁用',
  `created_by_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES ('1', 'admin', '$2y$10$IK.HGNDMOV9LYHIG7jMxb.0iEV85SSkf6Lv8GN9aaAuAIFbsVnaSS', '841324345@qq.com', '', 'J', 'qh', '20180306/1b4267fe836249627de672e0d1795350.jpeg', '1', '0', '1499568986', '1520325431', '127.0.0.1', '0', '127.0.0.1', '1', '1', '0');
INSERT INTO `admin` VALUES ('2', 'haha', '$2y$10$kKdgtSsVsZrP4X2RzsHhVuUmqdgJhSmp2AmA/iKp00XeYkpgnpP5q', '', '', '', '', '', '0', '0', '0', '1515761954', '', '0', '', '0', '1', '0');
INSERT INTO `admin` VALUES ('3', 'test', '$2y$10$anUpfaFdxve9b9mzmkCdfOEgoDpEss1glWk6.T5M2JnMnMh/3XvOO', '87@qq.com', '13334', '', '', '', '1', '0', '1517654504', '1519780738', '', '0', '', '0', '1', '1');
INSERT INTO `admin` VALUES ('5', 'test123', '$2y$10$BGcwPLnwqqxm9O5WzrybweNJRFQM0l88msP6FzkM5JqnZmL6XdGya', '841324345@qq.com', '13076814390', '', '', '', '1', '0', '1519452159', '1520325267', '', '0', '', '0', '1', '1');

-- ----------------------------
-- Table structure for admin_operation_log
-- ----------------------------
DROP TABLE IF EXISTS `admin_operation_log`;
CREATE TABLE `admin_operation_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `path` varchar(101) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 GET, 2POST, 3PUT, 4DELETE, 5OPTION',
  `ip` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `input` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  `table` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `admin_operation_log_user_id_index` (`admin_id`),
  KEY `table` (`table`) USING BTREE,
  KEY `created_at` (`created_at`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of admin_operation_log
-- ----------------------------
INSERT INTO `admin_operation_log` VALUES ('1', '1', '/test', '1', '127.0.0.1', '{ \"name\": \"Administrator\", \"password\": \"$2y$10$JWwBpjZmoMqD7PPkjUotIu22dsCIT9mh.ttxH6Lop3u4pogvgqKYO\", \"password_confirmation\": \"$2y$10$JWwBpjZmoMqD7PPkjUotIu22dsCIT9mh.ttxH6Lop3u4pogvgqKYO\", \"_token\": \"qnvNxqktM7WQagJVZ2MG8LSTEkjRDr7N8rgbwIn3\", \"_method\": \"PUT\", \"_previous_\": \"http:\\/\\/www.l.com\\/admin\" }', '0', '');

-- ----------------------------
-- Table structure for assigned_abilities
-- ----------------------------
DROP TABLE IF EXISTS `assigned_abilities`;
CREATE TABLE `assigned_abilities` (
  `ability_id` int(11) unsigned NOT NULL,
  `entity_id` int(11) unsigned NOT NULL,
  `entity_type` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '1用户，2角色'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of assigned_abilities
-- ----------------------------
INSERT INTO `assigned_abilities` VALUES ('24', '33', '2');
INSERT INTO `assigned_abilities` VALUES ('23', '33', '2');
INSERT INTO `assigned_abilities` VALUES ('17', '33', '2');
INSERT INTO `assigned_abilities` VALUES ('7', '33', '2');
INSERT INTO `assigned_abilities` VALUES ('5', '33', '2');
INSERT INTO `assigned_abilities` VALUES ('4', '33', '2');
INSERT INTO `assigned_abilities` VALUES ('17', '36', '2');
INSERT INTO `assigned_abilities` VALUES ('3', '33', '2');
INSERT INTO `assigned_abilities` VALUES ('2', '33', '2');
INSERT INTO `assigned_abilities` VALUES ('1', '33', '2');
INSERT INTO `assigned_abilities` VALUES ('25', '33', '2');

-- ----------------------------
-- Table structure for assigned_roles
-- ----------------------------
DROP TABLE IF EXISTS `assigned_roles`;
CREATE TABLE `assigned_roles` (
  `role_id` int(11) unsigned NOT NULL,
  `entity_id` int(11) unsigned NOT NULL,
  `entity_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1用户',
  UNIQUE KEY `role_id` (`role_id`,`entity_id`,`entity_type`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of assigned_roles
-- ----------------------------
INSERT INTO `assigned_roles` VALUES ('33', '2', '1');
INSERT INTO `assigned_roles` VALUES ('33', '3', '1');
INSERT INTO `assigned_roles` VALUES ('33', '5', '1');
INSERT INTO `assigned_roles` VALUES ('36', '3', '1');

-- ----------------------------
-- Table structure for category
-- ----------------------------
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `desc` varchar(255) NOT NULL DEFAULT '',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  `created_by_id` int(11) unsigned NOT NULL DEFAULT '0',
  `modified_at` int(11) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `parent` int(11) unsigned NOT NULL DEFAULT '0',
  `count` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of category
-- ----------------------------
INSERT INTO `category` VALUES ('1', '测试', 'fgfgf', '1508243598', '1', '1508243933', '0', '0', '0');

-- ----------------------------
-- Table structure for category_meta
-- ----------------------------
DROP TABLE IF EXISTS `category_meta`;
CREATE TABLE `category_meta` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) unsigned NOT NULL,
  `key` varchar(255) NOT NULL DEFAULT '',
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`) USING BTREE,
  KEY `key` (`key`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of category_meta
-- ----------------------------

-- ----------------------------
-- Table structure for comment
-- ----------------------------
DROP TABLE IF EXISTS `comment`;
CREATE TABLE `comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) unsigned NOT NULL,
  `author` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(80) NOT NULL DEFAULT '',
  `url` varchar(200) NOT NULL DEFAULT '',
  `ip` varchar(20) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '0未审核，1审核通过，2驳回',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父评论id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '登录用户id',
  `admin_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '管理员id',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  `audited_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '审核时间',
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`) USING BTREE,
  KEY `status` (`status`,`audited_at`) USING BTREE,
  KEY `parent_id` (`parent_id`) USING BTREE,
  KEY `email` (`email`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of comment
-- ----------------------------

-- ----------------------------
-- Table structure for comment_meta
-- ----------------------------
DROP TABLE IF EXISTS `comment_meta`;
CREATE TABLE `comment_meta` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) unsigned NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `comment_id` (`comment_id`) USING BTREE,
  KEY `key` (`key`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of comment_meta
-- ----------------------------

-- ----------------------------
-- Table structure for menu
-- ----------------------------
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `icon` varchar(100) NOT NULL DEFAULT '',
  `show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1显示，0不显示',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上级菜单id',
  `layer` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '菜单层级',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  `created_by_id` int(11) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1普通菜单，2系统菜单，不能被删除或修改',
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序权重值，值越小排序越靠前',
  `ability_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '权限id',
  `route` varchar(100) NOT NULL DEFAULT '' COMMENT '路由',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of menu
-- ----------------------------
INSERT INTO `menu` VALUES ('1', 'Menu management', 'zmdi zmdi-menu', '1', '13', '1', '0', '1500180853', '1', '2', '0', '5', '/admin/menu/action/list');
INSERT INTO `menu` VALUES ('13', 'System', 'fa fa-gears', '1', '0', '1', '0', '1500466810', '1', '1', '2', '9', '');
INSERT INTO `menu` VALUES ('14', 'Making modules', 'zmdi zmdi-widgets', '1', '13', '1', '0', '1500467096', '1', '1', '0', '29', '/admin/system/action/make-modules');
INSERT INTO `menu` VALUES ('45', 'Posts Manager', 'fa fa-book', '1', '0', '1', '0', '1517833547', '1', '1', '0', '0', '');
INSERT INTO `menu` VALUES ('16', 'Language Management', '', '1', '13', '1', '0', '1500644030', '1', '1', '3', '30', '/admin/language/action/list');
INSERT INTO `menu` VALUES ('17', 'Setting', '', '1', '13', '1', '0', '1501244109', '1', '1', '4', '31', '/admin/system/action/setting');
INSERT INTO `menu` VALUES ('18', 'Permissions', 'zmdi zmdi-power', '1', '0', '1', '0', '1501583290', '1', '1', '1', '26', '');
INSERT INTO `menu` VALUES ('19', 'Role', 'fa fa-user-plus', '1', '18', '1', '0', '1501592174', '1', '1', '0', '27', '/admin/role/action/list');
INSERT INTO `menu` VALUES ('34', 'Demo', 'fa fa-align-center', '1', '0', '1', '0', '1508157506', '1', '1', '0', '24', '');
INSERT INTO `menu` VALUES ('43', 'User Manager', 'fa fa-user', '1', '0', '1', '0', '1515230163', '1', '1', '0', '23', '');
INSERT INTO `menu` VALUES ('44', 'Admin', '', '1', '43', '1', '0', '1515230219', '1', '1', '0', '17', '/admin/admin/action/list');
INSERT INTO `menu` VALUES ('35', 'Products', '', '1', '34', '1', '0', '1508157865', '1', '1', '0', '0', '/admin/product/action/list');
INSERT INTO `menu` VALUES ('37', 'Abilities', '', '1', '18', '1', '0', '1515206612', '1', '1', '1', '28', '/admin/ability/action/list');
INSERT INTO `menu` VALUES ('46', 'Posts', '', '1', '45', '1', '0', '1517833575', '1', '1', '0', '0', '/admin/post/action/list');
INSERT INTO `menu` VALUES ('47', 'Operation log', '', '1', '13', '1', '0', '1518157762', '1', '1', '0', '0', '/admin/logs/action/list');

-- ----------------------------
-- Table structure for options
-- ----------------------------
DROP TABLE IF EXISTS `options`;
CREATE TABLE `options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `value` varchar(255) NOT NULL,
  `autoload` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of options
-- ----------------------------

-- ----------------------------
-- Table structure for post
-- ----------------------------
DROP TABLE IF EXISTS `post`;
CREATE TABLE `post` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `content` varchar(255) NOT NULL,
  `author` varchar(100) NOT NULL,
  `author_created_at` int(11) unsigned NOT NULL,
  `type` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `created_at` int(11) NOT NULL,
  `modified_at` int(11) NOT NULL,
  `created_by_id` int(11) NOT NULL,
  `comment_status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1打开评论，0关闭评论',
  `comment_count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `title` (`title`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of post
-- ----------------------------
INSERT INTO `post` VALUES ('1', '测试文章', '什么？？', 'Lxh', '0', '1', '1', '0', '0', '0', '1', '0');
INSERT INTO `post` VALUES ('2', '测试文章2', '哈哈哈哈啊哈哈哈哈哈哈哈啊哈哈哈哈哈哈哈哈哈哈', 'Jqh', '0', '2', '1', '0', '0', '0', '1', '0');
INSERT INTO `post` VALUES ('3', '五杀攻略', '送五杀', 'Lxh', '0', '1', '1', '0', '0', '0', '1', '0');

-- ----------------------------
-- Table structure for post_meta
-- ----------------------------
DROP TABLE IF EXISTS `post_meta`;
CREATE TABLE `post_meta` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) unsigned NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`) USING BTREE,
  KEY `key` (`key`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of post_meta
-- ----------------------------

-- ----------------------------
-- Table structure for product
-- ----------------------------
DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  `modified_at` int(111) unsigned NOT NULL DEFAULT '0',
  `created_by_id` int(11) unsigned NOT NULL DEFAULT '0',
  `is_hot` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1热品，0非热品',
  `is_new` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1新品，0非新品',
  `priority` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序优先级，值越大排序越靠前',
  `calendar` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1开启日历显示，0不显示',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '简介',
  `order_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '月销量',
  `start_date` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始销售时间，0不限制',
  `end_date` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束销售时间，0不限制',
  `timelimit` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1开启限时售卖，0关闭',
  `price` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '现价，单位厘，1000厘等于1元人民币',
  `counter_price` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '门市价，单位厘，1000厘等于1元人民币',
  `share_price` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分享可得价，单位厘',
  `stock` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '库存量',
  `imgs` varchar(255) NOT NULL DEFAULT '' COMMENT '产品图片，多个用“,”隔开',
  `level` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '景区评级，A-AAAAA，1表示A，2表示AA',
  `category_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '产品分类id',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of product
-- ----------------------------
INSERT INTO `product` VALUES ('1', '测试1', '0', '0', '0', '0', '1', '0', '0', '', '25', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('2', 'test2', '0', '0', '0', '1', '0', '5', '0', '哈哈！', '1', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('3', 'test3', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('4', 'test4', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('5', 'test5', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('6', '测试1', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('7', 'test2', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('8', 'test3', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('9', 'test4', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('10', 'test5', '0', '0', '0', '1', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('13', '测试1', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('14', 'test2', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('15', 'test3', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('16', 'test4', '0', '0', '0', '1', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('17', 'test5', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('18', '测试1', '0', '0', '0', '1', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('19', 'test2', '0', '0', '0', '1', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('20', 'test3', '0', '0', '0', '1', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('21', 'test478', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('22', 'test5', '0', '0', '0', '1', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('28', '测试1', '0', '0', '0', '1', '1', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('29', 'test2', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('30', 'test3', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('31', 'test4', '0', '0', '0', '1', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('32', 'test5', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('33', '测试1', '0', '0', '0', '1', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('37', 'test5', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('38', '测试', '0', '0', '0', '1', '1', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('40', '1', '0', '0', '0', '1', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('41', 'test4@qq.com', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('42', '12345', '0', '0', '0', '1', '1', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('43', 'http://tieba.baidu.com', '0', '0', '0', '1', '1', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('44', '2028-04-05 05:25:08', '0', '0', '0', '1', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');

-- ----------------------------
-- Table structure for role
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '角色唯一标识，只能填英文',
  `comment` varchar(255) NOT NULL DEFAULT '' COMMENT '角色描述',
  `created_at` int(11) unsigned NOT NULL,
  `created_by_id` int(11) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `modified_at` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(30) NOT NULL DEFAULT '' COMMENT '角色名称',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1后台角色，2前台角色',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of role
-- ----------------------------
INSERT INTO `role` VALUES ('36', 'test', 'haha', '1515584704', '2', '0', '0', 'test', '1');
INSERT INTO `role` VALUES ('33', 'universal', '普通角色', '1515386164', '1', '0', '0', '普通角色', '1');

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `password` varchar(150) NOT NULL,
  `email` varchar(50) NOT NULL DEFAULT '',
  `mobile` varchar(30) NOT NULL DEFAULT '',
  `first_name` varchar(20) NOT NULL DEFAULT '',
  `last_name` varchar(20) NOT NULL DEFAULT '',
  `avatar` varchar(100) NOT NULL DEFAULT '',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0未知，1男，2女',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  `update_at` int(11) NOT NULL DEFAULT '0',
  `last_login_ip` char(15) NOT NULL DEFAULT '',
  `last_login_time` int(11) unsigned NOT NULL DEFAULT '0',
  `reg_ip` char(15) NOT NULL DEFAULT '',
  `status` tinyint(11) unsigned NOT NULL DEFAULT '1' COMMENT '1激活，0禁用',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分享者id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', 'admin', '$2y$10$IK.HGNDMOV9LYHIG7jMxb.0iEV85SSkf6Lv8GN9aaAuAIFbsVnaSS', '', '', '', '', '', '0', '0', '1499568986', '0', '127.0.0.1', '0', '127.0.0.1', '1', '0');
