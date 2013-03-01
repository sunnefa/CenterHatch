SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `core__pages`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `core__pages` (
  `page_id` INT NOT NULL AUTO_INCREMENT ,
  `page_title` VARCHAR(255) NOT NULL ,
  `page_status` TINYINT(1)  NOT NULL ,
  `page_description` TEXT NULL ,
  `page_slug` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`page_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `core__modules`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `core__modules` (
  `module_id` INT NOT NULL AUTO_INCREMENT ,
  `module_name` VARCHAR(60) NOT NULL ,
  `module_path` VARCHAR(100) NOT NULL ,
  `module_is_active` TINYINT(1)  NOT NULL ,
  PRIMARY KEY (`module_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `core__user_roles`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `core__user_roles` (
  `role_id` INT NOT NULL AUTO_INCREMENT ,
  `role_name` VARCHAR(100) NOT NULL ,
  PRIMARY KEY (`role_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `core__users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `core__users` (
  `user_id` INT NOT NULL AUTO_INCREMENT ,
  `user_first_name` VARCHAR(100) NOT NULL ,
  `user_last_name` VARCHAR(100) NOT NULL ,
  `user_nick_name` VARCHAR(100) NULL ,
  `user_role` INT NOT NULL ,
  `user_email_address` VARCHAR(100) NOT NULL ,
  `user_password` VARCHAR(255) NOT NULL ,
  `user_registration_time` DATETIME NOT NULL ,
  `user_is_active` TINYINT(1)  NOT NULL ,
  `user_temp_token` VARCHAR(255) NULL ,
  `user_temp_token_expires` DATETIME NULL ,
  PRIMARY KEY (`user_id`) ,
  INDEX `user_role_id` (`user_role` ASC) ,
  CONSTRAINT `user_role_id`
    FOREIGN KEY (`user_role` )
    REFERENCES `core__user_roles` (`role_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `core__text`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `core__text` (
  `text_id` INT NOT NULL AUTO_INCREMENT ,
  `text_name` VARCHAR(100) NOT NULL ,
  `text` TEXT NULL ,
  `text_status` TINYINT(1)  NOT NULL ,
  PRIMARY KEY (`text_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `core__settings`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `core__settings` (
  `setting_id` INT NOT NULL AUTO_INCREMENT ,
  `setting_name` VARCHAR(100) NOT NULL ,
  `setting_value` TEXT NULL ,
  PRIMARY KEY (`setting_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `core__pages_modules`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `core__pages_modules` (
  `page_id` INT NOT NULL ,
  `module_id` INT NOT NULL ,
  `display_order` INT NOT NULL ,
  INDEX `module_page_id` (`page_id` ASC) ,
  INDEX `page_module_id` (`module_id` ASC) ,
  CONSTRAINT `module_page_id`
    FOREIGN KEY (`page_id` )
    REFERENCES `core__pages` (`page_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `page_module_id`
    FOREIGN KEY (`module_id` )
    REFERENCES `core__modules` (`module_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `core__text_pages`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `core__text_pages` (
  `text_id` INT NOT NULL ,
  `page_id` INT NOT NULL ,
  INDEX `text_page_id` (`text_id` ASC) ,
  INDEX `page_text_id` (`page_id` ASC) ,
  CONSTRAINT `text_page_id`
    FOREIGN KEY (`text_id` )
    REFERENCES `core__text` (`text_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `page_text_id`
    FOREIGN KEY (`page_id` )
    REFERENCES `core__pages` (`page_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `core__pages`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO core__pages (`page_id`, `page_title`, `page_status`, `page_description`, `page_slug`) VALUES (1, 'Login', 1, 'The login page', 'login');
INSERT INTO core__pages (`page_id`, `page_title`, `page_status`, `page_description`, `page_slug`) VALUES (2, 'Register', 1, 'The registration page', 'register');
INSERT INTO core__pages (`page_id`, `page_title`, `page_status`, `page_description`, `page_slug`) VALUES (3, 'Activate', 1, 'Activate your account', 'activate');
INSERT INTO core__pages (`page_id`, `page_title`, `page_status`, `page_description`, `page_slug`) VALUES (4, 'Account', 1, 'Manage your account', 'account');
INSERT INTO core__pages (`page_id`, `page_title`, `page_status`, `page_description`, `page_slug`) VALUES (5, 'Forgotten password', 1, 'Reset your password', 'password');

COMMIT;

-- -----------------------------------------------------
-- Data for table `core__modules`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO core__modules (`module_id`, `module_name`, `module_path`, `module_is_active`) VALUES (1, 'header', 'core/header.php', 1);
INSERT INTO core__modules (`module_id`, `module_name`, `module_path`, `module_is_active`) VALUES (2, 'footer', 'core/footer.php', 1);
INSERT INTO core__modules (`module_id`, `module_name`, `module_path`, `module_is_active`) VALUES (3, 'text', 'core/text.php', 1);
INSERT INTO core__modules (`module_id`, `module_name`, `module_path`, `module_is_active`) VALUES (4, 'login', 'users/login/login.php', 1);
INSERT INTO core__modules (`module_id`, `module_name`, `module_path`, `module_is_active`) VALUES (5, 'register', 'users/register/register.php', 1);
INSERT INTO core__modules (`module_id`, `module_name`, `module_path`, `module_is_active`) VALUES (6, 'account', 'users/account/account.php', 1);
INSERT INTO core__modules (`module_id`, `module_name`, `module_path`, `module_is_active`) VALUES (7, 'activate', 'users/activate.php', 1);
INSERT INTO core__modules (`module_id`, `module_name`, `module_path`, `module_is_active`) VALUES (8, 'password', 'users/password.php', 1);

COMMIT;

-- -----------------------------------------------------
-- Data for table `core__user_roles`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO core__user_roles (`role_id`, `role_name`) VALUES (1, 'user');
INSERT INTO core__user_roles (`role_id`, `role_name`) VALUES (2, 'admin');
INSERT INTO core__user_roles (`role_id`, `role_name`) VALUES (3, 'moderator');

COMMIT;

-- -----------------------------------------------------
-- Data for table `core__settings`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO core__settings (`setting_id`, `setting_name`, `setting_value`) VALUES (1, 'maintenance_mode', '0');
INSERT INTO core__settings (`setting_id`, `setting_name`, `setting_value`) VALUES (2, 'date_format', 'l jS \\of F, Y');
INSERT INTO core__settings (`setting_id`, `setting_name`, `setting_value`) VALUES (3, 'execute_php', '0');
INSERT INTO core__settings (`setting_id`, `setting_name`, `setting_value`) VALUES (4, 'debug_mode', '0');
INSERT INTO core__settings (`setting_id`, `setting_name`, `setting_value`) VALUES (5, 'nav_pages_in_header', '[\"login\", \"register\"]');
INSERT INTO core__settings (`setting_id`, `setting_name`, `setting_value`) VALUES (6, 'nav_pages_in_footer', '[\"password\", \"account\"]');

COMMIT;

-- -----------------------------------------------------
-- Data for table `core__pages_modules`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO core__pages_modules (`page_id`, `module_id`, `display_order`) VALUES (1, 1, 1);
INSERT INTO core__pages_modules (`page_id`, `module_id`, `display_order`) VALUES (1, 2, 3);
INSERT INTO core__pages_modules (`page_id`, `module_id`, `display_order`) VALUES (1, 4, 2);
INSERT INTO core__pages_modules (`page_id`, `module_id`, `display_order`) VALUES (2, 1, 1);
INSERT INTO core__pages_modules (`page_id`, `module_id`, `display_order`) VALUES (2, 2, 3);
INSERT INTO core__pages_modules (`page_id`, `module_id`, `display_order`) VALUES (2, 5, 2);
INSERT INTO core__pages_modules (`page_id`, `module_id`, `display_order`) VALUES (3, 1, 1);
INSERT INTO core__pages_modules (`page_id`, `module_id`, `display_order`) VALUES (3, 2, 3);
INSERT INTO core__pages_modules (`page_id`, `module_id`, `display_order`) VALUES (3, 7, 2);
INSERT INTO core__pages_modules (`page_id`, `module_id`, `display_order`) VALUES (4, 1, 1);
INSERT INTO core__pages_modules (`page_id`, `module_id`, `display_order`) VALUES (4, 2, 3);
INSERT INTO core__pages_modules (`page_id`, `module_id`, `display_order`) VALUES (4, 6, 2);
INSERT INTO core__pages_modules (`page_id`, `module_id`, `display_order`) VALUES (5, 1, 1);
INSERT INTO core__pages_modules (`page_id`, `module_id`, `display_order`) VALUES (5, 2, 3);
INSERT INTO core__pages_modules (`page_id`, `module_id`, `display_order`) VALUES (5, 8, 2);

COMMIT;
