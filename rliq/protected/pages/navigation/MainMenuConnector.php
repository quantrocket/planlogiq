<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class MainMenuConnector extends TPage{

    public function onPreInit($param) {
        parent::onPreInit($param);

        $docname = "tempXML";
        $ext = "xml";
        $header = "application/xml";
        
        $doc=new TXmlDocument('1.0','ISO-8859-1');
        $doc->TagName='menu';
        $doc->setAttribute('id',"0");

        $QVFile=new TXmlElement('item');
        $QVFile->setAttribute('id',"Main_App");
        $QVFile->setAttribute('img',"help_book.gif");
        $QVFile->setAttribute('text',"planlogIQ");

        $Home=new TXmlElement('item');
        $Home->setAttribute('id','Home');
        $Home->setAttribute('img','xpMyComp.gif');
        $Home->setAttribute('text',utf8_encode("Startpage"));
        $QVFile->Elements[]=$Home;

        if(!$this->User->isGuest){
            $mod_user=new TXmlElement('item');
            $mod_user->setAttribute('id','mod_user');
            $mod_user->setAttribute('img','org5.gif');
            $mod_user->setAttribute('text',utf8_encode("Mein Konto"));

            $ComMyAccount=new TXmlElement('item');
            $ComMyAccount->setAttribute('id','user.myaccount');
            $ComMyAccount->setAttribute('img','oJornal.gif');
            $ComMyAccount->setAttribute('text',utf8_encode("Mein Konto"));
            $mod_user->Elements[]=$ComMyAccount;

            if($this->User->isInRole('Administrator')){
                $ComADMyAccount=new TXmlElement('item');
                $ComADMyAccount->setAttribute('id','user.user');
                $ComADMyAccount->setAttribute('img','org1.gif');
                $ComADMyAccount->setAttribute('text',utf8_encode("Alle Benutzer"));
                $mod_user->Elements[]=$ComADMyAccount;
            }

            $QVFile->Elements[]=$mod_user;
        }
        
        $SepMan=new TXmlElement('item');
        $SepMan->setAttribute('id','sep_modMan');
        $SepMan->setAttribute('type','seperator');
        $QVFile->Elements[]=$SepMan;

        $Manual=new TXmlElement('item');
        $Manual->setAttribute('id','Handbuch');
        $Manual->setAttribute('img','memobook.gif');
        $Manual->setAttribute('text',utf8_encode("Handbuch"));
            $ManualLink=new TXmlElement('href');
            $ManualLink->setAttribute('target','_blank');
            $ManualLink->setValue('http://wiki.planlogiq.com');
            $Manual->Elements[]=$ManualLink;
        $QVFile->Elements[]=$Manual;

        $ST=new TXmlElement('item');
        $ST->setAttribute('id','user.logoutuser');
        $ST->setAttribute('img','radio_off.gif');
        $ST->setAttribute('text',utf8_encode("Logout"));
        $QVFile->Elements[]=$ST;
        $doc->Elements[]=$QVFile;

        if(!$this->User->isGuest){

            if($this->User->getModulRights('mod_organisation')){
                $ModOrganisation=new TXmlElement('item');
                $ModOrganisation->setAttribute('id',"mod_organisation");
                $ModOrganisation->setAttribute('img',"org6.gif");
                $ModOrganisation->setAttribute('text',"Organisation");

                $ComOrganisation=new TXmlElement('item');
                $ComOrganisation->setAttribute('id','organisation.orgworkspace');
                $ComOrganisation->setAttribute('img','org6.gif');
                $ComOrganisation->setAttribute('text',utf8_encode("Organisation"));
                $ModOrganisation->Elements[]=$ComOrganisation;

                $ComTermine=new TXmlElement('item');
                $ComTermine->setAttribute('id','termin.terworkspace');
                $ComTermine->setAttribute('img','oJornal.gif');
                $ComTermine->setAttribute('text',utf8_encode("Termine"));
                $ModOrganisation->Elements[]=$ComTermine;

                if($this->User->getModulRights('mod_zeiterfassung')){
                    $ComZeiterfassung=new TXmlElement('item');
                    $ComZeiterfassung->setAttribute('id','organisation.zeiterfassung');
                    $ComZeiterfassung->setAttribute('img','oJornal.gif');
                    $ComZeiterfassung->setAttribute('text',utf8_encode("Zeiterfassung"));
                    $ModOrganisation->Elements[]=$ComZeiterfassung;

                    if($this->User->getModulRights('mod_zeiterfassung_reports')){
                        $SepRepZeit=new TXmlElement('item');
                        $SepRepZeit->setAttribute('id','sep_modRepZeit');
                        $SepRepZeit->setAttribute('type','seperator');
                        $ModOrganisation->Elements[]=$SepRepZeit;

                        $ComZeiterfassungRepMB=new TXmlElement('item');
                        $ComZeiterfassungRepMB->setAttribute('id','reports.zeiterfassung.a_Zeiterfassung_Mitarbeiter');
                        $ComZeiterfassungRepMB->setAttribute('img','org7.gif');
                        $ComZeiterfassungRepMB->setAttribute('text',utf8_encode("REP: Zeiterfassung MB"));
                        $ModOrganisation->Elements[]=$ComZeiterfassungRepMB;
                        
                    }
                }

                $ComOrgBelegung=new TXmlElement('item');
                $ComOrgBelegung->setAttribute('id','organisation.organisationbelegung');
                $ComOrgBelegung->setAttribute('img','oJornal.gif');
                $ComOrgBelegung->setAttribute('text',utf8_encode("Organisation Belegung"));
                $ModOrganisation->Elements[]=$ComOrgBelegung;

                if($this->User->getModulRights('mod_process')){
                    $ModProzess=new TXmlElement('item');
                    $ModProzess->setAttribute('id','mod_process');
                    $ModProzess->setAttribute('img','oJornal.gif');
                    $ModProzess->setAttribute('text',utf8_encode("Prozesse"));

                        $ComProzess=new TXmlElement('item');
                        $ComProzess->setAttribute('id','prozess.proworkspace');
                        $ComProzess->setAttribute('img','org7.gif');
                        $ComProzess->setAttribute('text',utf8_encode("Definition"));
                        $ModProzess->Elements[]=$ComProzess;
                    
                    $ModOrganisation->Elements[]=$ModProzess;
                }

                if($this->User->isInRole('Administrator')){
                    $SepImporter=new TXmlElement('item');
                    $SepImporter->setAttribute('id','sep_modImporter');
                    $SepImporter->setAttribute('type','seperator');
                    $ModOrganisation->Elements[]=$SepImporter;

                    $ModImporter=new TXmlElement('item');
                    $ModImporter->setAttribute('id','mod_importer');
                    $ModImporter->setAttribute('img','oOutlook.gif');
                    $ModImporter->setAttribute('text',utf8_encode("Import"));
                    $ModOrganisation->Elements[]=$ModImporter;

                    $ComImportImmo=new TXmlElement('item');
                    $ComImportImmo->setAttribute('id','importer.ImportImmo');
                    $ComImportImmo->setAttribute('img','oOutlook.gif');
                    $ComImportImmo->setAttribute('text',utf8_encode("Import NPF"));
                    $ModImporter->Elements[]=$ComImportImmo;

                    $ComImportHTC=new TXmlElement('item');
                    $ComImportHTC->setAttribute('id','importer.ImportHTC');
                    $ComImportHTC->setAttribute('img','oOutlook.gif');
                    $ComImportHTC->setAttribute('text',utf8_encode("Import HTC"));
                    $ModImporter->Elements[]=$ComImportHTC;

                    $ComImportTasks=new TXmlElement('item');
                    $ComImportTasks->setAttribute('id','importer.ImportTasks');
                    $ComImportTasks->setAttribute('img','oOutlook.gif');
                    $ComImportTasks->setAttribute('text',utf8_encode("Import Tasks"));
                    $ModImporter->Elements[]=$ComImportTasks;
                }

                $doc->Elements[]=$ModOrganisation;
            }

            if($this->User->getModulRights('mod_risiko')){
                $ModRisiko=new TXmlElement('item');
                $ModRisiko->setAttribute('id',"mod_risiko");
                $ModRisiko->setAttribute('img',"org1.gif");
                $ModRisiko->setAttribute('text',"Risiken");

                    $ComRisikoarten=new TXmlElement('item');
                    $ComRisikoarten->setAttribute('id','risiko.risworkspace');
                    $ComRisikoarten->setAttribute('img','oOutlook.gif');
                    $ComRisikoarten->setAttribute('text',utf8_encode("Risikoarten"));
                    $ModRisiko->Elements[]=$ComRisikoarten;

                $doc->Elements[]=$ModRisiko;
            }

            if($this->User->getModulRights('mod_activity')){
                $ModActivity=new TXmlElement('item');
                $ModActivity->setAttribute('id',"mod_activity");
                $ModActivity->setAttribute('img',"oInboxF.gif");
                $ModActivity->setAttribute('text',"Projektmanagement");

                $ComActivity=new TXmlElement('item');
                $ComActivity->setAttribute('id','activity.actworkspace');
                $ComActivity->setAttribute('img','BookY.gif');
                $ComActivity->setAttribute('text',utf8_encode("Projektstruktur"));
                $ModActivity->Elements[]=$ComActivity;

                $ComZiele=new TXmlElement('item');
                $ComZiele->setAttribute('id','ziele.zieworkspace');
                $ComZiele->setAttribute('img','watch.gif');
                $ComZiele->setAttribute('text',utf8_encode("Ziele"));
                $ModActivity->Elements[]=$ComZiele;                

                $ComNetplan=new TXmlElement('item');
                $ComNetplan->setAttribute('id','activity.actlistview');
                $ComNetplan->setAttribute('img','BookY.gif');
                $ComNetplan->setAttribute('text',utf8_encode("Netzplan"));
                $ModActivity->Elements[]=$ComNetplan;

                $ComTimeplan=new TXmlElement('item');
                $ComTimeplan->setAttribute('id','activity.actterminlistview');
                $ComTimeplan->setAttribute('img','watch.gif');
                $ComTimeplan->setAttribute('text',utf8_encode("Zeitplan"));
                $ModActivity->Elements[]=$ComTimeplan;                                                

                $SepRess=new TXmlElement('item');
                $SepRess->setAttribute('id','sep_comRess');
                $SepRess->setAttribute('type','seperator');
                $ModActivity->Elements[]=$SepRess;

                $ComRess=new TXmlElement('item');
                $ComRess->setAttribute('id','organisation.ressourcenworkspace');
                $ComRess->setAttribute('img','org4.gif');
                $ComRess->setAttribute('text',utf8_encode("Ressourcen"));
                $ModActivity->Elements[]=$ComRess;

                $ComRessPlan=new TXmlElement('item');
                $ComRessPlan->setAttribute('id','organisation.ressourcenbelegung');
                $ComRessPlan->setAttribute('img','org5.gif');
                $ComRessPlan->setAttribute('text',utf8_encode("Ressourcen Plan"));
                $ModActivity->Elements[]=$ComRessPlan;

                $doc->Elements[]=$ModActivity;
            }

            if($this->User->getModulRights('mod_protokoll')){

                $ModProtokoll=new TXmlElement('item');
                $ModProtokoll->setAttribute('id',"mod_protokoll");
                $ModProtokoll->setAttribute('img',"org6.gif");
                $ModProtokoll->setAttribute('text',"Dokumentation");

                $ComProtokoll=new TXmlElement('item');
                $ComProtokoll->setAttribute('id','protokoll.prtworkspace');
                $ComProtokoll->setAttribute('img','book.gif');
                $ComProtokoll->setAttribute('text',utf8_encode("Protokolle"));
                $ModProtokoll->Elements[]=$ComProtokoll;

                $ComChangeManagement=new TXmlElement('item');
                $ComChangeManagement->setAttribute('id','changemanagement.rfcworkspace');
                $ComChangeManagement->setAttribute('img','book.gif');
                $ComChangeManagement->setAttribute('text',utf8_encode("Changemanagement"));
                $ModProtokoll->Elements[]=$ComChangeManagement;

                $doc->Elements[]=$ModProtokoll;
            }

            if($this->User->getModulRights('mod_planung')){
                $ModPlanung=new TXmlElement('item');
                $ModPlanung->setAttribute('id',"mod_planung");
                $ModPlanung->setAttribute('img',"oDrafts.gif");
                $ModPlanung->setAttribute('text',"Planung");

                $ComPlanung=new TXmlElement('item');
                $ComPlanung->setAttribute('id','reports.StrukturBerichtViewer');
                $ComPlanung->setAttribute('img','oDrafts.gif');
                $ComPlanung->setAttribute('text',utf8_encode("Planung"));
                $ModPlanung->Elements[]=$ComPlanung;
                                

                if($this->User->isInRole('Administrator')){

                    $SepPlaOne=new TXmlElement('item');
                    $SepPlaOne->setAttribute('id','sep_plaone');
                    $SepPlaOne->setAttribute('type','seperator');
                    $ModPlanung->Elements[]=$SepPlaOne;

                    $ModPlanungAdmin=new TXmlElement('item');
                    $ModPlanungAdmin->setAttribute('id',"mod_planung_admin");
                    $ModPlanungAdmin->setAttribute('img',"tree.gif");
                    $ModPlanungAdmin->setAttribute('text',"Administration");
                    $ModPlanung->Elements[]=$ModPlanungAdmin;

                        $ComDimensionen=new TXmlElement('item');
                        $ComDimensionen->setAttribute('id','struktur.dimmappingview');
                        $ComDimensionen->setAttribute('img','org7.gif');
                        $ComDimensionen->setAttribute('text',utf8_encode("Dimensionsmanager"));
                        $ModPlanungAdmin->Elements[]=$ComDimensionen;

                        $ComStrtypen=new TXmlElement('item');
                        $ComStrtypen->setAttribute('id','struktur.strukturtypen');
                        $ComStrtypen->setAttribute('img','org6.gif');
                        $ComStrtypen->setAttribute('text',utf8_encode("Strukturtypen"));
                        $ModPlanungAdmin->Elements[]=$ComStrtypen;

                        $ComSplash=new TXmlElement('item');
                        $ComSplash->setAttribute('id','struktur.splasherworkspace');
                        $ComSplash->setAttribute('img','org7.gif');
                        $ComSplash->setAttribute('text',utf8_encode("Splashing"));
                        $ModPlanungAdmin->Elements[]=$ComSplash;

                    $SepPlaTree=new TXmlElement('item');
                    $SepPlaTree->setAttribute('id','sep_platree');
                    $SepPlaTree->setAttribute('type','seperator');
                    $ModPlanung->Elements[]=$SepPlaTree;

                    $ComStrElemente=new TXmlElement('item');
                    $ComStrElemente->setAttribute('id','struktur.strworkspace');
                    $ComStrElemente->setAttribute('img','org1.gif');
                    $ComStrElemente->setAttribute('text',utf8_encode("Struktur manuell"));
                    $ModPlanung->Elements[]=$ComStrElemente;

                    $ComBerWorkspace=new TXmlElement('item');
                    $ComBerWorkspace->setAttribute('id','protokoll.berichteworkspace');
                    $ComBerWorkspace->setAttribute('img','book.gif');
                    $ComBerWorkspace->setAttribute('text',utf8_encode("Berichtsmanager"));
                    $ModPlanung->Elements[]=$ComBerWorkspace;

                    $SepPlaTwo=new TXmlElement('item');
                    $SepPlaTwo->setAttribute('id','sep_platwo');
                    $SepPlaTwo->setAttribute('type','seperator');
                    $ModPlanung->Elements[]=$SepPlaTwo;

                    $ModStrImporter=new TXmlElement('item');
                    $ModStrImporter->setAttribute('id','mod_strimporter');
                    $ModStrImporter->setAttribute('img','oOutlook.gif');
                    $ModStrImporter->setAttribute('text',utf8_encode("Import"));
                    $ModPlanung->Elements[]=$ModStrImporter;

                    $ComImportStruktur=new TXmlElement('item');
                    $ComImportStruktur->setAttribute('id','importer.importerworkspace');
                    $ComImportStruktur->setAttribute('img','oOutlook.gif');
                    $ComImportStruktur->setAttribute('text',utf8_encode("Import Saldenliste"));
                    $ModStrImporter->Elements[]=$ComImportStruktur;

                    $ComImportDimStruktur=new TXmlElement('item');
                    $ComImportDimStruktur->setAttribute('id','importer.importerworkspacedim');
                    $ComImportDimStruktur->setAttribute('img','oOutlook.gif');
                    $ComImportDimStruktur->setAttribute('text',utf8_encode("Import 2 Dimensionen"));
                    $ModStrImporter->Elements[]=$ComImportDimStruktur;
                }

                $ComPivots=new TXmlElement('item');
                $ComPivots->setAttribute('id','struktur.pivotworkspace');
                $ComPivots->setAttribute('img','org1.gif');
                $ComPivots->setAttribute('text',utf8_encode("Pivotberichte (beta)"));
                $ModPlanung->Elements[]=$ComPivots;

                $doc->Elements[]=$ModPlanung;
            }
        }

        //hier muss die logik fuer die basiswerte aus den dimensionen hin...
        //hier hole ich mir die Dimensionsgruppen

        $docName = "temp";

        $this->getResponse()->appendHeader("Content-Type:".$header);
        $this->getResponse()->appendHeader("Content-Disposition:inline;filename=".$docName.'.'.$ext);
       
        $doc->saveToFile('php://output');
        exit;
    }

}

?>