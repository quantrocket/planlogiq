<?php

class berichteworkspace extends TPage
{

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

        //the fields for the BerichteRecord
        private $BRprimarykey = "idta_berichte";
	private $BRfields = array("ber_name","ber_descr","idta_bericht_type","idtm_user","idtm_organisation","ber_id","ber_local_path","ber_mail_subject","ber_mail_body","ber_zyklus","ber_zyklus_start","ber_production_time");
	private $BRdatfields = array();
	private $BRtimefields = array("ber_zyklus_time");
	private $BRcheckboxfields = array("ber_zyklus_gap");
	private $BRhiddenfields = array();
	private $BRboolfields = array();

        //the fields for the BerichteRecord
        private $BORprimarykey = "idtm_berichte_has_organisation";
	private $BORfields = array("idtm_organisation","idta_berichte","bho_modul","bho_id");
	private $BORdatfields = array();
	private $BORtimefields = array();
	private $BORhiddenfields = array();
	private $BORboolfields = array();
	

	public function onLoad($param){
		
                parent::onLoad($param);
		
		if(!$this->isPostBack && !$this->isCallback){
                        $this->bindDropDown();
			$this->loadBerichte();
		}
	}

        public function getLogger($sender,$param){
            $SOAPClient = new QVHPMailer();
            $item = $param->Item;
            $data = $SOAPClient->checkStatus($item->lst_idta_berichte->Text);
            try{
                $listData = array();
                foreach($data->checkStatusResult->StatusListe AS $value){
                    foreach($value AS $item){
                        $listData[] = $item;
                    }
                }
            }catch(Exception $e){
                 echo 'Exception abgefangen: '. $e->getMessage(). "\n";
            }
            $this->QVMailerLogger->DataSource = $listData;
            $this->QVMailerLogger->dataBind();
        }

        public function startSOAPClient($sender,$param){
            $SOAPClient = new QVHPMailer();
            $item = $param->Item;
//            print_r($param->CommandParameter);
            switch ($param->CommandParameter){
                case 'ReportsRegisterJob':
                    $SOAPClient->ReportsRegisterCronJob($item->lst_idta_berichte->Text);
                    break;
                case 'ReportsUnregisterJob':
                    $SOAPClient->ReportsUnregisterCronJob($item->lst_idta_berichte->Text);
                    break;
                default:
                    $SOAPClient->mailMyReports($item->lst_idta_berichte->Text);
            }
        }

      public function open_Commatrix($sender,$param){
            $url=$this->getRequest()->constructUrl('page','reports.commatrix.CommunicationMatrixXML',array('idta_berichte'=>$param->Item->lst_idta_berichte->Text));
            $this->Response->redirect($url);
      }

