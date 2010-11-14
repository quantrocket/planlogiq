SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `harley` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ;
SHOW WARNINGS;
USE `harley`;

-- -----------------------------------------------------
-- Table `harley`.`tm_user_role`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_user_role` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_user_role` (
  `idtm_user_role` INT NOT NULL AUTO_INCREMENT ,
  `user_role_name` VARCHAR(45) NULL ,
  `user_role_rechte` INT NULL DEFAULT 0 ,
  PRIMARY KEY (`idtm_user_role`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_user` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_user` (
  `idtm_user` INT NOT NULL AUTO_INCREMENT ,
  `user_name` VARCHAR(45) NULL ,
  `user_vorname` VARCHAR(45) NULL ,
  `user_password` VARCHAR(45) NULL ,
  `idtm_user_role` INT NOT NULL DEFAULT 1 ,
  `user_username` VARCHAR(45) NOT NULL ,
  `user_mail` VARCHAR(45) NULL ,
  PRIMARY KEY (`idtm_user`, `user_username`) ,
  CONSTRAINT `fk_tm_user_role`
    FOREIGN KEY (`idtm_user_role` )
    REFERENCES `harley`.`tm_user_role` (`idtm_user_role` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'User Table';

SHOW WARNINGS;
CREATE INDEX `fk_tm_user_role` ON `harley`.`tm_user` (`idtm_user_role` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_partei`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_partei` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_partei` (
  `partei_name` VARCHAR(45) NULL ,
  `partei_name2` VARCHAR(45) NULL ,
  `partei_name3` VARCHAR(45) NULL ,
  `partei_vorname` VARCHAR(45) NULL ,
  `idtm_user` INT NOT NULL DEFAULT 1 ,
  `idta_partei` INT NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`idta_partei`) ,
  CONSTRAINT `fk_idtm_user`
    FOREIGN KEY (`idtm_user` )
    REFERENCES `harley`.`tm_user` (`idtm_user` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
COMMENT = 'Main Informations about Contact';

SHOW WARNINGS;
CREATE INDEX `fk_idtm_user` ON `harley`.`ta_partei` (`idtm_user` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_country`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_country` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_country` (
  `idtm_country` INT NOT NULL ,
  `country_iso` VARCHAR(45) NULL ,
  `country_ful` VARCHAR(45) NULL ,
  PRIMARY KEY (`idtm_country`) )
ENGINE = InnoDB
COMMENT = 'list of countries\n';

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_adresse`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_adresse` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_adresse` (
  `idta_adresse` INT NOT NULL AUTO_INCREMENT ,
  `adresse_street` VARCHAR(45) NULL ,
  `adresse_zip` VARCHAR(45) NULL ,
  `adresse_town` VARCHAR(45) NULL ,
  `idtm_country` INT NOT NULL DEFAULT 1 ,
  `adresse_lat` VARCHAR(10) NULL ,
  `adresse_long` VARCHAR(10) NULL ,
  PRIMARY KEY (`idta_adresse`) ,
  CONSTRAINT `fk_idtm_country`
    FOREIGN KEY (`idtm_country` )
    REFERENCES `harley`.`tm_country` (`idtm_country` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Adresses';

SHOW WARNINGS;
CREATE INDEX `fk_idtm_country` ON `harley`.`ta_adresse` (`idtm_country` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_partei_has_ta_adresse`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_partei_has_ta_adresse` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_partei_has_ta_adresse` (
  `idta_partei` INT NULL ,
  `idta_adresse` INT NULL ,
  CONSTRAINT `fk_idta_partei`
    FOREIGN KEY (`idta_partei` )
    REFERENCES `harley`.`ta_partei` (`idta_partei` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_idta_adresse`
    FOREIGN KEY (`idta_adresse` )
    REFERENCES `harley`.`ta_adresse` (`idta_adresse` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE UNIQUE INDEX `PK` ON `harley`.`ta_partei_has_ta_adresse` (`idta_partei` ASC, `idta_adresse` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_idta_partei` ON `harley`.`ta_partei_has_ta_adresse` (`idta_partei` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_idta_adresse` ON `harley`.`ta_partei_has_ta_adresse` (`idta_adresse` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_waren_kategorie`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_waren_kategorie` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_waren_kategorie` (
  `idtm_waren_kategorie` INT NOT NULL AUTO_INCREMENT ,
  `waren_kategorie_name` VARCHAR(45) NULL ,
  `waren_kategorie_beschreibung` TINYBLOB NULL ,
  `parent_idtm_waren_kategorie` INT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`idtm_waren_kategorie`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_preis_kategorie`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_preis_kategorie` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_preis_kategorie` (
  `idtm_preis_kategorie` INT NOT NULL AUTO_INCREMENT ,
  `preis_kategorie_name` VARCHAR(45) NULL ,
  `preis_kategorie_beschreibung` VARCHAR(45) NULL ,
  PRIMARY KEY (`idtm_preis_kategorie`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_fahrzeug_kategorie`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_fahrzeug_kategorie` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_fahrzeug_kategorie` (
  `idtm_fahrzeug_kategorie` INT NOT NULL AUTO_INCREMENT ,
  `fahrzeug_kategorie_name` VARCHAR(45) NULL ,
  `fahrzeug_kategorie_beschreibung` VARCHAR(45) NULL ,
  PRIMARY KEY (`idtm_fahrzeug_kategorie`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_waren`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_waren` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_waren` (
  `idta_waren` INT NOT NULL AUTO_INCREMENT ,
  `waren_artikelnummer` VARCHAR(45) NULL ,
  `waren_ean` VARCHAR(45) NULL ,
  `waren_beschreibung` MEDIUMBLOB NULL ,
  `idtm_waren_kategorie` INT NULL ,
  `waren_menge` FLOAT NULL ,
  `waren_gewicht` FLOAT NULL ,
  `waren_preis` FLOAT NULL ,
  `idtm_preis_kategorie` INT NULL ,
  `waren_dat_ea` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'ea = erstellt am' ,
  `waren_dat_lb` DATE NULL COMMENT 'Laufzeit bis' ,
  `idta_adresse` INT NULL ,
  `waren_typ` INT NULL ,
  `waren_bezeichnung` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_waren`) ,
  CONSTRAINT `fkidtm_waren_kategorie`
    FOREIGN KEY (`idtm_waren_kategorie` )
    REFERENCES `harley`.`tm_waren_kategorie` (`idtm_waren_kategorie` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fkidtm_preis_kategorie`
    FOREIGN KEY (`idtm_preis_kategorie` )
    REFERENCES `harley`.`tm_preis_kategorie` (`idtm_preis_kategorie` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fkidtm_adresse`
    FOREIGN KEY (`idta_adresse` )
    REFERENCES `harley`.`ta_adresse` (`idta_adresse` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fkidtm_waren_kategorie` ON `harley`.`ta_waren` (`idtm_waren_kategorie` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fkidtm_preis_kategorie` ON `harley`.`ta_waren` (`idtm_preis_kategorie` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fkidtm_adresse` ON `harley`.`ta_waren` (`idta_adresse` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_waren_has_ta_partei`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_waren_has_ta_partei` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_waren_has_ta_partei` (
  `idta_waren_has_ta_partei` INT NOT NULL AUTO_INCREMENT ,
  `idta_partei_vk` INT NULL ,
  `idta_partei_ek` INT NULL DEFAULT 0 ,
  `dat_ea` TIMESTAMP NULL ,
  `stat_status` INT NULL DEFAULT 0 ,
  `idta_waren` INT NULL ,
  PRIMARY KEY (`idta_waren_has_ta_partei`) ,
  CONSTRAINT `fkidta_partei_vk`
    FOREIGN KEY (`idta_partei_vk` )
    REFERENCES `harley`.`ta_partei` (`idta_partei` )
    ON DELETE SET NULL
    ON UPDATE SET NULL,
  CONSTRAINT `fkidta_partei_ek`
    FOREIGN KEY (`idta_partei_ek` )
    REFERENCES `harley`.`ta_partei` (`idta_partei` )
    ON DELETE SET NULL
    ON UPDATE SET NULL,
  CONSTRAINT `fkidta_waren`
    FOREIGN KEY (`idta_waren` )
    REFERENCES `harley`.`ta_waren` (`idta_waren` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
COMMENT = 'beziehung waren zu kauf und verkauf mit status und datum';

SHOW WARNINGS;
CREATE INDEX `fkidta_partei_vk` ON `harley`.`ta_waren_has_ta_partei` (`idta_partei_vk` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fkidta_partei_ek` ON `harley`.`ta_waren_has_ta_partei` (`idta_partei_ek` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fkidta_waren` ON `harley`.`ta_waren_has_ta_partei` (`idta_waren` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_fracht`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_fracht` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_fracht` (
  `idta_fracht` INT NOT NULL AUTO_INCREMENT ,
  `fracht_auftragsnummer` VARCHAR(45) NULL ,
  `fracht_status` INT NULL DEFAULT 1 COMMENT '1 = verhandelbar, 2 = fix' ,
  `fracht_sonstiges` MEDIUMBLOB NULL ,
  `idtm_fahrzeug_kategorie` INT NOT NULL ,
  PRIMARY KEY (`idta_fracht`) ,
  CONSTRAINT `fk_idtm_fahrzeug_kategorie`
    FOREIGN KEY (`idtm_fahrzeug_kategorie` )
    REFERENCES `harley`.`tm_fahrzeug_kategorie` (`idtm_fahrzeug_kategorie` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_idtm_fahrzeug_kategorie` ON `harley`.`ta_fracht` (`idtm_fahrzeug_kategorie` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_fracht_teilladung`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_fracht_teilladung` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_fracht_teilladung` (
  `idta_fracht_teilladung` INT NOT NULL AUTO_INCREMENT ,
  `idta_fracht` INT NOT NULL ,
  `idta_adresse_auf` INT NOT NULL ,
  `idtta_adresse_ab` INT NOT NULL ,
  `fracht_teilladung_auf_dat_ab` TIMESTAMP NULL ,
  `fracht_teilladung_auf_dat_bis` TIMESTAMP NULL ,
  `fracht_teilladung_ab_dat_ab` TIMESTAMP NULL ,
  `fracht_teilladung_ab_dat_bis` TIMESTAMP NULL ,
  `fracht_teilladung_name` VARCHAR(45) NULL ,
  `fracht_teilladung_temperatur` TINYINT(1) NULL ,
  `idta_waren` INT NOT NULL ,
  `fracht_teilladung_beladung` INT NULL DEFAULT 1 COMMENT '1 = seitlich, 2 = tbdf' ,
  `fracht_teilladung_entladung` INT NULL DEFAULT 1 COMMENT '1 = seitlich, 2 = tbdf' ,
  PRIMARY KEY (`idta_fracht_teilladung`) ,
  CONSTRAINT `fk_idta_fracht`
    FOREIGN KEY (`idta_fracht` )
    REFERENCES `harley`.`ta_fracht` (`idta_fracht` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_idta_fracht` ON `harley`.`ta_fracht_teilladung` (`idta_fracht` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_organisation_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_organisation_type` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_organisation_type` (
  `idta_organisation_type` INT NOT NULL AUTO_INCREMENT ,
  `org_type_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_organisation_type`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_einheit`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_einheit` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_einheit` (
  `idta_einheit` INT NOT NULL AUTO_INCREMENT ,
  `ein_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_einheit`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_rescalendar`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_rescalendar` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_rescalendar` (
  `idta_rescalendar` INT NOT NULL AUTO_INCREMENT ,
  `rescal_name` VARCHAR(45) NULL ,
  `rescal_descr` BLOB NULL ,
  `rescal_t1` VARCHAR(45) NULL ,
  `rescal_h1` DOUBLE NULL ,
  `rescal_t2` VARCHAR(45) NULL ,
  `rescal_h2` DOUBLE NULL ,
  `rescal_t3` VARCHAR(45) NULL ,
  `rescal_h3` DOUBLE NULL ,
  `rescal_t4` VARCHAR(45) NULL ,
  `rescal_h4` DOUBLE NULL ,
  `rescal_t5` VARCHAR(45) NULL ,
  `rescal_h5` DOUBLE NULL ,
  `rescal_t6` VARCHAR(45) NULL ,
  `rescal_h6` DOUBLE NULL ,
  `rescal_t7` VARCHAR(45) NULL ,
  `rescal_h7` DOUBLE NULL ,
  PRIMARY KEY (`idta_rescalendar`) )
ENGINE = InnoDB
COMMENT = 'ResCal';

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_ressource_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_ressource_type` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_ressource_type` (
  `idta_ressource_type` INT NOT NULL AUTO_INCREMENT ,
  `res_type_name` VARCHAR(45) NULL ,
  `res_type_descr` BLOB NULL ,
  `res_type_kosten` DOUBLE NULL ,
  PRIMARY KEY (`idta_ressource_type`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_ressource`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_ressource` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_ressource` (
  `idtm_ressource` INT NOT NULL AUTO_INCREMENT ,
  `res_name` VARCHAR(45) NULL ,
  `res_code` VARCHAR(45) NULL ,
  `idta_rescalendar` INT NULL ,
  `idta_ressource_type` INT NULL ,
  `res_produktivitaet` INT NULL ,
  `res_kosten` DOUBLE NULL COMMENT 'kosten pro std' ,
  `res_note` BLOB NULL ,
  `idta_einheit` INT NULL ,
  PRIMARY KEY (`idtm_ressource`) ,
  CONSTRAINT `fk_tm_ressource_ta_einheit`
    FOREIGN KEY (`idta_einheit` )
    REFERENCES `harley`.`ta_einheit` (`idta_einheit` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_ressource_ta_rescalendar`
    FOREIGN KEY (`idta_rescalendar` )
    REFERENCES `harley`.`ta_rescalendar` (`idta_rescalendar` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_ressource_ta_ressource_type`
    FOREIGN KEY (`idta_ressource_type` )
    REFERENCES `harley`.`ta_ressource_type` (`idta_ressource_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_tm_ressource_ta_einheit` ON `harley`.`tm_ressource` (`idta_einheit` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_tm_ressource_ta_rescalendar` ON `harley`.`tm_ressource` (`idta_rescalendar` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_tm_ressource_ta_ressource_type` ON `harley`.`tm_ressource` (`idta_ressource_type` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_organisation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_organisation` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_organisation` (
  `idtm_organisation` INT NOT NULL AUTO_INCREMENT ,
  `org_name` VARCHAR(45) NULL ,
  `org_descr` TINYBLOB NULL ,
  `parent_idtm_organisation` INT NOT NULL DEFAULT 0 ,
  `idta_organisation_type` INT NULL ,
  `idtm_user` INT NULL ,
  `org_mail` VARCHAR(45) NULL ,
  `org_idtm_user_role` INT NULL ,
  `org_eskalation` INT NULL DEFAULT 0 ,
  `org_klima` INT NULL DEFAULT 0 ,
  `org_bedeutung` INT NULL DEFAULT 0 ,
  `org_kommunikation` BLOB NULL ,
  `idtm_ressource` INT NULL ,
  PRIMARY KEY (`idtm_organisation`) ,
  CONSTRAINT `fk_tm_organisation`
    FOREIGN KEY (`idta_organisation_type` )
    REFERENCES `harley`.`ta_organisation_type` (`idta_organisation_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_org_tm_ressource`
    FOREIGN KEY (`idtm_ressource` )
    REFERENCES `harley`.`tm_ressource` (`idtm_ressource` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_tm_organisation` ON `harley`.`tm_organisation` (`idta_organisation_type` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_tm_org_tm_ressource` ON `harley`.`tm_organisation` (`idtm_ressource` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_struktur_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_struktur_type` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_struktur_type` (
  `idta_struktur_type` INT NOT NULL AUTO_INCREMENT ,
  `struktur_type_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_struktur_type`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_struktur`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_struktur` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_struktur` (
  `idtm_struktur` INT NOT NULL AUTO_INCREMENT ,
  `struktur_name` VARCHAR(45) NULL ,
  `struktur_descr` TINYBLOB NULL ,
  `parent_idtm_struktur` INT NOT NULL DEFAULT 0 ,
  `idta_struktur_type` INT NULL ,
  `idtm_stammdaten` INT NULL DEFAULT 0 ,
  PRIMARY KEY (`idtm_struktur`) ,
  CONSTRAINT `fk_tm_struktur`
    FOREIGN KEY (`idta_struktur_type` )
    REFERENCES `harley`.`ta_struktur_type` (`idta_struktur_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_tm_struktur` ON `harley`.`tm_struktur` (`idta_struktur_type` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_risiko_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_risiko_type` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_risiko_type` (
  `idta_risiko_type` INT NOT NULL AUTO_INCREMENT ,
  `ris_type_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_risiko_type`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_risiko`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_risiko` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_risiko` (
  `idtm_risiko` INT NOT NULL AUTO_INCREMENT ,
  `ris_name` VARCHAR(45) NULL ,
  `ris_descr` TINYBLOB NULL ,
  `parent_idtm_risiko` INT NOT NULL DEFAULT 0 ,
  `idta_risiko_type` INT NULL ,
  PRIMARY KEY (`idtm_risiko`) ,
  CONSTRAINT `fk_tm_risiko`
    FOREIGN KEY (`idta_risiko_type` )
    REFERENCES `harley`.`ta_risiko_type` (`idta_risiko_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_tm_risiko` ON `harley`.`tm_risiko` (`idta_risiko_type` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_prozess_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_prozess_type` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_prozess_type` (
  `idta_prozess_type` INT NOT NULL AUTO_INCREMENT ,
  `pro_type_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_prozess_type`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_prozess`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_prozess` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_prozess` (
  `idtm_prozess` INT NOT NULL AUTO_INCREMENT ,
  `pro_name` VARCHAR(45) NULL ,
  `pro_descr` BLOB NULL ,
  `parent_idtm_prozess` INT NOT NULL DEFAULT 0 ,
  `idta_prozess_type` INT NULL ,
  `pro_step` BIGINT NULL ,
  PRIMARY KEY (`idtm_prozess`) ,
  CONSTRAINT `fk_tm_prozess`
    FOREIGN KEY (`idta_prozess_type` )
    REFERENCES `harley`.`ta_prozess_type` (`idta_prozess_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_tm_prozess` ON `harley`.`tm_prozess` (`idta_prozess_type` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_prozess_step`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_prozess_step` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_prozess_step` (
  `idtm_prozess_step` INT NOT NULL AUTO_INCREMENT ,
  `prostep_name` VARCHAR(45) NULL ,
  `prostep_descr` BLOB NULL ,
  `idtm_prozess` INT NULL DEFAULT 0 ,
  `idtm_struktur` INT NULL DEFAULT 0 ,
  `parent_idtm_prozess_step` INT NULL DEFAULT 0 ,
  `idtm_organisation` INT NULL DEFAULT 0 ,
  `error_idtm_prozess_step` INT NULL DEFAULT 0 ,
  `prostep_valid` TINYINT(1) NULL DEFAULT 1 ,
  `prostep_cdate` TIMESTAMP NULL ,
  PRIMARY KEY (`idtm_prozess_step`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_aufgaben`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_aufgaben` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_aufgaben` (
  `idtm_aufgaben` INT NOT NULL AUTO_INCREMENT ,
  `auf_tabelle` VARCHAR(45) NULL ,
  `auf_id` INT NULL ,
  `idtm_organisation` INT NULL ,
  `auf_cdate` TIMESTAMP NULL ,
  `auf_beschreibung` BLOB NULL ,
  `auf_tdate` DATE NULL ,
  `auf_priority` INT NULL ,
  `auf_name` VARCHAR(45) NULL ,
  `auf_done` TINYINT(1) NULL DEFAULT 0 ,
  `auf_dauer` INT NULL COMMENT 'Dauer in Stunden' ,
  `auf_ddate` DATE NULL ,
  PRIMARY KEY (`idtm_aufgaben`) ,
  CONSTRAINT `fk_idtm_organisation_tm_aufgabe`
    FOREIGN KEY (`idtm_organisation` )
    REFERENCES `harley`.`tm_organisation` (`idtm_organisation` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_idtm_organisation_tm_aufgabe` ON `harley`.`tm_aufgaben` (`idtm_organisation` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_rcvalue`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_rcvalue` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_rcvalue` (
  `idtm_rcvalue` INT NOT NULL AUTO_INCREMENT ,
  `rcv_tabelle` VARCHAR(45) NULL ,
  `rcv_id` INT NULL ,
  `idtm_organisation` INT NULL ,
  `rcv_type` INT NULL DEFAULT 0 COMMENT '0=Risiko; 1=Chance' ,
  `rcv_comment` MEDIUMTEXT NULL ,
  `idtm_risiko` INT NULL ,
  PRIMARY KEY (`idtm_rcvalue`) ,
  CONSTRAINT `fk_idtm_organisation_tm_rcvalue`
    FOREIGN KEY (`idtm_organisation` )
    REFERENCES `harley`.`tm_organisation` (`idtm_organisation` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_idtm_risiko_tm_rcvalue`
    FOREIGN KEY (`idtm_risiko` )
    REFERENCES `harley`.`tm_risiko` (`idtm_risiko` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_idtm_organisation_tm_rcvalue` ON `harley`.`tm_rcvalue` (`idtm_organisation` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_idtm_risiko_tm_rcvalue` ON `harley`.`tm_rcvalue` (`idtm_risiko` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tt_rcvalue`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tt_rcvalue` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tt_rcvalue` (
  `idtt_rcvalue` INT NOT NULL AUTO_INCREMENT ,
  `rcv_ewk` FLOAT NULL ,
  `rcv_schaden` FLOAT NULL ,
  `rcv_prio` FLOAT NULL ,
  `rcv_cby` INT NULL ,
  `rcv_cdate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `idtm_rcvalue` INT NULL ,
  PRIMARY KEY (`idtt_rcvalue`) ,
  CONSTRAINT `fk_tm_rcvalue_tt_rcvalue`
    FOREIGN KEY (`idtm_rcvalue` )
    REFERENCES `harley`.`tm_rcvalue` (`idtm_rcvalue` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Die Tatsächlichen Werte';

SHOW WARNINGS;
CREATE INDEX `fk_tm_rcvalue_tt_rcvalue` ON `harley`.`tt_rcvalue` (`idtm_rcvalue` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_feldfunktion`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_feldfunktion` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_feldfunktion` (
  `idta_feldfunktion` INT NOT NULL AUTO_INCREMENT ,
  `ff_name` VARCHAR(45) NULL ,
  `pre_idta_feldfunktion` INT NULL DEFAULT 0 ,
  `ff_operator` VARCHAR(45) NULL COMMENT 'welche operation soll gemacht werden + * - oder geteilt' ,
  `ff_descr` VARCHAR(45) NULL ,
  `idta_struktur_type` INT NULL ,
  `ff_faktor` FLOAT NULL ,
  `ff_fix` TINYINT(1) NULL ,
  `ff_gewichtung` FLOAT NULL ,
  `ff_type` INT NULL DEFAULT 0 COMMENT '0 = Summe; 1=Durchschnitt; 2=Collector\n' ,
  `ff_default` FLOAT NULL DEFAULT 0 ,
  PRIMARY KEY (`idta_feldfunktion`) ,
  CONSTRAINT `fk_ta_struktur_type_ta_feldfunktion`
    FOREIGN KEY (`idta_struktur_type` )
    REFERENCES `harley`.`ta_struktur_type` (`idta_struktur_type` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_ta_struktur_type_ta_feldfunktion` ON `harley`.`ta_feldfunktion` (`idta_struktur_type` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_variante`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_variante` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_variante` (
  `idta_variante` INT NOT NULL AUTO_INCREMENT ,
  `idtm_user` INT NULL ,
  `var_descr` VARCHAR(45) NULL ,
  `w_id_variante` INT NULL ,
  PRIMARY KEY (`idta_variante`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tt_werte`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tt_werte` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tt_werte` (
  `idtt_werte` INT NOT NULL AUTO_INCREMENT ,
  `w_jahr` INT NULL DEFAULT 1 ,
  `w_monat` INT NULL DEFAULT 1 ,
  `w_wert` FLOAT NULL COMMENT 'Hier steht der Wert, den der User erfasst hat' ,
  `w_endwert` FLOAT NULL COMMENT 'in diesem feld steht dann der Wert, nachdem die operation stattgefunden hat' ,
  `idta_feldfunktion` INT NULL ,
  `idtm_struktur` INT NULL ,
  `w_id_variante` INT NULL DEFAULT 1 ,
  `w_dimkey` TEXT NULL ,
  PRIMARY KEY (`idtt_werte`) ,
  CONSTRAINT `fk_tt_werte_ta_feldfunktion`
    FOREIGN KEY (`idta_feldfunktion` )
    REFERENCES `harley`.`ta_feldfunktion` (`idta_feldfunktion` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tt_werte_tm_struktur`
    FOREIGN KEY (`idtm_struktur` )
    REFERENCES `harley`.`tm_struktur` (`idtm_struktur` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tt_werte_ta_variante`
    FOREIGN KEY (`w_id_variante` )
    REFERENCES `harley`.`ta_variante` (`idta_variante` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_tt_werte_ta_feldfunktion` ON `harley`.`tt_werte` (`idta_feldfunktion` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_tt_werte_tm_struktur` ON `harley`.`tt_werte` (`idtm_struktur` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_tt_werte_ta_variante` ON `harley`.`tt_werte` (`w_id_variante` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_perioden`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_perioden` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_perioden` (
  `idta_perioden` INT NOT NULL AUTO_INCREMENT ,
  `per_intern` BIGINT NULL ,
  `per_extern` VARCHAR(45) NULL ,
  `parent_idta_perioden` INT NULL ,
  PRIMARY KEY (`idta_perioden`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_collector`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_collector` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_collector` (
  `idta_collector` INT NOT NULL AUTO_INCREMENT ,
  `idta_feldfunktion` INT NULL ,
  `col_idtafeldfunktion` INT NULL ,
  `col_operator` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_collector`) ,
  CONSTRAINT `fk_ta_feldfunktion_ta_collector`
    FOREIGN KEY (`idta_feldfunktion` )
    REFERENCES `harley`.`ta_feldfunktion` (`idta_feldfunktion` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'unser sammler	';

SHOW WARNINGS;
CREATE INDEX `fk_ta_feldfunktion_ta_collector` ON `harley`.`ta_collector` (`idta_feldfunktion` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_ziele_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_ziele_type` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_ziele_type` (
  `idta_ziele_type` INT NOT NULL AUTO_INCREMENT ,
  `zie_type_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_ziele_type`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_ziele`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_ziele` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_ziele` (
  `idtm_ziele` INT NOT NULL AUTO_INCREMENT ,
  `zie_name` VARCHAR(45) NULL ,
  `zie_descr` BLOB NULL ,
  `parent_idtm_ziele` INT NOT NULL DEFAULT 0 ,
  `idta_ziele_type` INT NULL ,
  `idtm_activity` INT NULL ,
  PRIMARY KEY (`idtm_ziele`) ,
  CONSTRAINT `fk_tm_ziele_ta_ziele_type`
    FOREIGN KEY (`idta_ziele_type` )
    REFERENCES `harley`.`ta_ziele_type` (`idta_ziele_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Zielklasse, Globalziel und Zielunterklasse';

SHOW WARNINGS;
CREATE INDEX `fk_tm_ziele_ta_ziele_type` ON `harley`.`tm_ziele` (`idta_ziele_type` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tt_ziele`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tt_ziele` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tt_ziele` (
  `idtt_ziele` INT NOT NULL AUTO_INCREMENT ,
  `ttzie_name` VARCHAR(45) NULL ,
  `ttzie_descr` BLOB NULL ,
  `idtm_prozess` INT NULL DEFAULT 0 ,
  `idtm_ziele` INT NULL DEFAULT 0 ,
  `idtm_organisation` INT NULL DEFAULT 0 ,
  `error_idtt_ziele` INT NULL DEFAULT 0 ,
  `prostep_valid` TINYINT(1) NULL DEFAULT 1 ,
  `prostep_cdate` TIMESTAMP NULL ,
  PRIMARY KEY (`idtt_ziele`) ,
  CONSTRAINT `fk_tt_ziele_tm_ziele`
    FOREIGN KEY (`idtm_ziele` )
    REFERENCES `harley`.`tm_ziele` (`idtm_ziele` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Ziele';

SHOW WARNINGS;
CREATE INDEX `fk_tt_ziele_tm_ziele` ON `harley`.`tt_ziele` (`idtm_ziele` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_activity_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_activity_type` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_activity_type` (
  `idta_activity_type` INT NOT NULL AUTO_INCREMENT ,
  `act_type_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_activity_type`) )
ENGINE = InnoDB
COMMENT = 'Aktionstyp';

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_activity`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_activity` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_activity` (
  `idtm_activity` INT NOT NULL AUTO_INCREMENT ,
  `act_name` VARCHAR(45) NULL ,
  `act_descr` BLOB NULL ,
  `act_startdate` DATE NULL ,
  `act_enddate` DATE NULL ,
  `act_dauer` INT NULL ,
  `act_fortschritt` INT NULL DEFAULT 0 ,
  `idta_activity_type` INT NULL ,
  `idtm_organisation` INT NULL ,
  `parent_idtm_activity` INT NULL DEFAULT 0 ,
  `act_step` BIGINT NULL ,
  `act_pspcode` VARCHAR(45) NULL ,
  `act_faz` INT NULL DEFAULT 0 COMMENT 'Startzeitpunkt' ,
  `act_fez` INT NULL DEFAULT 1 COMMENT 'Fruehester Endzeitpunkt' ,
  `act_saz` INT NULL DEFAULT 0 COMMENT 'Spaetester Anfangszeitpunkt' ,
  `act_sez` INT NULL DEFAULT 1 COMMENT 'spatester Endzeitpunkt' ,
  `act_gp` INT NULL DEFAULT 0 COMMENT 'Gesamtpuffer' ,
  `act_fp` INT NULL DEFAULT 0 COMMENT 'freier Puffer' ,
  `act_kosten` DOUBLE NULL DEFAULT 0 ,
  PRIMARY KEY (`idtm_activity`) ,
  CONSTRAINT `fk_tm_activity_tm_organisation`
    FOREIGN KEY (`idtm_organisation` )
    REFERENCES `harley`.`tm_organisation` (`idtm_organisation` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_activity_ta_activity_type`
    FOREIGN KEY (`idta_activity_type` )
    REFERENCES `harley`.`ta_activity_type` (`idta_activity_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Projektphasen';

SHOW WARNINGS;
CREATE INDEX `fk_tm_activity_tm_organisation` ON `harley`.`tm_activity` (`idtm_organisation` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_tm_activity_ta_activity_type` ON `harley`.`tm_activity` (`idta_activity_type` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_activity_participants`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_activity_participants` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_activity_participants` (
  `idtm_activity` INT NOT NULL DEFAULT 0 ,
  `idtm_organisation` INT NOT NULL DEFAULT 0 ,
  `idtm_activity_participant` INT NOT NULL AUTO_INCREMENT ,
  `act_part_anwesend` TINYINT(1) NULL ,
  `act_part_notiz` BLOB NULL ,
  PRIMARY KEY (`idtm_activity_participant`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_protokoll_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_protokoll_type` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_protokoll_type` (
  `idta_protokoll_type` INT NOT NULL AUTO_INCREMENT ,
  `prt_type_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_protokoll_type`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_protokoll`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_protokoll` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_protokoll` (
  `idtm_protokoll` INT NOT NULL AUTO_INCREMENT ,
  `prt_name` VARCHAR(45) NULL ,
  `prt_cdate` DATE NULL ,
  `prt_location` VARCHAR(45) NULL ,
  `prt_dauer` DOUBLE NULL ,
  `idtm_organisation` INT NULL DEFAULT 0 COMMENT 'Erzeuger' ,
  `idtm_termin` INT NULL DEFAULT 0 ,
  `idta_protokoll_type` INT NULL DEFAULT 0 ,
  PRIMARY KEY (`idtm_protokoll`) ,
  CONSTRAINT `fk_tm_protokoll_ta_protokoll_type`
    FOREIGN KEY (`idta_protokoll_type` )
    REFERENCES `harley`.`ta_protokoll_type` (`idta_protokoll_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = '	';

SHOW WARNINGS;
CREATE INDEX `fk_tm_protokoll_ta_protokoll_type` ON `harley`.`tm_protokoll` (`idta_protokoll_type` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_protokoll_ergebnistype`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_protokoll_ergebnistype` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_protokoll_ergebnistype` (
  `idta_protokoll_ergebnistype` INT NOT NULL AUTO_INCREMENT ,
  `prt_ergtype_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_protokoll_ergebnistype`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_protokoll_detail_group`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_protokoll_detail_group` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_protokoll_detail_group` (
  `idta_protokoll_detail_group` INT NOT NULL AUTO_INCREMENT ,
  `idtm_protokoll` INT NULL DEFAULT 0 ,
  PRIMARY KEY (`idta_protokoll_detail_group`) ,
  CONSTRAINT `fk_tm_protokoll_ta_protokoll_detail_group`
    FOREIGN KEY (`idtm_protokoll` )
    REFERENCES `harley`.`tm_protokoll` (`idtm_protokoll` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_tm_protokoll_ta_protokoll_detail_group` ON `harley`.`ta_protokoll_detail_group` (`idtm_protokoll` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_protokoll_detail`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_protokoll_detail` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_protokoll_detail` (
  `idtm_protokoll_detail` INT NOT NULL AUTO_INCREMENT ,
  `idtt_ziele` INT NULL DEFAULT 0 ,
  `idta_protokoll_detail_group` INT NULL ,
  `prtdet_topic` VARCHAR(45) NULL ,
  `prtdet_descr` BLOB NULL ,
  `prtdet_cdate` TIMESTAMP NULL ,
  `idtm_user` INT NULL DEFAULT 0 ,
  `idta_protokoll_ergebnistype` INT NULL DEFAULT 0 ,
  `prtdet_wvl` TINYINT(1) NULL ,
  `prtdet_wvl_type` INT NULL DEFAULT 0 ,
  PRIMARY KEY (`idtm_protokoll_detail`) ,
  CONSTRAINT `fk_tm_protokoll_detail_ta_protokoll_ergebnistype`
    FOREIGN KEY (`idta_protokoll_ergebnistype` )
    REFERENCES `harley`.`ta_protokoll_ergebnistype` (`idta_protokoll_ergebnistype` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ta_protokoll_detail_group_tm_protokoll_detail`
    FOREIGN KEY (`idta_protokoll_detail_group` )
    REFERENCES `harley`.`ta_protokoll_detail_group` (`idta_protokoll_detail_group` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_tm_protokoll_detail_ta_protokoll_ergebnistype` ON `harley`.`tm_protokoll_detail` (`idta_protokoll_ergebnistype` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_ta_protokoll_detail_group_tm_protokoll_detail` ON `harley`.`tm_protokoll_detail` (`idta_protokoll_detail_group` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_activity_has_tt_ziele`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_activity_has_tt_ziele` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_activity_has_tt_ziele` (
  `idtt_ziele` INT NOT NULL ,
  `idtm_activity` INT NOT NULL ,
  `idtm_activity_has_tt_ziele` INT NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`idtm_activity_has_tt_ziele`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_inoutput_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_inoutput_type` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_inoutput_type` (
  `idta_inoutput_type` INT NOT NULL AUTO_INCREMENT ,
  `ino_type_name` VARCHAR(45) NULL ,
  `ino_type_descr` BLOB NULL ,
  PRIMARY KEY (`idta_inoutput_type`) )
ENGINE = InnoDB
COMMENT = 'In and Output Type	';

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_inoutput`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_inoutput` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_inoutput` (
  `idtm_inoutput` INT NOT NULL AUTO_INCREMENT ,
  `ino_tabelle` VARCHAR(45) NULL ,
  `ino_id` INT NULL ,
  `idta_inoutput_type` INT NULL ,
  `ino_descr` BLOB NULL ,
  `ino_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idtm_inoutput`) ,
  CONSTRAINT `fk_tm_inoutput_ta_inoutput_type`
    FOREIGN KEY (`idta_inoutput_type` )
    REFERENCES `harley`.`ta_inoutput_type` (`idta_inoutput_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Input and Output';

SHOW WARNINGS;
CREATE INDEX `fk_tm_inoutput_ta_inoutput_type` ON `harley`.`tm_inoutput` (`idta_inoutput_type` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_activity_activity`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_activity_activity` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_activity_activity` (
  `idta_activity_activity` INT NOT NULL AUTO_INCREMENT ,
  `idtm_activity` INT NULL ,
  `pre_idtm_activity` INT NULL ,
  `actact_type` INT NULL ,
  `actact_minz` INT NULL ,
  `actact_maxz` INT NULL ,
  PRIMARY KEY (`idta_activity_activity`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_inoutput_has_tm_activity`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_inoutput_has_tm_activity` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_inoutput_has_tm_activity` (
  `idtm_inoutput_has_tm_activity` INT NOT NULL AUTO_INCREMENT ,
  `idtm_activity` INT NULL ,
  `idtm_inoutput` INT NULL ,
  `ino_link_type` INT NULL DEFAULT 0 COMMENT '0=Output,1=Input' ,
  PRIMARY KEY (`idtm_inoutput_has_tm_activity`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_termin_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_termin_type` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_termin_type` (
  `idta_termin_type` INT NOT NULL AUTO_INCREMENT ,
  `ter_type_name` VARCHAR(45) NULL ,
  `ter_type_descr` BLOB NULL ,
  PRIMARY KEY (`idta_termin_type`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_termin`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_termin` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_termin` (
  `idtm_termin` INT NOT NULL AUTO_INCREMENT ,
  `ter_betreff` VARCHAR(45) NULL ,
  `ter_descr` BLOB NULL ,
  `ter_ort` VARCHAR(45) NULL ,
  `ter_startdate` DATE NULL ,
  `ter_starttime` TIME NULL ,
  `ter_enddate` DATE NULL ,
  `ter_endtime` TIME NULL ,
  `idtm_activity` INT NULL ,
  `idta_termin_type` INT NULL DEFAULT 1 ,
  PRIMARY KEY (`idtm_termin`) ,
  CONSTRAINT `fk_tm_termin_tm_activity`
    FOREIGN KEY (`idtm_activity` )
    REFERENCES `harley`.`tm_activity` (`idtm_activity` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_termin_ta_termin_type`
    FOREIGN KEY (`idta_termin_type` )
    REFERENCES `harley`.`ta_termin_type` (`idta_termin_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_tm_termin_tm_activity` ON `harley`.`tm_termin` (`idtm_activity` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_tm_termin_ta_termin_type` ON `harley`.`tm_termin` (`idta_termin_type` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_termin_organisation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_termin_organisation` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_termin_organisation` (
  `idtm_termin` INT NOT NULL DEFAULT 0 ,
  `idtm_organisation` INT NOT NULL DEFAULT 0 ,
  `idtm_termin_organisation` INT NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`idtm_termin_organisation`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_verteiler`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_verteiler` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_verteiler` (
  `idtm_verteiler` INT NOT NULL AUTO_INCREMENT ,
  `ver_name` VARCHAR(45) NULL ,
  `ver_descr` BLOB NULL ,
  `ver_valid` TINYINT(1) NULL ,
  `ver_zyklus` INT NULL ,
  `ver_day` INT NULL DEFAULT 0 ,
  PRIMARY KEY (`idtm_verteiler`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_verteiler_organisation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_verteiler_organisation` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_verteiler_organisation` (
  `idtm_verteiler` INT NOT NULL DEFAULT 0 ,
  `idtm_organisation` INT NOT NULL DEFAULT 0 ,
  `idtm_verteiler_organisation` INT NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`idtm_verteiler_organisation`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tt_rcvalue_netto`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tt_rcvalue_netto` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tt_rcvalue_netto` (
  `idtt_rcvalue` INT NOT NULL AUTO_INCREMENT ,
  `rcv_ewk` FLOAT NULL ,
  `rcv_schaden` FLOAT NULL ,
  `rcv_prio` FLOAT NULL ,
  `rcv_cby` INT NULL ,
  `rcv_cdate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `idtm_rcvalue` INT NULL ,
  `rcv_descr` BLOB NULL ,
  `rcv_kosten` DOUBLE NULL DEFAULT 0 ,
  PRIMARY KEY (`idtt_rcvalue`) ,
  CONSTRAINT `fk_tm_rcvalue_tt_rcvalue_netto`
    FOREIGN KEY (`idtm_rcvalue` )
    REFERENCES `harley`.`tm_rcvalue` (`idtm_rcvalue` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Die Tatsächlichen Werte netto';

SHOW WARNINGS;
CREATE INDEX `fk_tm_rcvalue_tt_rcvalue_netto` ON `harley`.`tt_rcvalue_netto` (`idtm_rcvalue` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_bericht_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_bericht_type` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_bericht_type` (
  `ber_type_name` VARCHAR(45) NULL ,
  `ber_type_descr` BLOB NULL ,
  `idta_bericht_type` INT NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`idta_bericht_type`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_berichte`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_berichte` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_berichte` (
  `idta_berichte` INT NOT NULL AUTO_INCREMENT ,
  `ber_name` VARCHAR(45) NULL COMMENT 'name' ,
  `ber_descr` BLOB NULL COMMENT 'beschreibung' ,
  `ber_cdate` TIMESTAMP NULL ,
  `idtm_user` INT NULL COMMENT 'ersteller' ,
  `idta_bericht_type` INT NULL ,
  PRIMARY KEY (`idta_berichte`) ,
  CONSTRAINT `fk_tab_tab_type`
    FOREIGN KEY (`idta_bericht_type` )
    REFERENCES `harley`.`ta_bericht_type` (`idta_bericht_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_tab_tab_type` ON `harley`.`ta_berichte` (`idta_bericht_type` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_bericht_struktur_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_bericht_struktur_type` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_bericht_struktur_type` (
  `ber_struktur_type_name` VARCHAR(45) NULL ,
  `ber_struktur_type_descr` BLOB NULL ,
  `idta_bericht_struktur_type` INT NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`idta_bericht_struktur_type`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_bericht_struktur`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_bericht_struktur` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_bericht_struktur` (
  `idta_bericht_struktur` INT NOT NULL AUTO_INCREMENT ,
  `idta_berichte` INT NULL ,
  `ber_struktur_name` VARCHAR(45) NULL ,
  `ber_struktur_descr` BLOB NULL ,
  `parent_idta_bericht_struktur` INT NULL ,
  `idta_bericht_struktur_type` INT NULL ,
  PRIMARY KEY (`idta_bericht_struktur`) ,
  CONSTRAINT `fk_ta_berichte_bericht_struktur`
    FOREIGN KEY (`idta_berichte` )
    REFERENCES `harley`.`ta_berichte` (`idta_berichte` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `ft_tbs_tbs_type`
    FOREIGN KEY (`idta_bericht_struktur_type` )
    REFERENCES `harley`.`ta_bericht_struktur_type` (`idta_bericht_struktur_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_ta_berichte_bericht_struktur` ON `harley`.`ta_bericht_struktur` (`idta_berichte` ASC) ;

SHOW WARNINGS;
CREATE INDEX `ft_tbs_tbs_type` ON `harley`.`ta_bericht_struktur` (`idta_bericht_struktur_type` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_contentcontainer_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_contentcontainer_type` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_contentcontainer_type` (
  `cc_type_name` VARCHAR(45) NULL ,
  `cc_type_descr` BLOB NULL ,
  `idta_contentcontainer_type` INT NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`idta_contentcontainer_type`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_contentcontainer`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_contentcontainer` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_contentcontainer` (
  `idta_contentcontainer` INT NOT NULL AUTO_INCREMENT ,
  `idta_bericht_struktur` INT NULL ,
  `idta_contentcontainer_type` INT NULL ,
  `ccc_name` VARCHAR(45) NULL ,
  `ccc_cdate` DATE NULL ,
  `idtm_organisation` INT NULL ,
  PRIMARY KEY (`idta_contentcontainer`) ,
  CONSTRAINT `fk_ta_bericht_struktur`
    FOREIGN KEY (`idta_bericht_struktur` )
    REFERENCES `harley`.`ta_bericht_struktur` (`idta_bericht_struktur` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_organisation_tm_cc`
    FOREIGN KEY (`idtm_organisation` )
    REFERENCES `harley`.`tm_organisation` (`idtm_organisation` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cc_cc_type`
    FOREIGN KEY (`idta_contentcontainer_type` )
    REFERENCES `harley`.`ta_contentcontainer_type` (`idta_contentcontainer_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_ta_bericht_struktur` ON `harley`.`ta_contentcontainer` (`idta_bericht_struktur` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_tm_organisation_tm_cc` ON `harley`.`ta_contentcontainer` (`idtm_organisation` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_cc_cc_type` ON `harley`.`ta_contentcontainer` (`idta_contentcontainer_type` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`xx_berechtigung`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`xx_berechtigung` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`xx_berechtigung` (
  `idxx_berechtigung` INT NOT NULL AUTO_INCREMENT ,
  `xx_modul` VARCHAR(45) NULL ,
  `xx_id` INT NULL DEFAULT 0 ,
  `xx_read` TINYINT(1) NULL ,
  `xx_write` TINYINT(1) NULL ,
  `xx_create` TINYINT(1) NULL ,
  `xx_delete` TINYINT(1) NULL ,
  `idtm_user` INT NULL ,
  `xx_parameter1` VARCHAR(45) NULL ,
  `xx_parameter2` INT NULL ,
  `xx_parameter3` DATE NULL ,
  PRIMARY KEY (`idxx_berechtigung`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tt_container_text`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tt_container_text` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tt_container_text` (
  `idtt_container_text` INT NOT NULL AUTO_INCREMENT ,
  `cc_text_descr` BLOB NULL ,
  `cc_text_cdate` DATE NULL ,
  `cc_text_valid` TINYINT(1) NULL ,
  `cc_text_parameter1` VARCHAR(45) NULL ,
  `cc_text_parameter2` INT NULL ,
  `cc_text_parameter3` DATE NULL ,
  `cc_text_fdate` DATE NULL COMMENT 'Abschlussdatum' ,
  `idta_contentcontainer` INT NULL ,
  `cc_text_version` INT NULL ,
  PRIMARY KEY (`idtt_container_text`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`qs_comments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`qs_comments` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`qs_comments` (
  `idqs_comments` INT NOT NULL AUTO_INCREMENT ,
  `idtm_organisation` INT NULL ,
  `com_cdate` DATE NULL ,
  `com_page` VARCHAR(45) NULL ,
  `com_id` INT NULL ,
  `com_content` BLOB NULL ,
  PRIMARY KEY (`idqs_comments`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_changerequest`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_changerequest` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_changerequest` (
  `idtm_changerequest` INT NOT NULL AUTO_INCREMENT ,
  `rfc_descr` BLOB NULL ,
  `rfc_ifnot` BLOB NULL ,
  `idtm_activity` INT NULL ,
  `rfc_code` VARCHAR(45) NULL ,
  `rfc_date` DATE NULL ,
  `rfc_suggestdate` DATE NULL ,
  `suggest_idtm_organisation` INT NULL ,
  `rfc_cdate` DATE NULL ,
  `rfc_gdate` DATE NULL ,
  `genemigt_idtm_organisation` INT NULL ,
  `rfc_status` INT NULL ,
  `rfc_dauer` DOUBLE NULL ,
  PRIMARY KEY (`idtm_changerequest`) )
ENGINE = InnoDB
COMMENT = 'Request of Change';

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tt_message`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tt_message` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tt_message` (
  `idtt_message` INT NOT NULL AUTO_INCREMENT ,
  `mes_date` TIMESTAMP NULL ,
  `to_idtm_organisation` INT NULL ,
  `from_idtm_organisation` INT NULL ,
  `mes_content` BLOB NULL ,
  PRIMARY KEY (`idtt_message`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`catalogue`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`catalogue` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`catalogue` (
  `cat_id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(100) NULL ,
  `source_lang` VARCHAR(100) NULL ,
  `target_lang` VARCHAR(100) NULL ,
  `date_created` INT(11) NULL ,
  `date_modified` INT(11) NULL ,
  `author` VARCHAR(255) NULL ,
  PRIMARY KEY (`cat_id`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`trans_unit`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`trans_unit` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`trans_unit` (
  `msg_id` INT NOT NULL AUTO_INCREMENT ,
  `cat_id` INT NULL ,
  `id` VARCHAR(255) NULL ,
  `source` TEXT NULL ,
  `target` TEXT NULL ,
  `comments` TEXT NULL ,
  `date_added` INT NULL ,
  `date_modified` INT NULL ,
  `author` VARCHAR(255) NULL ,
  `translated` TINYINT(1) NULL ,
  PRIMARY KEY (`msg_id`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_kosten_status`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_kosten_status` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_kosten_status` (
  `idta_kosten_status` INT NOT NULL AUTO_INCREMENT ,
  `kst_status_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_kosten_status`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_zeiterfassung`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_zeiterfassung` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_zeiterfassung` (
  `idtm_zeiterfassung` INT NOT NULL AUTO_INCREMENT ,
  `idtm_organisation` INT NULL ,
  `idtm_activity` INT NULL ,
  `zeit_date` DATE NULL ,
  `zeit_starttime` TIME NULL ,
  `zeit_endtime` TIME NULL ,
  `zeit_break` DOUBLE NULL ,
  `zeit_dauer` DOUBLE NULL ,
  `idta_kosten_status` INT NULL DEFAULT 1 ,
  `zeit_descr` BLOB NULL ,
  PRIMARY KEY (`idtm_zeiterfassung`) ,
  CONSTRAINT `fk_tm_zeiterfassung_ta_kosten_status`
    FOREIGN KEY (`idta_kosten_status` )
    REFERENCES `harley`.`ta_kosten_status` (`idta_kosten_status` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_tm_zeiterfassung_ta_kosten_status` ON `harley`.`tm_zeiterfassung` (`idta_kosten_status` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_termin_ressource`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_termin_ressource` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_termin_ressource` (
  `idtm_termin_ressource` INT NOT NULL AUTO_INCREMENT ,
  `idtm_termin` INT NULL ,
  `idtm_ressource` INT NULL ,
  PRIMARY KEY (`idtm_termin_ressource`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_stammdaten_group`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_stammdaten_group` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_stammdaten_group` (
  `idta_stammdaten_group` INT NULL AUTO_INCREMENT ,
  `stammdaten_group_name` VARCHAR(45) NULL ,
  `idta_struktur_type` INT NULL ,
  PRIMARY KEY (`idta_stammdaten_group`) ,
  CONSTRAINT `fk_ta_stammdaten_group_ta_struktur_type`
    FOREIGN KEY (`idta_struktur_type` )
    REFERENCES `harley`.`ta_struktur_type` (`idta_struktur_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_ta_stammdaten_group_ta_struktur_type` ON `harley`.`ta_stammdaten_group` (`idta_struktur_type` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_stammdaten`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_stammdaten` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_stammdaten` (
  `idtm_stammdaten` INT NULL AUTO_INCREMENT ,
  `stammdaten_name` VARCHAR(45) NULL ,
  `stammdaten_key_extern` VARCHAR(45) NULL ,
  `idta_stammdaten_group` INT NULL ,
  PRIMARY KEY (`idtm_stammdaten`) ,
  CONSTRAINT `fk_tm_stammdaten_ta_stammdaten_group`
    FOREIGN KEY (`idta_stammdaten_group` )
    REFERENCES `harley`.`ta_stammdaten_group` (`idta_stammdaten_group` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_tm_stammdaten_ta_stammdaten_group` ON `harley`.`tm_stammdaten` (`idta_stammdaten_group` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tt_stammdaten`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tt_stammdaten` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tt_stammdaten` (
  `idtt_stammdaten` INT NULL AUTO_INCREMENT ,
  `idta_feldfunktion` INT NULL ,
  `idta_variante` INT NULL ,
  `tt_stammdaten_value` DOUBLE NULL ,
  `idtm_stammdaten` INT NULL ,
  PRIMARY KEY (`idtt_stammdaten`) ,
  CONSTRAINT `fk_tm_stammdaten_tt_stammdaten`
    FOREIGN KEY (`idtm_stammdaten` )
    REFERENCES `harley`.`tm_stammdaten` (`idtm_stammdaten` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ta_variante_tt_stammdaten`
    FOREIGN KEY (`idta_variante` )
    REFERENCES `harley`.`ta_variante` (`idta_variante` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ta_feldfunktion_tt_stammdaten`
    FOREIGN KEY (`idta_feldfunktion` )
    REFERENCES `harley`.`ta_feldfunktion` (`idta_feldfunktion` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_tm_stammdaten_tt_stammdaten` ON `harley`.`tt_stammdaten` (`idtm_stammdaten` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_ta_variante_tt_stammdaten` ON `harley`.`tt_stammdaten` (`idta_variante` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_ta_feldfunktion_tt_stammdaten` ON `harley`.`tt_stammdaten` (`idta_feldfunktion` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_struktur_has_ta_stammdaten_group`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_struktur_has_ta_stammdaten_group` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_struktur_has_ta_stammdaten_group` (
  `idtm_struktur_has_ta_stammdaten_group` INT NULL AUTO_INCREMENT ,
  `idtm_struktur` INT NULL DEFAULT 1 ,
  `idta_stammdaten_group` INT NULL DEFAULT 1 ,
  PRIMARY KEY (`idtm_struktur_has_ta_stammdaten_group`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_aufgabe_ressource`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_aufgabe_ressource` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_aufgabe_ressource` (
  `idtm_aufgabe_ressource` INT NULL AUTO_INCREMENT ,
  `idtm_aufgabe` INT NULL ,
  `idtm_ressource` INT NULL ,
  `auf_res_dauer` DOUBLE NULL ,
  PRIMARY KEY (`idtm_aufgabe_ressource`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_activity_has_tm_organisation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_activity_has_tm_organisation` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_activity_has_tm_organisation` (
  `idtm_activity_has_tm_organisation` INT NULL AUTO_INCREMENT ,
  `idtm_activity` INT NULL ,
  `idtm_organisation` INT NULL ,
  PRIMARY KEY (`idtm_activity_has_tm_organisation`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`ta_pivot_bericht`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`ta_pivot_bericht` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`ta_pivot_bericht` (
  `idta_pivot_bericht` INT NULL AUTO_INCREMENT ,
  `idtm_user` INT NULL ,
  `pivot_bericht_cdate` DATE NULL ,
  `pivot_bericht_name` VARCHAR(45) NULL ,
  `idta_feldfunktion` INT NULL ,
  `pivot_bericht_operator` VARCHAR(45) NULL DEFAULT 'SUM' ,
  PRIMARY KEY (`idta_pivot_bericht`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `harley`.`tm_pivot`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley`.`tm_pivot` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `harley`.`tm_pivot` (
  `idtm_pivot` INT NULL AUTO_INCREMENT ,
  `idta_pivot_bericht` INT NULL ,
  `idta_stammdaten_group` INT NULL ,
  `parent_idtm_pivot` INT NULL ,
  `pivot_position` INT NULL DEFAULT 1 COMMENT '1 = Spalte, 0 = Zeile' ,
  `pivot_filter` INT NULL ,
  PRIMARY KEY (`idtm_pivot`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Placeholder table for view `harley`.`vv_activity_participants`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `harley`.`vv_activity_participants` (`id` INT);
SHOW WARNINGS;

-- -----------------------------------------------------
-- Placeholder table for view `harley`.`vv_activity_activity`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `harley`.`vv_activity_activity` (`id` INT);
SHOW WARNINGS;

-- -----------------------------------------------------
-- Placeholder table for view `harley`.`vv_activity_ziele`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `harley`.`vv_activity_ziele` (`id` INT);
SHOW WARNINGS;

-- -----------------------------------------------------
-- Placeholder table for view `harley`.`vv_activity_inoutput`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `harley`.`vv_activity_inoutput` (`id` INT);
SHOW WARNINGS;

-- -----------------------------------------------------
-- Placeholder table for view `harley`.`vv_termin_organisation`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `harley`.`vv_termin_organisation` (`id` INT);
SHOW WARNINGS;

-- -----------------------------------------------------
-- Placeholder table for view `harley`.`vv_protokoll_detail_aufgabe`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `harley`.`vv_protokoll_detail_aufgabe` (`id` INT);
SHOW WARNINGS;

-- -----------------------------------------------------
-- Placeholder table for view `harley`.`vv_verteiler_organisation`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `harley`.`vv_verteiler_organisation` (`id` INT);
SHOW WARNINGS;

-- -----------------------------------------------------
-- Placeholder table for view `harley`.`vv_termin_ressource`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `harley`.`vv_termin_ressource` (`id` INT);
SHOW WARNINGS;

-- -----------------------------------------------------
-- Placeholder table for view `harley`.`vv_struktur_stammdaten_group`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `harley`.`vv_struktur_stammdaten_group` (`id` INT);
SHOW WARNINGS;

-- -----------------------------------------------------
-- Placeholder table for view `harley`.`vv_aufgabe_ressource`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `harley`.`vv_aufgabe_ressource` (`id` INT);
SHOW WARNINGS;

-- -----------------------------------------------------
-- Placeholder table for view `harley`.`vv_activity_organisation`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `harley`.`vv_activity_organisation` (`id` INT);
SHOW WARNINGS;

-- -----------------------------------------------------
-- View `harley`.`vv_activity_participants`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `harley`.`vv_activity_participants` ;
SHOW WARNINGS;
DROP TABLE IF EXISTS `harley`.`vv_activity_participants`;
SHOW WARNINGS;
CREATE  OR REPLACE VIEW `harley`.`vv_activity_participants` AS
SELECT tm_activity_participants.*,tm_organisation.org_name,tm_user_role.user_role_name FROM tm_activity_participants INNER JOIN tm_organisation ON tm_organisation.idtm_organisation = tm_activity_participants.idtm_organisation
INNER JOIN tm_user_role ON tm_user_role.idtm_user_role = tm_organisation.org_idtm_user_role;
SHOW WARNINGS;

-- -----------------------------------------------------
-- View `harley`.`vv_activity_activity`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `harley`.`vv_activity_activity` ;
SHOW WARNINGS;
DROP TABLE IF EXISTS `harley`.`vv_activity_activity`;
SHOW WARNINGS;
CREATE  OR REPLACE VIEW `harley`.`vv_activity_activity` AS
SELECT a.*, b.act_name, c.act_name AS pre_act_name FROM ta_activity_activity a INNER JOIN tm_activity b ON a.idtm_activity = b.idtm_activity
INNER JOIN tm_activity c ON c.idtm_activity = a.pre_idtm_activity;
SHOW WARNINGS;

-- -----------------------------------------------------
-- View `harley`.`vv_activity_ziele`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `harley`.`vv_activity_ziele` ;
SHOW WARNINGS;
DROP TABLE IF EXISTS `harley`.`vv_activity_ziele`;
SHOW WARNINGS;
CREATE  OR REPLACE VIEW `harley`.`vv_activity_ziele` AS
SELECT a.*, b.ttzie_name FROM tm_activity_has_tt_ziele a INNER JOIN tt_ziele b ON a.idtt_ziele = b.idtt_ziele;
SHOW WARNINGS;

-- -----------------------------------------------------
-- View `harley`.`vv_activity_inoutput`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `harley`.`vv_activity_inoutput` ;
SHOW WARNINGS;
DROP TABLE IF EXISTS `harley`.`vv_activity_inoutput`;
SHOW WARNINGS;
CREATE  OR REPLACE VIEW `harley`.`vv_activity_inoutput` AS
SELECT a.*,b.act_name,c.ino_name FROM tm_inoutput_has_tm_activity a INNER JOIN tm_activity b ON a.idtm_activity = b.idtm_activity
INNER JOIN tm_inoutput c ON a.idtm_inoutput = c.idtm_inoutput;
SHOW WARNINGS;

-- -----------------------------------------------------
-- View `harley`.`vv_termin_organisation`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `harley`.`vv_termin_organisation` ;
SHOW WARNINGS;
DROP TABLE IF EXISTS `harley`.`vv_termin_organisation`;
SHOW WARNINGS;
CREATE  OR REPLACE VIEW `harley`.`vv_termin_organisation` AS
SELECT tm_termin_organisation.*,tm_organisation.org_name,tm_user_role.user_role_name FROM tm_termin_organisation INNER JOIN tm_organisation ON tm_organisation.idtm_organisation = tm_termin_organisation.idtm_organisation
INNER JOIN tm_user_role ON tm_user_role.idtm_user_role = tm_organisation.org_idtm_user_role;
SHOW WARNINGS;

-- -----------------------------------------------------
-- View `harley`.`vv_protokoll_detail_aufgabe`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `harley`.`vv_protokoll_detail_aufgabe` ;
SHOW WARNINGS;
DROP TABLE IF EXISTS `harley`.`vv_protokoll_detail_aufgabe`;
SHOW WARNINGS;
CREATE  OR REPLACE VIEW `harley`.`vv_protokoll_detail_aufgabe` AS
SELECT a.*, b.* FROM tm_protokoll_detail a INNER JOIN tm_aufgaben b ON b.auf_tabelle ='tm_protokoll_detail' AND a.idtm_protokoll_detail = b.auf_id;
SHOW WARNINGS;

-- -----------------------------------------------------
-- View `harley`.`vv_verteiler_organisation`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `harley`.`vv_verteiler_organisation` ;
SHOW WARNINGS;
DROP TABLE IF EXISTS `harley`.`vv_verteiler_organisation`;
SHOW WARNINGS;
CREATE  OR REPLACE VIEW `harley`.`vv_verteiler_organisation` AS
SELECT tm_verteiler_organisation.*,tm_organisation.org_name,tm_user_role.user_role_name FROM tm_verteiler_organisation INNER JOIN tm_organisation ON tm_organisation.idtm_organisation = tm_verteiler_organisation.idtm_organisation
INNER JOIN tm_user_role ON tm_user_role.idtm_user_role = tm_organisation.org_idtm_user_role;
SHOW WARNINGS;

-- -----------------------------------------------------
-- View `harley`.`vv_termin_ressource`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `harley`.`vv_termin_ressource` ;
SHOW WARNINGS;
DROP TABLE IF EXISTS `harley`.`vv_termin_ressource`;
SHOW WARNINGS;
CREATE  OR REPLACE VIEW `harley`.`vv_termin_ressource` AS
SELECT tm_termin_ressource.*,tm_ressource.res_name FROM tm_termin_ressource INNER JOIN tm_ressource ON tm_ressource.idtm_ressource = tm_termin_ressource.idtm_ressource;
SHOW WARNINGS;

-- -----------------------------------------------------
-- View `harley`.`vv_struktur_stammdaten_group`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `harley`.`vv_struktur_stammdaten_group` ;
SHOW WARNINGS;
DROP TABLE IF EXISTS `harley`.`vv_struktur_stammdaten_group`;
SHOW WARNINGS;
CREATE  OR REPLACE VIEW `harley`.`vv_struktur_stammdaten_group` AS
SELECT a.*, b.stammdaten_group_name FROM `tm_struktur_has_ta_stammdaten_group` a INNER JOIN ta_stammdaten_group b ON a.idta_stammdaten_group = b.idta_stammdaten_group;
SHOW WARNINGS;

-- -----------------------------------------------------
-- View `harley`.`vv_aufgabe_ressource`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `harley`.`vv_aufgabe_ressource` ;
SHOW WARNINGS;
DROP TABLE IF EXISTS `harley`.`vv_aufgabe_ressource`;
SHOW WARNINGS;
CREATE  OR REPLACE VIEW `harley`.`vv_aufgabe_ressource` AS
SELECT tm_aufgabe_ressource.*,tm_ressource.res_name FROM tm_aufgabe_ressource INNER JOIN tm_ressource ON tm_ressource.idtm_ressource = tm_aufgabe_ressource.idtm_ressource;
SHOW WARNINGS;

-- -----------------------------------------------------
-- View `harley`.`vv_activity_organisation`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `harley`.`vv_activity_organisation` ;
SHOW WARNINGS;
DROP TABLE IF EXISTS `harley`.`vv_activity_organisation`;
SHOW WARNINGS;
CREATE  OR REPLACE VIEW `harley`.`vv_activity_organisation` AS
SELECT a.*, b.org_name FROM tm_activity_has_tm_organisation a INNER JOIN tm_organisation b ON a.idtm_organisation = b.idtm_organisation;
SHOW WARNINGS;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
