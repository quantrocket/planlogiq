SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE  TABLE IF NOT EXISTS `harley2009`.`ta_automapping` (
  `idta_automapping` INT(11) NULL DEFAULT NULL AUTO_INCREMENT ,
  `ima_name` VARCHAR(45) NULL DEFAULT NULL ,
  `idtm_stammdaten` INT(11) NULL DEFAULT NULL ,
  `idta_feldfunktion` INT(11) NULL DEFAULT NULL ,
  `ama_faktor` FLOAT NULL DEFAULT NULL ,
  `ama_lauf` INT(11) NULL DEFAULT 1 ,
  `ama_source` VARCHAR(1) NULL DEFAULT NULL ,
  `ama_id` INT(11) NULL DEFAULT 1 COMMENT 'the id level' ,
  PRIMARY KEY (`idta_automapping`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci
COMMENT = 'here the rules for the mapping are stored';

CREATE  TABLE IF NOT EXISTS `harley2009`.`tm_importmapping` (
  `idtm_importmapping` INT(11) NULL DEFAULT NULL AUTO_INCREMENT ,
  `ima_name` VARCHAR(45) NULL DEFAULT NULL ,
  `ima_id1` VARCHAR(45) NULL DEFAULT NULL ,
  `ima_id2` VARCHAR(45) NULL DEFAULT NULL ,
  `ima_id3` VARCHAR(45) NULL DEFAULT NULL ,
  `ima_id4` VARCHAR(45) NULL DEFAULT NULL ,
  `ima_id5` VARCHAR(45) NULL DEFAULT NULL ,
  `ima_id6` VARCHAR(45) NULL DEFAULT NULL ,
  `ima_id7` VARCHAR(45) NULL DEFAULT NULL ,
  `ima_id8` VARCHAR(45) NULL DEFAULT NULL ,
  `ima_id9` VARCHAR(45) NULL DEFAULT NULL ,
  `ima_id10` VARCHAR(45) NULL DEFAULT NULL ,
  `idtm_struktur` INT(11) NULL DEFAULT NULL ,
  `idta_feldfunktion` INT(11) NULL DEFAULT NULL ,
  `ima_faktor` FLOAT NULL DEFAULT 1 ,
  `ima_lauf` INT(11) NULL DEFAULT 1 ,
  `ima_source` VARCHAR(1) NULL DEFAULT NULL ,
  `ima_path` VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (`idtm_importmapping`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci
COMMENT = 'the final mapping information';

DROP TABLE IF EXISTS `harley2009`.`pradocache` ;

ALTER TABLE `harley2009`.`ta_contentcontainer` DROP FOREIGN KEY `fk_cc_cc_type` ;

ALTER TABLE `harley2009`.`ta_contentcontainer` 
  ADD CONSTRAINT `fk_cc_cc_type`
  FOREIGN KEY (`idta_contentcontainer_type` )
  REFERENCES `harley2009`.`ta_contentcontainer_type` (`idta_contentcontainer_type` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `harley2009`.`ta_feldfunktion` CHANGE COLUMN `ff_fix` `ff_fix` TINYINT(1) NULL  ;

ALTER TABLE `harley2009`.`ta_fracht` DROP FOREIGN KEY `fk_idtm_fahrzeug_kategorie` ;

ALTER TABLE `harley2009`.`ta_fracht` 
  ADD CONSTRAINT `fk_idtm_fahrzeug_kategorie`
  FOREIGN KEY (`idtm_fahrzeug_kategorie` )
  REFERENCES `harley2009`.`tm_fahrzeug_kategorie` (`idtm_fahrzeug_kategorie` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `harley2009`.`ta_partei_has_ta_adresse` DROP FOREIGN KEY `fk_idta_adresse` ;

ALTER TABLE `harley2009`.`ta_partei_has_ta_adresse` 
  ADD CONSTRAINT `fk_idta_adresse`
  FOREIGN KEY (`idta_adresse` )
  REFERENCES `harley2009`.`ta_adresse` (`idta_adresse` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, DROP INDEX `PK` 
, ADD UNIQUE INDEX `PK` (`idta_partei` ASC, `idta_adresse` ASC) ;

ALTER TABLE `harley2009`.`ta_partei` 
DROP PRIMARY KEY 
, ADD PRIMARY KEY (`idta_partei`) ;

ALTER TABLE `harley2009`.`ta_pivot_bericht` CHANGE COLUMN `idta_pivot_bericht` `idta_pivot_bericht` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`ta_saisonalisierung` CHANGE COLUMN `idta_saisonalisierung` `idta_saisonalisierung` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`ta_sbs_collector` CHANGE COLUMN `idta_sbs_collector` `idta_sbs_collector` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`ta_sbz_collector` CHANGE COLUMN `idta_sbz_collector` `idta_sbz_collector` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`ta_stammdaten_group` CHANGE COLUMN `idta_stammdaten_group` `idta_stammdaten_group` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`ta_struktur_bericht_spalten` CHANGE COLUMN `idta_struktur_bericht_spalten` `idta_struktur_bericht_spalten` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  , CHANGE COLUMN `sbs_perioden_fix` `sbs_perioden_fix` TINYINT(1) NULL  , CHANGE COLUMN `sbs_cumulated` `sbs_cumulated` TINYINT(1) NULL  ;

ALTER TABLE `harley2009`.`ta_struktur_bericht_zeilen` CHANGE COLUMN `idta_struktur_bericht_zeilen` `idta_struktur_bericht_zeilen` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  , CHANGE COLUMN `sbz_detail` `sbz_detail` TINYINT(1) NULL  ;

ALTER TABLE `harley2009`.`ta_struktur_bericht` CHANGE COLUMN `idta_struktur_bericht` `idta_struktur_bericht` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`ta_waren_has_ta_partei` DROP FOREIGN KEY `fkidta_partei_ek` ;

ALTER TABLE `harley2009`.`ta_waren_has_ta_partei` 
  ADD CONSTRAINT `fkidta_partei_ek`
  FOREIGN KEY (`idta_partei_ek` )
  REFERENCES `harley2009`.`ta_partei` (`idta_partei` )
  ON DELETE SET NULL
  ON UPDATE SET NULL;

ALTER TABLE `harley2009`.`ta_waren` DROP FOREIGN KEY `fkidtm_adresse` , DROP FOREIGN KEY `fkidtm_preis_kategorie` ;

ALTER TABLE `harley2009`.`ta_waren` 
  ADD CONSTRAINT `fkidtm_adresse`
  FOREIGN KEY (`idta_adresse` )
  REFERENCES `harley2009`.`ta_adresse` (`idta_adresse` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fkidtm_preis_kategorie`
  FOREIGN KEY (`idtm_preis_kategorie` )
  REFERENCES `harley2009`.`tm_preis_kategorie` (`idtm_preis_kategorie` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `harley2009`.`tm_activity_has_tm_organisation` CHANGE COLUMN `idtm_activity_has_tm_organisation` `idtm_activity_has_tm_organisation` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`tm_aufgabe_ressource` CHANGE COLUMN `idtm_aufgabe_ressource` `idtm_aufgabe_ressource` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`tm_pivot` CHANGE COLUMN `idtm_pivot` `idtm_pivot` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`tm_stammdaten` CHANGE COLUMN `idtm_stammdaten` `idtm_stammdaten` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`tm_struktur_has_ta_stammdaten_group` CHANGE COLUMN `idtm_struktur_has_ta_stammdaten_group` `idtm_struktur_has_ta_stammdaten_group` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`tm_tempimport` CHANGE COLUMN `idtm_tempimport` `idtm_tempimport` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`tt_rcvalue_netto` DROP FOREIGN KEY `fk_tm_rcvalue_tt_rcvalue_netto` ;

ALTER TABLE `harley2009`.`tt_rcvalue_netto` 
  ADD CONSTRAINT `fk_tm_rcvalue_tt_rcvalue_netto`
  FOREIGN KEY (`idtm_rcvalue` )
  REFERENCES `harley2009`.`tm_rcvalue` (`idtm_rcvalue` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `harley2009`.`tt_saisonalisierung` CHANGE COLUMN `idtt_saisonalisierung` `idtt_saisonalisierung` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`tt_werte` DROP FOREIGN KEY `fk_tt_werte_ta_variante` ;

ALTER TABLE `harley2009`.`tt_werte` 
  ADD CONSTRAINT `fk_tt_werte_ta_variante`
  FOREIGN KEY (`w_id_variante` )
  REFERENCES `harley2009`.`ta_variante` (`idta_variante` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `harley2009`.`xx_berechtigung` CHANGE COLUMN `xx_read` `xx_read` TINYINT(1) NULL  , CHANGE COLUMN `xx_write` `xx_write` TINYINT(1) NULL  , CHANGE COLUMN `xx_create` `xx_create` TINYINT(1) NULL  , CHANGE COLUMN `xx_delete` `xx_delete` TINYINT(1) NULL  ;

ALTER TABLE `harley2009`.`ta_adresse` 
DROP PRIMARY KEY 
, ADD PRIMARY KEY (`idta_adresse`) ;

ALTER TABLE `harley2009`.`ta_fracht_teilladung` CHANGE COLUMN `fracht_teilladung_temperatur` `fracht_teilladung_temperatur` TINYINT(1) NULL  ;

ALTER TABLE `harley2009`.`tm_activity` DROP FOREIGN KEY `fk_tm_activity_ta_activity_type` , DROP FOREIGN KEY `fk_tm_activity_tm_organisation` ;

ALTER TABLE `harley2009`.`tm_activity` 
  ADD CONSTRAINT `fk_tm_activity_ta_activity_type`
  FOREIGN KEY (`idta_activity_type` )
  REFERENCES `harley2009`.`ta_activity_type` (`idta_activity_type` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_tm_activity_tm_organisation`
  FOREIGN KEY (`idtm_organisation` )
  REFERENCES `harley2009`.`tm_organisation` (`idtm_organisation` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `harley2009`.`tm_activity_participants` CHANGE COLUMN `act_part_anwesend` `act_part_anwesend` TINYINT(1) NULL  ;

ALTER TABLE `harley2009`.`tm_protokoll_detail` CHANGE COLUMN `prtdet_wvl` `prtdet_wvl` TINYINT(1) NULL  , DROP FOREIGN KEY `fk_ta_protokoll_detail_group_tm_protokoll_detail` ;

ALTER TABLE `harley2009`.`tm_protokoll_detail` 
  ADD CONSTRAINT `fk_ta_protokoll_detail_group_tm_protokoll_detail`
  FOREIGN KEY (`idta_protokoll_detail_group` )
  REFERENCES `harley2009`.`ta_protokoll_detail_group` (`idta_protokoll_detail_group` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `harley2009`.`tm_verteiler` CHANGE COLUMN `ver_valid` `ver_valid` TINYINT(1) NULL  ;

ALTER TABLE `harley2009`.`tt_container_text` CHANGE COLUMN `cc_text_valid` `cc_text_valid` TINYINT(1) NULL  ;

ALTER TABLE `harley2009`.`tt_stammdaten` CHANGE COLUMN `idtt_stammdaten` `idtt_stammdaten` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  , DROP FOREIGN KEY `fk_ta_feldfunktion_tt_stammdaten` , DROP FOREIGN KEY `fk_ta_variante_tt_stammdaten` ;

ALTER TABLE `harley2009`.`tt_stammdaten` 
  ADD CONSTRAINT `fk_ta_feldfunktion_tt_stammdaten`
  FOREIGN KEY (`idta_feldfunktion` )
  REFERENCES `harley2009`.`ta_feldfunktion` (`idta_feldfunktion` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_ta_variante_tt_stammdaten`
  FOREIGN KEY (`idta_variante` )
  REFERENCES `harley2009`.`ta_variante` (`idta_variante` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


-- -----------------------------------------------------
-- Placeholder table for view `harley2009`.`vv_activity_participants`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `harley2009`.`vv_activity_participants` (`id` INT);

-- -----------------------------------------------------
-- Placeholder table for view `harley2009`.`vv_activity_ziele`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `harley2009`.`vv_activity_ziele` (`id` INT);

-- -----------------------------------------------------
-- Placeholder table for view `harley2009`.`vv_termin_organisation`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `harley2009`.`vv_termin_organisation` (`id` INT);

-- -----------------------------------------------------
-- Placeholder table for view `harley2009`.`vv_verteiler_organisation`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `harley2009`.`vv_verteiler_organisation` (`id` INT);


USE harley2009;

-- -----------------------------------------------------
-- View `harley2009`.`vv_activity_participants`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley2009`.`vv_activity_participants`;
CREATE  OR REPLACE VIEW `harley2009`.`vv_activity_participants` AS
SELECT tm_activity_participants.*,tm_organisation.org_name,tm_user_role.user_role_name FROM tm_activity_participants INNER JOIN tm_organisation ON tm_organisation.idtm_organisation = tm_activity_participants.idtm_organisation
INNER JOIN tm_user_role ON tm_user_role.idtm_user_role = tm_organisation.org_idtm_user_role;


USE harley2009;

-- -----------------------------------------------------
-- View `harley2009`.`vv_activity_ziele`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley2009`.`vv_activity_ziele`;
CREATE  OR REPLACE VIEW `harley2009`.`vv_activity_ziele` AS
SELECT a.*, b.ttzie_name FROM tm_activity_has_tt_ziele a INNER JOIN tt_ziele b ON a.idtt_ziele = b.idtt_ziele;


USE harley2009;

-- -----------------------------------------------------
-- View `harley2009`.`vv_termin_organisation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley2009`.`vv_termin_organisation`;
CREATE  OR REPLACE VIEW `harley2009`.`vv_termin_organisation` AS
SELECT tm_termin_organisation.*,tm_organisation.org_name,tm_user_role.user_role_name FROM tm_termin_organisation INNER JOIN tm_organisation ON tm_organisation.idtm_organisation = tm_termin_organisation.idtm_organisation
INNER JOIN tm_user_role ON tm_user_role.idtm_user_role = tm_organisation.org_idtm_user_role;


USE harley2009;

-- -----------------------------------------------------
-- View `harley2009`.`vv_verteiler_organisation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `harley2009`.`vv_verteiler_organisation`;
CREATE  OR REPLACE VIEW `harley2009`.`vv_verteiler_organisation` AS
SELECT tm_verteiler_organisation.*,tm_organisation.org_name,tm_user_role.user_role_name FROM tm_verteiler_organisation INNER JOIN tm_organisation ON tm_organisation.idtm_organisation = tm_verteiler_organisation.idtm_organisation
INNER JOIN tm_user_role ON tm_user_role.idtm_user_role = tm_organisation.org_idtm_user_role;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