        public function bindDropDown(){
            $this->idta_bericht_type->DataSource=PFH::build_SQLPullDown(BerichtTypeRecord::finder(),"ta_bericht_type",array("idta_bericht_type","ber_type_name"));
            $this->idta_bericht_type->dataBind();

            $HRKEYTest = new PFHierarchyPullDown();
            $HRKEYTest->setStructureTable("tm_organisation");
            $HRKEYTest->setRecordClass(OrganisationRecord::finder());
            $HRKEYTest->setPKField("idtm_organisation");
            $HRKEYTest->setField("org_name");
            $HRKEYTest->letsrun();
            $this->rcidtm_organisation->DataSource=$HRKEYTest->myTree;
            $this->rcidtm_organisation->dataBind();

            $ar_ber_zyklus=array(0=>'select',1=>'daily',2=>'weekly',3=>'monthly');
            $ar_ber_zyklus_gap=array(1=>'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
//            for($i=0;$i<=30;$i++){
//                $ar_ber_zyklus_gap[$i]=$i+1;
//            }
            $this->ber_zyklus->DataSource=$ar_ber_zyklus;
            $this->ber_zyklus->dataBind();
            $this->ber_zyklus_gap->DataSource=$ar_ber_zyklus_gap;
            $this->ber_zyklus_gap->dataBind();
        }

        public function loadBerichte(){
            $this->lstBerichte->DataSource=BerichteRecord::finder()->findAll();
            $this->lstBerichte->dataBind();
        }

        public function loadBerichteOrganisation($sender,$param){
            $this->lstBerichteOrganisation->DataSource=BerichteOrganisationRecord::finder()->findAllByidta_berichte($this->page->idta_berichte->Text);
            $this->lstBerichteOrganisation->dataBind();
        }

        public function editlstBerichte($sender,$param){
            $item = $param->Item;
            $myitem=BerichteRecord::finder()->findByPK($item->lst_idta_berichte->Text);

            $monus = $this->BRprimarykey;

            $this->$monus->Text = $myitem->$monus;

            //HIDDEN
            foreach ($this->BRhiddenfields as $recordfield){
                    $this->$recordfield->setText($myitem->$recordfield);
            }
            //DATUM
            foreach ($this->BRdatfields as $recordfield){
                    $this->$recordfield->setDate($myitem->$recordfield);
            }
            //BOOL
            foreach ($this->BRboolfields as $recordfield){
                    $this->$recordfield->setChecked($myitem->$recordfield);
            }
            //CHECKBOX
            foreach ($this->BRcheckboxfields as $recordfield){
                $string = strval($myitem->$recordfield);
                $values = array();
                for($i = 0, $j = strlen($string);$i < $j;$i++){
                    $values[] = $string[$i];
                }
                $this->$recordfield->setSelectedValues($values);
            }
            //TIME
            foreach ($this->BRtimefields as $recordfield){
                    $my_time = explode(':',$myitem->$recordfield);
                    $my_time_text = $my_time[0].':'.$my_time[1];
                    $this->$recordfield->Text = $my_time_text;
            }
            //NON DATUM
            foreach ($this->BRfields as $recordfield){
                    $this->$recordfield->Text = $myitem->$recordfield;
            }
            $this->berichte_edit_status->Text = 1;
            $this->loadBerichte();
            $this->loadBerichteOrganisation($sender, $param);
            $this->BORNewClicked($sender,$param);
            $this->getLogger($sender, $param);
        }

        public function editlstBerichteOrganisation($sender,$param){
            $item = $param->Item;
            $myitem=BerichteOrganisationRecord::finder()->findByPK($item->lst_idtm_berichte_has_organisation->Text);

            $monus = $this->BORprimarykey;

            $this->$monus->Text = $myitem->$monus;
            
            //HIDDEN
            foreach ($this->BORhiddenfields as $recordfield){
                    $this->{'rc'.$recordfield}->setText($myitem->$recordfield);
            }
            //DATUM
            foreach ($this->BORdatfields as $recordfield){
                    $this->{'rc'.$recordfield}->setDate($myitem->$recordfield);
            }
            //BOOL
            foreach ($this->BORboolfields as $recordfield){
                    $this->{'rc'.$recordfield}->setChecked($myitem->$recordfield);
            }
            //TIME
            foreach ($this->BORtimefields as $recordfield){
                    $my_time = explode(':',$myitem->{'rc'.$recordfield});
                    $my_time_text = $my_time[0].':'.$my_time[1];
                    $this->{'rc'.$recordfield}->Text = $my_time_text;
            }
            //NON DATUM
            foreach ($this->BORfields as $recordfield){
                    $this->{'rc'.$recordfield}->Text = $myitem->$recordfield;
            }
            $this->berichteorganisation_edit_status->Text = 1;
            $this->loadBerichteOrganisation($sender, $param);
        }

	public function BRDeleteClicked($sender,$param){
            $Record = BerichteRecord::finder()->findByPK($this->{$this->BRprimarykey}->Text);
            $Record->delete();
            $this->loadBerichte();
            $this->BRNewClicked($sender,$param);
        }

        public function BORDeleteClicked($sender,$param){
            $Record = BerichteOrganisationRecord::finder()->findByPK($this->{$this->BORprimarykey}->Text);
            $Record->delete();
            $this->loadBerichteOrganisation($sender,$param);
            $this->BORNewClicked($sender,$param);
        }

        public function BRSaveClicked($sender,$param){
                if($this->berichte_edit_status->Text == '1'){
			$BREditRecord = BerichteRecord::finder()->findByPK($this->{$this->BRprimarykey}->Text);
		}
		else{
			$BREditRecord = new BerichteRecord;
		}
		//HIDDEN
		foreach ($this->BRhiddenfields as $recordfield){
			$BREditRecord->$recordfield = $this->$recordfield->Value;
		}
		//DATUM
		foreach ($this->BRdatfields as $recordfield){
			$BREditRecord->$recordfield=date('Y-m-d',$this->$recordfield->TimeStamp);
		}
		//BOOL
		foreach ($this->BRboolfields as $recordfield){
			$BREditRecord->$recordfield = $this->$recordfield->Checked?1:0;
		}
		//CHECKBOX
                foreach ($this->BRcheckboxfields as $recordfield){
                    $indices = $this->$recordfield->SelectedIndices;
                    $tmp = '';
                    foreach($indices AS $index){
                        $item = $this->$recordfield->Items[$index];
                        $tmp.=$item->Value;
                    }
                    $BREditRecord->$recordfield = $tmp;
                }
                foreach ($this->BRtimefields as $recordfield){
			$BREditRecord->$recordfield = $this->$recordfield->Text;
		}
                foreach ($this->BRfields as $recordfield){
			$BREditRecord->$recordfield = $this->$recordfield->Text;
		}
		$BREditRecord->save();
		$this->loadBerichte();
	}

        public function BORSaveClicked($sender,$param){
                if($this->berichteorganisation_edit_status->Text == '1'){
			$BREditRecord = BerichteOrganisationRecord::finder()->findByPK($this->{$this->BORprimarykey}->Text);
		}
		else{
			$BREditRecord = new BerichteOrganisationRecord;
		}
		//HIDDEN
		foreach ($this->BORhiddenfields as $recordfield){
			$BREditRecord->$recordfield = $this->{'rc'.$recordfield}->Value;
		}
		//DATUM
		foreach ($this->BORdatfields as $recordfield){
			$BREditRecord->$recordfield=date('Y-m-d',$this->{'rc'.$recordfield}->TimeStamp);
		}
		//BOOL
		foreach ($this->BORboolfields as $recordfield){
			$BREditRecord->$recordfield = $this->{'rc'.$recordfield}->Checked?1:0;
		}
		foreach ($this->BORtimefields as $recordfield){
			$BREditRecord->$recordfield = $this->{'rc'.$recordfield}->Text;
		}
                foreach ($this->BORfields as $recordfield){
			$BREditRecord->$recordfield = $this->{'rc'.$recordfield}->Text;
		}
                $BREditRecord->idta_berichte=$this->idta_berichte->Text;
		$BREditRecord->save();
                $this->loadBerichteOrganisation($sender,$param);
	}

	public function BRNewClicked($sender,$param){
            $monus = $this->BRprimarykey;
            $this->$monus->Text = '0';

                //HIDDEN
		foreach ($this->BRhiddenfields as $recordfield){
			$this->$recordfield->setValue('0');
		}
		//DATUM
		foreach ($this->BRdatfields as $recordfield){
			$this->$recordfield->setDate(date('Y-m-d',time()));
		}
		//BOOL
		foreach ($this->BRboolfields as $recordfield){
			$this->$recordfield->Checked(0);
		}
		//CHECKBOX
                foreach ($this->BRcheckboxfields as $recordfield){
                        $this->$recordfield->SetSelectedValue(0);
                }
                //NON DATUM
		foreach ($this->BRtimefields as $recordfield){
			$this->$recordfield->Text = '00:00';
		}
                //NON DATUM
		foreach ($this->BRfields as $recordfield){
			$this->$recordfield->Text = '0';
		}
		$this->berichte_edit_status->Text = '0';
        }

        public function BORNewClicked($sender,$param){
            $monus = $this->BORprimarykey;
            $this->$monus->Text = '0';

                //HIDDEN
		foreach ($this->BORhiddenfields as $recordfield){
			$this->{'rc'.$recordfield}->setValue('0');
		}
		//DATUM
		foreach ($this->BORdatfields as $recordfield){
			$this->{'rc'.$recordfield}->setDate(date('Y-m-d',time()));
		}
		//BOOL
		foreach ($this->BORboolfields as $recordfield){
			$this->{'rc'.$recordfield}->Checked(0);
		}
		//NON DATUM
		foreach ($this->BORtimefields as $recordfield){
			$this->{'rc'.$recordfield}->Text = '00:00';
		}
                //NON DATUM
		foreach ($this->BORfields as $recordfield){
			$this->{'rc'.$recordfield}->Text = '0';
		}
                $this->rcidtm_organisation->Text=1;
		$this->berichteorganisation_edit_status->Text = '0';
        }

        public function lstBerichte_PageIndexChanged($sender,$param)
            {
		$this->lstBerichte->CurrentPageIndex = $param->NewPageIndex;
		$this->loadBerichte();
            }

        public function lstBerichteOrganisation_PageIndexChanged($sender,$param)
            {
		$this->lstBerichteOrganisation->CurrentPageIndex = $param->NewPageIndex;
		$this->loadBerichteOrganisation($sender,$param);
            }
    
}
?>