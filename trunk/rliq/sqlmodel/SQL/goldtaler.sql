SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

-- -----------------------------------------------------
-- Table `db239003879`.`tm_user_role`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_user_role` (
  `idtm_user_role` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_role_name` VARCHAR(45) NULL ,
  `user_role_rechte` INT NULL DEFAULT 0 ,
  PRIMARY KEY (`idtm_user_role`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_user`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_user` (
  `idtm_user` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_name` VARCHAR(45) NULL ,
  `user_vorname` VARCHAR(45) NULL ,
  `user_password` VARCHAR(45) NULL ,
  `idtm_user_role` INT NOT NULL DEFAULT 1 ,
  `user_username` VARCHAR(45) NOT NULL ,
  `user_mail` VARCHAR(45) NULL ,
  PRIMARY KEY (`idtm_user`, `user_username`) ,
  INDEX `fk_tm_user_role` (`idtm_user_role` ASC) ,
  CONSTRAINT `fk_tm_user_role`
    FOREIGN KEY (`idtm_user_role` )
    REFERENCES `db239003879`.`tm_user_role` (`idtm_user_role` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'User Table';


-- -----------------------------------------------------
-- Table `db239003879`.`ta_partei`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_partei` (
  `partei_name` VARCHAR(45) NULL ,
  `partei_name2` VARCHAR(45) NULL ,
  `partei_name3` VARCHAR(45) NULL ,
  `partei_vorname` VARCHAR(45) NULL ,
  `idtm_user` INT NOT NULL DEFAULT 1 ,
  `idta_partei` INT(11) NOT NULL AUTO_INCREMENT ,
  INDEX `fk_idtm_user` (`idtm_user` ASC) ,
  PRIMARY KEY (`idta_partei`) ,
  CONSTRAINT `fk_idtm_user`
    FOREIGN KEY (`idtm_user` )
    REFERENCES `db239003879`.`tm_user` (`idtm_user` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
COMMENT = 'Main Informations about Contact';


-- -----------------------------------------------------
-- Table `db239003879`.`tm_country`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_country` (
  `idtm_country` INT(11) NOT NULL ,
  `country_iso` VARCHAR(45) NULL ,
  `country_ful` VARCHAR(45) NULL ,
  PRIMARY KEY (`idtm_country`) )
ENGINE = InnoDB
COMMENT = 'list of countries\n';


-- -----------------------------------------------------
-- Table `db239003879`.`ta_adresse`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_adresse` (
  `idta_adresse` INT(11) NOT NULL AUTO_INCREMENT ,
  `adresse_street` VARCHAR(45) NULL ,
  `adresse_zip` VARCHAR(45) NULL ,
  `adresse_town` VARCHAR(45) NULL ,
  `idtm_country` INT NOT NULL DEFAULT 1 ,
  `adresse_lat` VARCHAR(10) NULL ,
  `adresse_long` VARCHAR(10) NULL ,
  `adresse_ismain` TINYINT(1) NULL DEFAULT 0 ,
  INDEX `fk_idtm_country` (`idtm_country` ASC) ,
  PRIMARY KEY (`idta_adresse`) ,
  CONSTRAINT `fk_idtm_country`
    FOREIGN KEY (`idtm_country` )
    REFERENCES `db239003879`.`tm_country` (`idtm_country` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Adresses';


-- -----------------------------------------------------
-- Table `db239003879`.`ta_partei_has_ta_adresse`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_partei_has_ta_adresse` (
  `idta_partei` INT NULL ,
  `idta_adresse` INT NULL ,
  UNIQUE INDEX `PK` (`idta_partei` ASC, `idta_adresse` ASC) ,
  INDEX `fk_idta_partei` (`idta_partei` ASC) ,
  INDEX `fk_idta_adresse` (`idta_adresse` ASC) ,
  CONSTRAINT `fk_idta_partei`
    FOREIGN KEY (`idta_partei` )
    REFERENCES `db239003879`.`ta_partei` (`idta_partei` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_idta_adresse`
    FOREIGN KEY (`idta_adresse` )
    REFERENCES `db239003879`.`ta_adresse` (`idta_adresse` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_waren_kategorie`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_waren_kategorie` (
  `idtm_waren_kategorie` INT(11) NOT NULL AUTO_INCREMENT ,
  `waren_kategorie_name` VARCHAR(45) NULL ,
  `waren_kategorie_beschreibung` TINYBLOB NULL ,
  `parent_idtm_waren_kategorie` INT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`idtm_waren_kategorie`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_preis_kategorie`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_preis_kategorie` (
  `idtm_preis_kategorie` INT(11) NOT NULL AUTO_INCREMENT ,
  `preis_kategorie_name` VARCHAR(45) NULL ,
  `preis_kategorie_beschreibung` VARCHAR(45) NULL ,
  PRIMARY KEY (`idtm_preis_kategorie`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_fahrzeug_kategorie`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_fahrzeug_kategorie` (
  `idtm_fahrzeug_kategorie` INT(11) NOT NULL AUTO_INCREMENT ,
  `fahrzeug_kategorie_name` VARCHAR(45) NULL ,
  `fahrzeug_kategorie_beschreibung` VARCHAR(45) NULL ,
  PRIMARY KEY (`idtm_fahrzeug_kategorie`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_waren`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_waren` (
  `idta_waren` INT(11) NOT NULL AUTO_INCREMENT ,
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
  INDEX `fkidtm_waren_kategorie` (`idtm_waren_kategorie` ASC) ,
  INDEX `fkidtm_preis_kategorie` (`idtm_preis_kategorie` ASC) ,
  INDEX `fkidtm_adresse` (`idta_adresse` ASC) ,
  CONSTRAINT `fkidtm_waren_kategorie`
    FOREIGN KEY (`idtm_waren_kategorie` )
    REFERENCES `db239003879`.`tm_waren_kategorie` (`idtm_waren_kategorie` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fkidtm_preis_kategorie`
    FOREIGN KEY (`idtm_preis_kategorie` )
    REFERENCES `db239003879`.`tm_preis_kategorie` (`idtm_preis_kategorie` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fkidtm_adresse`
    FOREIGN KEY (`idta_adresse` )
    REFERENCES `db239003879`.`ta_adresse` (`idta_adresse` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_waren_has_ta_partei`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_waren_has_ta_partei` (
  `idta_waren_has_ta_partei` INT NOT NULL AUTO_INCREMENT ,
  `idta_partei_vk` INT NULL ,
  `idta_partei_ek` INT NULL DEFAULT 0 ,
  `dat_ea` TIMESTAMP NULL ,
  `stat_status` INT NULL DEFAULT 0 ,
  `idta_waren` INT NULL ,
  PRIMARY KEY (`idta_waren_has_ta_partei`) ,
  INDEX `fkidta_partei_vk` (`idta_partei_vk` ASC) ,
  INDEX `fkidta_partei_ek` (`idta_partei_ek` ASC) ,
  INDEX `fkidta_waren` (`idta_waren` ASC) ,
  CONSTRAINT `fkidta_partei_vk`
    FOREIGN KEY (`idta_partei_vk` )
    REFERENCES `db239003879`.`ta_partei` (`idta_partei` )
    ON DELETE SET NULL
    ON UPDATE SET NULL,
  CONSTRAINT `fkidta_partei_ek`
    FOREIGN KEY (`idta_partei_ek` )
    REFERENCES `db239003879`.`ta_partei` (`idta_partei` )
    ON DELETE SET NULL
    ON UPDATE SET NULL,
  CONSTRAINT `fkidta_waren`
    FOREIGN KEY (`idta_waren` )
    REFERENCES `db239003879`.`ta_waren` (`idta_waren` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
COMMENT = 'beziehung waren zu kauf und verkauf mit status und datum';


-- -----------------------------------------------------
-- Table `db239003879`.`ta_fracht`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_fracht` (
  `idta_fracht` INT(11) NOT NULL AUTO_INCREMENT ,
  `fracht_auftragsnummer` VARCHAR(45) NULL ,
  `fracht_status` INT NULL DEFAULT 1 COMMENT '1 = verhandelbar, 2 = fix' ,
  `fracht_sonstiges` MEDIUMBLOB NULL ,
  `idtm_fahrzeug_kategorie` INT NOT NULL ,
  PRIMARY KEY (`idta_fracht`) ,
  INDEX `fk_idtm_fahrzeug_kategorie` (`idtm_fahrzeug_kategorie` ASC) ,
  CONSTRAINT `fk_idtm_fahrzeug_kategorie`
    FOREIGN KEY (`idtm_fahrzeug_kategorie` )
    REFERENCES `db239003879`.`tm_fahrzeug_kategorie` (`idtm_fahrzeug_kategorie` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_fracht_teilladung`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_fracht_teilladung` (
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
  INDEX `fk_idta_fracht` (`idta_fracht` ASC) ,
  CONSTRAINT `fk_idta_fracht`
    FOREIGN KEY (`idta_fracht` )
    REFERENCES `db239003879`.`ta_fracht` (`idta_fracht` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_organisation_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_organisation_type` (
  `idta_organisation_type` INT(11) NOT NULL AUTO_INCREMENT ,
  `org_type_name` VARCHAR(45) NULL ,
  `org_type_monat_jahr` DATE NULL ,
  PRIMARY KEY (`idta_organisation_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_einheit`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_einheit` (
  `idta_einheit` INT(11) NOT NULL AUTO_INCREMENT ,
  `ein_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_einheit`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_rescalendar`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_rescalendar` (
  `idta_rescalendar` INT(11) NOT NULL AUTO_INCREMENT ,
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


-- -----------------------------------------------------
-- Table `db239003879`.`ta_ressource_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_ressource_type` (
  `idta_ressource_type` INT(11) NOT NULL AUTO_INCREMENT ,
  `res_type_name` VARCHAR(45) NULL ,
  `res_type_descr` BLOB NULL ,
  `res_type_kosten` DOUBLE NULL ,
  PRIMARY KEY (`idta_ressource_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_ressource`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_ressource` (
  `idtm_ressource` INT(11) NOT NULL AUTO_INCREMENT ,
  `res_name` VARCHAR(45) NULL ,
  `res_code` VARCHAR(45) NULL ,
  `idta_rescalendar` INT NULL ,
  `idta_ressource_type` INT NULL ,
  `res_produktivitaet` INT NULL ,
  `res_kosten` DOUBLE NULL COMMENT 'kosten pro std' ,
  `res_note` BLOB NULL ,
  `idta_einheit` INT NULL ,
  PRIMARY KEY (`idtm_ressource`) ,
  INDEX `fk_tm_ressource_ta_einheit` (`idta_einheit` ASC) ,
  INDEX `fk_tm_ressource_ta_rescalendar` (`idta_rescalendar` ASC) ,
  INDEX `fk_tm_ressource_ta_ressource_type` (`idta_ressource_type` ASC) ,
  CONSTRAINT `fk_tm_ressource_ta_einheit`
    FOREIGN KEY (`idta_einheit` )
    REFERENCES `db239003879`.`ta_einheit` (`idta_einheit` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_ressource_ta_rescalendar`
    FOREIGN KEY (`idta_rescalendar` )
    REFERENCES `db239003879`.`ta_rescalendar` (`idta_rescalendar` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_ressource_ta_ressource_type`
    FOREIGN KEY (`idta_ressource_type` )
    REFERENCES `db239003879`.`ta_ressource_type` (`idta_ressource_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_organisation`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_organisation` (
  `idtm_organisation` INT(11) NOT NULL AUTO_INCREMENT ,
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
  `org_ntuser` VARCHAR(100) NULL ,
  `org_name1` VARCHAR(45) NULL ,
  `org_name2` VARCHAR(45) NULL ,
  `org_anrede` VARCHAR(15) NULL ,
  `org_briefanrede` VARCHAR(45) NULL ,
  `org_vorname` VARCHAR(45) NULL ,
  `org_matchkey` VARCHAR(45) NULL ,
  `org_uid` VARCHAR(15) NULL ,
  `org_finanzamt` VARCHAR(45) NULL ,
  `org_steuernummer` VARCHAR(45) NULL ,
  `org_referat` VARCHAR(10) NULL ,
  `org_gemeinde` VARCHAR(45) NULL ,
  `org_katastragemeinde` VARCHAR(45) NULL ,
  `org_grundstuecksnummer` VARCHAR(45) NULL ,
  `org_einlagezahl` VARCHAR(45) NULL ,
  `org_baujahr` INT(4) NULL ,
  `org_wohnungen` INT(4) NULL ,
  `org_fk_internal` VARCHAR(45) NULL ,
  `org_birthday_date` DATE NULL COMMENT 'Birthday' ,
  `org_specialday_date` DATE NULL COMMENT 'Birthday of Wife nameday...' ,
  `idta_organisation_art` INT NULL COMMENT 'Eigentum oder Miete' ,
  `org_steuerart` VARCHAR(45) NULL COMMENT 'Regelbesteuerung' ,
  `org_einzugsdatum` DATE NULL ,
  `org_auszugsdatum` DATE NULL ,
  PRIMARY KEY (`idtm_organisation`) ,
  INDEX `fk_tm_organisation` (`idta_organisation_type` ASC) ,
  INDEX `fk_tm_org_tm_ressource` (`idtm_ressource` ASC) ,
  CONSTRAINT `fk_tm_organisation`
    FOREIGN KEY (`idta_organisation_type` )
    REFERENCES `db239003879`.`ta_organisation_type` (`idta_organisation_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_org_tm_ressource`
    FOREIGN KEY (`idtm_ressource` )
    REFERENCES `db239003879`.`tm_ressource` (`idtm_ressource` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_struktur_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_struktur_type` (
  `idta_struktur_type` INT(11) NOT NULL AUTO_INCREMENT ,
  `struktur_type_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_struktur_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_struktur`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_struktur` (
  `idtm_struktur` INT(11) NOT NULL AUTO_INCREMENT ,
  `struktur_name` VARCHAR(45) NULL ,
  `parent_idtm_struktur` INT NOT NULL DEFAULT 0 ,
  `idta_struktur_type` INT NULL ,
  `idtm_stammdaten` INT NULL DEFAULT 0 ,
  `struktur_lft` INT NULL COMMENT 'nested modell links' ,
  `struktur_rgt` INT NULL COMMENT 'nested modell rechts' ,
  PRIMARY KEY (`idtm_struktur`) ,
  INDEX `fk_tm_struktur` (`idta_struktur_type` ASC) ,
  CONSTRAINT `fk_tm_struktur`
    FOREIGN KEY (`idta_struktur_type` )
    REFERENCES `db239003879`.`ta_struktur_type` (`idta_struktur_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_risiko_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_risiko_type` (
  `idta_risiko_type` INT(11) NOT NULL AUTO_INCREMENT ,
  `ris_type_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_risiko_type`) )
ENGINE = InnoDB
COMMENT = 'Risikoarten';


-- -----------------------------------------------------
-- Table `db239003879`.`tm_risiko`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_risiko` (
  `idtm_risiko` INT(11) NOT NULL AUTO_INCREMENT ,
  `ris_name` VARCHAR(45) NULL ,
  `ris_descr` TINYBLOB NULL ,
  `parent_idtm_risiko` INT NOT NULL DEFAULT 0 ,
  `idta_risiko_type` INT NULL ,
  PRIMARY KEY (`idtm_risiko`) ,
  INDEX `fk_tm_risiko` (`idta_risiko_type` ASC) ,
  CONSTRAINT `fk_tm_risiko`
    FOREIGN KEY (`idta_risiko_type` )
    REFERENCES `db239003879`.`ta_risiko_type` (`idta_risiko_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Risiken';


-- -----------------------------------------------------
-- Table `db239003879`.`ta_prozess_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_prozess_type` (
  `idta_prozess_type` INT(11) NOT NULL AUTO_INCREMENT ,
  `pro_type_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_prozess_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_prozess`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_prozess` (
  `idtm_prozess` INT NOT NULL AUTO_INCREMENT ,
  `pro_name` VARCHAR(45) NULL ,
  `pro_descr` BLOB NULL ,
  `parent_idtm_prozess` INT NOT NULL DEFAULT 0 ,
  `idta_prozess_type` INT NULL ,
  `pro_step` BIGINT NULL ,
  PRIMARY KEY (`idtm_prozess`) ,
  INDEX `fk_tm_prozess` (`idta_prozess_type` ASC) ,
  CONSTRAINT `fk_tm_prozess`
    FOREIGN KEY (`idta_prozess_type` )
    REFERENCES `db239003879`.`ta_prozess_type` (`idta_prozess_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_prozess_step`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_prozess_step` (
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


-- -----------------------------------------------------
-- Table `db239003879`.`tm_aufgaben`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_aufgaben` (
  `idtm_aufgaben` INT NOT NULL AUTO_INCREMENT ,
  `auf_tabelle` VARCHAR(45) NULL ,
  `auf_id` INT NULL ,
  `idtm_organisation` INT NULL ,
  `auf_cdate` TIMESTAMP NOT NULL ,
  `auf_beschreibung` BLOB NULL ,
  `auf_tdate` DATE NULL ,
  `auf_priority` INT NULL ,
  `auf_name` VARCHAR(45) NULL ,
  `auf_done` TINYINT(1) NULL DEFAULT 0 ,
  `auf_dauer` INT NULL COMMENT 'Dauer in Stunden' ,
  `auf_ddate` DATE NULL ,
  `auf_idtm_organisation` INT NULL ,
  `idta_aufgaben_type` INT NULL ,
  `auf_tag` VARCHAR(45) NULL COMMENT 'field to classify the content' ,
  `auf_zeichen_eigen` VARCHAR(45) NULL ,
  `auf_zeichen_fremd` VARCHAR(45) NULL ,
  PRIMARY KEY (`idtm_aufgaben`) ,
  INDEX `fk_idtm_organisation_tm_aufgabe` (`idtm_organisation` ASC) ,
  CONSTRAINT `fk_idtm_organisation_tm_aufgabe`
    FOREIGN KEY (`idtm_organisation` )
    REFERENCES `db239003879`.`tm_organisation` (`idtm_organisation` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_rcvalue`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_rcvalue` (
  `idtm_rcvalue` INT(11) NOT NULL AUTO_INCREMENT ,
  `rcv_tabelle` VARCHAR(45) NULL ,
  `rcv_id` INT NULL ,
  `idtm_organisation` INT NULL ,
  `rcv_type` INT NULL DEFAULT 0 COMMENT '0=Risiko; 1=Chance' ,
  `rcv_comment` MEDIUMTEXT NULL ,
  `idtm_risiko` INT NULL ,
  PRIMARY KEY (`idtm_rcvalue`) ,
  INDEX `fk_idtm_organisation_tm_rcvalue` (`idtm_organisation` ASC) ,
  INDEX `fk_idtm_risiko_tm_rcvalue` (`idtm_risiko` ASC) ,
  CONSTRAINT `fk_idtm_organisation_tm_rcvalue`
    FOREIGN KEY (`idtm_organisation` )
    REFERENCES `db239003879`.`tm_organisation` (`idtm_organisation` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_idtm_risiko_tm_rcvalue`
    FOREIGN KEY (`idtm_risiko` )
    REFERENCES `db239003879`.`tm_risiko` (`idtm_risiko` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tt_rcvalue`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tt_rcvalue` (
  `idtt_rcvalue` INT NOT NULL AUTO_INCREMENT ,
  `rcv_ewk` FLOAT NULL ,
  `rcv_schaden` FLOAT NULL ,
  `rcv_prio` FLOAT NULL ,
  `rcv_cby` INT NULL ,
  `rcv_cdate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `idtm_rcvalue` INT NULL ,
  PRIMARY KEY (`idtt_rcvalue`) ,
  INDEX `fk_tm_rcvalue_tt_rcvalue` (`idtm_rcvalue` ASC) ,
  CONSTRAINT `fk_tm_rcvalue_tt_rcvalue`
    FOREIGN KEY (`idtm_rcvalue` )
    REFERENCES `db239003879`.`tm_rcvalue` (`idtm_rcvalue` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Die Tatsächlichen Werte';


-- -----------------------------------------------------
-- Table `db239003879`.`ta_feldfunktion`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_feldfunktion` (
  `idta_feldfunktion` INT(11) NOT NULL AUTO_INCREMENT ,
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
  `ff_readonly` TINYINT(1) NULL DEFAULT 0 ,
  `ff_order` INT NULL DEFAULT 0 ,
  `ff_calcopening` TINYINT(1) NULL DEFAULT 0 COMMENT 'linked to opening balance' ,
  `ff_cashbalance` INT NULL DEFAULT 0 COMMENT '0 neutral 1 cashin 2 cashout 3 taxin 4 taxout' ,
  PRIMARY KEY (`idta_feldfunktion`) ,
  INDEX `fk_ta_struktur_type_ta_feldfunktion` (`idta_struktur_type` ASC) ,
  CONSTRAINT `fk_ta_struktur_type_ta_feldfunktion`
    FOREIGN KEY (`idta_struktur_type` )
    REFERENCES `db239003879`.`ta_struktur_type` (`idta_struktur_type` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_variante`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_variante` (
  `idta_variante` INT(11) NOT NULL AUTO_INCREMENT ,
  `idtm_user` INT NULL ,
  `var_descr` VARCHAR(45) NULL ,
  `w_id_variante` INT NULL ,
  `var_default` TINYINT(1) NULL DEFAULT 0 ,
  `idta_perioden` VARCHAR(45) NULL DEFAULT 0 ,
  PRIMARY KEY (`idta_variante`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tt_werte`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tt_werte` (
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
  INDEX `fk_tt_werte_ta_feldfunktion` (`idta_feldfunktion` ASC) ,
  INDEX `fk_tt_werte_tm_struktur` (`idtm_struktur` ASC) ,
  INDEX `fk_tt_werte_ta_variante` (`w_id_variante` ASC) ,
  CONSTRAINT `fk_tt_werte_ta_feldfunktion`
    FOREIGN KEY (`idta_feldfunktion` )
    REFERENCES `db239003879`.`ta_feldfunktion` (`idta_feldfunktion` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tt_werte_tm_struktur`
    FOREIGN KEY (`idtm_struktur` )
    REFERENCES `db239003879`.`tm_struktur` (`idtm_struktur` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tt_werte_ta_variante`
    FOREIGN KEY (`w_id_variante` )
    REFERENCES `db239003879`.`ta_variante` (`idta_variante` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_perioden`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_perioden` (
  `idta_perioden` INT NOT NULL AUTO_INCREMENT ,
  `per_intern` BIGINT NULL ,
  `per_extern` VARCHAR(45) NULL ,
  `parent_idta_perioden` INT NULL ,
  PRIMARY KEY (`idta_perioden`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_collector`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_collector` (
  `idta_collector` INT NOT NULL AUTO_INCREMENT ,
  `idta_feldfunktion` INT NULL ,
  `col_idtafeldfunktion` INT NULL ,
  `col_operator` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_collector`) ,
  INDEX `fk_ta_feldfunktion_ta_collector` (`idta_feldfunktion` ASC) ,
  CONSTRAINT `fk_ta_feldfunktion_ta_collector`
    FOREIGN KEY (`idta_feldfunktion` )
    REFERENCES `db239003879`.`ta_feldfunktion` (`idta_feldfunktion` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'unser sammler	';


-- -----------------------------------------------------
-- Table `db239003879`.`ta_ziele_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_ziele_type` (
  `idta_ziele_type` INT(11) NOT NULL AUTO_INCREMENT ,
  `zie_type_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_ziele_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_ziele`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_ziele` (
  `idtm_ziele` INT(11) NOT NULL AUTO_INCREMENT ,
  `zie_name` VARCHAR(45) NULL ,
  `zie_descr` BLOB NULL ,
  `parent_idtm_ziele` INT NOT NULL DEFAULT 0 ,
  `idta_ziele_type` INT NULL ,
  PRIMARY KEY (`idtm_ziele`) ,
  INDEX `fk_tm_ziele_ta_ziele_type` (`idta_ziele_type` ASC) ,
  CONSTRAINT `fk_tm_ziele_ta_ziele_type`
    FOREIGN KEY (`idta_ziele_type` )
    REFERENCES `db239003879`.`ta_ziele_type` (`idta_ziele_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Zielklasse, Globalziel und Zielunterklasse';


-- -----------------------------------------------------
-- Table `db239003879`.`tt_ziele`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tt_ziele` (
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
  INDEX `fk_tt_ziele_tm_ziele` (`idtm_ziele` ASC) ,
  CONSTRAINT `fk_tt_ziele_tm_ziele`
    FOREIGN KEY (`idtm_ziele` )
    REFERENCES `db239003879`.`tm_ziele` (`idtm_ziele` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Ziele';


-- -----------------------------------------------------
-- Table `db239003879`.`ta_activity_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_activity_type` (
  `idta_activity_type` INT(11) NOT NULL AUTO_INCREMENT ,
  `act_type_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_activity_type`) )
ENGINE = InnoDB
COMMENT = 'Aktionstyp';


-- -----------------------------------------------------
-- Table `db239003879`.`tm_activity`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_activity` (
  `idtm_activity` INT(11) NOT NULL AUTO_INCREMENT ,
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
  INDEX `fk_tm_activity_tm_organisation` (`idtm_organisation` ASC) ,
  INDEX `fk_tm_activity_ta_activity_type` (`idta_activity_type` ASC) ,
  CONSTRAINT `fk_tm_activity_tm_organisation`
    FOREIGN KEY (`idtm_organisation` )
    REFERENCES `db239003879`.`tm_organisation` (`idtm_organisation` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_activity_ta_activity_type`
    FOREIGN KEY (`idta_activity_type` )
    REFERENCES `db239003879`.`ta_activity_type` (`idta_activity_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Projektphasen';


-- -----------------------------------------------------
-- Table `db239003879`.`tm_activity_participants`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_activity_participants` (
  `idtm_activity` INT NOT NULL DEFAULT 0 ,
  `idtm_organisation` INT NOT NULL DEFAULT 0 ,
  `idtm_activity_participant` INT NOT NULL AUTO_INCREMENT ,
  `act_part_anwesend` TINYINT(1) NULL ,
  `act_part_notiz` BLOB NULL ,
  PRIMARY KEY (`idtm_activity_participant`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_protokoll_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_protokoll_type` (
  `idta_protokoll_type` INT(11) NOT NULL AUTO_INCREMENT ,
  `prt_type_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_protokoll_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_protokoll`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_protokoll` (
  `idtm_protokoll` INT(11) NOT NULL AUTO_INCREMENT ,
  `prt_name` VARCHAR(45) NULL ,
  `prt_cdate` DATE NULL ,
  `prt_location` VARCHAR(45) NULL ,
  `prt_dauer` DOUBLE NULL ,
  `idtm_organisation` INT NULL DEFAULT 0 COMMENT 'Erzeuger' ,
  `idtm_termin` INT NULL DEFAULT 0 ,
  `idta_protokoll_type` INT NULL DEFAULT 0 ,
  PRIMARY KEY (`idtm_protokoll`) ,
  INDEX `fk_tm_protokoll_ta_protokoll_type` (`idta_protokoll_type` ASC) ,
  CONSTRAINT `fk_tm_protokoll_ta_protokoll_type`
    FOREIGN KEY (`idta_protokoll_type` )
    REFERENCES `db239003879`.`ta_protokoll_type` (`idta_protokoll_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = '	';


-- -----------------------------------------------------
-- Table `db239003879`.`ta_protokoll_ergebnistype`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_protokoll_ergebnistype` (
  `idta_protokoll_ergebnistype` INT(11) NOT NULL AUTO_INCREMENT ,
  `prt_ergtype_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_protokoll_ergebnistype`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_protokoll_detail_group`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_protokoll_detail_group` (
  `idta_protokoll_detail_group` INT(11) NOT NULL AUTO_INCREMENT ,
  `idtm_protokoll` INT NULL DEFAULT 0 ,
  PRIMARY KEY (`idta_protokoll_detail_group`) ,
  INDEX `fk_tm_protokoll_ta_protokoll_detail_group` (`idtm_protokoll` ASC) ,
  CONSTRAINT `fk_tm_protokoll_ta_protokoll_detail_group`
    FOREIGN KEY (`idtm_protokoll` )
    REFERENCES `db239003879`.`tm_protokoll` (`idtm_protokoll` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_protokoll_detail`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_protokoll_detail` (
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
  INDEX `fk_tm_protokoll_detail_ta_protokoll_ergebnistype` (`idta_protokoll_ergebnistype` ASC) ,
  INDEX `fk_ta_protokoll_detail_group_tm_protokoll_detail` (`idta_protokoll_detail_group` ASC) ,
  CONSTRAINT `fk_tm_protokoll_detail_ta_protokoll_ergebnistype`
    FOREIGN KEY (`idta_protokoll_ergebnistype` )
    REFERENCES `db239003879`.`ta_protokoll_ergebnistype` (`idta_protokoll_ergebnistype` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ta_protokoll_detail_group_tm_protokoll_detail`
    FOREIGN KEY (`idta_protokoll_detail_group` )
    REFERENCES `db239003879`.`ta_protokoll_detail_group` (`idta_protokoll_detail_group` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_activity_has_tt_ziele`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_activity_has_tt_ziele` (
  `idtt_ziele` INT NOT NULL ,
  `idtm_activity` INT NOT NULL ,
  `idtm_activity_has_tt_ziele` INT NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`idtm_activity_has_tt_ziele`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_inoutput_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_inoutput_type` (
  `idta_inoutput_type` INT(11) NOT NULL AUTO_INCREMENT ,
  `ino_type_name` VARCHAR(45) NULL ,
  `ino_type_descr` BLOB NULL ,
  PRIMARY KEY (`idta_inoutput_type`) )
ENGINE = InnoDB
COMMENT = 'In and Output Type	';


-- -----------------------------------------------------
-- Table `db239003879`.`tm_inoutput`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_inoutput` (
  `idtm_inoutput` INT NOT NULL AUTO_INCREMENT ,
  `ino_tabelle` VARCHAR(45) NULL ,
  `ino_id` INT NULL ,
  `idta_inoutput_type` INT NULL ,
  `ino_descr` BLOB NULL ,
  `ino_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idtm_inoutput`) ,
  INDEX `fk_tm_inoutput_ta_inoutput_type` (`idta_inoutput_type` ASC) ,
  CONSTRAINT `fk_tm_inoutput_ta_inoutput_type`
    FOREIGN KEY (`idta_inoutput_type` )
    REFERENCES `db239003879`.`ta_inoutput_type` (`idta_inoutput_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Input and Output';


-- -----------------------------------------------------
-- Table `db239003879`.`ta_activity_activity`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_activity_activity` (
  `idta_activity_activity` INT NOT NULL AUTO_INCREMENT ,
  `idtm_activity` INT NULL ,
  `pre_idtm_activity` INT NULL ,
  `actact_type` INT NULL ,
  `actact_minz` INT NULL ,
  `actact_maxz` INT NULL ,
  PRIMARY KEY (`idta_activity_activity`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_inoutput_has_tm_activity`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_inoutput_has_tm_activity` (
  `idtm_inoutput_has_tm_activity` INT NOT NULL AUTO_INCREMENT ,
  `idtm_activity` INT NULL ,
  `idtm_inoutput` INT NULL ,
  `ino_link_type` INT NULL DEFAULT 0 COMMENT '0=Output,1=Input' ,
  PRIMARY KEY (`idtm_inoutput_has_tm_activity`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_termin_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_termin_type` (
  `idta_termin_type` INT(11) NOT NULL AUTO_INCREMENT ,
  `ter_type_name` VARCHAR(45) NULL ,
  `ter_type_descr` BLOB NULL ,
  PRIMARY KEY (`idta_termin_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_termin`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_termin` (
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
  INDEX `fk_tm_termin_tm_activity` (`idtm_activity` ASC) ,
  INDEX `fk_tm_termin_ta_termin_type` (`idta_termin_type` ASC) ,
  CONSTRAINT `fk_tm_termin_tm_activity`
    FOREIGN KEY (`idtm_activity` )
    REFERENCES `db239003879`.`tm_activity` (`idtm_activity` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_termin_ta_termin_type`
    FOREIGN KEY (`idta_termin_type` )
    REFERENCES `db239003879`.`ta_termin_type` (`idta_termin_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_termin_organisation`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_termin_organisation` (
  `idtm_termin` INT NOT NULL DEFAULT 0 ,
  `idtm_organisation` INT NOT NULL DEFAULT 0 ,
  `idtm_termin_organisation` INT NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`idtm_termin_organisation`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_verteiler`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_verteiler` (
  `idtm_verteiler` INT NOT NULL AUTO_INCREMENT ,
  `ver_name` VARCHAR(45) NULL ,
  `ver_descr` BLOB NULL ,
  `ver_valid` TINYINT(1) NULL ,
  `ver_zyklus` INT NULL ,
  `ver_day` INT NULL DEFAULT 0 ,
  PRIMARY KEY (`idtm_verteiler`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_verteiler_organisation`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_verteiler_organisation` (
  `idtm_verteiler` INT NOT NULL DEFAULT 0 ,
  `idtm_organisation` INT NOT NULL DEFAULT 0 ,
  `idtm_verteiler_organisation` INT NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`idtm_verteiler_organisation`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tt_rcvalue_netto`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tt_rcvalue_netto` (
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
  INDEX `fk_tm_rcvalue_tt_rcvalue_netto` (`idtm_rcvalue` ASC) ,
  CONSTRAINT `fk_tm_rcvalue_tt_rcvalue_netto`
    FOREIGN KEY (`idtm_rcvalue` )
    REFERENCES `db239003879`.`tm_rcvalue` (`idtm_rcvalue` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Die Tatsächlichen Werte netto';


-- -----------------------------------------------------
-- Table `db239003879`.`ta_bericht_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_bericht_type` (
  `ber_type_name` VARCHAR(45) NULL ,
  `ber_type_descr` BLOB NULL ,
  `idta_bericht_type` INT(11) NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`idta_bericht_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_berichte`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_berichte` (
  `idta_berichte` INT(11) NOT NULL AUTO_INCREMENT ,
  `ber_name` VARCHAR(45) NULL COMMENT 'name' ,
  `ber_descr` BLOB NULL COMMENT 'beschreibung' ,
  `ber_cdate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `idtm_user` INT NULL COMMENT 'ersteller' ,
  `idta_bericht_type` INT NULL ,
  `idtm_organisation` INT NULL COMMENT 'Verantwortlich' ,
  `ber_id` VARCHAR(45) NULL COMMENT 'QV Report ID' ,
  `ber_mail_subject` VARCHAR(100) NULL COMMENT 'Mail Betreff' ,
  `ber_mail_body` BLOB NULL COMMENT 'Mail TXT' ,
  `ber_local_path` VARCHAR(100) NULL COMMENT 'Pfad im Dateisystem	' ,
  `ber_zyklus` INT NULL DEFAULT 1 COMMENT '1=Tag 2=Woche 3 Monat' ,
  `ber_zyklus_gap` INT NULL ,
  `ber_zyklus_start` INT NULL DEFAULT 1 ,
  `ber_production_time` DOUBLE NULL DEFAULT 8 COMMENT 'Herstelldauer in Stunden' ,
  PRIMARY KEY (`idta_berichte`) ,
  INDEX `fk_tab_tab_type` (`idta_bericht_type` ASC) ,
  CONSTRAINT `fk_tab_tab_type`
    FOREIGN KEY (`idta_bericht_type` )
    REFERENCES `db239003879`.`ta_bericht_type` (`idta_bericht_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_bericht_struktur_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_bericht_struktur_type` (
  `ber_struktur_type_name` VARCHAR(45) NULL ,
  `ber_struktur_type_descr` BLOB NULL ,
  `idta_bericht_struktur_type` INT(11) NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`idta_bericht_struktur_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_bericht_struktur`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_bericht_struktur` (
  `idta_bericht_struktur` INT(11) NOT NULL AUTO_INCREMENT ,
  `idta_berichte` INT NULL ,
  `ber_struktur_name` VARCHAR(45) NULL ,
  `ber_struktur_descr` BLOB NULL ,
  `parent_idta_bericht_struktur` INT NULL ,
  `idta_bericht_struktur_type` INT NULL ,
  PRIMARY KEY (`idta_bericht_struktur`) ,
  INDEX `fk_ta_berichte_bericht_struktur` (`idta_berichte` ASC) ,
  INDEX `ft_tbs_tbs_type` (`idta_bericht_struktur_type` ASC) ,
  CONSTRAINT `fk_ta_berichte_bericht_struktur`
    FOREIGN KEY (`idta_berichte` )
    REFERENCES `db239003879`.`ta_berichte` (`idta_berichte` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `ft_tbs_tbs_type`
    FOREIGN KEY (`idta_bericht_struktur_type` )
    REFERENCES `db239003879`.`ta_bericht_struktur_type` (`idta_bericht_struktur_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_contentcontainer_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_contentcontainer_type` (
  `cc_type_name` VARCHAR(45) NULL ,
  `cc_type_descr` BLOB NULL ,
  `idta_contentcontainer_type` INT(11) NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`idta_contentcontainer_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_contentcontainer`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_contentcontainer` (
  `idta_contentcontainer` INT NOT NULL AUTO_INCREMENT ,
  `idta_bericht_struktur` INT NULL ,
  `idta_contentcontainer_type` INT NULL ,
  `ccc_name` VARCHAR(45) NULL ,
  `ccc_cdate` DATE NULL ,
  `idtm_organisation` INT NULL ,
  PRIMARY KEY (`idta_contentcontainer`) ,
  INDEX `fk_ta_bericht_struktur` (`idta_bericht_struktur` ASC) ,
  INDEX `fk_tm_organisation_tm_cc` (`idtm_organisation` ASC) ,
  INDEX `fk_cc_cc_type` (`idta_contentcontainer_type` ASC) ,
  CONSTRAINT `fk_ta_bericht_struktur`
    FOREIGN KEY (`idta_bericht_struktur` )
    REFERENCES `db239003879`.`ta_bericht_struktur` (`idta_bericht_struktur` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tm_organisation_tm_cc`
    FOREIGN KEY (`idtm_organisation` )
    REFERENCES `db239003879`.`tm_organisation` (`idtm_organisation` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cc_cc_type`
    FOREIGN KEY (`idta_contentcontainer_type` )
    REFERENCES `db239003879`.`ta_contentcontainer_type` (`idta_contentcontainer_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`xx_berechtigung`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`xx_berechtigung` (
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


-- -----------------------------------------------------
-- Table `db239003879`.`tt_container_text`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tt_container_text` (
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


-- -----------------------------------------------------
-- Table `db239003879`.`qs_comments`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`qs_comments` (
  `idqs_comments` INT NOT NULL AUTO_INCREMENT ,
  `idtm_organisation` INT NULL ,
  `com_cdate` DATE NULL ,
  `com_page` VARCHAR(45) NULL ,
  `com_id` INT NULL ,
  `com_content` BLOB NULL ,
  `com_modul` VARCHAR(100) NULL ,
  `idta_variante` INT NULL ,
  `idta_periode` INT NULL ,
  PRIMARY KEY (`idqs_comments`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_changerequest`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_changerequest` (
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


-- -----------------------------------------------------
-- Table `db239003879`.`tt_message`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tt_message` (
  `idtt_message` INT NOT NULL AUTO_INCREMENT ,
  `mes_date` TIMESTAMP NULL ,
  `to_idtm_organisation` INT NULL ,
  `from_idtm_organisation` INT NULL ,
  `mes_content` BLOB NULL ,
  PRIMARY KEY (`idtt_message`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`catalogue`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`catalogue` (
  `cat_id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(100) NULL ,
  `source_lang` VARCHAR(100) NULL ,
  `target_lang` VARCHAR(100) NULL ,
  `date_created` INT(11) NULL ,
  `date_modified` INT(11) NULL ,
  `author` VARCHAR(255) NULL ,
  PRIMARY KEY (`cat_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`trans_unit`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`trans_unit` (
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


-- -----------------------------------------------------
-- Table `db239003879`.`ta_kosten_status`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_kosten_status` (
  `idta_kosten_status` INT(11) NOT NULL AUTO_INCREMENT ,
  `kst_status_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_kosten_status`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_zeiterfassung`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_zeiterfassung` (
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
  INDEX `fk_tm_zeiterfassung_ta_kosten_status` (`idta_kosten_status` ASC) ,
  CONSTRAINT `fk_tm_zeiterfassung_ta_kosten_status`
    FOREIGN KEY (`idta_kosten_status` )
    REFERENCES `db239003879`.`ta_kosten_status` (`idta_kosten_status` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_termin_ressource`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_termin_ressource` (
  `idtm_termin_ressource` INT NOT NULL AUTO_INCREMENT ,
  `idtm_termin` INT NULL ,
  `idtm_ressource` INT NULL ,
  PRIMARY KEY (`idtm_termin_ressource`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_stammdaten_group`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_stammdaten_group` (
  `idta_stammdaten_group` INT(11) NULL DEFAULT NULL AUTO_INCREMENT ,
  `stammdaten_group_name` VARCHAR(45) NULL ,
  `idta_struktur_type` INT NULL ,
  `parent_idta_stammdaten_group` INT NULL DEFAULT 0 ,
  `stammdaten_group_original` TINYINT(1) NULL DEFAULT 0 ,
  `stammdaten_group_create` TINYINT(1) NULL DEFAULT 1 ,
  PRIMARY KEY (`idta_stammdaten_group`) ,
  INDEX `fk_ta_stammdaten_group_ta_struktur_type` (`idta_struktur_type` ASC) ,
  CONSTRAINT `fk_ta_stammdaten_group_ta_struktur_type`
    FOREIGN KEY (`idta_struktur_type` )
    REFERENCES `db239003879`.`ta_struktur_type` (`idta_struktur_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_stammdaten`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_stammdaten` (
  `idtm_stammdaten` INT(11) NULL DEFAULT NULL AUTO_INCREMENT ,
  `stammdaten_name` VARCHAR(45) NULL ,
  `stammdaten_key_extern` VARCHAR(45) NULL ,
  `idta_stammdaten_group` INT NULL ,
  PRIMARY KEY (`idtm_stammdaten`) ,
  INDEX `fk_tm_stammdaten_ta_stammdaten_group` (`idta_stammdaten_group` ASC) ,
  CONSTRAINT `fk_tm_stammdaten_ta_stammdaten_group`
    FOREIGN KEY (`idta_stammdaten_group` )
    REFERENCES `db239003879`.`ta_stammdaten_group` (`idta_stammdaten_group` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tt_stammdaten`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tt_stammdaten` (
  `idtt_stammdaten` INT NULL AUTO_INCREMENT ,
  `idta_feldfunktion` INT NULL ,
  `idta_variante` INT NULL ,
  `tt_stammdaten_value` DOUBLE NULL ,
  `idtm_stammdaten` INT NULL ,
  `idta_periode` INT NULL ,
  PRIMARY KEY (`idtt_stammdaten`) ,
  INDEX `fk_tm_stammdaten_tt_stammdaten` (`idtm_stammdaten` ASC) ,
  INDEX `fk_ta_variante_tt_stammdaten` (`idta_variante` ASC) ,
  INDEX `fk_ta_feldfunktion_tt_stammdaten` (`idta_feldfunktion` ASC) ,
  INDEX `fk_ta_periode_tt_stammdaten` (`idta_periode` ASC) ,
  CONSTRAINT `fk_tm_stammdaten_tt_stammdaten`
    FOREIGN KEY (`idtm_stammdaten` )
    REFERENCES `db239003879`.`tm_stammdaten` (`idtm_stammdaten` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ta_variante_tt_stammdaten`
    FOREIGN KEY (`idta_variante` )
    REFERENCES `db239003879`.`ta_variante` (`idta_variante` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ta_feldfunktion_tt_stammdaten`
    FOREIGN KEY (`idta_feldfunktion` )
    REFERENCES `db239003879`.`ta_feldfunktion` (`idta_feldfunktion` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ta_periode_tt_stammdaten`
    FOREIGN KEY (`idta_periode` )
    REFERENCES `db239003879`.`ta_perioden` (`idta_perioden` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_struktur_has_ta_stammdaten_group`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_struktur_has_ta_stammdaten_group` (
  `idtm_struktur_has_ta_stammdaten_group` INT NULL AUTO_INCREMENT ,
  `idtm_struktur` INT NULL DEFAULT 1 ,
  `idta_stammdaten_group` INT NULL DEFAULT 1 ,
  PRIMARY KEY (`idtm_struktur_has_ta_stammdaten_group`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_aufgabe_ressource`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_aufgabe_ressource` (
  `idtm_aufgabe_ressource` INT NULL AUTO_INCREMENT ,
  `idtm_aufgabe` INT NULL ,
  `idtm_ressource` INT NULL ,
  `auf_res_dauer` DOUBLE NULL ,
  PRIMARY KEY (`idtm_aufgabe_ressource`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_activity_has_tm_organisation`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_activity_has_tm_organisation` (
  `idtm_activity_has_tm_organisation` INT NULL AUTO_INCREMENT ,
  `idtm_activity` INT NULL ,
  `idtm_organisation` INT NULL ,
  PRIMARY KEY (`idtm_activity_has_tm_organisation`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_pivot_bericht`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_pivot_bericht` (
  `idta_pivot_bericht` INT NULL AUTO_INCREMENT ,
  `idtm_user` INT NULL ,
  `pivot_bericht_cdate` DATE NULL ,
  `pivot_bericht_name` VARCHAR(45) NULL ,
  `idta_feldfunktion` INT NULL ,
  `pivot_bericht_operator` VARCHAR(45) NULL DEFAULT 'SUM' ,
  `idta_variante` INT NULL DEFAULT 1 ,
  PRIMARY KEY (`idta_pivot_bericht`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_pivot`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_pivot` (
  `idtm_pivot` INT NULL AUTO_INCREMENT ,
  `idta_pivot_bericht` INT NULL ,
  `idta_stammdaten_group` INT NULL ,
  `parent_idtm_pivot` INT NULL ,
  `pivot_position` INT NULL DEFAULT 1 COMMENT '1 = Spalte, 0 = Zeile' ,
  `pivot_filter` INT NULL ,
  PRIMARY KEY (`idtm_pivot`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_struktur_tm_struktur`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_struktur_tm_struktur` (
  `idtm_struktur_from` INT NULL ,
  `idtm_struktur_to` INT NULL ,
  `idta_feldfunktion` INT NULL ,
  `idtm_struktur_tm_struktur` INT NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`idtm_struktur_tm_struktur`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_struktur_bericht`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_struktur_bericht` (
  `idta_struktur_bericht` INT NULL AUTO_INCREMENT ,
  `idtm_user` INT NULL ,
  `pivot_struktur_cdate` DATE NULL ,
  `pivot_struktur_name` VARCHAR(45) NULL ,
  `sb_order` INT NULL ,
  `sb_startbericht` TINYINT(1) NULL ,
  PRIMARY KEY (`idta_struktur_bericht`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_struktur_bericht_zeilen`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_struktur_bericht_zeilen` (
  `idta_struktur_bericht_zeilen` INT NULL AUTO_INCREMENT ,
  `idta_feldfunktion` INT NULL ,
  `sbz_spacer_label` VARCHAR(45) NULL ,
  `sbz_type` INT NULL ,
  `sbz_detail` TINYINT(1) NULL ,
  `sbz_label` VARCHAR(45) NULL ,
  `idta_struktur_bericht` INT NULL ,
  `sbz_order` INT NULL DEFAULT 0 ,
  `sbz_input` TINYINT(1) NULL DEFAULT 0 ,
  `idtm_stammdaten` INT NULL DEFAULT 0 ,
  PRIMARY KEY (`idta_struktur_bericht_zeilen`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_struktur_bericht_spalten`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_struktur_bericht_spalten` (
  `idta_struktur_bericht_spalten` INT NULL AUTO_INCREMENT ,
  `idta_perioden_gap` INT NULL ,
  `sbs_perioden_fix` TINYINT(1) NULL ,
  `sbs_cumulated` TINYINT(1) NULL ,
  `idta_variante` INT NULL ,
  `idta_struktur_bericht` INT NULL ,
  `sbs_order` INT NULL ,
  `sbs_input` TINYINT(1) NULL DEFAULT 0 ,
  `sbs_idta_variante_fix` TINYINT(1) NULL DEFAULT 0 ,
  `sbs_idtm_struktur` INT NULL COMMENT 'der Startknoten  beim ersten Aufruf' ,
  `sbs_struktur_switch_type` INT NULL COMMENT '0=keiner 1=fix 2=variabel' ,
  `sbs_bericht_operator` VARCHAR(45) NULL DEFAULT 'SUM' COMMENT 'Operator' ,
  PRIMARY KEY (`idta_struktur_bericht_spalten`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_sbz_collector`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_sbz_collector` (
  `idta_sbz_collector` INT NULL AUTO_INCREMENT ,
  `idta_struktur_bericht_zeilen` INT NULL COMMENT 'Zielspalte' ,
  `row_idta_struktur_bericht_zeilen` INT NULL ,
  `sbz_collector_operator` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_sbz_collector`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_sbs_collector`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_sbs_collector` (
  `idta_sbs_collector` INT NULL AUTO_INCREMENT ,
  `idta_struktur_bericht_spalten` INT NULL COMMENT 'Zielspalte' ,
  `row_idta_struktur_bericht_spalten` INT NULL ,
  `sbs_collector_operator` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_sbs_collector`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tm_berichte_has_organisation`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_berichte_has_organisation` (
  `idtm_berichte_has_organisation` INT NOT NULL AUTO_INCREMENT ,
  `idta_berichte` INT NULL ,
  `idtm_organisation` INT NULL ,
  `bho_modul` VARCHAR(100) NULL DEFAULT 'idtm_strultur' COMMENT 'the name of the keyfield' ,
  `bho_id` VARCHAR(45) NULL DEFAULT '1' COMMENT 'the value of the keyfield' ,
  PRIMARY KEY (`idtm_berichte_has_organisation`) )
ENGINE = InnoDB
COMMENT = 'Mapping Berichte Organisation';


-- -----------------------------------------------------
-- Table `db239003879`.`tt_workflow`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tt_workflow` (
  `idtt_workflow` INT NOT NULL AUTO_INCREMENT ,
  `wfl_modul` VARCHAR(100) NULL ,
  `wfl_id` INT NULL ,
  `idtm_user` INT NULL ,
  `idta_periode` INT NULL ,
  `idta_variante` INT NULL ,
  `wfl_cdate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `wfl_status` INT NULL COMMENT '1offen 2bearbeitung 3pruefung 4genehmigt 5geschlossen' ,
  PRIMARY KEY (`idtt_workflow`) )
ENGINE = InnoDB
COMMENT = 'The information for workflow';


-- -----------------------------------------------------
-- Table `db239003879`.`tt_saisonalisierung`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tt_saisonalisierung` (
  `idtt_saisonalisierung` INT NULL AUTO_INCREMENT ,
  `idta_saisonalisierung` INT NULL ,
  `idta_periode` INT NULL ,
  `sai_wert` FLOAT NULL DEFAULT 0 COMMENT 'Wert aus der Gewichtung' ,
  PRIMARY KEY (`idtt_saisonalisierung`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`ta_saisonalisierung`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_saisonalisierung` (
  `idta_saisonalisierung` INT NULL AUTO_INCREMENT ,
  `sai_name` VARCHAR(45) NULL ,
  `idtm_struktur` INT NULL COMMENT 'The startnode\n' ,
  `idta_feldfunktion` INT NULL COMMENT 'related fields' ,
  PRIMARY KEY (`idta_saisonalisierung`) )
ENGINE = InnoDB
COMMENT = 'stammdaten zur saison';


-- -----------------------------------------------------
-- Table `db239003879`.`tm_fahrtenbuch`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_fahrtenbuch` (
  `idtm_fahrtenbuch` INT NOT NULL AUTO_INCREMENT ,
  `idtm_zeiterfassung` INT NULL ,
  `fahrt_von` VARCHAR(100) NULL ,
  `fahrt_nach` VARCHAR(100) NULL ,
  `fahrt_status` INT NULL DEFAULT 1 COMMENT '1abbrechenbar2ausweisbar3privat' ,
  `fahrt_km` FLOAT NULL DEFAULT 0 COMMENT 'gefahrene km' ,
  PRIMARY KEY (`idtm_fahrtenbuch`) )
ENGINE = InnoDB
COMMENT = 'hier lege ich die fahrten ab';


-- -----------------------------------------------------
-- Table `db239003879`.`tm_tempimport`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_tempimport` (
  `idtm_tempimport` INT NULL AUTO_INCREMENT ,
  `ti_name` VARCHAR(45) NULL ,
  `user_name` VARCHAR(45) NULL ,
  `ti_id1` VARCHAR(45) NULL ,
  `ti_id2` VARCHAR(45) NULL ,
  `ti_id3` VARCHAR(45) NULL ,
  `ti_id4` VARCHAR(45) NULL ,
  `ti_id5` VARCHAR(45) NULL ,
  `ti_id6` VARCHAR(45) NULL ,
  `ti_id7` VARCHAR(45) NULL ,
  `ti_id8` VARCHAR(45) NULL ,
  `ti_id9` VARCHAR(45) NULL ,
  `ti_id10` VARCHAR(45) NULL ,
  `ti_label1` VARCHAR(90) NULL ,
  `ti_label2` VARCHAR(90) NULL ,
  `ti_label3` VARCHAR(90) NULL ,
  `ti_label4` VARCHAR(90) NULL ,
  `ti_label5` VARCHAR(90) NULL ,
  `ti_label6` VARCHAR(90) NULL ,
  `ti_label7` VARCHAR(90) NULL ,
  `ti_label8` VARCHAR(90) NULL ,
  `ti_label9` VARCHAR(90) NULL ,
  `ti_label10` VARCHAR(90) NULL ,
  `ti_value1` FLOAT NULL ,
  `ti_value2` FLOAT NULL ,
  `ti_value3` FLOAT NULL ,
  `ti_value4` INT NULL ,
  `per_intern` BIGINT NULL ,
  PRIMARY KEY (`idtm_tempimport`) )
ENGINE = InnoDB
COMMENT = 'The table in which the values are stored between';


-- -----------------------------------------------------
-- Table `db239003879`.`tm_importmapping`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_importmapping` (
  `idtm_importmapping` INT NULL AUTO_INCREMENT ,
  `ima_name` VARCHAR(45) NULL ,
  `ima_id1` VARCHAR(45) NULL ,
  `ima_id2` VARCHAR(45) NULL ,
  `ima_id3` VARCHAR(45) NULL ,
  `ima_id4` VARCHAR(45) NULL ,
  `ima_id5` VARCHAR(45) NULL ,
  `ima_id6` VARCHAR(45) NULL ,
  `ima_id7` VARCHAR(45) NULL ,
  `ima_id8` VARCHAR(45) NULL ,
  `ima_id9` VARCHAR(45) NULL ,
  `ima_id10` VARCHAR(45) NULL ,
  `idtm_struktur` INT NULL ,
  `idta_feldfunktion` INT NULL ,
  `ima_faktor` FLOAT NULL DEFAULT 1 ,
  `ima_lauf` INT NULL DEFAULT 1 ,
  `ima_source` VARCHAR(1) NULL ,
  `ima_path` VARCHAR(255) NULL ,
  PRIMARY KEY (`idtm_importmapping`) )
ENGINE = InnoDB
COMMENT = 'the final mapping information';


-- -----------------------------------------------------
-- Table `db239003879`.`ta_automapping`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_automapping` (
  `idta_automapping` INT NULL AUTO_INCREMENT ,
  `ima_name` VARCHAR(45) NULL ,
  `idtm_stammdaten` INT NULL ,
  `idta_feldfunktion` INT NULL ,
  `ama_faktor` FLOAT NULL ,
  `ama_lauf` INT NULL DEFAULT 1 ,
  `ama_source` VARCHAR(1) NULL ,
  `ama_id` INT NULL DEFAULT 1 COMMENT 'the id level' ,
  `ti_id` VARCHAR(45) NULL ,
  `ti_label` VARCHAR(90) NULL ,
  PRIMARY KEY (`idta_automapping`) )
ENGINE = InnoDB
COMMENT = 'here the rules for the mapping are stored';


-- -----------------------------------------------------
-- Table `db239003879`.`tt_user_felder`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tt_user_felder` (
  `idtt_user_felder` INT NULL AUTO_INCREMENT ,
  `user_id` INT NULL ,
  `tuf_feldname` VARCHAR(55) NULL ,
  PRIMARY KEY (`idtt_user_felder`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tt_stammdaten_stammdaten`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tt_stammdaten_stammdaten` (
  `idtt_stammdaten_stammdaten` INT NOT NULL AUTO_INCREMENT ,
  `idtm_stammdaten_group` INT NULL ,
  `idtm_stammdaten` INT NULL ,
  PRIMARY KEY (`idtt_stammdaten_stammdaten`) )
ENGINE = InnoDB
COMMENT = 'Gruppierung der Dimensionen';


-- -----------------------------------------------------
-- Table `db239003879`.`ta_fortschreibung`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_fortschreibung` (
  `idta_fortschreibung` INT NOT NULL AUTO_INCREMENT ,
  `for_name` VARCHAR(45) NULL ,
  `idtm_struktur` INT NULL ,
  `from_idta_periode` INT NULL ,
  `to_idta_periode` INT NULL ,
  `idta_variante` INT NULL ,
  `for_faktor` FLOAT NULL DEFAULT 0 ,
  `idta_feldfunktion` INT NULL ,
  `idta_fortschreibungs_type` INT NULL DEFAULT 1 ,
  PRIMARY KEY (`idta_fortschreibung`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db239003879`.`tt_user_log`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tt_user_log` (
  `idtt_user_log` INT NULL AUTO_INCREMENT ,
  `idtm_user` INT NULL ,
  `ul_status` VARCHAR(45) NULL ,
  `ul_ipadress` VARCHAR(45) NULL ,
  `ul_time` TIMESTAMP NULL ,
  PRIMARY KEY (`idtt_user_log`) )
ENGINE = InnoDB
COMMENT = 'User Logging';


-- -----------------------------------------------------
-- Table `db239003879`.`tm_organisation_has_ta_adresse`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`tm_organisation_has_ta_adresse` (
  `idtm_organisation_has_ta_adresse` INT NOT NULL AUTO_INCREMENT ,
  `idtm_organisation` INT NULL ,
  `idta_adresse` INT NULL ,
  PRIMARY KEY (`idtm_organisation_has_ta_adresse`) )
ENGINE = InnoDB
COMMENT = 'The link between the organisation and the adress';


-- -----------------------------------------------------
-- Table `db239003879`.`ta_bankkonto`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_bankkonto` (
  `idta_bankkonto` INT NOT NULL AUTO_INCREMENT ,
  `bak_kontowortlaut` VARCHAR(45) NULL ,
  `bak_geldinstitut` VARCHAR(45) NULL ,
  `bak_blz` VARCHAR(10) NULL ,
  `bak_konto` VARCHAR(12) NULL ,
  `bak_bic` VARCHAR(20) NULL ,
  `bak_iban` VARCHAR(30) NULL ,
  `idtm_organisation` INT NULL ,
  `bak_ismain` TINYINT(1) NULL ,
  PRIMARY KEY (`idta_bankkonto`) )
ENGINE = InnoDB
COMMENT = 'Alle Informationen zum Bankkonto';


-- -----------------------------------------------------
-- Table `db239003879`.`ta_objekt`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_objekt` (
  `idta_objekt` INT NOT NULL AUTO_INCREMENT ,
  `idtm_organisation` INT NULL ,
  `obj_nutzflaeche` FLOAT NULL ,
  `obj_nutzflaeche_date` DATE NULL ,
  `obj_gbanteile` INT NULL ,
  `obj_gbanteile_date` DATE NULL ,
  `obj_nutzflaeche_type` INT NULL COMMENT '1 Wohnzwecke 2 Betrieblich' ,
  PRIMARY KEY (`idta_objekt`) )
ENGINE = InnoDB
COMMENT = 'Alles mit dem Objekt';


-- -----------------------------------------------------
-- Table `db239003879`.`ta_kommunikation`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_kommunikation` (
  `idta_kommunikation` INT NOT NULL AUTO_INCREMENT ,
  `kom_type` INT NULL DEFAULT 1 COMMENT '1 Telefon 2 Fax 3 Mail' ,
  `kom_information` VARCHAR(45) NULL ,
  `kom_ismain` TINYINT(1) NULL ,
  `idtm_organisation` INT NULL COMMENT 'related to organisation' ,
  `kom_ismainmain` TINYINT(1) NULL COMMENT 'the real main main sender' ,
  PRIMARY KEY (`idta_kommunikation`) )
ENGINE = InnoDB
COMMENT = 'Alle Infos zur Kontaktierung';


-- -----------------------------------------------------
-- Table `db239003879`.`ta_aufgaben_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_aufgaben_type` (
  `idta_aufgaben_type` INT NOT NULL AUTO_INCREMENT ,
  `auf_type_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idta_aufgaben_type`) )
ENGINE = InnoDB
COMMENT = 'aufgaben typ';


-- -----------------------------------------------------
-- Table `db239003879`.`ta_kaution`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `db239003879`.`ta_kaution` (
  `idta_kaution` INT NOT NULL AUTO_INCREMENT ,
  `idtm_organisation` INT NULL ,
  `kau_jahr` INT(4) NULL ,
  `kau_betrag` FLOAT NULL ,
  `kau_hinterlegung` VARCHAR(45) NULL ,
  `kau_infotext` BLOB NULL ,
  PRIMARY KEY (`idta_kaution`) )
ENGINE = InnoDB
COMMENT = 'alles was mit der kaution zu tun hat';


-- -----------------------------------------------------
-- Placeholder table for view `db239003879`.`vv_activity_participants`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db239003879`.`vv_activity_participants` (`idtm_activity` INT, `idtm_organisation` INT, `idtm_activity_participant` INT, `act_part_anwesend` INT, `act_part_notiz` INT, `org_name` INT, `user_role_name` INT);

-- -----------------------------------------------------
-- Placeholder table for view `db239003879`.`vv_activity_activity`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db239003879`.`vv_activity_activity` (`idta_activity_activity` INT, `idtm_activity` INT, `pre_idtm_activity` INT, `actact_type` INT, `actact_minz` INT, `actact_maxz` INT, `act_name` INT, `pre_act_name` INT);

-- -----------------------------------------------------
-- Placeholder table for view `db239003879`.`vv_activity_ziele`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db239003879`.`vv_activity_ziele` (`idtt_ziele` INT, `idtm_activity` INT, `idtm_activity_has_tt_ziele` INT, `ttzie_name` INT);

-- -----------------------------------------------------
-- Placeholder table for view `db239003879`.`vv_activity_inoutput`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db239003879`.`vv_activity_inoutput` (`idtm_inoutput_has_tm_activity` INT, `idtm_activity` INT, `idtm_inoutput` INT, `ino_link_type` INT, `act_name` INT, `ino_name` INT);

-- -----------------------------------------------------
-- Placeholder table for view `db239003879`.`vv_termin_organisation`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db239003879`.`vv_termin_organisation` (`idtm_termin` INT, `idtm_organisation` INT, `idtm_termin_organisation` INT, `org_name` INT, `user_role_name` INT);

-- -----------------------------------------------------
-- Placeholder table for view `db239003879`.`vv_protokoll_detail_aufgabe`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db239003879`.`vv_protokoll_detail_aufgabe` (`idtm_protokoll_detail` INT, `idtt_ziele` INT, `idta_protokoll_detail_group` INT, `prtdet_topic` INT, `prtdet_descr` INT, `prtdet_cdate` INT, `idtm_user` INT, `idta_protokoll_ergebnistype` INT, `prtdet_wvl` INT, `prtdet_wvl_type` INT, `idtm_aufgaben` INT, `auf_tabelle` INT, `auf_id` INT, `idtm_organisation` INT, `auf_cdate` INT, `auf_beschreibung` INT, `auf_tdate` INT, `auf_priority` INT, `auf_name` INT, `auf_done` INT, `auf_dauer` INT, `auf_ddate` INT, `auf_idtm_organisation` INT, `idta_aufgaben_type` INT, `auf_tag` INT, `auf_zeichen_eigen` INT, `auf_zeichen_fremd` INT);

-- -----------------------------------------------------
-- Placeholder table for view `db239003879`.`vv_verteiler_organisation`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db239003879`.`vv_verteiler_organisation` (`idtm_verteiler` INT, `idtm_organisation` INT, `idtm_verteiler_organisation` INT, `org_name` INT, `user_role_name` INT);

-- -----------------------------------------------------
-- Placeholder table for view `db239003879`.`vv_termin_ressource`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db239003879`.`vv_termin_ressource` (`idtm_termin_ressource` INT, `idtm_termin` INT, `idtm_ressource` INT, `res_name` INT);

-- -----------------------------------------------------
-- Placeholder table for view `db239003879`.`vv_struktur_stammdaten_group`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db239003879`.`vv_struktur_stammdaten_group` (`idtm_struktur_has_ta_stammdaten_group` INT, `idtm_struktur` INT, `idta_stammdaten_group` INT, `stammdaten_group_name` INT);

-- -----------------------------------------------------
-- Placeholder table for view `db239003879`.`vv_aufgabe_ressource`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db239003879`.`vv_aufgabe_ressource` (`idtm_aufgabe_ressource` INT, `idtm_aufgabe` INT, `idtm_ressource` INT, `auf_res_dauer` INT, `res_name` INT);

-- -----------------------------------------------------
-- Placeholder table for view `db239003879`.`vv_activity_organisation`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db239003879`.`vv_activity_organisation` (`idtm_activity_has_tm_organisation` INT, `idtm_activity` INT, `idtm_organisation` INT, `org_name` INT);

-- -----------------------------------------------------
-- Placeholder table for view `db239003879`.`vv_pivot_stammdaten_group`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db239003879`.`vv_pivot_stammdaten_group` (`idtm_pivot` INT, `idta_pivot_bericht` INT, `idta_stammdaten_group` INT, `parent_idtm_pivot` INT, `pivot_position` INT, `pivot_filter` INT, `stammdaten_group_name` INT);

-- -----------------------------------------------------
-- Placeholder table for view `db239003879`.`vv_collector_feldfunktion`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db239003879`.`vv_collector_feldfunktion` (`idta_collector` INT, `idta_feldfunktion` INT, `col_idtafeldfunktion` INT, `col_operator` INT, `ff_name` INT);

-- -----------------------------------------------------
-- Placeholder table for view `db239003879`.`vv_mapping_stammdaten`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db239003879`.`vv_mapping_stammdaten` (`stammdaten_group_name` INT, `group_stammdaten_name` INT, `detail_stammdaten_name` INT);

-- -----------------------------------------------------
-- Placeholder table for view `db239003879`.`vv_struktur`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db239003879`.`vv_struktur` (`idtm_struktur` INT, `parent_idtm_struktur` INT, `struktur_name` INT, `idta_struktur_type` INT);

-- -----------------------------------------------------
-- Placeholder table for view `db239003879`.`vv_termin_ressource_organisation`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db239003879`.`vv_termin_ressource_organisation` (`idtm_termin` INT, `ter_startdate` INT, `ter_enddate` INT, `ter_starttime` INT, `ter_endtime` INT, `idtm_organisation` INT, `idtm_ressource` INT);

-- -----------------------------------------------------
-- View `db239003879`.`vv_activity_participants`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db239003879`.`vv_activity_participants`;
USE db239003879;
CREATE  OR REPLACE VIEW `db239003879`.`vv_activity_participants` AS
SELECT tm_activity_participants.*,tm_organisation.org_name,tm_user_role.user_role_name FROM tm_activity_participants INNER JOIN tm_organisation ON tm_organisation.idtm_organisation = tm_activity_participants.idtm_organisation
INNER JOIN tm_user_role ON tm_user_role.idtm_user_role = tm_organisation.org_idtm_user_role;

-- -----------------------------------------------------
-- View `db239003879`.`vv_activity_activity`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db239003879`.`vv_activity_activity`;
USE db239003879;
CREATE  OR REPLACE VIEW `db239003879`.`vv_activity_activity` AS
SELECT a.*, b.act_name, c.act_name AS pre_act_name FROM ta_activity_activity a INNER JOIN tm_activity b ON a.idtm_activity = b.idtm_activity
INNER JOIN tm_activity c ON c.idtm_activity = a.pre_idtm_activity;

-- -----------------------------------------------------
-- View `db239003879`.`vv_activity_ziele`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db239003879`.`vv_activity_ziele`;
USE db239003879;
CREATE  OR REPLACE VIEW `db239003879`.`vv_activity_ziele` AS
SELECT a.*, b.ttzie_name FROM tm_activity_has_tt_ziele a INNER JOIN tt_ziele b ON a.idtt_ziele = b.idtt_ziele;

-- -----------------------------------------------------
-- View `db239003879`.`vv_activity_inoutput`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db239003879`.`vv_activity_inoutput`;
USE db239003879;
CREATE  OR REPLACE VIEW `db239003879`.`vv_activity_inoutput` AS
SELECT a.*,b.act_name,c.ino_name FROM tm_inoutput_has_tm_activity a INNER JOIN tm_activity b ON a.idtm_activity = b.idtm_activity
INNER JOIN tm_inoutput c ON a.idtm_inoutput = c.idtm_inoutput;

-- -----------------------------------------------------
-- View `db239003879`.`vv_termin_organisation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db239003879`.`vv_termin_organisation`;
USE db239003879;
CREATE  OR REPLACE VIEW `db239003879`.`vv_termin_organisation` AS
SELECT tm_termin_organisation.*,tm_organisation.org_name,tm_user_role.user_role_name FROM tm_termin_organisation INNER JOIN tm_organisation ON tm_organisation.idtm_organisation = tm_termin_organisation.idtm_organisation
INNER JOIN tm_user_role ON tm_user_role.idtm_user_role = tm_organisation.org_idtm_user_role;

-- -----------------------------------------------------
-- View `db239003879`.`vv_protokoll_detail_aufgabe`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db239003879`.`vv_protokoll_detail_aufgabe`;
USE db239003879;
CREATE  OR REPLACE VIEW `db239003879`.`vv_protokoll_detail_aufgabe` AS
SELECT a.*, b.* FROM tm_protokoll_detail a INNER JOIN tm_aufgaben b ON b.auf_tabelle ='tm_protokoll_detail' AND a.idtm_protokoll_detail = b.auf_id;

-- -----------------------------------------------------
-- View `db239003879`.`vv_verteiler_organisation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db239003879`.`vv_verteiler_organisation`;
USE db239003879;
CREATE  OR REPLACE VIEW `db239003879`.`vv_verteiler_organisation` AS
SELECT tm_verteiler_organisation.*,tm_organisation.org_name,tm_user_role.user_role_name FROM tm_verteiler_organisation INNER JOIN tm_organisation ON tm_organisation.idtm_organisation = tm_verteiler_organisation.idtm_organisation
INNER JOIN tm_user_role ON tm_user_role.idtm_user_role = tm_organisation.org_idtm_user_role;

-- -----------------------------------------------------
-- View `db239003879`.`vv_termin_ressource`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db239003879`.`vv_termin_ressource`;
USE db239003879;
CREATE  OR REPLACE VIEW `db239003879`.`vv_termin_ressource` AS
SELECT tm_termin_ressource.*,tm_ressource.res_name FROM tm_termin_ressource INNER JOIN tm_ressource ON tm_ressource.idtm_ressource = tm_termin_ressource.idtm_ressource;

-- -----------------------------------------------------
-- View `db239003879`.`vv_struktur_stammdaten_group`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db239003879`.`vv_struktur_stammdaten_group`;
USE db239003879;
CREATE  OR REPLACE VIEW `db239003879`.`vv_struktur_stammdaten_group` AS
SELECT a.*, b.stammdaten_group_name FROM `tm_struktur_has_ta_stammdaten_group` a INNER JOIN ta_stammdaten_group b ON a.idta_stammdaten_group = b.idta_stammdaten_group;

-- -----------------------------------------------------
-- View `db239003879`.`vv_aufgabe_ressource`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db239003879`.`vv_aufgabe_ressource`;
USE db239003879;
CREATE  OR REPLACE VIEW `db239003879`.`vv_aufgabe_ressource` AS
SELECT tm_aufgabe_ressource.*,tm_ressource.res_name FROM tm_aufgabe_ressource INNER JOIN tm_ressource ON tm_ressource.idtm_ressource = tm_aufgabe_ressource.idtm_ressource;

-- -----------------------------------------------------
-- View `db239003879`.`vv_activity_organisation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db239003879`.`vv_activity_organisation`;
USE db239003879;
CREATE  OR REPLACE VIEW `db239003879`.`vv_activity_organisation` AS
SELECT a.*, b.org_name FROM tm_activity_has_tm_organisation a INNER JOIN tm_organisation b ON a.idtm_organisation = b.idtm_organisation;

-- -----------------------------------------------------
-- View `db239003879`.`vv_pivot_stammdaten_group`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db239003879`.`vv_pivot_stammdaten_group`;
USE db239003879;
CREATE  OR REPLACE VIEW `db239003879`.`vv_pivot_stammdaten_group` AS
SELECT a.*, b.stammdaten_group_name FROM tm_pivot a INNER JOIN ta_stammdaten_group b ON a.idta_stammdaten_group = b.idta_stammdaten_group;

-- -----------------------------------------------------
-- View `db239003879`.`vv_collector_feldfunktion`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db239003879`.`vv_collector_feldfunktion`;
USE db239003879;
CREATE  OR REPLACE VIEW `db239003879`.`vv_collector_feldfunktion` AS
SELECT a.*, b.ff_name FROM ta_collector a INNER JOIN ta_feldfunktion b ON a.col_idtafeldfunktion = b.idta_feldfunktion;

-- -----------------------------------------------------
-- View `db239003879`.`vv_mapping_stammdaten`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db239003879`.`vv_mapping_stammdaten`;
USE db239003879;
CREATE  OR REPLACE VIEW `db239003879`.`vv_mapping_stammdaten` AS
select stammdaten_group_name AS stammdaten_group_name ,b.stammdaten_name AS group_stammdaten_name,a.stammdaten_name AS detail_stammdaten_name from tt_stammdaten_stammdaten inner join tm_stammdaten a on a.idtm_stammdaten=tt_stammdaten_stammdaten.idtm_stammdaten
inner join tm_stammdaten b on b.idtm_stammdaten=tt_stammdaten_stammdaten.idtm_stammdaten_group inner join ta_stammdaten_group on ta_stammdaten_group.idta_stammdaten_group = b.idta_stammdaten_group;

-- -----------------------------------------------------
-- View `db239003879`.`vv_struktur`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db239003879`.`vv_struktur`;
USE db239003879;
CREATE  OR REPLACE VIEW `db239003879`.`vv_struktur` AS
SELECT idtm_struktur,parent_idtm_struktur,struktur_name,idta_struktur_type FROM tm_struktur ORDER BY idta_struktur_type, struktur_name;;

-- -----------------------------------------------------
-- View `db239003879`.`vv_termin_ressource_organisation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db239003879`.`vv_termin_ressource_organisation`;
USE db239003879;
CREATE  OR REPLACE VIEW `db239003879`.`vv_termin_ressource_organisation` AS
SELECT tm_termin.idtm_termin, ter_startdate, ter_enddate, ter_starttime, ter_endtime, idtm_organisation, idtm_ressource 
FROM `tm_termin` 
INNER JOIN tm_termin_organisation ON tm_termin.idtm_termin = tm_termin_organisation.idtm_termin 
INNER JOIN tm_termin_ressource ON tm_termin.idtm_termin = tm_termin_ressource.idtm_termin
;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `db239003879`.`tm_user_role`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`tm_user_role` (`idtm_user_role`, `user_role_name`, `user_role_rechte`) values (1, 'Administrator', 4);
insert into `db239003879`.`tm_user_role` (`idtm_user_role`, `user_role_name`, `user_role_rechte`) values (2, 'CEO', 3);
insert into `db239003879`.`tm_user_role` (`idtm_user_role`, `user_role_name`, `user_role_rechte`) values (3, 'Abteilungsleiter', 2);
insert into `db239003879`.`tm_user_role` (`idtm_user_role`, `user_role_name`, `user_role_rechte`) values (4, 'Mitarbeiter', 1);
insert into `db239003879`.`tm_user_role` (`idtm_user_role`, `user_role_name`, `user_role_rechte`) values (5, 'Projektleiter', 3);
insert into `db239003879`.`tm_user_role` (`idtm_user_role`, `user_role_name`, `user_role_rechte`) values (6, 'Projektmitarbeiter', 2);
insert into `db239003879`.`tm_user_role` (`idtm_user_role`, `user_role_name`, `user_role_rechte`) values (7, 'CFO', 3);
insert into `db239003879`.`tm_user_role` (`idtm_user_role`, `user_role_name`, `user_role_rechte`) values (8, 'Benutzer', 1);
insert into `db239003879`.`tm_user_role` (`idtm_user_role`, `user_role_name`, `user_role_rechte`) values (9, 'Beobachter', 1);
insert into `db239003879`.`tm_user_role` (`idtm_user_role`, `user_role_name`, `user_role_rechte`) values (10, 'Regionalleiter', 2);
insert into `db239003879`.`tm_user_role` (`idtm_user_role`, `user_role_name`, `user_role_rechte`) values (11, 'Kunde', 2);
insert into `db239003879`.`tm_user_role` (`idtm_user_role`, `user_role_name`, `user_role_rechte`) values (12, 'Berater', 2);

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`tm_user`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`tm_user` (`idtm_user`, `user_name`, `user_vorname`, `user_password`, `idtm_user_role`, `user_username`, `user_mail`) values (1, 'flip', 'flip', 'flip', 1, 'flip', 'pf@com-x-cha.com');

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`ta_partei`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`ta_partei` (`partei_name`, `partei_name2`, `partei_name3`, `partei_vorname`, `idtm_user`, `idta_partei`) values ('Frenzel GmbH', '', '', '', 1, 1);

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`tm_country`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`tm_country` (`idtm_country`, `country_iso`, `country_ful`) values (1, 'AUT', 'Austria');
insert into `db239003879`.`tm_country` (`idtm_country`, `country_iso`, `country_ful`) values (2, 'GER', 'Germany');
insert into `db239003879`.`tm_country` (`idtm_country`, `country_iso`, `country_ful`) values (3, 'FRA', 'France');
insert into `db239003879`.`tm_country` (`idtm_country`, `country_iso`, `country_ful`) values (4, 'ESP', 'Spain');

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`ta_adresse`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`ta_adresse` (`idta_adresse`, `adresse_street`, `adresse_zip`, `adresse_town`, `idtm_country`, `adresse_lat`, `adresse_long`, `adresse_ismain`) values (1, 'Test', '12345', 'Stuttgart', 1, '', '', NULL);

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`ta_partei_has_ta_adresse`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`ta_partei_has_ta_adresse` (`idta_partei`, `idta_adresse`) values (1, 1);

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`tm_waren_kategorie`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`tm_waren_kategorie` (`idtm_waren_kategorie`, `waren_kategorie_name`, `waren_kategorie_beschreibung`, `parent_idtm_waren_kategorie`) values (1, 'Energie', NULL, 0);

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`tm_preis_kategorie`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`tm_preis_kategorie` (`idtm_preis_kategorie`, `preis_kategorie_name`, `preis_kategorie_beschreibung`) values (1, 'min', 'Mindestpreis');
insert into `db239003879`.`tm_preis_kategorie` (`idtm_preis_kategorie`, `preis_kategorie_name`, `preis_kategorie_beschreibung`) values (2, 'fix', 'Fixpreis');
insert into `db239003879`.`tm_preis_kategorie` (`idtm_preis_kategorie`, `preis_kategorie_name`, `preis_kategorie_beschreibung`) values (3, 'vhb', 'Vehandlungsbasis');
insert into `db239003879`.`tm_preis_kategorie` (`idtm_preis_kategorie`, `preis_kategorie_name`, `preis_kategorie_beschreibung`) values (4, 'max', 'Maximalpreis');

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`tm_fahrzeug_kategorie`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`tm_fahrzeug_kategorie` (`idtm_fahrzeug_kategorie`, `fahrzeug_kategorie_name`, `fahrzeug_kategorie_beschreibung`) values (1, 'Sattelzug', '');
insert into `db239003879`.`tm_fahrzeug_kategorie` (`idtm_fahrzeug_kategorie`, `fahrzeug_kategorie_name`, `fahrzeug_kategorie_beschreibung`) values (2, 'Hängerzug', '');
insert into `db239003879`.`tm_fahrzeug_kategorie` (`idtm_fahrzeug_kategorie`, `fahrzeug_kategorie_name`, `fahrzeug_kategorie_beschreibung`) values (3, 'Planenaufbau', '');
insert into `db239003879`.`tm_fahrzeug_kategorie` (`idtm_fahrzeug_kategorie`, `fahrzeug_kategorie_name`, `fahrzeug_kategorie_beschreibung`) values (4, 'Jumbo', '');
insert into `db239003879`.`tm_fahrzeug_kategorie` (`idtm_fahrzeug_kategorie`, `fahrzeug_kategorie_name`, `fahrzeug_kategorie_beschreibung`) values (5, 'Möbelkoffer', '');
insert into `db239003879`.`tm_fahrzeug_kategorie` (`idtm_fahrzeug_kategorie`, `fahrzeug_kategorie_name`, `fahrzeug_kategorie_beschreibung`) values (6, 'Tieflader', '');
insert into `db239003879`.`tm_fahrzeug_kategorie` (`idtm_fahrzeug_kategorie`, `fahrzeug_kategorie_name`, `fahrzeug_kategorie_beschreibung`) values (7, 'Kipper', 'Kipper');

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`ta_organisation_type`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`ta_organisation_type` (`idta_organisation_type`, `org_type_name`, `org_type_monat_jahr`) values (1, 'Organisation', NULL);
insert into `db239003879`.`ta_organisation_type` (`idta_organisation_type`, `org_type_name`, `org_type_monat_jahr`) values (2, 'Abteilung', NULL);
insert into `db239003879`.`ta_organisation_type` (`idta_organisation_type`, `org_type_name`, `org_type_monat_jahr`) values (3, 'Team', NULL);
insert into `db239003879`.`ta_organisation_type` (`idta_organisation_type`, `org_type_name`, `org_type_monat_jahr`) values (4, 'Mitarbeiter', NULL);
insert into `db239003879`.`ta_organisation_type` (`idta_organisation_type`, `org_type_name`, `org_type_monat_jahr`) values (5, 'WEG', NULL);
insert into `db239003879`.`ta_organisation_type` (`idta_organisation_type`, `org_type_name`, `org_type_monat_jahr`) values (6, 'Mietobjekt', NULL);
insert into `db239003879`.`ta_organisation_type` (`idta_organisation_type`, `org_type_name`, `org_type_monat_jahr`) values (7, 'Objekt', NULL);
insert into `db239003879`.`ta_organisation_type` (`idta_organisation_type`, `org_type_name`, `org_type_monat_jahr`) values (8, 'Partei', NULL);
insert into `db239003879`.`ta_organisation_type` (`idta_organisation_type`, `org_type_name`, `org_type_monat_jahr`) values (9, 'HTC', NULL);

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`ta_einheit`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`ta_einheit` (`idta_einheit`, `ein_name`) values (1, 'Stunde');
insert into `db239003879`.`ta_einheit` (`idta_einheit`, `ein_name`) values (2, 'Stück');
insert into `db239003879`.`ta_einheit` (`idta_einheit`, `ein_name`) values (3, 'ccm');

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`ta_rescalendar`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`ta_rescalendar` (`idta_rescalendar`, `rescal_name`, `rescal_descr`, `rescal_t1`, `rescal_h1`, `rescal_t2`, `rescal_h2`, `rescal_t3`, `rescal_h3`, `rescal_t4`, `rescal_h4`, `rescal_t5`, `rescal_h5`, `rescal_t6`, `rescal_h6`, `rescal_t7`, `rescal_h7`) values (1, 'Standard', NULL, '09:00-17:00', 8, '09:00-17:00', 8, '09:00-17:00', 8, '09:00-17:00', 8, '09:00-17:00', 8, '09:00-17:00', 8, '09:00-17:00', 8);

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`ta_ressource_type`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`ta_ressource_type` (`idta_ressource_type`, `res_type_name`, `res_type_descr`, `res_type_kosten`) values (1, 'Mensch', NULL, 0);
insert into `db239003879`.`ta_ressource_type` (`idta_ressource_type`, `res_type_name`, `res_type_descr`, `res_type_kosten`) values (2, 'Maschine', NULL, 0);
insert into `db239003879`.`ta_ressource_type` (`idta_ressource_type`, `res_type_name`, `res_type_descr`, `res_type_kosten`) values (3, 'Material', NULL, 0);
insert into `db239003879`.`ta_ressource_type` (`idta_ressource_type`, `res_type_name`, `res_type_descr`, `res_type_kosten`) values (4, 'Raum', NULL, 0);

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`tm_ressource`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`tm_ressource` (`idtm_ressource`, `res_name`, `res_code`, `idta_rescalendar`, `idta_ressource_type`, `res_produktivitaet`, `res_kosten`, `res_note`, `idta_einheit`) values (1, 'Standard', 'STD-001', 1, 1, 100, 100, NULL, 1);

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`ta_struktur_type`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`ta_struktur_type` (`idta_struktur_type`, `struktur_type_name`) values (1, 'Unternehmen');
insert into `db239003879`.`ta_struktur_type` (`idta_struktur_type`, `struktur_type_name`) values (2, 'Profitcenter');
insert into `db239003879`.`ta_struktur_type` (`idta_struktur_type`, `struktur_type_name`) values (3, 'Kostenstelle');
insert into `db239003879`.`ta_struktur_type` (`idta_struktur_type`, `struktur_type_name`) values (4, 'Umsatz');
insert into `db239003879`.`ta_struktur_type` (`idta_struktur_type`, `struktur_type_name`) values (5, 'Aufwand');
insert into `db239003879`.`ta_struktur_type` (`idta_struktur_type`, `struktur_type_name`) values (6, 'kalk. Kosten');
insert into `db239003879`.`ta_struktur_type` (`idta_struktur_type`, `struktur_type_name`) values (7, 'Anlagevermögen');
insert into `db239003879`.`ta_struktur_type` (`idta_struktur_type`, `struktur_type_name`) values (8, 'Umlaufvermögen');
insert into `db239003879`.`ta_struktur_type` (`idta_struktur_type`, `struktur_type_name`) values (9, 'Lager');
insert into `db239003879`.`ta_struktur_type` (`idta_struktur_type`, `struktur_type_name`) values (10, 'Verbindlichkeiten LuL');
insert into `db239003879`.`ta_struktur_type` (`idta_struktur_type`, `struktur_type_name`) values (11, 'Forderungen LuL');
insert into `db239003879`.`ta_struktur_type` (`idta_struktur_type`, `struktur_type_name`) values (12, 'sonstige Verbindlichkeiten');
insert into `db239003879`.`ta_struktur_type` (`idta_struktur_type`, `struktur_type_name`) values (13, 'sonstige Forderungen');
insert into `db239003879`.`ta_struktur_type` (`idta_struktur_type`, `struktur_type_name`) values (14, 'Eigenkapital');
insert into `db239003879`.`ta_struktur_type` (`idta_struktur_type`, `struktur_type_name`) values (15, 'Ertrag');
insert into `db239003879`.`ta_struktur_type` (`idta_struktur_type`, `struktur_type_name`) values (16, 'PP Spezial');

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`ta_risiko_type`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`ta_risiko_type` (`idta_risiko_type`, `ris_type_name`) values (1, 'Hauptrisiko');
insert into `db239003879`.`ta_risiko_type` (`idta_risiko_type`, `ris_type_name`) values (2, 'Risikoarten');

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`tm_risiko`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`tm_risiko` (`idtm_risiko`, `ris_name`, `ris_descr`, `parent_idtm_risiko`, `idta_risiko_type`) values (1, 'Allgemeine Risiken', NULL, 0, 1);
insert into `db239003879`.`tm_risiko` (`idtm_risiko`, `ris_name`, `ris_descr`, `parent_idtm_risiko`, `idta_risiko_type`) values (2, 'Länderrisiken', NULL, 1, 1);
insert into `db239003879`.`tm_risiko` (`idtm_risiko`, `ris_name`, `ris_descr`, `parent_idtm_risiko`, `idta_risiko_type`) values (3, 'Operationale Risiken', NULL, 1, 1);
insert into `db239003879`.`tm_risiko` (`idtm_risiko`, `ris_name`, `ris_descr`, `parent_idtm_risiko`, `idta_risiko_type`) values (4, 'Marktrisiko', NULL, 1, 1);
insert into `db239003879`.`tm_risiko` (`idtm_risiko`, `ris_name`, `ris_descr`, `parent_idtm_risiko`, `idta_risiko_type`) values (5, 'Liquidätsrisiko', NULL, 1, 1);
insert into `db239003879`.`tm_risiko` (`idtm_risiko`, `ris_name`, `ris_descr`, `parent_idtm_risiko`, `idta_risiko_type`) values (6, 'Kaufmännische Risiken', NULL, 1, 1);
insert into `db239003879`.`tm_risiko` (`idtm_risiko`, `ris_name`, `ris_descr`, `parent_idtm_risiko`, `idta_risiko_type`) values (7, 'Technische Risiken', NULL, 1, 1);
insert into `db239003879`.`tm_risiko` (`idtm_risiko`, `ris_name`, `ris_descr`, `parent_idtm_risiko`, `idta_risiko_type`) values (8, 'Terminrisiken', NULL, 1, 1);
insert into `db239003879`.`tm_risiko` (`idtm_risiko`, `ris_name`, `ris_descr`, `parent_idtm_risiko`, `idta_risiko_type`) values (9, 'Ressourcen Risiken', NULL, 1, 1);
insert into `db239003879`.`tm_risiko` (`idtm_risiko`, `ris_name`, `ris_descr`, `parent_idtm_risiko`, `idta_risiko_type`) values (10, 'Kommunikationsrisiken', NULL, 1, 1);
insert into `db239003879`.`tm_risiko` (`idtm_risiko`, `ris_name`, `ris_descr`, `parent_idtm_risiko`, `idta_risiko_type`) values (11, 'Rechtliche Risiken', NULL, 3, 2);
insert into `db239003879`.`tm_risiko` (`idtm_risiko`, `ris_name`, `ris_descr`, `parent_idtm_risiko`, `idta_risiko_type`) values (12, 'Hardware Probleme', NULL, 7, 2);
insert into `db239003879`.`tm_risiko` (`idtm_risiko`, `ris_name`, `ris_descr`, `parent_idtm_risiko`, `idta_risiko_type`) values (13, 'Veränderung der IT-Strategie', NULL, 7, 2);

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`ta_prozess_type`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`ta_prozess_type` (`idta_prozess_type`, `pro_type_name`) values (1, 'Managementprozess');
insert into `db239003879`.`ta_prozess_type` (`idta_prozess_type`, `pro_type_name`) values (2, 'Kernprozess');
insert into `db239003879`.`ta_prozess_type` (`idta_prozess_type`, `pro_type_name`) values (3, 'Teilprozess');
insert into `db239003879`.`ta_prozess_type` (`idta_prozess_type`, `pro_type_name`) values (4, 'Hilfsprozess');

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`tm_prozess`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`tm_prozess` (`idtm_prozess`, `pro_name`, `pro_descr`, `parent_idtm_prozess`, `idta_prozess_type`, `pro_step`) values (1, 'Projekt', NULL, 0, 1, 0);
insert into `db239003879`.`tm_prozess` (`idtm_prozess`, `pro_name`, `pro_descr`, `parent_idtm_prozess`, `idta_prozess_type`, `pro_step`) values (2, 'Pr - Presales', NULL, 1, 2, 0);
insert into `db239003879`.`tm_prozess` (`idtm_prozess`, `pro_name`, `pro_descr`, `parent_idtm_prozess`, `idta_prozess_type`, `pro_step`) values (3, 'Pr - Projektdesign', NULL, 1, 2, 0);
insert into `db239003879`.`tm_prozess` (`idtm_prozess`, `pro_name`, `pro_descr`, `parent_idtm_prozess`, `idta_prozess_type`, `pro_step`) values (4, 'Pr - Programmierung', NULL, 1, 2, 0);
insert into `db239003879`.`tm_prozess` (`idtm_prozess`, `pro_name`, `pro_descr`, `parent_idtm_prozess`, `idta_prozess_type`, `pro_step`) values (5, 'Pr - Implementierung', NULL, 1, 2, 0);
insert into `db239003879`.`tm_prozess` (`idtm_prozess`, `pro_name`, `pro_descr`, `parent_idtm_prozess`, `idta_prozess_type`, `pro_step`) values (6, 'Pr - Stabilisierung', NULL, 1, 2, 0);
insert into `db239003879`.`tm_prozess` (`idtm_prozess`, `pro_name`, `pro_descr`, `parent_idtm_prozess`, `idta_prozess_type`, `pro_step`) values (7, 'Pr - Test', NULL, 1, 2, 0);
insert into `db239003879`.`tm_prozess` (`idtm_prozess`, `pro_name`, `pro_descr`, `parent_idtm_prozess`, `idta_prozess_type`, `pro_step`) values (8, 'Pr - Abnahme', NULL, 1, 2, 0);

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`ta_ziele_type`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`ta_ziele_type` (`idta_ziele_type`, `zie_type_name`) values (1, 'Globalziel');
insert into `db239003879`.`ta_ziele_type` (`idta_ziele_type`, `zie_type_name`) values (2, 'Zielklasse');
insert into `db239003879`.`ta_ziele_type` (`idta_ziele_type`, `zie_type_name`) values (3, 'Zielunterklasse');

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`tm_ziele`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`tm_ziele` (`idtm_ziele`, `zie_name`, `zie_descr`, `parent_idtm_ziele`, `idta_ziele_type`) values (1, 'Globalziel', , 0, 1);
insert into `db239003879`.`tm_ziele` (`idtm_ziele`, `zie_name`, `zie_descr`, `parent_idtm_ziele`, `idta_ziele_type`) values (2, 'Funktionsziele', , 1, 2);
insert into `db239003879`.`tm_ziele` (`idtm_ziele`, `zie_name`, `zie_descr`, `parent_idtm_ziele`, `idta_ziele_type`) values (3, 'Zeitziele', , 1, 2);
insert into `db239003879`.`tm_ziele` (`idtm_ziele`, `zie_name`, `zie_descr`, `parent_idtm_ziele`, `idta_ziele_type`) values (4, 'Nicht-Ziele', , 1, 2);
insert into `db239003879`.`tm_ziele` (`idtm_ziele`, `zie_name`, `zie_descr`, `parent_idtm_ziele`, `idta_ziele_type`) values (5, 'Politische Ziele', , 1, 2);

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`ta_activity_type`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`ta_activity_type` (`idta_activity_type`, `act_type_name`) values (1, 'Meilenstein');
insert into `db239003879`.`ta_activity_type` (`idta_activity_type`, `act_type_name`) values (2, 'Projekt');
insert into `db239003879`.`ta_activity_type` (`idta_activity_type`, `act_type_name`) values (3, 'Teilaufgabe');
insert into `db239003879`.`ta_activity_type` (`idta_activity_type`, `act_type_name`) values (4, 'Aufgabenpaket');

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`ta_protokoll_type`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`ta_protokoll_type` (`idta_protokoll_type`, `prt_type_name`) values (1, 'Lenkungsausschuss');
insert into `db239003879`.`ta_protokoll_type` (`idta_protokoll_type`, `prt_type_name`) values (2, 'Statussitzung');
insert into `db239003879`.`ta_protokoll_type` (`idta_protokoll_type`, `prt_type_name`) values (3, 'Arbeitssitzung');
insert into `db239003879`.`ta_protokoll_type` (`idta_protokoll_type`, `prt_type_name`) values (4, 'Telefonprotokoll');

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`ta_protokoll_ergebnistype`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`ta_protokoll_ergebnistype` (`idta_protokoll_ergebnistype`, `prt_ergtype_name`) values (1, 'Beschluss');
insert into `db239003879`.`ta_protokoll_ergebnistype` (`idta_protokoll_ergebnistype`, `prt_ergtype_name`) values (2, 'Auftrag');
insert into `db239003879`.`ta_protokoll_ergebnistype` (`idta_protokoll_ergebnistype`, `prt_ergtype_name`) values (3, 'Information');
insert into `db239003879`.`ta_protokoll_ergebnistype` (`idta_protokoll_ergebnistype`, `prt_ergtype_name`) values (4, 'Terminvereinbarung');

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`ta_inoutput_type`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`ta_inoutput_type` (`idta_inoutput_type`, `ino_type_name`, `ino_type_descr`) values (1, 'Funktion', NULL);
insert into `db239003879`.`ta_inoutput_type` (`idta_inoutput_type`, `ino_type_name`, `ino_type_descr`) values (2, 'Werte', NULL);
insert into `db239003879`.`ta_inoutput_type` (`idta_inoutput_type`, `ino_type_name`, `ino_type_descr`) values (3, 'sonstiges', NULL);

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`ta_termin_type`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`ta_termin_type` (`idta_termin_type`, `ter_type_name`, `ter_type_descr`) values (1, 'Termin', NULL);
insert into `db239003879`.`ta_termin_type` (`idta_termin_type`, `ter_type_name`, `ter_type_descr`) values (2, 'Urlaub', NULL);
insert into `db239003879`.`ta_termin_type` (`idta_termin_type`, `ter_type_name`, `ter_type_descr`) values (3, 'Krankheit', NULL);

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`ta_bericht_type`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`ta_bericht_type` (`ber_type_name`, `ber_type_descr`, `idta_bericht_type`) values ('1', , NULL);
insert into `db239003879`.`ta_bericht_type` (`ber_type_name`, `ber_type_descr`, `idta_bericht_type`) values ('2', , NULL);
insert into `db239003879`.`ta_bericht_type` (`ber_type_name`, `ber_type_descr`, `idta_bericht_type`) values ('3', , NULL);

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`ta_bericht_struktur_type`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`ta_bericht_struktur_type` (`ber_struktur_type_name`, `ber_struktur_type_descr`, `idta_bericht_struktur_type`) values ('Abschnitt', , 1);
insert into `db239003879`.`ta_bericht_struktur_type` (`ber_struktur_type_name`, `ber_struktur_type_descr`, `idta_bericht_struktur_type`) values ('Text', , 2);
insert into `db239003879`.`ta_bericht_struktur_type` (`ber_struktur_type_name`, `ber_struktur_type_descr`, `idta_bericht_struktur_type`) values ('Content Container', , 3);
insert into `db239003879`.`ta_bericht_struktur_type` (`ber_struktur_type_name`, `ber_struktur_type_descr`, `idta_bericht_struktur_type`) values ('sonstiges', , 4);

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`ta_contentcontainer_type`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`ta_contentcontainer_type` (`cc_type_name`, `cc_type_descr`, `idta_contentcontainer_type`) values ('1', , NULL);
insert into `db239003879`.`ta_contentcontainer_type` (`cc_type_name`, `cc_type_descr`, `idta_contentcontainer_type`) values ('2', , NULL);
insert into `db239003879`.`ta_contentcontainer_type` (`cc_type_name`, `cc_type_descr`, `idta_contentcontainer_type`) values ('3', , NULL);
insert into `db239003879`.`ta_contentcontainer_type` (`cc_type_name`, `cc_type_descr`, `idta_contentcontainer_type`) values ('4', , NULL);

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`catalogue`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`catalogue` (`cat_id`, `name`, `source_lang`, `target_lang`, `date_created`, `date_modified`, `author`) values (1, 'messages', '', '', 0, 0, '');
insert into `db239003879`.`catalogue` (`cat_id`, `name`, `source_lang`, `target_lang`, `date_created`, `date_modified`, `author`) values (2, 'messages.en_US', '', '', 0, 0, '');
insert into `db239003879`.`catalogue` (`cat_id`, `name`, `source_lang`, `target_lang`, `date_created`, `date_modified`, `author`) values (3, 'messages.en_AU', '', '', 0, 0, '');

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`trans_unit`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`trans_unit` (`msg_id`, `cat_id`, `id`, `source`, `target`, `comments`, `date_added`, `date_modified`, `author`, `translated`) values (1, 1, '1', 'Hello', 'Hello Word', NULL, 0, 0, '', '1');

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`ta_kosten_status`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`ta_kosten_status` (`idta_kosten_status`, `kst_status_name`) values (1, 'abrechenbar');
insert into `db239003879`.`ta_kosten_status` (`idta_kosten_status`, `kst_status_name`) values (2, 'ausweisbar');
insert into `db239003879`.`ta_kosten_status` (`idta_kosten_status`, `kst_status_name`) values (3, 'intern');
insert into `db239003879`.`ta_kosten_status` (`idta_kosten_status`, `kst_status_name`) values (4, 'sonstiges');

COMMIT;

-- -----------------------------------------------------
-- Data for table `db239003879`.`ta_aufgaben_type`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `db239003879`;
insert into `db239003879`.`ta_aufgaben_type` (`idta_aufgaben_type`, `auf_type_name`) values (1, 'Telefonat');
insert into `db239003879`.`ta_aufgaben_type` (`idta_aufgaben_type`, `auf_type_name`) values (2, 'Gespräch');
insert into `db239003879`.`ta_aufgaben_type` (`idta_aufgaben_type`, `auf_type_name`) values (3, 'Notiz');
insert into `db239003879`.`ta_aufgaben_type` (`idta_aufgaben_type`, `auf_type_name`) values (4, 'Fax');
insert into `db239003879`.`ta_aufgaben_type` (`idta_aufgaben_type`, `auf_type_name`) values (5, 'Brief');
insert into `db239003879`.`ta_aufgaben_type` (`idta_aufgaben_type`, `auf_type_name`) values (6, 'Mail');

COMMIT;
