/*
Navicat MySQL Data Transfer

Source Server         : locahost
Source Server Version : 50714
Source Host           : localhost:3306
Source Database       : lxh

Target Server Type    : MYSQL
Target Server Version : 50714
File Encoding         : 65001

Date: 2018-04-02 14:46:28
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
INSERT INTO `admin` VALUES ('3', 'test', '$2y$10$anUpfaFdxve9b9mzmkCdfOEgoDpEss1glWk6.T5M2JnMnMh/3XvOO', '87@qq.com', '13334', '', '', '', '1', '0', '1517654504', '1519780738', '', '0', '', '0', '1', '1');
INSERT INTO `admin` VALUES ('5', 'testhuiui123', '$2y$10$BGcwPLnwqqxm9O5WzrybweNJRFQM0l88msP6FzkM5JqnZmL6XdGya', '841324345@qq.com', '13076814390', '', '', '', '1', '0', '1519452159', '1522133382', '', '0', '', '0', '1', '1');

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
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of admin_login_log
-- ----------------------------
INSERT INTO `admin_login_log` VALUES ('10', '6ec18f33b3b66c6dfdf0f566acd60ca97934bb822805e5ef088939f08514fe1c', '43sWkU1521199337.60655aaba8e994103', '1521199337', '1521199392', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('11', 'b7195c0101594c4e5edb122e7e832945bf59e5d4fce0d0ec2afc9b04919ac176', '0wJOfd1521199388.09135aaba91c164c9', '1521199388', '1521199502', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('12', 'e55e165d7e4f28ec97370a20e4ae7e3169784af578f57fef6210376ff9d94cdf', '3qwTIn1521424634.17535aaf18fa2ace5', '1521424634', '1521451736', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('13', '8a06ad14c1b99b646811dee87b0959c74c0b00c3595e2e1ecbdc4cc54dd6a037', 'jLGdTp1521453694.23785aaf8a7e3a103', '1521453694', '1521459228', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('14', '18744ea6b7c74577fbf5fca24848f51dcfdee0bec83216461e21f1be09e49c52', 'P326id1521459228.86065aafa01cd21a4', '1521459228', '1521508501', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('15', '955f41862c2c50af910c589a72784436e8f0298d89a0b0ee9c0a1d2a8e8d9084', 'fltgzY1521515611.24675ab07c5b3c390', '1521515611', '1521515930', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('16', '700aa2ebcccd5f8e64463395469bc85b4349cee19dc74af20c2be3cf06d2b2e1', 'lc8Ujh1521515930.91965ab07d9ae0852', '1521515930', '1521515954', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('17', 'ccc3ee3694da8507828971ddd1f79cec865e09777fba4183457655486aec600b', 'DJvu5f1521515954.49715ab07db2795f2', '1521515954', '1521515966', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('18', '280626a9aac54b37628704524610a679967b044fdd745e1d4c4c8f9005e63b93', 'hjNM6B1521515966.58465ab07dbe8eba0', '1521515966', '1521516000', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('19', '20cfcb000ca01ac3b4d5738d02685cf0bc70458fc73070c069b2cbe518061812', 'IkaFLw1521516000.0685ab07de010983', '1521516000', '1521516024', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('20', '46f3ebbcd5af2be0f039346fd1e4daf84adb414f0ab5287ef21ee140f3f3a730', 'vEgOSt1521516024.85635ab07df8d10cd', '1521516024', '1521526171', '127.0.0.1', '0', '0', '604800', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('21', 'a760f5ef94cba254004d26a22dfc945b0f6f64ee3283777ae325f28421a3b391', 'dqQRu01521526157.62245ab0a58d97f2d', '1521526157', '1521526393', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('22', 'd1247c1186cb4293a250688ec12e877589d25aeb7c191e65f6cc8398165e1bba', 'YtIFjO1521526321.57265ab0a6318bce9', '1521526321', '1521526447', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('23', '052f3785acd88e3dac59bb9f31796989db14ac8915b07d6e84e8d3d2567300f8', 'EKogTS1521526442.06325ab0a6aa0f719', '1521526442', '1521526489', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('24', '4582ae498298140922009fa0e1ebcc2b4f4a2f0c8f045972ea0c1ba75cd91017', 'iMRwcz1521526485.10025ab0a6d518743', '1521526485', '1521526528', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('25', '1e37701e8b998b79a655c9ee6472a4885c75652ba1a01d6e1f490db5fbdfb302', 'Xjli1h1521526498.685ab0a6e2a606a', '1521526498', '1521526564', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('26', '0f6d7d956c3ecafdc70ba7d3c18de01f6880fe1acdba8fa01c78191d2abf6249', '3XCcRz1521526560.86685ab0a720d39cf', '1521526560', '1521526584', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('27', '21f0e25cef0e92495cd8248ef1d66d6c8977ae8a2ed44a827481415d7c18bc52', 'BgFWh21521526574.35185ab0a72e55e47', '1521526574', '1521526605', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('28', '2499604e2015e396c97b1b82a938a9c6bc5ef891cda1141fc6ae2da57276e845', 'j9Lgzd1521526601.32185ab0a7494e8f6', '1521526601', '1521526642', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('29', 'be57cb066deae4628fb336fa6aaad6e564f329503aabaae87ff14f0fc4631b92', 'yIFDh01521526638.86235ab0a76ed288e', '1521526638', '1521526671', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('30', 'a100540a0bfe1052e48c1bd6d1467f3f23f0dd3a2ae46208b562cd1350245e84', 'KQciE31521526667.34115ab0a78b53491', '1521526667', '1521526773', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('31', '6ca3648951aeabcd2b0e20e5e3931a8be623cd78e23a2e530e31d360c73ad543', 'IoJfuk1521526728.07295ab0a7c811cc9', '1521526728', '1521526993', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('32', 'afdadfa952056208712458ec6e9e9f5f75500932eba6d6001aeaa12507597414', 'RrlBGk1521526990.01935ab0a8ce04b7c', '1521526990', '1521527021', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('33', 'e9b882d8f7f09f210dc86b8f8a313b11bc31eab61b2a410101674d298810eae4', 'zchqEB1521527014.48315ab0a8e675f11', '1521527014', '1521527042', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('34', '22931c3c39d648364792438a720207d6b8b844d164370e33fcc0b9e522e8387f', '90GC4K1521527032.71395ab0a8f8ae4d7', '1521527032', '1521527058', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('35', '48cfc8742f4d2165d249acdb2c7519d62b9ff845e88dc689f98e0113d5367d25', 'KmVQR61521527052.50495ab0a90c7b44c', '1521527052', '1521527606', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('36', 'a0fa8fca7efc37d4fb65f4d21e33a8f850c90d4e7ac30ea625f3752dfebabad9', 'qQ6Dfb1521527602.93515ab0ab32e44f3', '1521527602', '1521527628', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('37', 'efc9c3b83257e723e74f3e73a9bf2b379723921b271dd8d56d08f3280b0c31f4', 'tZjeLJ1521527618.90135ab0ab42dc0e2', '1521527618', '1521527658', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('38', '2de8e6c82bb847e8a44836f88bcfaccbfa8cfba729e83d848843448306c7c6a9', 'qrNC4B1521527655.04115ab0ab670a06f', '1521527655', '1521527684', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('39', '93589a62a155d2a84717f71caa8f942bf8869f5149a33909dd64c1f574ec353c', 'RTBYrj1521527680.05815ab0ab800e305', '1521527680', '1521527698', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('40', 'a1ccd867eccdc43a3c3becb50d6813c6c1b2b70ebce982a2cda23e5995d5193c', 'vkQCgo1521527696.10635ab0ab9019f2b', '1521527696', '1521599434', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('41', '7b61cef03a14ff407e715997120bb48485c65be685d428f0006b8c2f1418e8fa', 'GVYBxc1521600816.23475ab1c930394e0', '1521600816', '1521600819', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('42', '32e31ddefc0b1f188defef53a1de67ab830147a541eee565efe8475334fbf1cd', 'ZFXvxm1521682177.3445ab3070153ff2', '1521682177', '0', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('43', '3019970c38e28873d4a9f11a86c7f35bb9ee83cb6395b59375f3f91ce62b2fa7', 'xsiRTD1521697555.77065ab34313bc275', '1521697555', '1521697566', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('44', 'a869480f60f9ce225103d2a7432f2445bb7dc42cac2fea97f7b8c309382f1e33', 'ECdBse1521697566.36415ab3431e58e27', '1521697566', '1521697602', '127.0.0.1', '0', '0', '604800', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('45', '42661e024d682a177ac4f41a1bc5607449baf46eef50b23e4219fb954a310b89', 'kDq8Zl1521697602.35965ab3434257c8b', '1521697602', '1521697772', '127.0.0.1', '0', '0', '604800', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('46', '46c181a2a20db9584165edb42f665a1a23d5d69063f535c074967cb13f1bca24', 'kBrZmx1521697772.51055ab343ec7ca51', '1521697772', '1521698080', '127.0.0.1', '0', '0', '604800', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('47', 'dff82cc27d5ff7d4321c0d7266bc387a19c323b96177f3d076dc8c848bd88553', 'FuZW5c1521698081.0265ab34521065b3', '1521698081', '1521698090', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('48', '650e3537290fad121f279883ce35fa6670932c41a23da335ebf5585391ccc12c', 'DLgVWI1521698090.30925ab3452a4b803', '1521698090', '1521698265', '127.0.0.1', '0', '0', '604800', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('49', '8922abedcd19f0383f4928ead489c45fc550e3c1929fed23d634b905a4b429ec', 'SIHkYz1521698266.01425ab345da037ad', '1521698266', '1521698275', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('50', '9ac43a75c1cd965d50e89a20f6d35decf380961bf149ebd04074b8c6562f98a8', '2nYLdV1521698275.16445ab345e328254', '1521698275', '1521773731', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('51', '1ef3ecdf5709f6de1922b81ab67ff6a0404570e439ec827090d0fa5fb75677ce', '4gbMNu1521773732.93655ab46ca4e4a45', '1521773732', '1521775512', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('52', 'ea290b6f7b22ccce4b03fa919286e18827abf60b3ac66c931b1317a564f9b51f', 'GcmKBa1521775512.48575ab4739876948', '1521775512', '1521785035', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('53', '8be621dbf73b0342acee7a9b501e2520ecd41710fb966da647010704e2a61901', 'yIFDh01521785189.40235ab499656238f', '1521785189', '1521785190', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('54', '9d44593d96007ef8a34cb33e3b0dfe1b4bc20d4536ed80525d46beb3186e3103', 'b95CBf1521785264.34515ab499b05444a', '1521785264', '1521785386', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('55', '19cd43c5b226eabf9c5042eb99275d28bae001fcea129f85ece3640df665eb45', 'Lgl2Oa1521785386.04725ab49a2a0b84e', '1521785386', '1522136590', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('56', '21533d8c1d093efb90fd67e4fb962db362d9c4c22319200466f595e0ecd21edf', 'LTVjnO1522137287.94155ab9f8c7e5da8', '1522137287', '0', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('57', '798b7f3d7926d53b7a15db9d75f29e5df02d483590cffc1b9f0bd9e81d0fe4b4', 'G6yNgP1522285758.76375abc3cbeba722', '1522285758', '1522292783', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('58', '6c130c5323ba3d72ded6e9c7680bc0b7ae4a3928c6e6aec32e44b53f8961da45', 'XJWsnk1522292783.47345abc582f73935', '1522292783', '1522293097', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('59', '460893171cd2c92a5094970803fd86a6b31a26574f3df08c0b09e76697c96730', '9aypxb1522293097.16965abc596929682', '1522293097', '1522293597', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('60', '906e037b1a625a98cc51572d2ab4594e4096f99459ba48348296981722daa30b', 'sWwEhL1522293597.30555abc5b5d4a964', '1522293597', '1522294489', '127.0.0.1', '0', '0', '7200', '1', '0', '1');
INSERT INTO `admin_login_log` VALUES ('61', '93b72ecf77897dd01118d6213ef5509bee0b140f8fa28dc5dd051bdf8bd994af', 'A5hxQW1522294489.62845abc5ed9996db', '1522294489', '0', '127.0.0.1', '0', '1', '7200', '1', '0', '1');

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
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0其他，1新增，2删除，3删除',
  PRIMARY KEY (`id`),
  KEY `admin_operation_log_user_id_index` (`admin_id`),
  KEY `table` (`table`) USING BTREE,
  KEY `created_at` (`created_at`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of admin_operation_log
-- ----------------------------
INSERT INTO `admin_operation_log` VALUES ('1', '1', '/test', '1', '127.0.0.1', '{ \"name\": \"Administrator\", \"password\": \"$2y$10$JWwBpjZmoMqD7PPkjUotIu22dsCIT9mh.ttxH6Lop3u4pogvgqKYO\", \"password_confirmation\": \"$2y$10$JWwBpjZmoMqD7PPkjUotIu22dsCIT9mh.ttxH6Lop3u4pogvgqKYO\", \"_token\": \"qnvNxqktM7WQagJVZ2MG8LSTEkjRDr7N8rgbwIn3\", \"_method\": \"PUT\", \"_previous_\": \"http:\\/\\/www.l.com\\/admin\" }', '0', '', '0');

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
INSERT INTO `demo` VALUES ('2', '有匪', null, '2', '4', '破雪刀', null, '1', '周翡', '0', '1', null, null);
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
