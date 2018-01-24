/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50540
Source Host           : localhost:3306
Source Database       : xyk

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2017-12-08 23:09:00
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for xyk_adminlog
-- ----------------------------
DROP TABLE IF EXISTS `xyk_adminlog`;
CREATE TABLE `xyk_adminlog` (
  `LogId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UserId` int(10) unsigned DEFAULT NULL,
  `LogType` int(11) DEFAULT '0',
  `Description` text,
  `LoginIp` varchar(15) DEFAULT NULL,
  `AddTime` datetime DEFAULT NULL,
  `Ext1` varchar(50) DEFAULT NULL,
  `Ext2` varchar(50) DEFAULT NULL,
  `Ext3` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`LogId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='后台用户日志表';

-- ----------------------------
-- Records of xyk_adminlog
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_adminuser
-- ----------------------------
DROP TABLE IF EXISTS `xyk_adminuser`;
CREATE TABLE `xyk_adminuser` (
  `UserId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `MerchantId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Id',
  `Username` varchar(60) NOT NULL,
  `RealName` varchar(20) NOT NULL,
  `Email` varchar(60) DEFAULT NULL,
  `Password` varchar(32) NOT NULL,
  `UsertypeId` int(11) unsigned DEFAULT NULL,
  `Ip` varchar(15) DEFAULT NULL,
  `AddTime` datetime NOT NULL,
  `IsUsed` int(11) NOT NULL DEFAULT '0',
  `LastLogin` datetime DEFAULT NULL,
  `LastIp` varchar(15) DEFAULT NULL,
  `AttemptTime` int(10) unsigned DEFAULT '0' COMMENT '尝试登录时间(时间戳)',
  `AttemptNums` tinyint(2) unsigned DEFAULT '0' COMMENT '尝试登录次数',
  `Ext1` varchar(50) DEFAULT NULL,
  `Ext2` varchar(50) DEFAULT NULL,
  `Ext3` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`UserId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='后台用户表';

-- ----------------------------
-- Records of xyk_adminuser
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_apiresponselog
-- ----------------------------
DROP TABLE IF EXISTS `xyk_apiresponselog`;
CREATE TABLE `xyk_apiresponselog` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `RequestId` int(11) unsigned DEFAULT NULL,
  `ResultCode` varchar(10) NOT NULL COMMENT '响应码',
  `ErrorCode` varchar(10) DEFAULT NULL COMMENT '错误编码',
  `Message` varchar(255) DEFAULT NULL COMMENT '描述',
  `Content` text NOT NULL COMMENT '响应内容',
  `AddTime` datetime NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=117579 DEFAULT CHARSET=utf8 COMMENT='接口响应日志';

-- ----------------------------
-- Records of xyk_apiresponselog
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_area
-- ----------------------------
DROP TABLE IF EXISTS `xyk_area`;
CREATE TABLE `xyk_area` (
  `AreaID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `AreaName` varchar(50) NOT NULL,
  `RootID` int(10) unsigned NOT NULL,
  `ChildAmount` int(10) unsigned NOT NULL,
  `Depth` int(11) NOT NULL,
  `Sort` int(11) NOT NULL,
  `IsOpen` tinyint(1) NOT NULL DEFAULT '0',
  `Ext1` varchar(50) DEFAULT NULL,
  `Ext2` varchar(50) DEFAULT NULL,
  `Ext3` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`AreaID`)
) ENGINE=InnoDB AUTO_INCREMENT=46647 DEFAULT CHARSET=utf8 COMMENT='地区表';

-- ----------------------------
-- Records of xyk_area
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_bankcard
-- ----------------------------
DROP TABLE IF EXISTS `xyk_bankcard`;
CREATE TABLE `xyk_bankcard` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '银行卡ID',
  `BankName` varchar(100) DEFAULT NULL COMMENT '银行卡名称',
  `statue` int(11) DEFAULT '0' COMMENT '银行卡设置状态; 0|正常；1|限制',
  `Isvalid` tinyint(1) DEFAULT '0' COMMENT '是否有效（1|有效，0|无效）',
  `AddTime` int(50) DEFAULT '0',
  `BankCode` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '银行卡编号',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COMMENT='银行卡列表';

-- ----------------------------
-- Records of xyk_bankcard
-- ----------------------------
INSERT INTO `xyk_bankcard` VALUES ('1', '工商银行', '0', '1', '0', 'ICBC');
INSERT INTO `xyk_bankcard` VALUES ('2', '农业银行', '0', '1', '0', 'ABC');
INSERT INTO `xyk_bankcard` VALUES ('3', '中国银行', '0', '1', '0', 'BOC');
INSERT INTO `xyk_bankcard` VALUES ('4', '建设银行', '0', '0', '0', 'CCB');
INSERT INTO `xyk_bankcard` VALUES ('5', '招商银行', '0', '0', '0', 'CMBCHINA');
INSERT INTO `xyk_bankcard` VALUES ('6', '邮政储蓄', '0', '0', '0', 'POST');
INSERT INTO `xyk_bankcard` VALUES ('7', '中信银行', '0', '0', '0', 'ECITIC');
INSERT INTO `xyk_bankcard` VALUES ('8', '光大银行', '0', '0', '0', 'CEB');
INSERT INTO `xyk_bankcard` VALUES ('9', '交通银行', '0', '0', '0', 'BOCO');
INSERT INTO `xyk_bankcard` VALUES ('10', '兴业银行', '0', '0', '0', 'CIB');
INSERT INTO `xyk_bankcard` VALUES ('11', '民生银行', '0', '0', '0', 'CMBC');
INSERT INTO `xyk_bankcard` VALUES ('12', '平安银行', '0', '0', '0', 'PINGAN');
INSERT INTO `xyk_bankcard` VALUES ('13', '广发银行', '0', '0', '0', 'CGB');
INSERT INTO `xyk_bankcard` VALUES ('14', '北京银行', '0', '0', '0', 'BCCB');
INSERT INTO `xyk_bankcard` VALUES ('15', '华夏银行', '0', '0', '0', 'HXB');
INSERT INTO `xyk_bankcard` VALUES ('16', '浦发银行', '0', '0', '0', 'SPDB');
INSERT INTO `xyk_bankcard` VALUES ('17', '上海银行', '0', '0', '0', 'SHB');
INSERT INTO `xyk_bankcard` VALUES ('18', '渤海银行', '0', '0', '0', 'CBHB');
INSERT INTO `xyk_bankcard` VALUES ('19', '江苏银行', '0', '0', '0', 'JSB');

-- ----------------------------
-- Table structure for xyk_billdetails
-- ----------------------------
DROP TABLE IF EXISTS `xyk_billdetails`;
CREATE TABLE `xyk_billdetails` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `BillId` int(11) unsigned NOT NULL COMMENT '账单ID',
  `CreditId` int(11) unsigned DEFAULT '0' COMMENT '信用卡id',
  `BankId` int(11) unsigned DEFAULT '0' COMMENT '银行卡id',
  `UserId` int(11) unsigned DEFAULT NULL COMMENT '用户id',
  `OrderNum` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '订单编号',
  `CardId` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '交易卡号',
  `CreateTime` int(50) unsigned DEFAULT '0' COMMENT '创建时间',
  `AddTime` int(50) unsigned DEFAULT '0',
  `Amount` decimal(10,2) unsigned DEFAULT NULL COMMENT '交易金额',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='账单详情表';

-- ----------------------------
-- Records of xyk_billdetails
-- ----------------------------
INSERT INTO `xyk_billdetails` VALUES ('45', '48', '1', '0', '82', '20171206100035733189', '6225768758046880', '1512525635', '1512525635', '2427.00', null, null);
INSERT INTO `xyk_billdetails` VALUES ('46', '49', '1', '0', '82', '20171206100035171319', '6225768758046880', '1512525635', '1512525635', '1170.00', null, null);
INSERT INTO `xyk_billdetails` VALUES ('47', '50', '1', '0', '82', '20171206100035620097', '6225768758046880', '1512525635', '1512525635', '1257.00', null, null);
INSERT INTO `xyk_billdetails` VALUES ('48', '51', '1', '0', '82', '20171206100035514584', '6225768758046880', '1512525635', '1512525635', '1785.00', null, null);
INSERT INTO `xyk_billdetails` VALUES ('49', '52', '1', '0', '82', '20171206100035295648', '6225768758046880', '1512525635', '1512525635', '765.00', null, null);
INSERT INTO `xyk_billdetails` VALUES ('50', '53', '1', '0', '82', '20171206100035132869', '6225768758046880', '1512525635', '1512525635', '1020.00', null, null);
INSERT INTO `xyk_billdetails` VALUES ('51', '54', '1', '0', '82', '20171206100035241232', '6225768758046880', '1512525635', '1512525635', '788.00', null, null);
INSERT INTO `xyk_billdetails` VALUES ('52', '55', '1', '0', '82', '20171206100035833501', '6225768758046880', '1512525635', '1512525635', '384.00', null, null);
INSERT INTO `xyk_billdetails` VALUES ('53', '56', '1', '0', '82', '20171206100035670439', '6225768758046880', '1512525635', '1512525635', '404.00', null, null);
INSERT INTO `xyk_billdetails` VALUES ('55', '58', '1', '0', '82', '20171206172119180134', '6225768758046880', '1512552079', '1512552079', '2500.00', null, null);
INSERT INTO `xyk_billdetails` VALUES ('56', '59', '1', '0', '82', '20171207123356634596', '6225768758046880', '1512621236', '1512621236', '1.00', null, null);
INSERT INTO `xyk_billdetails` VALUES ('57', '60', '1', '0', '82', '20171207123713264932', '6225768758046880', '1512621433', '1512621433', '0.50', null, null);
INSERT INTO `xyk_billdetails` VALUES ('58', '61', '1', '0', '82', '20171207123745976700', '6225768758046880', '1512621465', '1512621465', '1.00', null, null);
INSERT INTO `xyk_billdetails` VALUES ('59', '62', '1', '0', '82', '20171207123922244107', '6225768758046880', '1512621562', '1512621562', '1.00', null, null);
INSERT INTO `xyk_billdetails` VALUES ('60', '63', '1', '0', '82', '20171207123940190725', '6225768758046880', '1512621580', '1512621580', '1.00', null, null);
INSERT INTO `xyk_billdetails` VALUES ('61', '64', '1', '0', '82', '20171207123941777760', '6225768758046880', '1512621581', '1512621581', '1.00', null, null);
INSERT INTO `xyk_billdetails` VALUES ('62', '65', '1', '0', '82', '20171207123945302799', '6225768758046880', '1512621585', '1512621585', '1.00', null, null);
INSERT INTO `xyk_billdetails` VALUES ('63', '66', '1', '0', '82', '20171207124028735956', '6225768758046880', '1512621628', '1512621628', '1.00', null, null);
INSERT INTO `xyk_billdetails` VALUES ('64', '67', '1', '0', '82', '20171207124237436638', '6225768758046880', '1512621757', '1512621757', '1.00', null, null);
INSERT INTO `xyk_billdetails` VALUES ('65', '68', '1', '0', '82', '20171207124254207970', '6225768758046880', '1512621774', '1512621774', '1.00', null, null);

-- ----------------------------
-- Table structure for xyk_billlistlog
-- ----------------------------
DROP TABLE IF EXISTS `xyk_billlistlog`;
CREATE TABLE `xyk_billlistlog` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '账单ID',
  `CreditId` int(11) DEFAULT NULL COMMENT '交易卡 id  ',
  `BankId` int(11) DEFAULT NULL COMMENT '结算卡 id',
  `UserId` int(11) unsigned DEFAULT NULL COMMENT '用户id',
  `BackTime` int(50) unsigned DEFAULT '0' COMMENT '还款时间',
  `status` int(11) unsigned DEFAULT '0' COMMENT '还款状态; 0|失败；1|成功; 2|处理中',
  `AddTime` int(50) unsigned DEFAULT '0',
  `Amount` decimal(10,2) unsigned DEFAULT NULL COMMENT '账单金额',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `Type` int(11) DEFAULT NULL COMMENT '1 充值 2 提现 3 还款 4 还款消费 5 保证金收取 6 保证金返还',
  `feeType` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '手续费类型',
  `TableId` int(11) DEFAULT NULL COMMENT '业务表ID',
  `PayBankId` int(11) DEFAULT NULL COMMENT '如果设置了 使用银行卡来收取保证金 字段为银行卡id',
  `SysFee` decimal(10,2) DEFAULT NULL COMMENT '系统手续费',
  `From` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '钱来源',
  `To` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '钱 去向',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='账单列表';

-- ----------------------------
-- Records of xyk_billlistlog
-- ----------------------------
INSERT INTO `xyk_billlistlog` VALUES ('49', '1', '0', '82', '0', '1', '1512525635', '1170.00', '2017-12-06 10:00:35', '2017-12-06 15:41:33', '4', '', '180', null, null, null, null);
INSERT INTO `xyk_billlistlog` VALUES ('58', '1', '0', '82', '0', '2', '1512552079', '2500.00', '2017-12-06 17:21:19', '2017-12-06 17:24:52', '5', '', '180', null, null, null, null);
INSERT INTO `xyk_billlistlog` VALUES ('59', '1', '0', '82', '0', '2', '1512621236', '1.00', '2017-12-07 12:33:56', null, '1', '', '0', null, null, null, null);
INSERT INTO `xyk_billlistlog` VALUES ('60', '1', '0', '82', '0', '2', '1512621433', '0.50', '2017-12-07 12:37:13', null, '1', '', '0', null, null, null, null);
INSERT INTO `xyk_billlistlog` VALUES ('61', '1', '0', '82', '0', '2', '1512621465', '1.00', '2017-12-07 12:37:45', null, '1', '', '0', null, null, null, null);
INSERT INTO `xyk_billlistlog` VALUES ('62', '1', '0', '82', '0', '2', '1512621562', '1.00', '2017-12-07 12:39:22', null, '1', '', '0', null, null, null, null);
INSERT INTO `xyk_billlistlog` VALUES ('63', '1', '0', '82', '0', '2', '1512621580', '1.00', '2017-12-07 12:39:40', null, '1', '', '0', null, null, null, null);
INSERT INTO `xyk_billlistlog` VALUES ('64', '1', '0', '82', '0', '2', '1512621581', '1.00', '2017-12-07 12:39:41', null, '1', '', '0', null, null, null, null);
INSERT INTO `xyk_billlistlog` VALUES ('65', '1', '0', '82', '0', '2', '1512621585', '1.00', '2017-12-07 12:39:45', null, '1', '', '0', null, null, null, null);
INSERT INTO `xyk_billlistlog` VALUES ('66', '1', '0', '82', '0', '2', '1512621628', '1.00', '2017-12-07 12:40:28', null, '1', '', '0', null, null, null, null);
INSERT INTO `xyk_billlistlog` VALUES ('67', '1', '0', '82', '0', '2', '1512621757', '1.00', '2017-12-07 12:42:37', null, '1', '', '0', null, null, null, null);
INSERT INTO `xyk_billlistlog` VALUES ('68', '1', '0', '82', '0', '2', '1512621774', '1.00', '2017-12-07 12:42:54', null, '1', '', '0', null, null, null, null);

-- ----------------------------
-- Table structure for xyk_category
-- ----------------------------
DROP TABLE IF EXISTS `xyk_category`;
CREATE TABLE `xyk_category` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',
  `ParentCid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父级分类ID',
  `Name` varchar(50) NOT NULL COMMENT '分类名称',
  `IsLeaf` tinyint(1) unsigned DEFAULT NULL COMMENT '是否为页子类目(true/false)',
  `SortOrder` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '类目建议排序号',
  `FeatureList` varchar(20) DEFAULT NULL COMMENT '特征列表',
  `DateRequire` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否需要选择日期',
  `Isvalid` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否有效',
  `AddTime` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  `UpdateTime` int(10) unsigned DEFAULT '0' COMMENT '修改时间',
  `Ext1` varchar(50) DEFAULT NULL,
  `Ext2` varchar(50) DEFAULT NULL,
  `Ext3` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `UK_CATEGORY_CID` (`Cid`),
  KEY `parent_cid` (`ParentCid`),
  KEY `is_leaf` (`IsLeaf`),
  KEY `I_SortOrder` (`SortOrder`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='票分类表';

-- ----------------------------
-- Records of xyk_category
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_centerlog
-- ----------------------------
DROP TABLE IF EXISTS `xyk_centerlog`;
CREATE TABLE `xyk_centerlog` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `DomainUri` varchar(50) DEFAULT NULL COMMENT '请求域名',
  `LogType` tinyint(1) DEFAULT '1' COMMENT '日志类型(1分发请求 2分发响应 3分发异步通知)',
  `Content` text COMMENT '内容',
  `AddTime` datetime NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='分发日志';

-- ----------------------------
-- Records of xyk_centerlog
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_creditcard
-- ----------------------------
DROP TABLE IF EXISTS `xyk_creditcard`;
CREATE TABLE `xyk_creditcard` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '银行卡ID',
  `CreditCard` varchar(100) DEFAULT NULL COMMENT '银行卡名称',
  `statue` int(11) DEFAULT '0' COMMENT '信用卡设置状态; 0|正常；1|限制',
  `Isvalid` tinyint(1) DEFAULT '0' COMMENT '是否有效（1|有效，0|无效）',
  `AddTime` int(50) DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='信用卡列表列表';

-- ----------------------------
-- Records of xyk_creditcard
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_dic
-- ----------------------------
DROP TABLE IF EXISTS `xyk_dic`;
CREATE TABLE `xyk_dic` (
  `DicId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `DicKey` smallint(5) unsigned NOT NULL COMMENT '键',
  `DicValue` varchar(50) NOT NULL COMMENT '值',
  `EnumName` varchar(50) NOT NULL DEFAULT '' COMMENT '枚举名称',
  `DicTypeId` int(10) unsigned NOT NULL COMMENT '字典分类ID',
  `Description` varchar(500) NOT NULL COMMENT '描述',
  `DicOrder` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `AddDate` datetime NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`DicId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='字典表';

-- ----------------------------
-- Records of xyk_dic
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_dictype
-- ----------------------------
DROP TABLE IF EXISTS `xyk_dictype`;
CREATE TABLE `xyk_dictype` (
  `DicTypeId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `TypeName` varchar(100) NOT NULL COMMENT '字典分类名',
  `TypeEnumName` varchar(50) NOT NULL DEFAULT '' COMMENT '字典类型英文名',
  `Description` varchar(500) NOT NULL COMMENT '描述',
  `DictypeOrder` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `CreateTime` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`DicTypeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='字典分类表';

-- ----------------------------
-- Records of xyk_dictype
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_fee
-- ----------------------------
DROP TABLE IF EXISTS `xyk_fee`;
CREATE TABLE `xyk_fee` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `PlanFee` decimal(10,2) DEFAULT '0.00' COMMENT '计划手续费 百分比',
  `SettleFee` decimal(10,2) DEFAULT '0.00' COMMENT '提现手续费 百分比',
  `PayFee` decimal(10,2) DEFAULT '0.00' COMMENT '充值手续费 百分比',
  `RepayFee` decimal(10,2) DEFAULT '0.00' COMMENT '还款费率',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of xyk_fee
-- ----------------------------
INSERT INTO `xyk_fee` VALUES ('1', '1.00', '1.00', '1.00', '1.00');

-- ----------------------------
-- Table structure for xyk_jyexception
-- ----------------------------
DROP TABLE IF EXISTS `xyk_jyexception`;
CREATE TABLE `xyk_jyexception` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ExceptionType` smallint(5) unsigned NOT NULL COMMENT '异常/错误 类型(1.阿里大于发送短信)',
  `Content` text COMMENT '异常/错误内容(请求的内容与返回的异常内容一起写入该字段)',
  `AddTime` int(10) unsigned DEFAULT NULL COMMENT '数据添加时间',
  `Ext1` varchar(50) DEFAULT NULL,
  `Ext2` varchar(50) DEFAULT NULL,
  `Ext3` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `I_EXCEPTION_TYPE` (`ExceptionType`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COMMENT='异常表';

-- ----------------------------
-- Records of xyk_jyexception
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_labelcontent
-- ----------------------------
DROP TABLE IF EXISTS `xyk_labelcontent`;
CREATE TABLE `xyk_labelcontent` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `UserId` int(11) unsigned NOT NULL COMMENT '用户Id',
  `MerchantId` int(11) unsigned NOT NULL COMMENT '商户Id',
  `LabelId` int(11) unsigned NOT NULL COMMENT '标签Id',
  `Content` varchar(100) DEFAULT '' COMMENT '标签内容',
  `OptionId` varchar(200) NOT NULL DEFAULT '' COMMENT '标签对应选项的id，单选则为空',
  `AddTime` int(10) unsigned DEFAULT '0' COMMENT '添加时间',
  `UpdateTime` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`Id`),
  KEY `INDEX_USERID` (`UserId`),
  KEY `INDEX_MERCHANT` (`MerchantId`)
) ENGINE=InnoDB AUTO_INCREMENT=154 DEFAULT CHARSET=utf8mb4 COMMENT='商户自定义标签的用户内容表';

-- ----------------------------
-- Records of xyk_labelcontent
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_labeloption
-- ----------------------------
DROP TABLE IF EXISTS `xyk_labeloption`;
CREATE TABLE `xyk_labeloption` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `MerchantId` int(11) unsigned NOT NULL COMMENT '商户Id',
  `LabelId` int(11) unsigned NOT NULL COMMENT '标签Id',
  `Sort` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '排序',
  `Name` varchar(100) DEFAULT '' COMMENT '选项内容',
  `Isvalid` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否有效(1有效 0无效)',
  `AddTime` int(10) unsigned DEFAULT '0' COMMENT '添加时间',
  `UpdateTime` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`Id`),
  KEY `INDEX_LCMERCHANTID` (`MerchantId`),
  KEY `INDEX_LABELID` (`LabelId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户自定义标签选项表';

-- ----------------------------
-- Records of xyk_labeloption
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_menu
-- ----------------------------
DROP TABLE IF EXISTS `xyk_menu`;
CREATE TABLE `xyk_menu` (
  `MenuId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `MenuName` varchar(20) NOT NULL,
  `MenuDes` varchar(300) DEFAULT NULL,
  `Url` varchar(100) DEFAULT NULL,
  `NewUrl` varchar(255) DEFAULT NULL COMMENT '菜单地址',
  `RootId` int(11) NOT NULL DEFAULT '0',
  `ParentId` int(11) NOT NULL DEFAULT '0',
  `RankId` int(11) DEFAULT NULL COMMENT '菜单级别',
  `MenuOrder` smallint(6) NOT NULL DEFAULT '0',
  `type` int(11) NOT NULL DEFAULT '1' COMMENT '菜单类型',
  `Addtime` datetime NOT NULL,
  PRIMARY KEY (`MenuId`),
  UNIQUE KEY `RootId` (`RootId`)
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=utf8 COMMENT='后台菜单表';

-- ----------------------------
-- Records of xyk_menu
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_merchant
-- ----------------------------
DROP TABLE IF EXISTS `xyk_merchant`;
CREATE TABLE `xyk_merchant` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '商户Id',
  `Name` varchar(100) NOT NULL COMMENT '商户名称',
  `MerchantNo` varchar(20) DEFAULT NULL COMMENT '商户编号',
  `Tel` varchar(50) DEFAULT NULL COMMENT '电话（可填多个）',
  `Logo` varchar(150) DEFAULT NULL COMMENT '商户logo',
  `ImageTime` int(10) unsigned DEFAULT NULL COMMENT '图片时间',
  `DomainKey` varchar(20) DEFAULT '' COMMENT '图片服务器二级域名',
  `Address` varchar(150) DEFAULT '' COMMENT '具体地址',
  `Contact` varchar(50) DEFAULT '' COMMENT '联系人',
  `Summary` text COMMENT '商户简介',
  `TicketType` varchar(100) DEFAULT '' COMMENT '票务类型，多个用逗号隔开,id取category',
  `AddTime` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  `UpdateTime` int(10) unsigned DEFAULT '0' COMMENT '更新时间',
  `Ext1` varchar(50) DEFAULT NULL,
  `Ext2` varchar(50) DEFAULT NULL,
  `Ext3` varchar(50) DEFAULT NULL,
  `Ext4` varchar(50) DEFAULT NULL,
  `Ext5` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='商户表';

-- ----------------------------
-- Records of xyk_merchant
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_merchantaccount
-- ----------------------------
DROP TABLE IF EXISTS `xyk_merchantaccount`;
CREATE TABLE `xyk_merchantaccount` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '商户Id',
  `MerchantNo` varchar(20) DEFAULT NULL COMMENT '商户编号',
  `TotalBalance` decimal(17,3) DEFAULT '0.000' COMMENT '帐户余额(包含冻结的)',
  `Balance` decimal(17,3) DEFAULT '0.000' COMMENT '帐户可用余额',
  `AddTime` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  `UpdateTime` int(10) unsigned DEFAULT '0' COMMENT '更新时间',
  `Ext1` varchar(50) DEFAULT NULL,
  `Ext2` varchar(50) DEFAULT NULL,
  `Ext3` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商户账户表';

-- ----------------------------
-- Records of xyk_merchantaccount
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_merchantaccountlog
-- ----------------------------
DROP TABLE IF EXISTS `xyk_merchantaccountlog`;
CREATE TABLE `xyk_merchantaccountlog` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `MerchantId` int(11) unsigned NOT NULL COMMENT '商户Id',
  `Amount` decimal(17,3) DEFAULT '0.000' COMMENT '金额',
  `FundType` tinyint(1) NOT NULL DEFAULT '0' COMMENT '收支类型(0支出 1收入)',
  `Des` varchar(200) DEFAULT NULL COMMENT '账户金额变动描述',
  `AddTime` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  `Ext1` varchar(50) DEFAULT NULL,
  `Ext2` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商户账户日志表';

-- ----------------------------
-- Records of xyk_merchantaccountlog
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_merchantad
-- ----------------------------
DROP TABLE IF EXISTS `xyk_merchantad`;
CREATE TABLE `xyk_merchantad` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `MerchantId` int(11) unsigned NOT NULL COMMENT '商户Id',
  `ImageUrl` varchar(200) DEFAULT NULL COMMENT '广告图片地址',
  `LinkUrl` varchar(200) DEFAULT NULL COMMENT '广告跳转地址',
  `AdType` smallint(5) DEFAULT '0' COMMENT '广告类型',
  `Des` varchar(200) DEFAULT NULL COMMENT '描述说明',
  `Isvalid` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效',
  `Sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `AddTime` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  `UpdateTime` int(10) unsigned DEFAULT '0' COMMENT '更新时间',
  `Ext1` varchar(50) DEFAULT NULL,
  `Ext2` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COMMENT='商户广告/轮播图表';

-- ----------------------------
-- Records of xyk_merchantad
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_merchantlabel
-- ----------------------------
DROP TABLE IF EXISTS `xyk_merchantlabel`;
CREATE TABLE `xyk_merchantlabel` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `MerchantId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商户id',
  `Label` varchar(50) NOT NULL COMMENT '自定义标签名称',
  `Required` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否必填',
  `Sort` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '排序',
  `Status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用(1启用 0禁用)',
  `Isvalid` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否有效(1有效 0已删除)',
  `Type` varchar(20) NOT NULL DEFAULT 'text' COMMENT '标签内容类型(text:文本框 optional:下拉框 optionaltext:下拉文本 multicheck:多选 radio:单选)',
  `AddTime` int(10) unsigned DEFAULT NULL COMMENT '添加时间',
  `UpdateTime` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`Id`),
  KEY `INDEX_MERCHANTID` (`MerchantId`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COMMENT='商户自定义标签表';

-- ----------------------------
-- Records of xyk_merchantlabel
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_merchantusers
-- ----------------------------
DROP TABLE IF EXISTS `xyk_merchantusers`;
CREATE TABLE `xyk_merchantusers` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '商户用户Id',
  `MerchantId` int(11) unsigned NOT NULL COMMENT '商户Id',
  `Username` varchar(50) NOT NULL COMMENT '商户用户名',
  `Password` varchar(32) NOT NULL COMMENT '登录密码',
  `RealName` varchar(20) DEFAULT NULL COMMENT '真实姓名',
  `MainUser` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否是主用户(0不是 1是)',
  `Status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '帐号状态(0停用 1启用)',
  `Isvalid` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0账户删除 1账户存在',
  `AddTime` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  `UpdateTime` int(10) unsigned DEFAULT '0' COMMENT '更新时间',
  `Ext1` varchar(50) DEFAULT NULL,
  `Ext2` varchar(50) DEFAULT NULL,
  `Ext3` varchar(50) DEFAULT NULL,
  `Ext4` varchar(50) DEFAULT NULL,
  `Ext5` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `UK_VENUEUSERS_USERNAME` (`Username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商户用户表';

-- ----------------------------
-- Records of xyk_merchantusers
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_news
-- ----------------------------
DROP TABLE IF EXISTS `xyk_news`;
CREATE TABLE `xyk_news` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `MerchantId` int(11) unsigned NOT NULL COMMENT '所属商户Id',
  `Title` varchar(200) NOT NULL COMMENT '资讯标题',
  `SubTitle` varchar(100) DEFAULT '' COMMENT '子标题',
  `KeyWords` varchar(100) DEFAULT '' COMMENT '关键词',
  `Author` varchar(50) DEFAULT NULL COMMENT '作者',
  `Description` varchar(1000) DEFAULT NULL COMMENT '描述',
  `Content` longtext NOT NULL COMMENT '资讯内容',
  `PublishSource` varchar(50) DEFAULT '' COMMENT '来源',
  `NewsType` int(11) DEFAULT '0' COMMENT '资讯类型',
  `Isvalid` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否有效',
  `Top` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否置顶显示',
  `IsDisplay` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `Image` varchar(150) CHARACTER SET utf8 DEFAULT NULL COMMENT '图片名',
  `ImageTime` int(10) unsigned DEFAULT NULL COMMENT '图片时间',
  `DomainKey` varchar(20) CHARACTER SET utf8 DEFAULT '' COMMENT '图片服务器二级域名',
  `EffectiveTime` int(10) unsigned NOT NULL COMMENT '生效时间',
  `AddTime` int(10) unsigned DEFAULT NULL COMMENT '资讯添加时间',
  `UpdateTime` int(10) unsigned DEFAULT NULL COMMENT '资讯修改时间',
  `Ext1` varchar(50) DEFAULT NULL,
  `Ext2` varchar(50) DEFAULT NULL,
  `Ext3` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `I_ZIXUN_MERCHANTID` (`MerchantId`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COMMENT='资讯表';

-- ----------------------------
-- Records of xyk_news
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_newseffect
-- ----------------------------
DROP TABLE IF EXISTS `xyk_newseffect`;
CREATE TABLE `xyk_newseffect` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `NewsId` int(11) unsigned NOT NULL COMMENT '资讯Id',
  `UserId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户Id,游客为0',
  `ActionType` smallint(5) unsigned NOT NULL DEFAULT '10001' COMMENT '资讯动作类型，参考字典',
  `Content` varchar(500) DEFAULT NULL COMMENT '内容(如果是评论，即为评论内容，其他的可不填)',
  `AddTime` int(10) unsigned DEFAULT NULL COMMENT '数据添加时间',
  `Ext1` varchar(50) DEFAULT NULL,
  `Ext2` varchar(50) DEFAULT NULL,
  `Ext3` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `I_INFEFFECT_INFID` (`NewsId`) USING BTREE,
  KEY `I_INFEFFECT_TYPE` (`ActionType`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户对资讯的影响表';

-- ----------------------------
-- Records of xyk_newseffect
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_newsstatistics
-- ----------------------------
DROP TABLE IF EXISTS `xyk_newsstatistics`;
CREATE TABLE `xyk_newsstatistics` (
  `NewsId` int(11) unsigned NOT NULL COMMENT 'information表中的id',
  `ReadTimes` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '浏览次数',
  `LikeTimes` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '资讯被赞的次数',
  `UnLikeTimes` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '资讯被"踩"的次数',
  `CommentTimes` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '评论次数',
  `AddTime` int(10) unsigned DEFAULT NULL COMMENT '数据添加时间',
  `UpdateTime` int(10) unsigned DEFAULT NULL COMMENT '数据修改时间',
  `Ext1` varchar(50) DEFAULT NULL,
  `Ext2` varchar(50) DEFAULT NULL,
  `Ext3` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`NewsId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='资讯统计表（阅读次数、被赞次数、被踩次数等）';

-- ----------------------------
-- Records of xyk_newsstatistics
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_order
-- ----------------------------
DROP TABLE IF EXISTS `xyk_order`;
CREATE TABLE `xyk_order` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `OrderNum` varchar(30) NOT NULL COMMENT '交易编号',
  `hUserId` int(11) unsigned NOT NULL COMMENT '合利宝用户ID',
  `Mobile` int(11) DEFAULT NULL COMMENT '手机号',
  `OrderName` varchar(255) DEFAULT '' COMMENT '合利宝姓名',
  `OrderUserId` int(11) unsigned NOT NULL COMMENT '下单者的用户Id',
  `UserId` int(11) unsigned NOT NULL COMMENT '普通用户Id',
  `MerchantId` int(11) unsigned NOT NULL COMMENT '商户Id',
  `MerchantNum` varchar(50) DEFAULT NULL COMMENT '合利宝商户订单号',
  `Num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '购买数量',
  `TotalFee` decimal(11,3) DEFAULT '0.000' COMMENT '订单总金额',
  `OrderSource` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1信用卡；2储蓄卡',
  `Status` smallint(5) unsigned NOT NULL DEFAULT '10001' COMMENT '订单状态（参考字典）',
  `dealType` tinyint(1) NOT NULL COMMENT '交易类型',
  `RefundTime` int(10) unsigned DEFAULT '0' COMMENT '退票时间',
  `Isvalid` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效(0无效 1有效)订单超时未支付置为无效',
  `PayTime` int(10) unsigned DEFAULT '0' COMMENT '付款时间',
  `PayType` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '支付途径,1|信用卡 ；2|储蓄卡',
  `AddTime` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  `Ext1` varchar(50) DEFAULT NULL,
  `Ext2` varchar(50) DEFAULT NULL,
  `Ext3` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `ORDERNUM` (`OrderNum`)
) ENGINE=InnoDB AUTO_INCREMENT=1129 DEFAULT CHARSET=utf8 COMMENT='票订单表';

-- ----------------------------
-- Records of xyk_order
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_plan
-- ----------------------------
DROP TABLE IF EXISTS `xyk_plan`;
CREATE TABLE `xyk_plan` (
  `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT '计划表',
  `StartDate` datetime DEFAULT NULL,
  `EndDate` datetime DEFAULT NULL,
  `status` int(11) DEFAULT NULL COMMENT '0 初始化 ； 1 保证金已打， 计划执行中 ； 2 计划完成，等待退保证金； 3 保证金退还完成; 4 保证金收取中； 5 失败； 6 计划为创建',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `UserId` int(11) DEFAULT NULL,
  `BankId` int(11) DEFAULT NULL,
  `TotalMoney` decimal(10,2) DEFAULT NULL COMMENT '还款总金额',
  `fee` decimal(10,0) DEFAULT NULL COMMENT '手续费',
  `CashDeposit` decimal(10,2) DEFAULT NULL COMMENT '保证金',
  `res` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '失败原因 只有状态5 出现',
  `PayBankId` int(11) DEFAULT NULL COMMENT '扣款的银行卡  如果是0  走余额',
  `SysFee` decimal(10,2) DEFAULT '0.00' COMMENT '系统统计 调用接口使用费率 总合',
  `times` int(11) DEFAULT '0' COMMENT '笔数',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=404 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of xyk_plan
-- ----------------------------
INSERT INTO `xyk_plan` VALUES ('402', '2017-12-05 00:00:00', '2017-12-06 23:59:59', '6', '2017-12-08 00:16:18', '2017-12-08 00:16:18', '82', '1', '10000.00', '75', '2500.00', null, '0', '225.00', '3');
INSERT INTO `xyk_plan` VALUES ('403', '2017-12-05 00:00:00', '2017-12-06 23:59:59', '6', '2017-12-08 10:37:50', '2017-12-08 10:37:50', '82', '1', '10000.00', '75', '2500.00', null, '0', '225.00', '3');

-- ----------------------------
-- Table structure for xyk_plan_detail
-- ----------------------------
DROP TABLE IF EXISTS `xyk_plan_detail`;
CREATE TABLE `xyk_plan_detail` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `PlanId` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `Money` decimal(10,2) DEFAULT NULL,
  `Type` int(11) DEFAULT NULL COMMENT '1 还款  2 套现',
  `PayTime` datetime DEFAULT NULL,
  `status` int(11) DEFAULT NULL COMMENT '0 准备中  1已完成  2 处理中 ',
  `OrderNum` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `BankId` int(11) DEFAULT NULL COMMENT 'bankdcard 表ID',
  `SerialNum` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '平台流水',
  `Batch` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '批次 用于标注  哪个套现跟哪个还款是一组的',
  `sort` int(11) DEFAULT NULL,
  `SysFee` decimal(10,2) DEFAULT '0.00' COMMENT '每次接口收取费用 总合添加到 plan表',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3727 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of xyk_plan_detail
-- ----------------------------
INSERT INTO `xyk_plan_detail` VALUES ('3709', '402', '2017-12-08 00:16:18', null, '1955.00', '1', '2017-12-05 11:35:35', '0', null, '1', null, '201712080016185492390', '1', '19.55');
INSERT INTO `xyk_plan_detail` VALUES ('3710', '402', '2017-12-08 00:16:18', '2017-12-08 00:16:18', '793.00', '2', '2017-12-05 12:08:49', '0', null, '1', null, '201712080016185492390', '2', '7.93');
INSERT INTO `xyk_plan_detail` VALUES ('3711', '402', '2017-12-08 00:16:18', '2017-12-08 00:16:18', '1162.00', '2', '2017-12-05 12:12:31', '0', null, '1', null, '201712080016185492390', '3', '11.62');
INSERT INTO `xyk_plan_detail` VALUES ('3712', '402', '2017-12-08 00:16:18', null, '2418.00', '1', '2017-12-05 12:44:09', '0', null, '1', null, '201712080016185165841', '4', '24.18');
INSERT INTO `xyk_plan_detail` VALUES ('3713', '402', '2017-12-08 00:16:18', '2017-12-08 00:16:18', '884.00', '2', '2017-12-05 18:45:17', '0', null, '1', null, '201712080016185165841', '5', '8.84');
INSERT INTO `xyk_plan_detail` VALUES ('3714', '402', '2017-12-08 00:16:18', '2017-12-08 00:16:18', '1534.00', '2', '2017-12-05 20:59:41', '0', null, '1', null, '201712080016185165841', '6', '15.34');
INSERT INTO `xyk_plan_detail` VALUES ('3715', '402', '2017-12-08 00:16:18', null, '5627.00', '1', '2017-12-06 15:11:16', '0', null, '1', null, '201712080016182702262', '7', '56.27');
INSERT INTO `xyk_plan_detail` VALUES ('3716', '402', '2017-12-08 00:16:18', '2017-12-08 00:16:18', '2748.00', '2', '2017-12-06 16:37:59', '0', null, '1', null, '201712080016182702262', '8', '27.48');
INSERT INTO `xyk_plan_detail` VALUES ('3717', '402', '2017-12-08 00:16:18', '2017-12-08 00:16:18', '2879.00', '2', '2017-12-06 16:40:20', '0', null, '1', null, '201712080016182702262', '9', '28.79');
INSERT INTO `xyk_plan_detail` VALUES ('3718', '403', '2017-12-08 10:37:50', null, '2374.00', '1', '2017-12-05 07:48:01', '0', null, '1', null, '201712081037508178850', '1', '23.74');
INSERT INTO `xyk_plan_detail` VALUES ('3719', '403', '2017-12-08 10:37:50', '2017-12-08 10:37:50', '910.00', '2', '2017-12-05 08:00:24', '0', null, '1', null, '201712081037508178850', '2', '9.10');
INSERT INTO `xyk_plan_detail` VALUES ('3720', '403', '2017-12-08 10:37:50', '2017-12-08 10:37:50', '1464.00', '2', '2017-12-05 08:31:18', '0', null, '1', null, '201712081037508178850', '3', '14.64');
INSERT INTO `xyk_plan_detail` VALUES ('3721', '403', '2017-12-08 10:37:50', null, '1726.00', '1', '2017-12-05 09:57:40', '0', null, '1', null, '201712081037506552141', '4', '17.26');
INSERT INTO `xyk_plan_detail` VALUES ('3722', '403', '2017-12-08 10:37:50', '2017-12-08 10:37:50', '742.00', '2', '2017-12-05 18:48:54', '0', null, '1', null, '201712081037506552141', '5', '7.42');
INSERT INTO `xyk_plan_detail` VALUES ('3723', '403', '2017-12-08 10:37:50', '2017-12-08 10:37:50', '984.00', '2', '2017-12-05 19:47:15', '0', null, '1', null, '201712081037506552141', '6', '9.84');
INSERT INTO `xyk_plan_detail` VALUES ('3724', '403', '2017-12-08 10:37:50', null, '5900.00', '1', '2017-12-06 17:09:02', '0', null, '1', null, '201712081037502418792', '7', '59.00');
INSERT INTO `xyk_plan_detail` VALUES ('3725', '403', '2017-12-08 10:37:50', '2017-12-08 10:37:50', '2827.00', '2', '2017-12-06 19:53:31', '0', null, '1', null, '201712081037502418792', '8', '28.27');
INSERT INTO `xyk_plan_detail` VALUES ('3726', '403', '2017-12-08 10:37:50', '2017-12-08 10:37:50', '3073.00', '2', '2017-12-06 20:38:48', '0', null, '1', null, '201712081037502418792', '9', '30.73');

-- ----------------------------
-- Table structure for xyk_plan_sys
-- ----------------------------
DROP TABLE IF EXISTS `xyk_plan_sys`;
CREATE TABLE `xyk_plan_sys` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `PlanDayTimes` int(11) DEFAULT '2' COMMENT '计划每日还款笔数',
  `TaoTimes` int(11) DEFAULT '2' COMMENT '套现笔数',
  `OpenPlanFeePay` int(11) DEFAULT '0' COMMENT '1 打开计划支付费率 0 关闭计划支付费率',
  `OpenPlanFeeRepay` int(11) DEFAULT '0' COMMENT '1 打开计划还款费率 0 关闭计划还款费率',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of xyk_plan_sys
-- ----------------------------
INSERT INTO `xyk_plan_sys` VALUES ('1', '2', '2', '1', '1');

-- ----------------------------
-- Table structure for xyk_profit
-- ----------------------------
DROP TABLE IF EXISTS `xyk_profit`;
CREATE TABLE `xyk_profit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '获取收益用户',
  `first_user_id` int(11) DEFAULT NULL COMMENT '一级邀请人',
  `second_user_id` int(11) DEFAULT NULL COMMENT '二级邀请人',
  `money` float(11,2) DEFAULT NULL COMMENT '收益金额',
  `time` int(11) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL COMMENT '收益说明',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xyk_profit
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_refund
-- ----------------------------
DROP TABLE IF EXISTS `xyk_refund`;
CREATE TABLE `xyk_refund` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `OrderNum` varchar(30) NOT NULL COMMENT '交易编号',
  `UserId` int(11) unsigned NOT NULL COMMENT '普通用户Id',
  `RefundAmount` decimal(11,3) DEFAULT '0.000' COMMENT '退款金额',
  `Remark` varchar(255) DEFAULT '' COMMENT '说明',
  `RefundStatus` tinyint(1) NOT NULL DEFAULT '0' COMMENT '退款是否成功:0失败 1成功',
  `AddTime` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  `Ext1` varchar(50) DEFAULT NULL,
  `Ext2` varchar(50) DEFAULT NULL,
  `Ext3` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `ORDERNUM` (`OrderNum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='退款表';

-- ----------------------------
-- Records of xyk_refund
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_repay
-- ----------------------------
DROP TABLE IF EXISTS `xyk_repay`;
CREATE TABLE `xyk_repay` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `OrderNum` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Money` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `UserId` int(11) DEFAULT NULL,
  `SerialNum` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `BankId` int(11) DEFAULT NULL COMMENT '连接 bankdcard表的 id ',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `status` int(11) DEFAULT NULL COMMENT '0 等待执行 1  成功  2 失败',
  `FeeType` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '手续费付款  PAYER 付款方指用户  RECEIVER 指自己 商户',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of xyk_repay
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_rights
-- ----------------------------
DROP TABLE IF EXISTS `xyk_rights`;
CREATE TABLE `xyk_rights` (
  `RightsId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `RightsName` varchar(100) DEFAULT NULL,
  `RightsDes` varchar(300) DEFAULT NULL,
  `MarkingId` int(6) unsigned NOT NULL DEFAULT '0',
  `MenuId` int(10) unsigned NOT NULL DEFAULT '0',
  `Addtime` datetime NOT NULL,
  PRIMARY KEY (`RightsId`),
  UNIQUE KEY `MarkingId` (`MarkingId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台权限表';

-- ----------------------------
-- Records of xyk_rights
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_role
-- ----------------------------
DROP TABLE IF EXISTS `xyk_role`;
CREATE TABLE `xyk_role` (
  `RoleId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `MerchantId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所属商户id',
  `RoleName` varchar(30) NOT NULL,
  `RoleDes` varchar(300) NOT NULL,
  `Addtime` datetime NOT NULL,
  PRIMARY KEY (`RoleId`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='后台角色表';

-- ----------------------------
-- Records of xyk_role
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_rolerightsrelation
-- ----------------------------
DROP TABLE IF EXISTS `xyk_rolerightsrelation`;
CREATE TABLE `xyk_rolerightsrelation` (
  `RelationId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `RoleId` int(10) unsigned NOT NULL DEFAULT '0',
  `RightsId` varchar(45) DEFAULT NULL,
  `Addtime` datetime NOT NULL,
  PRIMARY KEY (`RelationId`),
  KEY `I_RoleId` (`RoleId`),
  KEY `I_RightsId` (`RightsId`)
) ENGINE=InnoDB AUTO_INCREMENT=2417 DEFAULT CHARSET=utf8 COMMENT='后台 权限-角色 关联表';

-- ----------------------------
-- Records of xyk_rolerightsrelation
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_sessions
-- ----------------------------
DROP TABLE IF EXISTS `xyk_sessions`;
CREATE TABLE `xyk_sessions` (
  `SessionId` varchar(32) NOT NULL,
  `UserId` int(10) unsigned NOT NULL DEFAULT '0',
  `Ip` varchar(100) DEFAULT NULL,
  `LastVisit` int(10) unsigned NOT NULL DEFAULT '0',
  `Expiration` int(10) unsigned NOT NULL DEFAULT '0',
  `SessionData` varchar(500) NOT NULL,
  PRIMARY KEY (`SessionId`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COMMENT='session表';

-- ----------------------------
-- Records of xyk_sessions
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_sms
-- ----------------------------
DROP TABLE IF EXISTS `xyk_sms`;
CREATE TABLE `xyk_sms` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Mobile` varchar(30) NOT NULL COMMENT '接收短信的手机号码',
  `SmsType` smallint(5) unsigned NOT NULL DEFAULT '10001' COMMENT '短信类型(登录验证码、电子票转增.参考字典)',
  `SmsPlatform` smallint(5) unsigned NOT NULL DEFAULT '10001' COMMENT '短信平台(阿里大于 沃动等.参考字典,默认阿里大于)',
  `TemplateId` varchar(50) DEFAULT NULL COMMENT '模板Id',
  `SmsParam` varchar(255) DEFAULT '' COMMENT '短信参数,json格式存放',
  `BackId` varchar(50) DEFAULT '' COMMENT '短信接口返回ID',
  `Content` varchar(255) DEFAULT '' COMMENT '实际发送的短信内容',
  `AddTime` int(11) unsigned DEFAULT '0' COMMENT '添加时间',
  `Ext1` varchar(50) DEFAULT NULL,
  `Ext2` varchar(50) DEFAULT NULL,
  `Ext3` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `SMS_MOBILE` (`Mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=529 DEFAULT CHARSET=utf8 COMMENT='短信发送记录';

-- ----------------------------
-- Records of xyk_sms
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_smsbasictemplates
-- ----------------------------
DROP TABLE IF EXISTS `xyk_smsbasictemplates`;
CREATE TABLE `xyk_smsbasictemplates` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT '短信模板名称',
  `SmsType` smallint(5) unsigned NOT NULL DEFAULT '10001' COMMENT '短信类型(绑定微信公众好验证码、电子票转增等.参考字典)',
  `ParamKey` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '短信模板变量，多个用逗号隔开',
  `SmsTemplate` varchar(500) CHARACTER SET utf8 DEFAULT '' COMMENT '短信模板内容',
  `TemplateId` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '模板Id',
  `Isvalid` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1有效 0无效',
  `AddTime` int(10) unsigned DEFAULT '0' COMMENT '数据添加时间',
  `UpdateTime` int(10) unsigned DEFAULT NULL COMMENT '数据更新时间',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of xyk_smsbasictemplates
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_smsparam
-- ----------------------------
DROP TABLE IF EXISTS `xyk_smsparam`;
CREATE TABLE `xyk_smsparam` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `SmsType` smallint(5) unsigned NOT NULL DEFAULT '10001' COMMENT '短信类型(绑定微信公众好验证码、电子票转增等.参考字典)',
  `ParamCn` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '变量中文名',
  `ParamEn` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '变量名(英文)',
  `Required` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否必须(0:非必须 1必须)',
  `Isvalid` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0无效  1有效',
  `AddTime` int(10) unsigned DEFAULT NULL COMMENT '数据添加时间',
  `UpdateTime` int(10) unsigned DEFAULT NULL COMMENT '数据更新时间',
  PRIMARY KEY (`Id`),
  KEY `KEY_SMSTYPE` (`SmsType`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of xyk_smsparam
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_smstemplates
-- ----------------------------
DROP TABLE IF EXISTS `xyk_smstemplates`;
CREATE TABLE `xyk_smstemplates` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) DEFAULT '' COMMENT '模板名称',
  `TemplateId` varchar(50) DEFAULT NULL COMMENT '模板Id',
  `MerchantId` int(11) unsigned NOT NULL COMMENT '商户Id',
  `SmsType` smallint(5) unsigned NOT NULL DEFAULT '10001' COMMENT '短信类型(登录验证码、电子票转增.参考字典)',
  `ParamKey` varchar(255) DEFAULT '' COMMENT '短信模板变量，多个用逗号隔开,和短信队列表的ParamValue组成一对',
  `SmsTemplate` varchar(500) DEFAULT '' COMMENT '短信模板内容',
  `Isvalid` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否有效(0无效 1有效) 同一个类型的，只能有一个有效',
  `AddTime` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  `UpdateTime` int(10) unsigned DEFAULT '0' COMMENT '修改时间',
  `Ext1` varchar(50) DEFAULT NULL,
  `Ext2` varchar(50) DEFAULT NULL,
  `Ext3` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8 COMMENT='短信模板';

-- ----------------------------
-- Records of xyk_smstemplates
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_sys
-- ----------------------------
DROP TABLE IF EXISTS `xyk_sys`;
CREATE TABLE `xyk_sys` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `mac` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'mac地址',
  `ip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `domain` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of xyk_sys
-- ----------------------------
INSERT INTO `xyk_sys` VALUES ('1', '00-50-56-C0-00-08', '127.0.0.1', 'api.dev');

-- ----------------------------
-- Table structure for xyk_userbindccard
-- ----------------------------
DROP TABLE IF EXISTS `xyk_userbindccard`;
CREATE TABLE `xyk_userbindccard` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '银行卡ID',
  `BankId` varchar(50) DEFAULT '0' COMMENT '银行卡id',
  `UserId` int(11) unsigned NOT NULL COMMENT '用户id',
  `BankName` varchar(100) DEFAULT NULL COMMENT '银行卡名称',
  `BankNumber` varchar(50) NOT NULL DEFAULT '0' COMMENT '银行卡号',
  `IsDefault` tinyint(1) DEFAULT '0' COMMENT '是否默认（1|默认，0|不默认）',
  `status` int(11) DEFAULT '0' COMMENT '状态; 0|正常 ； 1|冻结',
  `AddTime` int(50) DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COMMENT='用户绑定的银行卡';

-- ----------------------------
-- Records of xyk_userbindccard
-- ----------------------------
INSERT INTO `xyk_userbindccard` VALUES ('6', '8a6019b556ad4cf7a79a61d388989a68', '82', '中信银行', '6217710804856110', '0', '1', '1512121593');

-- ----------------------------
-- Table structure for xyk_userbinddcard
-- ----------------------------
DROP TABLE IF EXISTS `xyk_userbinddcard`;
CREATE TABLE `xyk_userbinddcard` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '银行卡ID',
  `CreditId` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '交易卡id',
  `UserId` int(11) unsigned DEFAULT NULL COMMENT '用户id',
  `CreditName` varchar(100) DEFAULT NULL COMMENT '信用卡名称',
  `CreditNumber` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '0' COMMENT '信用卡号',
  `status` int(11) unsigned DEFAULT '0' COMMENT '状态; 0|正常 ； 1|冻结  2|解绑',
  `IsDefault` tinyint(1) unsigned DEFAULT '0' COMMENT '是否默认（1|默认；0|不默认）',
  `AddTime` int(50) DEFAULT '0',
  `CVN` int(10) unsigned DEFAULT NULL COMMENT 'SVN2码',
  `Quota` decimal(10,2) unsigned DEFAULT NULL COMMENT '信用卡额度',
  `AccountDate` datetime DEFAULT NULL COMMENT '账号日',
  `RepaymentDate` datetime DEFAULT NULL COMMENT '还款日',
  `Type` int(11) DEFAULT NULL COMMENT '1 借记卡 2 贷记卡',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='用户绑定的信用卡';

-- ----------------------------
-- Records of xyk_userbinddcard
-- ----------------------------
INSERT INTO `xyk_userbinddcard` VALUES ('1', '48cfb204ba8b4a3f870ea4c567399272', '82', '招商银行', '6225768758046880', '0', '1', '1512114970', '449', '15000.00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2');

-- ----------------------------
-- Table structure for xyk_userblacklist
-- ----------------------------
DROP TABLE IF EXISTS `xyk_userblacklist`;
CREATE TABLE `xyk_userblacklist` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `UserId` int(11) unsigned NOT NULL COMMENT '用户Id',
  `MerchantId` int(11) unsigned NOT NULL COMMENT '商户Id',
  `Mobile` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '手机号码',
  `Isvalid` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '数据是否有效(1:禁止购票启用 0:禁止购票关闭)',
  `AddTime` int(10) unsigned DEFAULT NULL COMMENT '数据添加时间',
  `UpdateTime` int(10) unsigned DEFAULT NULL COMMENT '数据更新时间',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of xyk_userblacklist
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_usercontact
-- ----------------------------
DROP TABLE IF EXISTS `xyk_usercontact`;
CREATE TABLE `xyk_usercontact` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `UserId` int(11) unsigned NOT NULL COMMENT '用户Id',
  `MerchantId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商户Id(建议插入数据的时候填上此值)',
  `Contact` varchar(50) NOT NULL COMMENT '联系人姓名',
  `CertType` smallint(5) unsigned NOT NULL DEFAULT '10001' COMMENT '证件类型(参考字典)',
  `CertNo` varchar(50) NOT NULL COMMENT '证件号码',
  `Isvalid` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否有效',
  `IsActivated` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已激活(初期程序插入数据填已激活，以后可能是默认未激活，需要认证通过后置为已激活)',
  `AddTime` int(10) unsigned DEFAULT NULL COMMENT '数据添加时间',
  `UpdateTime` int(10) unsigned DEFAULT NULL COMMENT '数据更新时间',
  `Ext1` varchar(255) DEFAULT NULL,
  `Ext2` varchar(255) DEFAULT NULL,
  `Ext3` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `CONTACT_KEY_USERID` (`UserId`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8 COMMENT='购票实名认证常用联系人表';

-- ----------------------------
-- Records of xyk_usercontact
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_userrolerelation
-- ----------------------------
DROP TABLE IF EXISTS `xyk_userrolerelation`;
CREATE TABLE `xyk_userrolerelation` (
  `UserrelationId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `RoleId` int(10) unsigned NOT NULL,
  `UserId` int(10) unsigned NOT NULL,
  `Addtime` datetime NOT NULL,
  PRIMARY KEY (`UserrelationId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台 用户-角色 关联表';

-- ----------------------------
-- Records of xyk_userrolerelation
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_users
-- ----------------------------
DROP TABLE IF EXISTS `xyk_users`;
CREATE TABLE `xyk_users` (
  `UserId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `MerchantId` int(11) unsigned DEFAULT '0' COMMENT '代理商ID',
  `Mobile` varchar(20) DEFAULT NULL COMMENT '手机号（作为登录用户名）',
  `Password` varchar(32) DEFAULT '' COMMENT '密码',
  `PayPassword` varchar(32) DEFAULT '' COMMENT '支付密码',
  `Email` varchar(80) DEFAULT '' COMMENT '邮箱',
  `Username` varchar(50) DEFAULT NULL COMMENT '用户名、昵称',
  `UserAvatar` varchar(50) DEFAULT NULL,
  `Account` decimal(10,2) DEFAULT '0.00' COMMENT '账户余额',
  `bindBankId` varchar(255) DEFAULT '0' COMMENT '绑定的银行卡id，多个用,号隔开。',
  `IP` varchar(30) DEFAULT '' COMMENT '用户注册IP',
  `Status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '帐号状态(0.相当于未激活 1.正常 2.禁用)',
  `AreaId` smallint(5) unsigned NOT NULL DEFAULT '10001' COMMENT '区域Id,参见字典',
  `InviteOne` int(11) unsigned DEFAULT '0' COMMENT '一级会员',
  `InviteTwo` int(11) unsigned DEFAULT '0' COMMENT '二级会员',
  `InviteThree` int(11) unsigned DEFAULT '0' COMMENT '三级会员',
  `InviterId` int(11) unsigned DEFAULT '0' COMMENT '邀请人ID',
  `BankId` varchar(100) DEFAULT NULL COMMENT '银行卡id,多个用，号分割',
  `CreditId` varchar(100) DEFAULT NULL COMMENT '信用卡id,多个用，号分割',
  `QRcode` varchar(50) DEFAULT NULL COMMENT '二维码',
  `AddTime` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  `IDCard` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '身份证号码',
  `Ext1` varchar(50) DEFAULT NULL,
  `Ext2` varchar(50) DEFAULT NULL,
  `Ext3` varchar(50) DEFAULT NULL,
  `token` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`UserId`),
  UNIQUE KEY `UK_USERS_MOBILE` (`Mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8 COMMENT='普通用户表';

-- ----------------------------
-- Records of xyk_users
-- ----------------------------

-- ----------------------------
-- Table structure for xyk_verify
-- ----------------------------
DROP TABLE IF EXISTS `xyk_verify`;
CREATE TABLE `xyk_verify` (
  `mobile` varchar(11) DEFAULT NULL COMMENT '手机号',
  `code` varchar(6) DEFAULT NULL COMMENT '验证码',
  `time` int(11) DEFAULT NULL COMMENT '过期时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xyk_verify
-- ----------------------------
