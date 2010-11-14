SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE  TABLE IF NOT EXISTS `db241933781`.`ta_bankkonto` (
  `idta_bankkonto` INT(11) NOT NULL ,
  `bak_kontowortlaut` VARCHAR(45) NULL DEFAULT NULL ,
  `bak_geldinstitut` VARCHAR(45) NULL DEFAULT NULL ,
  `bak_blz` VARCHAR(10) NULL DEFAULT NULL ,
  `bak_konto` VARCHAR(12) NULL DEFAULT NULL ,
  `bak_bic` VARCHAR(20) NULL DEFAULT NULL ,
  `bak_iban` VARCHAR(30) NULL DEFAULT NULL ,
  `idtm_organisation` INT(11) NULL DEFAULT NULL ,
  `bak_ismain` TINYINT(1) NULL DEFAULT NULL ,
  PRIMARY KEY (`idta_bankkonto`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin
COMMENT = 'Alle Informationen zum Bankkonto';

CREATE  TABLE IF NOT EXISTS `db241933781`.`ta_objekt` (
  `idta_objekt` INT(11) NOT NULL ,
  `idtm_organisation` INT(11) NULL DEFAULT NULL ,
  `obj_nutzflaeche` FLOAT NULL DEFAULT NULL ,
  `obj_nutzflaeche_date` DATE NULL DEFAULT NULL ,
  `obj_gbanteile` INT(11) NULL DEFAULT NULL ,
  `obj_gbanteile_date` DATE NULL DEFAULT NULL ,
  PRIMARY KEY (`idta_objekt`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin
COMMENT = 'Alles mit dem Objekt';

CREATE  TABLE IF NOT EXISTS `db241933781`.`tm_organisation_has_ta_adresse` (
  `idtm_organisation_has_ta_adresse` INT(11) NOT NULL ,
  `idtm_organisation` INT(11) NULL DEFAULT NULL ,
  `idta_adresse` INT(11) NULL DEFAULT NULL ,
  PRIMARY KEY (`idtm_organisation_has_ta_adresse`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin
COMMENT = 'The link between the organisation and the adress';

DROP TABLE IF EXISTS `db241933781`.`pradocache` ;

ALTER TABLE `db241933781`.`catalogue` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`qs_comments` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_activity_activity` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_activity_type` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_automapping` CHARACTER SET = utf8 , COLLATE = utf8_bin , CHANGE COLUMN `idta_automapping` `idta_automapping` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `db241933781`.`ta_berichte` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_bericht_struktur_type` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_bericht_struktur` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_bericht_type` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_collector` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_contentcontainer_type` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_contentcontainer` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_einheit` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_feldfunktion` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_fortschreibung` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_fracht` CHARACTER SET = utf8 , COLLATE = utf8_bin , DROP FOREIGN KEY `fk_idtm_fahrzeug_kategorie` ;

ALTER TABLE `db241933781`.`ta_fracht` 
  ADD CONSTRAINT `fk_idtm_fahrzeug_kategorie`
  FOREIGN KEY (`idtm_fahrzeug_kategorie` )
  REFERENCES `db241933781`.`tm_fahrzeug_kategorie` (`idtm_fahrzeug_kategorie` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `db241933781`.`ta_inoutput_type` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_kosten_status` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_partei_has_ta_adresse` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_partei` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_perioden` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_pivot_bericht` CHARACTER SET = utf8 , COLLATE = utf8_bin , CHANGE COLUMN `idta_pivot_bericht` `idta_pivot_bericht` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `db241933781`.`ta_protokoll_detail_group` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_protokoll_ergebnistype` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_protokoll_type` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_prozess_type` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_rescalendar` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_ressource_type` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_risiko_type` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_saisonalisierung` CHARACTER SET = utf8 , COLLATE = utf8_bin , CHANGE COLUMN `idta_saisonalisierung` `idta_saisonalisierung` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `db241933781`.`ta_sbs_collector` CHARACTER SET = utf8 , COLLATE = utf8_bin , CHANGE COLUMN `idta_sbs_collector` `idta_sbs_collector` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `db241933781`.`ta_sbz_collector` CHARACTER SET = utf8 , COLLATE = utf8_bin , CHANGE COLUMN `idta_sbz_collector` `idta_sbz_collector` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `db241933781`.`ta_stammdaten_group` CHARACTER SET = utf8 , COLLATE = utf8_bin , CHANGE COLUMN `idta_stammdaten_group` `idta_stammdaten_group` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `db241933781`.`ta_struktur_bericht_spalten` CHARACTER SET = utf8 , COLLATE = utf8_bin , CHANGE COLUMN `idta_struktur_bericht_spalten` `idta_struktur_bericht_spalten` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `db241933781`.`ta_struktur_bericht_zeilen` CHARACTER SET = utf8 , COLLATE = utf8_bin , CHANGE COLUMN `idta_struktur_bericht_zeilen` `idta_struktur_bericht_zeilen` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `db241933781`.`ta_struktur_bericht` CHARACTER SET = utf8 , COLLATE = utf8_bin , CHANGE COLUMN `idta_struktur_bericht` `idta_struktur_bericht` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `db241933781`.`ta_termin_type` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_waren_has_ta_partei` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_waren` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_activity_has_tm_organisation` CHARACTER SET = utf8 , COLLATE = utf8_bin , CHANGE COLUMN `idtm_activity_has_tm_organisation` `idtm_activity_has_tm_organisation` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `db241933781`.`tm_activity_has_tt_ziele` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_aufgaben` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_aufgabe_ressource` CHARACTER SET = utf8 , COLLATE = utf8_bin , CHANGE COLUMN `idtm_aufgabe_ressource` `idtm_aufgabe_ressource` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `db241933781`.`tm_berichte_has_organisation` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_changerequest` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_country` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_fahrtenbuch` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_fahrzeug_kategorie` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_importmapping` CHARACTER SET = utf8 , COLLATE = utf8_bin , CHANGE COLUMN `idtm_importmapping` `idtm_importmapping` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `db241933781`.`tm_inoutput_has_tm_activity` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_inoutput` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_organisation` CHARACTER SET = utf8 , COLLATE = utf8_bin , ADD COLUMN `org_anrede` VARCHAR(15) NULL DEFAULT NULL  AFTER `org_ntuser` , ADD COLUMN `org_baujahr` INT(4) NULL DEFAULT NULL  AFTER `org_anrede` , ADD COLUMN `org_briefanrede` VARCHAR(45) NULL DEFAULT NULL  AFTER `org_anrede` , ADD COLUMN `org_einlagezahl` VARCHAR(45) NULL DEFAULT NULL  AFTER `org_briefanrede` , ADD COLUMN `org_finanzamt` VARCHAR(45) NULL DEFAULT NULL  AFTER `org_briefanrede` , ADD COLUMN `org_gemeinde` VARCHAR(45) NULL DEFAULT NULL  AFTER `org_finanzamt` , ADD COLUMN `org_grundstuecksnummer` VARCHAR(45) NULL DEFAULT NULL  AFTER `org_gemeinde` , ADD COLUMN `org_katastragemeinde` VARCHAR(45) NULL DEFAULT NULL  AFTER `org_gemeinde` , ADD COLUMN `org_matchkey` VARCHAR(45) NULL DEFAULT NULL  AFTER `org_briefanrede` , ADD COLUMN `org_name1` VARCHAR(45) NULL DEFAULT NULL  AFTER `org_ntuser` , ADD COLUMN `org_name2` VARCHAR(45) NULL DEFAULT NULL  AFTER `org_name1` , ADD COLUMN `org_referat` VARCHAR(10) NULL DEFAULT NULL  AFTER `org_finanzamt` , ADD COLUMN `org_steuernummer` VARCHAR(45) NULL DEFAULT NULL  AFTER `org_finanzamt` , ADD COLUMN `org_uid` VARCHAR(15) NULL DEFAULT NULL  AFTER `org_matchkey` , ADD COLUMN `org_vorname` VARCHAR(45) NULL DEFAULT NULL  AFTER `org_briefanrede` , ADD COLUMN `org_wohnungen` INT(4) NULL DEFAULT NULL  AFTER `org_baujahr` ;

ALTER TABLE `db241933781`.`tm_pivot` CHARACTER SET = utf8 , COLLATE = utf8_bin , CHANGE COLUMN `idtm_pivot` `idtm_pivot` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `db241933781`.`tm_preis_kategorie` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_prozess_step` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_prozess` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_rcvalue` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_ressource` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_risiko` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_stammdaten` CHARACTER SET = utf8 , COLLATE = utf8_bin , CHANGE COLUMN `idtm_stammdaten` `idtm_stammdaten` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `db241933781`.`tm_struktur_has_ta_stammdaten_group` CHARACTER SET = utf8 , COLLATE = utf8_bin , CHANGE COLUMN `idtm_struktur_has_ta_stammdaten_group` `idtm_struktur_has_ta_stammdaten_group` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `db241933781`.`tm_struktur_tm_struktur` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_struktur` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_tempimport` CHARACTER SET = utf8 , COLLATE = utf8_bin , CHANGE COLUMN `idtm_tempimport` `idtm_tempimport` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `db241933781`.`tm_termin_ressource` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_user_role` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_user` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_waren_kategorie` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_zeiterfassung` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_ziele` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`trans_unit` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tt_rcvalue_netto` CHARACTER SET = utf8 , COLLATE = utf8_bin , DROP FOREIGN KEY `fk_tm_rcvalue_tt_rcvalue_netto` ;

ALTER TABLE `db241933781`.`tt_rcvalue_netto` 
  ADD CONSTRAINT `fk_tm_rcvalue_tt_rcvalue_netto`
  FOREIGN KEY (`idtm_rcvalue` )
  REFERENCES `db241933781`.`tm_rcvalue` (`idtm_rcvalue` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `db241933781`.`tt_rcvalue` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tt_saisonalisierung` CHARACTER SET = utf8 , COLLATE = utf8_bin , CHANGE COLUMN `idtt_saisonalisierung` `idtt_saisonalisierung` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `db241933781`.`tt_stammdaten_stammdaten` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tt_werte` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tt_workflow` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tt_ziele` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`xx_berechtigung` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_adresse` CHARACTER SET = utf8 , COLLATE = utf8_bin , ADD COLUMN `adresse_ismain` TINYINT(1) NULL DEFAULT 0  AFTER `adresse_long` ;

ALTER TABLE `db241933781`.`ta_fracht_teilladung` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_organisation_type` CHARACTER SET = utf8 , COLLATE = utf8_bin , ADD COLUMN `org_type_monat_jahr` DATE NULL DEFAULT NULL  AFTER `org_type_name` ;

ALTER TABLE `db241933781`.`ta_struktur_type` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_variante` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`ta_ziele_type` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_activity` CHARACTER SET = utf8 , COLLATE = utf8_bin , DROP FOREIGN KEY `fk_tm_activity_ta_activity_type` , DROP FOREIGN KEY `fk_tm_activity_tm_organisation` ;

ALTER TABLE `db241933781`.`tm_activity` 
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

ALTER TABLE `db241933781`.`tm_activity_participants` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_protokoll` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_protokoll_detail` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_termin` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_termin_organisation` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_verteiler` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tm_verteiler_organisation` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tt_container_text` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tt_message` CHARACTER SET = utf8 , COLLATE = utf8_bin ;

ALTER TABLE `db241933781`.`tt_stammdaten` CHARACTER SET = utf8 , COLLATE = utf8_bin , CHANGE COLUMN `idtt_stammdaten` `idtt_stammdaten` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `db241933781`.`tt_user_felder` CHARACTER SET = utf8 , COLLATE = utf8_bin , CHANGE COLUMN `idtt_user_felder` `idtt_user_felder` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;

ALTER TABLE `db241933781`.`tt_user_log` CHARACTER SET = utf8 , COLLATE = utf8_bin , CHANGE COLUMN `idtt_user_log` `idtt_user_log` INT(11) NULL DEFAULT NULL AUTO_INCREMENT  ;


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
