/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50714
Source Host           : localhost:3306
Source Database       : lxh

Target Server Type    : MYSQL
Target Server Version : 50714
File Encoding         : 65001

Date: 2018-01-08 00:11:00
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
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of abilities
-- ----------------------------
INSERT INTO `abilities` VALUES ('1', 'user.read', '用户查看', '1515207448', '1515207550', '1', '0', '1', '查看用户列表');
INSERT INTO `abilities` VALUES ('2', 'user.edit', '用户编辑', '1515207533', '0', '1', '0', '1', '');
INSERT INTO `abilities` VALUES ('3', 'user.add', '用户新增', '1515207584', '0', '1', '0', '1', '');
INSERT INTO `abilities` VALUES ('4', 'user.delete', '用户删除', '1515207646', '0', '1', '0', '1', '');
INSERT INTO `abilities` VALUES ('5', 'menu.read', '菜单查看', '1515207863', '0', '1', '0', '1', '');
INSERT INTO `abilities` VALUES ('6', 'menu.add', '菜单新增', '1515207911', '0', '1', '0', '1', '');
INSERT INTO `abilities` VALUES ('7', 'menu.edit', '菜单编辑', '1515207928', '0', '1', '0', '1', '');
INSERT INTO `abilities` VALUES ('8', 'menu.delete', '菜单删除', '1515207953', '0', '1', '0', '1', '');
INSERT INTO `abilities` VALUES ('9', 'system.setting', '系统设置', '1515213158', '0', '1', '0', '1', '系统设置菜单进入权限');
INSERT INTO `abilities` VALUES ('17', 'admin.read', '管理员查看', '1515230219', '1515295922', '0', '0', '1', '');
INSERT INTO `abilities` VALUES ('18', 'user.manager', 'user.manager', '1515296457', '0', '0', '0', '1', '');

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES ('1', 'admin', '$2y$10$IK.HGNDMOV9LYHIG7jMxb.0iEV85SSkf6Lv8GN9aaAuAIFbsVnaSS', 'dfdsfdsdsfdfdfdsfdsfsd', '', 'J', 'qh', '', '0', '0', '1499568986', '0', '127.0.0.1', '0', '127.0.0.1', '1', '1', '0');

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
INSERT INTO `assigned_roles` VALUES ('20', '1', '2');
INSERT INTO `assigned_roles` VALUES ('31', '1', '1');

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of category
-- ----------------------------
INSERT INTO `category` VALUES ('1', '测试', 'fgfgf', '1508243598', '1', '1508243933', '0');

-- ----------------------------
-- Table structure for conversation
-- ----------------------------
DROP TABLE IF EXISTS `conversation`;
CREATE TABLE `conversation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `start` int(11) unsigned NOT NULL COMMENT '会话开始时间',
  `end` int(11) unsigned NOT NULL COMMENT '会话结束时间，一般结束时间为1小时，如客服把客户转给其他人，也会结束此会话并创建新的会话',
  `from` varchar(100) NOT NULL DEFAULT '' COMMENT '发起会话者',
  `to` varchar(100) NOT NULL DEFAULT '' COMMENT '接收会话者',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1普通会话，2转接的会话',
  `shifted_by` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `from` (`from`) USING BTREE,
  KEY `to` (`to`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of conversation
-- ----------------------------
INSERT INTO `conversation` VALUES ('1', '1513757867', '0', 'testpsid1', '江清华', '1', '');
INSERT INTO `conversation` VALUES ('2', '1513759170', '0', 'testpsid1', '江清华', '1', '');
INSERT INTO `conversation` VALUES ('3', '1513759227', '0', 'testpsid1', '李汉陪', '1', '');
INSERT INTO `conversation` VALUES ('4', '1513759355', '0', 'testpsid1', '江清华', '1', '');

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
  `controller` varchar(20) NOT NULL DEFAULT '',
  `action` varchar(20) NOT NULL DEFAULT '' COMMENT 'action',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  `created_by_id` int(11) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1普通菜单，2系统菜单，不能被删除或修改',
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序权重值，值越小排序越靠前',
  `ability_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '权限id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of menu
