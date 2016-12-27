-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema ecopro_knowledge
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema ecopro_knowledge
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `ecopro_knowledge` DEFAULT CHARACTER SET utf8 ;
USE `ecopro_knowledge` ;

-- -----------------------------------------------------
-- Table `ecopro_knowledge`.`ep_category`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecopro_knowledge`.`ep_category` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` CHAR(10) NOT NULL,
  `classify` CHAR(10) NOT NULL,
  `img_url` VARCHAR(45) NULL,
  `parent_id` INT NOT NULL,
  `parent_name` CHAR(10) NULL,
  `popular` CHAR(10) NULL,
  `academic` CHAR(10) NULL,
  `timestamp` DATETIME NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ecopro_knowledge`.`ep_detail`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecopro_knowledge`.`ep_detail` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `category_name` CHAR(20) NOT NULL,
  `disease` TEXT NULL,
  `condition` TEXT NULL,
  `optimum` TEXT NULL,
  `measures` TEXT NULL,
  `timestamp` DATETIME NULL,
  `ep_category_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_ep_detail_ep_category_idx` (`ep_category_id` ASC),
  CONSTRAINT `fk_ep_detail_ep_category`
    FOREIGN KEY (`ep_category_id`)
    REFERENCES `ecopro_knowledge`.`ep_category` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ecopro_knowledge`.`ep_participial`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecopro_knowledge`.`ep_participial` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` CHAR(20) NOT NULL,
  `ep_category_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_participial_name` (`name` ASC),
  INDEX `fk_ep_participial_ep_category1_idx` (`ep_category_id` ASC),
  CONSTRAINT `fk_ep_participial_ep_category1`
    FOREIGN KEY (`ep_category_id`)
    REFERENCES `ecopro_knowledge`.`ep_category` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
