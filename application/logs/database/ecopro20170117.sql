-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema ecopro
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema ecopro
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `ecopro` DEFAULT CHARACTER SET utf8 ;
USE `ecopro` ;

-- -----------------------------------------------------
-- Table `ecopro`.`qa_white_list`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecopro`.`qa_white_list` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(10) NULL COMMENT '姓名',
  `identity` VARCHAR(10) NOT NULL COMMENT '身份:成功3次以上关注用户 达人 专家',
  `phone` VARCHAR(13) NULL,
  `email` VARCHAR(45) NULL,
  `is_enabled` TINYINT(1) NOT NULL DEFAULT 0,
  `is_white` TINYINT(1) NOT NULL DEFAULT 0,
  `register_time` TIMESTAMP NULL,
  `weixin` VARCHAR(45) NULL,
  `security` CHAR(6) NULL,
  `signature` VARCHAR(45) NULL,
  `city` CHAR(10) NULL,
  `rank` INT NULL COMMENT '等级',
  `sex` CHAR(10) NULL COMMENT 'male female',
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ecopro`.`qa_question`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecopro`.`qa_question` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(45) NOT NULL,
  `content` TEXT NULL,
  `weixin` VARCHAR(45) NOT NULL,
  `parent_id` INT NULL,
  `create_time` TIMESTAMP NULL,
  `nickname` VARCHAR(45) NULL,
  `identity` VARCHAR(10) NULL,
  `is_audit` TINYINT(1) NULL DEFAULT 0,
  `is_white` TINYINT(1) NULL DEFAULT 0,
  `is_sensitive` TINYINT(1) NULL DEFAULT 1,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ecopro`.`qa_refence_gallery`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecopro`.`qa_refence_gallery` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `file_name` VARCHAR(255) NULL,
  `tag` VARCHAR(45) NULL,
  `tech_url` VARCHAR(255) NULL,
  `question_id` INT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
