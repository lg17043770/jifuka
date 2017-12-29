/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : blessed_card_collection

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2017-12-26 10:19:21
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for bcc_group
-- ----------------------------
DROP TABLE IF EXISTS `bcc_group`;
CREATE TABLE `bcc_group` (
  `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `group_creater_openid` char(28) DEFAULT NULL COMMENT '开团者openid',
  `group_status` tinyint(1) unsigned DEFAULT '1' COMMENT '开团状态：1为开团中，2为已成团，3为失败',
  `group_members_total` int(10) unsigned DEFAULT '4' COMMENT '成团数量，目前固定为4人',
  `group_members_count` int(10) unsigned DEFAULT '1' COMMENT '参团人数，默认1表示开团者，当达到4时，调用送券接口',
  `group_created_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `group_update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='开团表';

-- ----------------------------
-- Records of bcc_group
-- ----------------------------
INSERT INTO `bcc_group` VALUES ('4', '1234567891234567891234567899', '1', '4', '2', '1514010877', null);
INSERT INTO `bcc_group` VALUES ('5', '1234567891234567891234567898', '1', '4', '1', '1514010972', null);
INSERT INTO `bcc_group` VALUES ('6', '1234567891234567891234567897', '2', '4', '4', '1514010986', null);
INSERT INTO `bcc_group` VALUES ('7', '1234567891234567891234567890', '1', '4', '1', '1514013804', null);
INSERT INTO `bcc_group` VALUES ('15', '1234567891234567891234567891', '1', '4', '1', '1514173785', null);
INSERT INTO `bcc_group` VALUES ('16', '1234567891234567891234567893', '2', '4', '4', '1514173812', null);
INSERT INTO `bcc_group` VALUES ('17', '1234567891234567891234567893', '1', '4', '1', '1514182087', null);

-- ----------------------------
-- Table structure for bcc_group_detail
-- ----------------------------
DROP TABLE IF EXISTS `bcc_group_detail`;
CREATE TABLE `bcc_group_detail` (
  `detail_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `detail_openid` char(28) DEFAULT NULL COMMENT '组团队员的openid',
  `detail_created_time` int(11) unsigned DEFAULT NULL COMMENT '参团时间',
  `detail_card_code` varchar(50) DEFAULT NULL COMMENT '优惠券券码，通过送券接口获取',
  `detail_group_id` int(10) unsigned DEFAULT NULL COMMENT '所属团的团id',
  `detail_updated_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间,记录第一次创建时,此处为NULL',
  PRIMARY KEY (`detail_id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COMMENT='开团明细表';

-- ----------------------------
-- Records of bcc_group_detail
-- ----------------------------
INSERT INTO `bcc_group_detail` VALUES ('1', '1234567891234567891234567899', '1514010877', null, '4', null);
INSERT INTO `bcc_group_detail` VALUES ('2', '1234567891234567891234567898', '1514010972', null, '5', null);
INSERT INTO `bcc_group_detail` VALUES ('3', '1234567891234567891234567897', '1514010986', '1952095702', '6', '1514013499');
INSERT INTO `bcc_group_detail` VALUES ('11', '1234567891234567891234567890', '1514013401', '1682939740', '6', '1514013499');
INSERT INTO `bcc_group_detail` VALUES ('12', '1234567891234567891234567891', '1514013480', '425498943', '6', '1514013499');
INSERT INTO `bcc_group_detail` VALUES ('13', '1234567891234567891234567892', '1514013499', '1418784031', '6', '1514013499');
INSERT INTO `bcc_group_detail` VALUES ('14', '1234567891234567891234567890', '1514013791', null, '4', null);
INSERT INTO `bcc_group_detail` VALUES ('15', '1234567891234567891234567890', '1514013804', null, '7', null);
INSERT INTO `bcc_group_detail` VALUES ('27', '1234567891234567891234567898', '1514173865', '187980030', '16', '1514173865');
INSERT INTO `bcc_group_detail` VALUES ('26', '1234567891234567891234567896', '1514173858', '726008999', '16', '1514173865');
INSERT INTO `bcc_group_detail` VALUES ('25', '1234567891234567891234567895', '1514173849', '1715906418', '16', '1514173865');
INSERT INTO `bcc_group_detail` VALUES ('24', '1234567891234567891234567893', '1514173812', '1276132743', '16', '1514173865');
INSERT INTO `bcc_group_detail` VALUES ('23', '1234567891234567891234567891', '1514173785', null, '15', null);
INSERT INTO `bcc_group_detail` VALUES ('28', '1234567891234567891234567893', '1514182087', null, '17', null);

-- ----------------------------
-- Table structure for bcc_user
-- ----------------------------
DROP TABLE IF EXISTS `bcc_user`;
CREATE TABLE `bcc_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_openid` char(28) NOT NULL COMMENT '用户的openid',
  `user_nickname` varchar(30) NOT NULL COMMENT '用户昵称',
  `user_avatar_url` varchar(255) DEFAULT '' COMMENT '用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。',
  `user_created_time` int(11) DEFAULT NULL COMMENT '用户注册时间',
  `user_updated_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `user_unionid` char(29) DEFAULT NULL COMMENT '用户的unionid',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Records of bcc_user
-- ----------------------------
INSERT INTO `bcc_user` VALUES ('3', '1234567891234567891234567891', 'lifegood', 'http://www.leiyu.com', null, null, null);
INSERT INTO `bcc_user` VALUES ('4', '1234567891234567891234567892', 'lifegood', 'http://www.leiyu234.com', '1513849441', '1513849477', null);
INSERT INTO `bcc_user` VALUES ('5', '1234567891234567891234567893', 'lifegood3344', 'http://www.sldfk.com', '1513921676', null, null);
INSERT INTO `bcc_user` VALUES ('6', '1234567891234567891234567894', 'lifegood3344', 'http://www.sdkf.com', '1513922399', null, null);
INSERT INTO `bcc_user` VALUES ('7', '1234567891234567891234567895', 'lifegood33441', 'http://www.sdkf2.com', '1514009025', null, null);
INSERT INTO `bcc_user` VALUES ('8', '1234567891234567891234567896', 'lifegood33441', 'http://www.leigao112.com', '1514009139', null, null);
INSERT INTO `bcc_user` VALUES ('9', '1234567891234567891234567897', 'lifegood33441', 'http://www.leigao113.com', '1514009145', null, null);
INSERT INTO `bcc_user` VALUES ('10', '1234567891234567891234567898', 'lifegood33441', 'http://www.leigao114.com', '1514009150', null, null);
INSERT INTO `bcc_user` VALUES ('11', '1234567891234567891234567899', 'lifegood33441', 'http://lie.com', '1514009154', '1514166646', null);
INSERT INTO `bcc_user` VALUES ('31', 'a123456789123456789123456789', '', '', '1514184694', null, '');
