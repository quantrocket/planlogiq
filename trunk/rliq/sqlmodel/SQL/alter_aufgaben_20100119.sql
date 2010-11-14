SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DROP SCHEMA IF EXISTS `db241933781` ;

DROP TABLE IF EXISTS `harley2009`.`pradocache` ;

ALTER TABLE `harley2009`.`ta_automapping` CHANGE COLUMN `idta_automapping` `idta_automapping` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`ta_fracht` DROP FOREIGN KEY `fk_idtm_fahrzeug_kategorie` ;

ALTER TABLE `harley2009`.`ta_fracht` 
  ADD CONSTRAINT `fk_idtm_fahrzeug_kategorie`
  FOREIGN KEY (`idtm_fahrzeug_kategorie` )
  REFERENCES `db241933781`.`tm_fahrzeug_kategorie` (`idtm_fahrzeug_kategorie` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `harley2009`.`ta_pivot_bericht` CHANGE COLUMN `idta_pivot_bericht` `idta_pivot_bericht` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`ta_saisonalisierung` CHANGE COLUMN `idta_saisonalisierung` `idta_saisonalisierung` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`ta_sbs_collector` CHANGE COLUMN `idta_sbs_collector` `idta_sbs_collector` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`ta_sbz_collector` CHANGE COLUMN `idta_sbz_collector` `idta_sbz_collector` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`ta_stammdaten_group` CHANGE COLUMN `idta_stammdaten_group` `idta_stammdaten_group` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`ta_struktur_bericht_spalten` CHANGE COLUMN `idta_struktur_bericht_spalten` `idta_struktur_bericht_spalten` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`ta_struktur_bericht_zeilen` CHANGE COLUMN `idta_struktur_bericht_zeilen` `idta_struktur_bericht_zeilen` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`ta_struktur_bericht` CHANGE COLUMN `idta_struktur_bericht` `idta_struktur_bericht` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`tm_activity_has_tm_organisation` CHANGE COLUMN `idtm_activity_has_tm_organisation` `idtm_activity_has_tm_organisation` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`tm_aufgaben` ADD COLUMN `auf_zeichen_eigen` VARCHAR(45) NULL DEFAULT NULL  AFTER `auf_tag` , ADD COLUMN `auf_zeichen_fremd` VARCHAR(45) NULL DEFAULT NULL  AFTER `auf_zeichen_eigen` ;

ALTER TABLE `harley2009`.`tm_aufgabe_ressource` CHANGE COLUMN `idtm_aufgabe_ressource` `idtm_aufgabe_ressource` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`tm_importmapping` CHANGE COLUMN `idtm_importmapping` `idtm_importmapping` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`tm_pivot` CHANGE COLUMN `idtm_pivot` `idtm_pivot` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`tm_stammdaten` CHANGE COLUMN `idtm_stammdaten` `idtm_stammdaten` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`tm_struktur_has_ta_stammdaten_group` CHANGE COLUMN `idtm_struktur_has_ta_stammdaten_group` `idtm_struktur_has_ta_stammdaten_group` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`tm_tempimport` CHANGE COLUMN `idtm_tempimport` `idtm_tempimport` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`tt_rcvalue_netto` DROP FOREIGN KEY `fk_tm_rcvalue_tt_rcvalue_netto` ;

ALTER TABLE `harley2009`.`tt_rcvalue_netto` 
  ADD CONSTRAINT `fk_tm_rcvalue_tt_rcvalue_netto`
  FOREIGN KEY (`idtm_rcvalue` )
  REFERENCES `db241933781`.`tm_rcvalue` (`idtm_rcvalue` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `harley2009`.`tt_saisonalisierung` CHANGE COLUMN `idtt_saisonalisierung` `idtt_saisonalisierung` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`tm_activity` DROP FOREIGN KEY `fk_tm_activity_ta_activity_type` , DROP FOREIGN KEY `fk_tm_activity_tm_organisation` ;

ALTER TABLE `harley2009`.`tm_activity` 
  ADD CONSTRAINT `fk_tm_activity_ta_activity_type`
  FOREIGN KEY (`idta_activity_type` )
  REFERENCES `db241933781`.`ta_activity_type` (`idta_activity_type` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_tm_activity_tm_organisation`
  FOREIGN KEY (`idtm_organisation` )
  REFERENCES `db241933781`.`tm_organisation` (`idtm_organisation` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `harley2009`.`tt_stammdaten` CHANGE COLUMN `idtt_stammdaten` `idtt_stammdaten` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`tt_user_felder` CHANGE COLUMN `idtt_user_felder` `idtt_user_felder` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `harley2009`.`tt_user_log` CHANGE COLUMN `idtt_user_log` `idtt_user_log` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;


-- -----------------------------------------------------
-- Placeholder table for view `db241933781`.`vv_activity_participants`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db241933781`.`vv_activity_participants` (`idtm_activity` INT, `idtm_organisation` INT, `idtm_activity_participant` INT, `act_part_anwesend` INT, `act_part_notiz` INT, `org_name` INT, `user_role_name` INT);

-- -----------------------------------------------------
-- Placeholder table for view `db241933781`.`vv_activity_ziele`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db241933781`.`vv_activity_ziele` (`idtt_ziele` INT, `idtm_activity` INT, `idtm_activity_has_tt_ziele` INT, `ttzie_name` INT);

-- -----------------------------------------------------
-- Placeholder table for view `db241933781`.`vv_aufgabe_ressource`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db241933781`.`vv_aufgabe_ressource` (`idtm_aufgabe_ressource` INT, `idtm_aufgabe` INT, `idtm_ressource` INT, `auf_res_dauer` INT, `res_name` INT);

-- -----------------------------------------------------
-- Placeholder table for view `db241933781`.`vv_collector_feldfunktion`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db241933781`.`vv_collector_feldfunktion` (`idta_collector` INT, `idta_feldfunktion` INT, `col_idtafeldfunktion` INT, `col_operator` INT, `ff_name` INT);

-- -----------------------------------------------------
-- Placeholder table for view `db241933781`.`vv_mapping_stammdaten`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db241933781`.`vv_mapping_stammdaten` (`stammdaten_group_name` INT, `group_stammdaten_name` INT, `detail_stammdaten_name` INT);

-- -----------------------------------------------------
-- Placeholder table for view `db241933781`.`vv_struktur`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db241933781`.`vv_struktur` (`idtm_struktur` INT, `parent_idtm_struktur` INT, `struktur_name` INT, `idta_struktur_type` INT);

-- -----------------------------------------------------
-- Placeholder table for view `db241933781`.`vv_termin_ressource_organisation`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db241933781`.`vv_termin_ressource_organisation` (`idtm_termin` INT, `ter_startdate` INT, `ter_enddate` INT, `ter_starttime` INT, `ter_endtime` INT, `idtm_organisation` INT, `idtm_ressource` INT);


USE db241933781;

-- -----------------------------------------------------
-- View `db241933781`.`vv_activity_participants`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db241933781`.`vv_activity_participants`;
USE db241933781;
CREATE  OR REPLACE VIEW `db241933781`.`vv_activity_participants` AS
SELECT tm_activity_participants.*,tm_organisation.org_name,tm_user_role.user_role_name FROM tm_activity_participants INNER JOIN tm_organisation ON tm_organisation.idtm_organisation = tm_activity_participants.idtm_organisation
INNER JOIN tm_user_role ON tm_user_role.idtm_user_role = tm_organisation.org_idtm_user_role;


USE db241933781;

-- -----------------------------------------------------
-- View `db241933781`.`vv_activity_ziele`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db241933781`.`vv_activity_ziele`;
USE db241933781;
CREATE  OR REPLACE VIEW `db241933781`.`vv_activity_ziele` AS
SELECT a.*, b.ttzie_name FROM tm_activity_has_tt_ziele a INNER JOIN tt_ziele b ON a.idtt_ziele = b.idtt_ziele;


USE db241933781;

-- -----------------------------------------------------
-- View `db241933781`.`vv_aufgabe_ressource`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db241933781`.`vv_aufgabe_ressource`;
USE db241933781;
CREATE  OR REPLACE VIEW `db241933781`.`vv_aufgabe_ressource` AS
SELECT tm_aufgabe_ressource.*,tm_ressource.res_name FROM tm_aufgabe_ressource INNER JOIN tm_ressource ON tm_ressource.idtm_ressource = tm_aufgabe_ressource.idtm_ressource;


USE db241933781;

-- -----------------------------------------------------
-- View `db241933781`.`vv_collector_feldfunktion`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db241933781`.`vv_collector_feldfunktion`;
USE db241933781;
CREATE  OR REPLACE VIEW `db241933781`.`vv_collector_feldfunktion` AS
SELECT a.*, b.ff_name FROM ta_collector a INNER JOIN ta_feldfunktion b ON a.col_idtafeldfunktion = b.idta_feldfunktion;


USE db241933781;

-- -----------------------------------------------------
-- View `db241933781`.`vv_mapping_stammdaten`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db241933781`.`vv_mapping_stammdaten`;
USE db241933781;
CREATE  OR REPLACE VIEW `db241933781`.`vv_mapping_stammdaten` AS
select stammdaten_group_name AS stammdaten_group_name ,b.stammdaten_name AS group_stammdaten_name,a.stammdaten_name AS detail_stammdaten_name from tt_stammdaten_stammdaten inner join tm_stammdaten a on a.idtm_stammdaten=tt_stammdaten_stammdaten.idtm_stammdaten
inner join tm_stammdaten b on b.idtm_stammdaten=tt_stammdaten_stammdaten.idtm_stammdaten_group inner join ta_stammdaten_group on ta_stammdaten_group.idta_stammdaten_group = b.idta_stammdaten_group;


USE db241933781;

-- -----------------------------------------------------
-- View `db241933781`.`vv_struktur`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db241933781`.`vv_struktur`;
USE db241933781;
CREATE  OR REPLACE VIEW `db241933781`.`vv_struktur` AS
SELECT idtm_struktur,parent_idtm_struktur,struktur_name,idta_struktur_type FROM tm_struktur ORDER BY idta_struktur_type, struktur_name;;


USE db241933781;

-- -----------------------------------------------------
-- View `db241933781`.`vv_termin_ressource_organisation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `db241933781`.`vv_termin_ressource_organisation`;
USE db241933781;
CREATE  OR REPLACE VIEW `db241933781`.`vv_termin_ressource_organisation` AS
SELECT tm_termin.idtm_termin, ter_startdate, ter_enddate, ter_starttime, ter_endtime, idtm_organisation, idtm_ressource 
FROM `tm_termin` 
INNER JOIN tm_termin_organisation ON tm_termin.idtm_termin = tm_termin_organisation.idtm_termin 
INNER JOIN tm_termin_ressource ON tm_termin.idtm_termin = tm_termin_ressource.idtm_termin
;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
