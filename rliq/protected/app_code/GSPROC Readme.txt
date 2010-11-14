*** Programming Rules

in new mode, every database field has an element, named as in the database

//CODE
	$warenRecord = new WarenRecord;
	$fields = array("waren_artikelnummer","waren_ean","waren_bezeichnung","waren_menge","waren_gewicht","waren_preis","waren_dat_lb","waren_typ","idtm_waren_kategorie","idtm_preis_kategorie","idta_adresse");
	foreach ($fields as $recordfield){
			$warenRecord->$recordfield = $this->$recordfield->Text;
	}
	$warenRecord->save();
	$this->Response->redirect($this->getRequest()->constructUrl('page',"logik.partei"));
		
//CODE END

for each bindList, if we add the function sorting, and then the event page changes, all lists need to be reloaded at the end

//CODE
	$this->bindListNo1();
	$this->bindListNo2();
	aso.
//CODE END

for the editing of a view, we implement the following code for saving the data...

//CODE

		$warenRecord = WarenRecord::finder()->findByPK($this->edidta_waren->Data);
	
		$fields = array("waren_artikelnummer","waren_ean","waren_bezeichnung","waren_menge","waren_gewicht","waren_preis","waren_dat_lb","waren_typ","idtm_waren_kategorie","idtm_preis_kategorie","idta_adresse");
		
		foreach ($fields as $recordfield){
			$edrecordfield = 'ed'.$recordfield;
			$warenRecord->$recordfield = $this->$edrecordfield->Text;
		}

		$warenRecord->save();
			
		$this->Response->redirect($this->getRequest()->constructUrl('page',"logik.partei"));
		
//CODE END

for filling the values into an existing form, we build the current code

//CODE


*** TODO-List

-> Waren: The Date of the delivery shouldn�t be changeable because other parts of the application link to it


*** Settings Server

 MySQL-Datenbank erstellt
Die Daten Ihrer MySQL-Datenbank entnehmen Sie bitte der unteren �bersicht.
Datenbankname 	db247494299
Hostname 	db1505.1und1.de
Port 	3306 (Standardport)
Benutzername 	dbo247494299
Passwort 	Vtbj3vw7
Beschreibung 	market4energy
Version 	MySQL5.0
Status 	Die Einrichtung wird gestartet.
