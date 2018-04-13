/*
Navicat MySQL Data Transfer

Source Server         : locahost
Source Server Version : 50714
Source Host           : localhost:3306
Source Database       : lxh

Target Server Type    : MYSQL
Target Server Version : 50714
File Encoding         : 65001

Date: 2018-04-13 17:44:49
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for abilities
-- ----------------------------
DROP TABLE IF EXISTS `abilities`;
CREATE TABLE `abilities` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(30) NOT NULL DEFAULT '' COMMENT '权限唯一标识，只能填英文',
  `title` varchar(255) NOT NULL COMMENT '权限名称',
  `created_at` int(11) NOT NULL,
  `modified_at` int(11) unsigned NOT NULL DEFAULT '0',
  `created_by_id` int(11) unsigned NOT NULL DEFAULT '0',
  `forbidden` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0允许的权限，1禁止的权限',
  `comment` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`slug`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of abilities
-- ----------------------------
INSERT INTO `abilities` VALUES ('1', 'user.read', '用户查看', '1515207448', '1515207550', '1', '0', '查看用户列表');
INSERT INTO `abilities` VALUES ('2', 'user.update', '用户编辑', '1515207533', '1515671037', '1', '0', '');
INSERT INTO `abilities` VALUES ('3', 'user.add', '用户新增', '1515207584', '0', '1', '0', '');
INSERT INTO `abilities` VALUES ('4', 'user.delete', '用户删除', '1515207646', '0', '1', '0', '');
INSERT INTO `abilities` VALUES ('5', 'menu.read', '菜单查看', '1515207863', '0', '1', '0', '');
INSERT INTO `abilities` VALUES ('6', 'menu.add', '菜单新增', '1515207911', '0', '1', '0', '');
INSERT INTO `abilities` VALUES ('7', 'menu.edit', '菜单编辑', '1515207928', '0', '1', '0', '');
INSERT INTO `abilities` VALUES ('8', 'menu.delete', '菜单删除', '1515207953', '0', '1', '0', '');
INSERT INTO `abilities` VALUES ('9', 'system.manager', '系统管理', '1515213159', '1515760393', '1', '0', '系统设置菜单进入权限');
INSERT INTO `abilities` VALUES ('17', 'admin.read', '管理员查看', '1515230219', '1517881473', '0', '0', '测试123');
INSERT INTO `abilities` VALUES ('30', 'language.read', '语言包查看', '1515760352', '1515760370', '1', '0', '');
INSERT INTO `abilities` VALUES ('23', 'user.manager', '用户管理', '1515758278', '1515758633', '1', '0', '');
INSERT INTO `abilities` VALUES ('24', 'product.manager', '产品系统', '1515758654', '1518144506', '1', '0', '123');
INSERT INTO `abilities` VALUES ('26', 'permissions.manager', '权限管理', '1515758747', '1515758758', '1', '0', '');
INSERT INTO `abilities` VALUES ('27', 'role.read', '角色查看', '1515758787', '0', '1', '0', '');
INSERT INTO `abilities` VALUES ('28', 'ability.read', 'Ability.read', '1515758949', '1515760282', '1', '0', '');
INSERT INTO `abilities` VALUES ('29', 'create.module', 'Create.module', '1515759578', '0', '1', '0', '');
INSERT INTO `abilities` VALUES ('31', 'system.setting', 'System.setting', '1515760412', '0', '1', '0', '');

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
  `last_login_ip` int(11) unsigned NOT NULL DEFAULT '0',
  `last_login_time` int(11) unsigned NOT NULL DEFAULT '0',
  `reg_ip` int(11) unsigned NOT NULL DEFAULT '0',
  `is_admin` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1激活，0禁用',
  `created_by_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES ('1', 'admin', '$2y$10$IK.HGNDMOV9LYHIG7jMxb.0iEV85SSkf6Lv8GN9aaAuAIFbsVnaSS', '841324345@qq.com', '', 'J', 'qh', '20180306/1b4267fe836249627de672e0d1795350.jpeg', '1', '0', '1499568986', '1520325431', '0', '0', '0', '1', '1', '0');
INSERT INTO `admin` VALUES ('3', 'test', '$2y$10$anUpfaFdxve9b9mzmkCdfOEgoDpEss1glWk6.T5M2JnMnMh/3XvOO', '87@qq.com', '13334', 't', ' w', '', '1', '0', '1517654504', '1519780738', '0', '0', '0', '0', '1', '1');
INSERT INTO `admin` VALUES ('5', 'testh99', '$2y$10$BGcwPLnwqqxm9O5WzrybweNJRFQM0l88msP6FzkM5JqnZmL6XdGya', '841324345@qq.com', '13076814390', '1', 'ere', '', '1', '0', '1519452159', '1522743264', '0', '0', '0', '0', '1', '1');

-- ----------------------------
-- Table structure for admin_login_log
-- ----------------------------
DROP TABLE IF EXISTS `admin_login_log`;
CREATE TABLE `admin_login_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(150) NOT NULL,
  `key` varchar(100) NOT NULL DEFAULT '',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  `logout_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '登出时间，只有当用户手动登出时或被踢下线时才会有值',
  `ip` varchar(50) NOT NULL DEFAULT '',
  `device` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '浏览器类型（预留）',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1有效，0无效',
  `life` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '登录有效期，单位秒，0表示记录无效',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '登录用户id',
  `app` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '用户登录的应用入口，预留字段',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1站内session登录，2授权开放登录',
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`) USING BTREE,
  KEY `user_id` (`user_id`,`active`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of admin_login_log
-- ----------------------------
INSERT INTO `admin_login_log` VALUES ('69', '0c6a12d5648019acd81df37d1918ef6b0895c95cb314c82043768981c67f403a', 'mKDslg1523607675.9445ad0687be6789', '1523607675', '0', '2130706433', '0', '0', '0', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('70', 'e452113e1825669089619d6b22336f5c7fa0f068127ece07cc239d69f7c60801', 'Wu4QAD1523608005.89595ad069c5dabc3', '1523608005', '1523608406', '2130706433', '0', '0', '0', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('71', 'd94c27df06485d7c4c413a1875e1a3dc6a29da4669ea8c4ea6f58e7591e119c8', 'g50jor1523608547.44865ad06be36d87a', '1523608547', '1523608560', '2130706433', '0', '0', '0', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('72', '5b7b396d570b8ddcabb3e065a0558dd083c789c1f6f3814fd0aa47e4000cf44c', 'Gnwu5O1523608650.65835ad06c4aa0bad', '1523608650', '1523608653', '2130706433', '0', '0', '0', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('73', '8534eac1c1dd5c4ca1fe3ad33d879c8e4f61e8d3f33fda140b9676c037b535d7', 'rqON6j1523608655.85385ad06c4fd073a', '1523608655', '1523608744', '2130706433', '0', '0', '0', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('74', 'ac34168c0d9853c562a8a414a58f34e9563e98c4bb41c4d34ac8d53f496f1968', 'HYNZso1523608781.35965ad06ccd57cdd', '1523608781', '0', '2130706433', '0', '0', '0', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('75', '6c7b59e326f9d870f40afe294486e02d46a2f85b92abecd05bec705acf11fd8c', 'EAQF2d1523608793.6465ad06cd99db7d', '1523608793', '0', '2130706433', '0', '0', '0', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('76', 'bb3d302d7a95cd7838fd8f0c5042823d6632a23c532d6fd80afad1a2e705528f', 'nHwG9b1523608807.64695ad06ce79ded8', '1523608807', '0', '2130706433', '0', '0', '0', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('77', '42aa32effa00a626b473999742478b49aa3036c08b4725b912e040360fd1c5f0', 'CdEXwI1523608833.60445ad06d019391a', '1523608833', '0', '2130706433', '0', '0', '0', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('78', '7f3472086c38e3ee864e23b64bcc01dba7949030ffc81f661964a61284cc37c7', 'MnVdb81523608875.89385ad06d2bda378', '1523608875', '0', '2130706433', '0', '0', '0', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('79', 'f24c68e934d7ea4ba17881556b83074aa4f4b1f4376b11f576f1ac3088b81a55', 'cEMrIR1523608900.02085ad06d4405180', '1523608900', '1523608916', '2130706433', '0', '0', '0', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('80', 'd2b8d46fa9085bc49427ae956b23d0779299c4a4ca31892a950a98eb3bec14d4', 'WagMjU1523608924.97585ad06d5cee3f4', '1523608924', '1523609038', '2130706433', '0', '0', '0', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('81', 'b2054162943f692790dbb8757e0f1a0624c25b99bcbdfc9d50457bec45b1f9e3', '1fUoh91523609041.86275ad06dd1d29cf', '1523609041', '0', '2130706433', '0', '0', '0', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('82', 'e9f58668d0436e3d4a285ddac8ff02531c0433e6104e9bd62dc5eb6ead067b1c', 'VtbleP1523609233.28475ad06e9145834', '1523609233', '1523609333', '2130706433', '0', '0', '604800', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('83', 'e725d3923be9b57d4eb2fd057c2138ee9a0b488f86784ae568045032c3d9ad33', 'tmsU6i1523609338.9835ad06efaefff1', '1523609338', '1523609341', '2130706433', '0', '0', '0', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('84', 'cfb915e3f99c0dc76912d869984f1e7674ee758d42c28f6626072c17d7a1ad22', 'QJn3Y71523609442.70525ad06f62ac2a5', '1523609442', '0', '2130706433', '0', '1', '604800', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('85', '100cd205c69d5d9b27eaf5852bc858e4f37035653398da15eed03df0aa6eec99', 'dl7p5C1523610280.72845ad072a8b1d60', '1523610280', '0', '2130706433', '0', '0', '604800', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('86', '821618b4144fbd9a62fd58a322e31ba35bed63a44277fbe9bb8801a13ea4a673', 'zRkEJ61523610325.57575ad072d58c8ea', '1523610325', '1523610372', '2130706433', '0', '0', '604800', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('87', 'ec51282a01351aa4441e7e02c54c0d52a325df01b70000fd6f0ecc8a178223b8', 'V5QSL21523610380.69335ad0730ca9414', '1523610380', '0', '2130706433', '0', '1', '604800', '1', '0', '1');

-- ----------------------------
-- Table structure for admin_operation_log
-- ----------------------------
DROP TABLE IF EXISTS `admin_operation_log`;
CREATE TABLE `admin_operation_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `path` varchar(101) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 GET, 2POST, 3PUT, 4DELETE, 5OPTION',
  `ip` int(11) unsigned NOT NULL,
  `input` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  `table` varchar(50) NOT NULL DEFAULT '',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0其他，1新增，2删除，3删除',
  PRIMARY KEY (`id`),
  KEY `admin_operation_log_user_id_index` (`admin_id`),
  KEY `table` (`table`) USING BTREE,
  KEY `created_at` (`created_at`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of admin_operation_log
-- ----------------------------
INSERT INTO `admin_operation_log` VALUES ('1', '1', '/test', '1', '2130706433', '{ \"name\": \"Administrator\", \"password\": \"$2y$10$JWwBpjZmoMqD7PPkjUotIu22dsCIT9mh.ttxH6Lop3u4pogvgqKYO\", \"password_confirmation\": \"$2y$10$JWwBpjZmoMqD7PPkjUotIu22dsCIT9mh.ttxH6Lop3u4pogvgqKYO\", \"_token\": \"qnvNxqktM7WQagJVZ2MG8LSTEkjRDr7N8rgbwIn3\", \"_method\": \"PUT\", \"_previous_\": \"http:\\/\\/www.l.com\\/admin\" }', '0', '', '0');
INSERT INTO `admin_operation_log` VALUES ('2', '1', '/lxh/demo/action/test1', '1', '2130706433', '{\"name\":\"test\",\"age\":19,\"address\":\"Guangdong\"}', '1522721437', 'test', '1');
INSERT INTO `admin_operation_log` VALUES ('3', '1', '/lxh/api/admin/view/5', '2', '2130706433', '{\"id\":\"5\",\"username\":\"testh99\",\"email\":\"841324345@qq.com\",\"mobile\":\"13076814390\",\"avatar\":\"\",\"status\":\"1\",\"is_admin\":\"0\",\"sex\":\"1\",\"roles\":\"33\"}', '1522743264', 'admin', '2');
INSERT INTO `admin_operation_log` VALUES ('4', '1', '/lxh/api/admin/3', '4', '2130706433', '{\"id\":\"3\"}', '1522744089', 'admin', '6');
INSERT INTO `admin_operation_log` VALUES ('5', '1', '/lxh/api/admin/restore', '2', '2130706433', '3', '1522744113', 'admin', '8');
INSERT INTO `admin_operation_log` VALUES ('6', '1', '/lxh/api/admin/batch-delete', '2', '2130706433', '3,5', '1522744176', 'admin', '7');
INSERT INTO `admin_operation_log` VALUES ('7', '1', '/lxh/api/admin/restore', '2', '2130706433', '5,3', '1522744186', 'admin', '8');
INSERT INTO `admin_operation_log` VALUES ('8', '1', '/lxh/api/role/view/36', '2', '2130706433', '{\"id\":\"36\",\"title\":\"te123dfd\",\"name\":\"test\",\"comment\":\"haha\",\"abilities\":\"0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0\"}', '1523347999', 'role', '2');
INSERT INTO `admin_operation_log` VALUES ('9', '1', '/lxh/api/role/view/36', '2', '2130706433', '{\"id\":\"36\",\"title\":\"te1123\",\"name\":\"test\",\"comment\":\"haha\",\"abilities\":\"0,0,3,0,0,0,0,0,9,17,0,0,0,0,0,0,0,0\"}', '1523348194', 'role', '2');
INSERT INTO `admin_operation_log` VALUES ('10', '1', '/lxh/api/role/view/36', '2', '2130706433', '{\"id\":\"36\",\"title\":\"te909\",\"name\":\"test\",\"comment\":\"haha\",\"abilities\":\"0,0,3,4,5,0,0,8,0,17,0,0,0,0,0,0,0,0\"}', '1523356210', 'role', '2');

-- ----------------------------
-- Table structure for admin_trash
-- ----------------------------
DROP TABLE IF EXISTS `admin_trash`;
CREATE TABLE `admin_trash` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of admin_trash
-- ----------------------------

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
INSERT INTO `assigned_abilities` VALUES ('17', '36', '2');
INSERT INTO `assigned_abilities` VALUES ('23', '33', '2');
INSERT INTO `assigned_abilities` VALUES ('17', '33', '2');
INSERT INTO `assigned_abilities` VALUES ('9', '33', '2');
INSERT INTO `assigned_abilities` VALUES ('7', '33', '2');
INSERT INTO `assigned_abilities` VALUES ('5', '33', '2');
INSERT INTO `assigned_abilities` VALUES ('4', '33', '2');
INSERT INTO `assigned_abilities` VALUES ('8', '36', '2');
INSERT INTO `assigned_abilities` VALUES ('5', '36', '2');
INSERT INTO `assigned_abilities` VALUES ('4', '36', '2');
INSERT INTO `assigned_abilities` VALUES ('3', '33', '2');
INSERT INTO `assigned_abilities` VALUES ('2', '33', '2');
INSERT INTO `assigned_abilities` VALUES ('1', '33', '2');
INSERT INTO `assigned_abilities` VALUES ('3', '36', '2');
INSERT INTO `assigned_abilities` VALUES ('24', '33', '2');

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
-- Table structure for demo
-- ----------------------------
DROP TABLE IF EXISTS `demo`;
CREATE TABLE `demo` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `text` varchar(255) DEFAULT NULL,
  `expand` varchar(255) DEFAULT NULL,
  `select` varchar(255) DEFAULT NULL,
  `muti_select` varchar(255) DEFAULT NULL,
  `editor` text,
  `date` varchar(255) DEFAULT NULL,
  `checkbox` varchar(255) DEFAULT NULL,
  `radio` varchar(255) DEFAULT NULL,
  `switch` tinyint(1) unsigned DEFAULT '1',
  `checked` tinyint(1) DEFAULT '1' COMMENT '0',
  `image` varchar(255) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of demo
-- ----------------------------
INSERT INTO `demo` VALUES ('1', '历史的尘埃', null, '0', '2,4', 'Grid网格布局示例', '1519780738', '15,12,3', '阿萨', '1', '0', null, null);
INSERT INTO `demo` VALUES ('2', '有匪', null, '3', '4', '破雪刀', null, '1', '周翡', '1', '1', null, null);
INSERT INTO `demo` VALUES ('3', '剑来', null, '3', '5', '剑修', null, '5', '陈平安', '0', '1', null, null);

-- ----------------------------
-- Table structure for demo_trash
-- ----------------------------
DROP TABLE IF EXISTS `demo_trash`;
CREATE TABLE `demo_trash` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `text` varchar(255) DEFAULT NULL,
  `expand` varchar(255) DEFAULT NULL,
  `select` varchar(255) DEFAULT NULL,
  `muti_select` varchar(255) DEFAULT NULL,
  `editor` text,
  `date` varchar(255) DEFAULT NULL,
  `checkbox` varchar(255) DEFAULT NULL,
  `radio` varchar(255) DEFAULT NULL,
  `switch` tinyint(1) unsigned DEFAULT '1',
  `checked` tinyint(1) DEFAULT '1' COMMENT '0',
  `image` varchar(255) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of demo_trash
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
  `use_route_prefix` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否使用路由前缀',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of menu
-- ----------------------------
INSERT INTO `menu` VALUES ('1', 'Menu management', 'zmdi zmdi-menu', '1', '13', '1', '0', '1500180853', '1', '2', '0', '5', '/menu/action/list', '1');
INSERT INTO `menu` VALUES ('13', 'System', 'fa fa-gears', '1', '0', '1', '0', '1500466810', '1', '1', '2', '9', '', '1');
INSERT INTO `menu` VALUES ('14', 'Making modules', 'zmdi zmdi-widgets', '1', '13', '1', '0', '1500467096', '1', '1', '0', '29', '/system/action/make-modules', '1');
INSERT INTO `menu` VALUES ('45', 'Posts Manager', 'fa fa-book', '1', '0', '1', '0', '1517833547', '1', '1', '0', '0', '', '0');
INSERT INTO `menu` VALUES ('16', 'Language Management', '', '1', '13', '1', '0', '1500644030', '1', '1', '3', '30', '/language/action/list', '1');
INSERT INTO `menu` VALUES ('17', 'Setting', '', '1', '13', '1', '0', '1501244109', '1', '1', '4', '31', '/system/action/setting', '1');
INSERT INTO `menu` VALUES ('18', 'Permissions', 'fa fa-buysellads', '1', '0', '1', '0', '1501583290', '1', '1', '1', '26', '', '0');
INSERT INTO `menu` VALUES ('19', 'Role', 'fa fa-user-plus', '1', '18', '1', '0', '1501592174', '1', '1', '0', '27', '/role/action/list', '1');
INSERT INTO `menu` VALUES ('34', 'Demo', 'fa fa-align-center', '1', '0', '1', '0', '1508157506', '1', '1', '0', '24', '', '0');
INSERT INTO `menu` VALUES ('43', 'User Manager', 'fa fa-user', '1', '0', '1', '0', '1515230163', '1', '1', '0', '23', '', '1');
INSERT INTO `menu` VALUES ('44', 'Admin', '', '1', '43', '1', '0', '1515230219', '1', '1', '0', '17', '/admin/action/list', '1');
INSERT INTO `menu` VALUES ('35', 'Trash Demo', '', '1', '34', '1', '0', '1508157865', '1', '1', '0', '0', '/demo/action/list', '1');
INSERT INTO `menu` VALUES ('37', 'Abilities', '', '1', '18', '1', '0', '1515206612', '1', '1', '1', '28', '/ability/action/list', '1');
INSERT INTO `menu` VALUES ('46', 'Posts', '', '1', '45', '1', '0', '1517833575', '1', '1', '0', '0', '/post/action/list', '1');
INSERT INTO `menu` VALUES ('47', 'Operation log', '', '1', '13', '1', '0', '1518157762', '1', '1', '0', '0', '/logs/action/list', '1');

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
INSERT INTO `product` VALUES ('13', '测试1', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('14', 'test2', '0', '0', '0', '1', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '1');
INSERT INTO `product` VALUES ('15', 'test3', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('16', 'test4', '0', '0', '0', '1', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('17', 'test5', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('18', '测试1', '0', '0', '0', '1', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('19', 'test2', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('20', 'test3', '0', '0', '0', '1', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('21', 'test478', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('22', 'test5', '0', '0', '0', '1', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('28', '测试1', '0', '0', '0', '1', '1', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('29', 'test2', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('30', 'test3', '0', '0', '0', '1', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('31', 'test4', '0', '0', '0', '1', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('32', 'test5', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('33', '1', '0', '0', '0', '1', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('37', '2', '0', '0', '0', '1', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('38', '测试', '0', '0', '0', '0', '1', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('40', '343', '0', '0', '0', '1', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('41', 'test4@qq.com', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '1');
INSERT INTO `product` VALUES ('42', '12345', '0', '0', '0', '1', '1', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');
INSERT INTO `product` VALUES ('43', 'http://tieba.baidu.com', '0', '0', '0', '1', '1', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '1');
INSERT INTO `product` VALUES ('44', '2028-04-05 05:00:08', '0', '0', '0', '1', '0', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '1', '0', '0');

-- ----------------------------
-- Table structure for role
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(30) NOT NULL COMMENT '角色唯一标识，只能填英文',
  `comment` varchar(255) NOT NULL DEFAULT '' COMMENT '角色描述',
  `created_at` int(11) unsigned NOT NULL,
  `created_by_id` int(11) unsigned NOT NULL DEFAULT '0',
  `modified_at` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(30) NOT NULL DEFAULT '' COMMENT '角色名称',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`slug`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of role
-- ----------------------------
INSERT INTO `role` VALUES ('36', 'test', 'haha', '1515584704', '2', '0', 'te909');
INSERT INTO `role` VALUES ('33', 'universal', '普通角色', '1515386164', '1', '0', '普通角色');

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
