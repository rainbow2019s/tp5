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
-- Table `ecopro`.`ep_activities`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecopro`.`ep_activities` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL COMMENT '模块名称',
  `domain` VARCHAR(45) NULL COMMENT '域名',
  `host_ip_address` VARCHAR(45) NULL COMMENT '主机地址',
  `is_enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `entrance_url` VARCHAR(45) NOT NULL COMMENT '入口URL',
  `entrance_alias` VARCHAR(45) NOT NULL COMMENT '入口别名',
  `timestamp` DATETIME NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ecopro`.`ep_admin_users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecopro`.`ep_admin_users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL COMMENT '管理员名称',
  `register` DATE NULL COMMENT '注册日期',
  `phone` VARCHAR(45) NOT NULL COMMENT '联系方式',
  `email` VARCHAR(45) NULL,
  `is_enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `is_super` TINYINT(1) NOT NULL DEFAULT 0,
  `password` VARCHAR(45) NOT NULL COMMENT '口令',
  `token` CHAR(13) NOT NULL,
  `timestamp` DATETIME NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ecopro`.`ep_admin_app`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecopro`.`ep_admin_app` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `ep_activities_id` INT NOT NULL,
  `ep_admin_users_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_ep_admin_app_ep_activities_idx` (`ep_activities_id` ASC),
  INDEX `fk_ep_admin_app_ep_admin_users1_idx` (`ep_admin_users_id` ASC),
  CONSTRAINT `fk_ep_admin_app_ep_activities`
    FOREIGN KEY (`ep_activities_id`)
    REFERENCES `ecopro`.`ep_activities` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ep_admin_app_ep_admin_users1`
    FOREIGN KEY (`ep_admin_users_id`)
    REFERENCES `ecopro`.`ep_admin_users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