-- ----------------------------
INSERT INTO `menu` VALUES ('1', 'Menu management', 'zmdi zmdi-menu', '1', '13', '1', 'Menu', 'List', '0', '1500180853', '1', '2', '0', '0');
INSERT INTO `menu` VALUES ('13', 'System', 'fa fa-gears', '1', '0', '1', '', '', '0', '1500466810', '1', '1', '2', '0');
INSERT INTO `menu` VALUES ('14', 'Making modules', 'zmdi zmdi-widgets', '1', '13', '1', 'System', 'MakeModules', '0', '1500467096', '1', '1', '0', '0');
INSERT INTO `menu` VALUES ('15', 'Create reports', 'zmdi zmdi-view-list', '1', '13', '1', 'System', 'CreateReports', '0', '1500467299', '1', '1', '2', '0');
INSERT INTO `menu` VALUES ('16', 'Language Management', '', '1', '13', '1', 'Language', 'List', '0', '1500644030', '1', '1', '3', '0');
INSERT INTO `menu` VALUES ('17', 'Setting', '', '1', '13', '1', 'System', 'Setting', '0', '1501244109', '1', '1', '4', '0');
INSERT INTO `menu` VALUES ('18', 'Permissions', 'fa fa-pencil fa-fw', '1', '0', '1', '', '', '0', '1501583290', '1', '1', '1', '0');
INSERT INTO `menu` VALUES ('19', 'Role', 'fa fa-user-plus', '1', '18', '1', 'Role', 'List', '0', '1501592174', '1', '1', '0', '0');
INSERT INTO `menu` VALUES ('34', 'Products system', 'fa fa-opencart', '1', '0', '1', '', '', '0', '1508157506', '1', '1', '0', '0');
INSERT INTO `menu` VALUES ('43', 'User Manager', 'fa fa-users', '1', '0', '1', '', '', '0', '1515230163', '1', '1', '0', '18');
INSERT INTO `menu` VALUES ('44', 'Admin', '', '1', '43', '1', 'Admin', 'List', '0', '1515230219', '1', '1', '0', '17');
INSERT INTO `menu` VALUES ('36', 'Category', '', '1', '34', '1', 'Category', 'List', '0', '1508240328', '1', '1', '1', '0');
INSERT INTO `menu` VALUES ('35', 'Products', '', '1', '34', '1', 'Product', 'List', '0', '1508157865', '1', '1', '0', '0');
INSERT INTO `menu` VALUES ('37', 'Abilities', '', '1', '18', '1', 'Ability', 'List', '0', '1515206612', '1', '1', '1', '0');

-- ----------------------------
-- Table structure for order
-- ----------------------------
DROP TABLE IF EXISTS `order`;
CREATE TABLE `order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `no` varchar(32) NOT NULL DEFAULT '' COMMENT '订单号',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '1',
  `modified_at` int(11) unsigned NOT NULL DEFAULT '0',
  `modified_by_id` int(11) unsigned NOT NULL DEFAULT '0',
  `total_price` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单总金额，单位厘',
  `pay_method` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1微信支付，2支付宝支付，3银行卡支付',
  `pay_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未付款，1付款中，2付款成功，3付款失败',
  `total_share_price` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '拉人者可得金额，单位厘',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单所属用户',
  `share_user` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '拉人者id，如没有则填写0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of order
-- ----------------------------

-- ----------------------------
-- Table structure for order_product
-- ----------------------------
DROP TABLE IF EXISTS `order_product`;
CREATE TABLE `order_product` (
  `id` int(11) unsigned NOT NULL,
  `order_id` int(11) unsigned NOT NULL,
  `product_name` varchar(11) NOT NULL DEFAULT '' COMMENT '产品名称',
  `product_num` smallint(6) unsigned NOT NULL DEFAULT '1' COMMENT '产品数量',
  `product_price` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '产品价格',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单产品表';

-- ----------------------------
-- Records of order_product
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
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4;

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
INSERT INTO `product` VALUES ('10', 'test5', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('13', '测试1', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('14', 'test2', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('15', 'test3', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('16', 'test4', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('17', 'test5', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('18', '测试1', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('19', 'test2', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('20', 'test3', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('21', 'test4', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('22', 'test5', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('28', '测试1', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('29', 'test2', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('30', 'test3', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('31', 'test4', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('32', 'test5', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('33', '测试1', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('34', 'test2', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('35', 'test3', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('36', 'test4', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('37', 'test5', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('38', '测试1', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('39', 'test2', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('40', 'test3', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('41', 'test4', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('42', 'test5', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('43', '测试1', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('44', 'test2', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('45', 'test3', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('46', 'test4', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('47', 'test5', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');

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
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of role
-- ----------------------------
INSERT INTO `role` VALUES ('15', 'dsfsdf', 'fdfdsf', '1515167353', '1', '0', '0', 'dfds', '1');
INSERT INTO `role` VALUES ('32', 'admin.normal', '', '1515236040', '1', '0', '0', '普通管理员', '1');

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
