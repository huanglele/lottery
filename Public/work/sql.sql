-- MySQL Script generated by MySQL Workbench
-- 07/21/16 10:00:45
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema lottery
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema lottery
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `lottery` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `lottery` ;

-- -----------------------------------------------------
-- Table `lottery`.`team`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lottery`.`team` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '',
  `area` VARCHAR(45) NULL COMMENT '',
  `name` VARCHAR(45) NULL COMMENT '',
  `icon` VARCHAR(255) NULL COMMENT '',
  `coach` VARCHAR(45) NULL COMMENT '教练',
  `desc` TEXT NULL COMMENT '简介',
  `type` TINYINT(8) NULL COMMENT '球队类型',
  PRIMARY KEY (`id`)  COMMENT '')
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `lottery`.`match`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lottery`.`match` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '',
  `name` VARCHAR(45) NULL COMMENT '比赛名称',
  `host_id` INT(10) UNSIGNED NOT NULL COMMENT '',
  `guess_id` INT(10) UNSIGNED NOT NULL COMMENT '',
  `b_time` DATETIME NULL COMMENT '',
  `place` VARCHAR(45) NULL COMMENT '比赛位置',
  `rate` VARCHAR(45) NULL COMMENT '赔率array(\'win\'=>3.23,\'lose\'=>3.2,\'draw\'=>2.0) json格式数据',
  `result` TINYINT(8) NULL COMMENT '',
  `type` TINYINT(8) UNSIGNED NOT NULL COMMENT '',
  `is_show` TINYINT(8) UNSIGNED NOT NULL COMMENT '前台是否显示，0不显示，1显示',
  `times` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '当前参加人次',
  PRIMARY KEY (`id`)  COMMENT '')
ENGINE = MyISAM
COMMENT = '比赛信息';


-- -----------------------------------------------------
-- Table `lottery`.`user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lottery`.`user` (
  `id` INT ZEROFILL NOT NULL COMMENT '',
  `openid` CHAR(28) NOT NULL COMMENT '',
  `nickname` VARCHAR(45) NULL COMMENT '',
  `coin` INT(10) NOT NULL COMMENT '积分',
  `subscribe` TINYINT(8) NULL COMMENT '是否关注',
  `subscribe_time` INT(0) NULL COMMENT '',
  `sex` TINYINT(8) NOT NULL DEFAULT 0 COMMENT '',
  `headimgurl` VARCHAR(255) NULL COMMENT '',
  `last_time` INT(10) UNSIGNED NOT NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '')
ENGINE = MyISAM
COMMENT = '用户表';


-- -----------------------------------------------------
-- Table `lottery`.`coin`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lottery`.`coin` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '',
  `uid` INT(10) UNSIGNED NOT NULL COMMENT '',
  `amount` INT(10) NOT NULL COMMENT '变化数量，有符号，正为增加，负为扣',
  `type` TINYINT(8) NOT NULL DEFAULT 0 COMMENT '',
  `time` DATETIME NOT NULL COMMENT '',
  `note` VARCHAR(255) NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '')
ENGINE = MyISAM
COMMENT = 'coin记录表';


-- -----------------------------------------------------
-- Table `lottery`.`match_record`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lottery`.`match_record` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT COMMENT '',
  `mid` INT(10) UNSIGNED NOT NULL COMMENT '比赛的id',
  `uid` INT(10) UNSIGNED NOT NULL COMMENT '',
  `option` TINYINT(8) UNSIGNED NOT NULL COMMENT '',
  `cost` INT(10) UNSIGNED NOT NULL COMMENT '花费',
  `reward` INT(10) UNSIGNED NOT NULL COMMENT '回报',
  `status` TINYINT(8) UNSIGNED NOT NULL COMMENT '状态1为揭晓，2失利，3正确',
  PRIMARY KEY (`id`)  COMMENT '')
ENGINE = MyISAM
COMMENT = '球赛竞猜记录表';


-- -----------------------------------------------------
-- Table `lottery`.`admin`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lottery`.`admin` (
  `aid` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `name` VARCHAR(45) NULL COMMENT '',
  `password` VARCHAR(45) NULL COMMENT '',
  PRIMARY KEY (`aid`)  COMMENT '')
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `lottery`.`config`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lottery`.`config` (
  `key` VARCHAR(255) NULL COMMENT '',
  `value` TEXT NULL COMMENT '')
ENGINE = MyISAM;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;